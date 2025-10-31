<?php
// local/course_archiver/classes/task/archive_courses.php

namespace local_course_archiver\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/course_archiver/locallib.php');

class archive_courses extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('taskname', 'local_course_archiver');
    }

    public function execute() {
        global $DB;
        
        // 1. الخروج إذا كانت الأرشفة التلقائية مُعطلة
        if (!get_config('local_course_archiver', 'enable_auto_archiving')) {
            return;
        }

        // 2. قراءة إعدادات الشروط
        $days_after_enddate = (int)get_config('local_course_archiver', 'archive_after_enddate');
        $days_no_activity = (int)get_config('local_course_archiver', 'archive_after_noactivity');
        
        if ($days_after_enddate <= 0 && $days_no_activity <= 0) {
            return;
        }
        
        // 3. تحديد الحقل المخصص وقيمته
        $field_shortname = 'archive_status';
        $customfield = $DB->get_record('customfield_fields', ['shortname' => $field_shortname, 'component' => 'core_course']);
        if (!$customfield) {
            // يمكن استبدال هذه الرسالة بنظام إخطارات للمدير
            return;
        }
        
        $customfieldid = $customfield->id;
        $archived_value = LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED;

        // 4. بناء استعلام SQL للمقررات المرشحة للأرشفة
        $now = time();
        $params = [];
        $sql_conditions = [];

        // الشرط الأول: انتهاء المقرر منذ X يوم (enddate)
        if ($days_after_enddate > 0) {
            $enddate_threshold = $now - ($days_after_enddate * DAYSECS);
            // ابحث عن المقررات التي لديها تاريخ انتهاء محدد (enddate > 0) وتجاوزت الحد
            $sql_conditions[] = "(c.enddate > 0 AND c.enddate < :enddate_thresh)";
            $params['enddate_thresh'] = $enddate_threshold;
        }

        // الشرط الثاني: عدم وجود نشاط طلابي منذ X يوم (lastcourseaccess)
        if ($days_no_activity > 0) {
            $activity_threshold = $now - ($days_no_activity * DAYSECS);
            // ابحث عن المقررات التي لم يتم الوصول إليها مؤخراً
            $sql_conditions[] = "c.lastcourseaccess < :activity_thresh";
            $params['activity_thresh'] = $activity_threshold;
        }
        
        $conditions_sql = implode(' OR ', $sql_conditions); // يكفي تحقيق أي من الشرطين للأرشفة

        // الاستعلام عن المقررات (تجاهل الموقع الرئيسي وتجنب أرشفة المقرر المؤرشف مسبقًا)
        $sql = "
            SELECT c.id, c.fullname
            FROM {course} c
            LEFT JOIN {customfield_data} cfd ON cfd.instanceid = c.id AND cfd.fieldid = :fieldid
            WHERE 
                c.id != :siteid AND c.visible = 1 AND
                ({$conditions_sql}) AND
                (cfd.value IS NULL OR cfd.value != :archived_val OR cfd.value = '')
        ";
        
        $params['fieldid'] = $customfieldid;
        $params['siteid'] = SITEID;
        $params['archived_val'] = $archived_value_raw;
        
        $courses_to_archive = $DB->get_records_sql($sql, $params);
        
        // 5. تنفيذ التحديث
        if (!empty($courses_to_archive)) {
            $cf_manager = new \core_course_customfield\course_manager();
            
            foreach ($courses_to_archive as $course) {
                // تحديث قيمة الحقل المخصص إلى 'archived'
                $cf_manager->set_field_value($customfieldid, $course->id, $archived_value_raw);
                $cf_manager->save_field_values($course->id);
                
                // ملاحظة: مُعالج الأحداث course_update_handler الذي أنشأناه في الخطوة السابقة 
                // سيكتشف تلقائياً تغيير قيمة الحقل المخصص وسيقوم بإخفاء المقرر (visible = 0).
                
                // تسجيل الإجراء
                add_to_log($course->id, 'local_course_archiver', 'auto_archived', '', "تم أرشفة المقرر تلقائيًا بواسطة المهمة المجدولة.");
            }
        }
    }
}