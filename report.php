<?php
// local/course_archiver/report.php

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/course/lib.php');

// يجب استخدام الـ ID الذي تم تعريفه في settings.php
admin_externalpage_setup('local_course_archiver_report'); 

$title = get_string('report_page_label', 'local_course_archiver');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Add CSS to the page
$PAGE->requires->css('/local/course_archiver/styles.css');
$PAGE->requires->css('/local/course_archiver/styles/report.css');


// Include constants and functions
require_once(__DIR__ . '/locallib.php');

// Define constants if not already defined
if (!defined('LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED')) {
    define('LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED', 1);
    define('LOCAL_COURSE_ARCHIVER_STATUS_ACTIVE', 0);
}

// استخدام واجهة برمجة التطبيقات (API) لحقول مودل المخصصة
$field_shortname = 'archive_status';
$archived_value = LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED;

// 1. جلب الحقل المخصص باستخدام الـ API
$handler = core_course\customfield\course_handler::create();

// Handle export functionality before any output
$export = optional_param('export', 0, PARAM_BOOL);
if ($export && confirm_sesskey()) {
    global $DB;
    
    // Clear any previous output
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Get the custom field
    $field = $DB->get_record('customfield_field', ['shortname' => 'archive_status']);
    
    if ($field) {
        // Get all courses with archive_status = 1 (archived)
        $sql = "SELECT c.*, cat.name as categoryname
                FROM {course} c
                JOIN {customfield_data} cd ON cd.instanceid = c.id
                JOIN {course_categories} cat ON cat.id = c.category
                WHERE cd.fieldid = :fieldid 
                AND cd.value = :archivedvalue
                AND c.id != :siteid
                ORDER BY c.fullname ASC";
            
        $params = [
            'fieldid' => $field->id,
            'archivedvalue' => LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED,
            'siteid' => SITEID
        ];
        
        $archived_courses = $DB->get_records_sql($sql, $params);
        
        // Set filename with current date
        $filename = 'archived_courses_' . date('Y-m-d') . '.csv';
        
        // Set headers for download
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fputs($output, "\xEF\xBB\xBF");
        
        // Add CSV headers in Arabic
        $headers = [
            get_string('course', 'moodle'),
            get_string('shortname', 'moodle'),
            get_string('visible', 'moodle'),
            get_string('category'),
            'ID',
            'تاريخ الإنشاء',
            'آخر تحديث',
            'الرابط'
        ];
        fputcsv($output, $headers);
        
        // Add data rows
        foreach ($archived_courses as $course) {
            $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
            // Get category name, handle potential missing category
            $categoryname = '';
            if (isset($course->categoryname)) {
                $categoryname = format_string($course->categoryname);
            } else {
                // Fallback: Get category name separately if not in initial query
                $category = core_course_category::get($course->category, IGNORE_MISSING);
                $categoryname = $category ? format_string($category->name) : get_string('unknown', 'local_course_archiver');
            }
            
            $row = [
                format_string($course->fullname),
                format_string($course->shortname),
                $course->visible ? get_string('yes') : get_string('no'),
                $categoryname,
                $course->id,
                userdate($course->timecreated, get_string('strftimedatetime')),
                userdate($course->timemodified, get_string('strftimedatetime')),
                $courseurl->out(false)
            ];
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit();
    }
}

// Add page header
echo $OUTPUT->header();

// Add inline CSS directly in the page
echo '
<style>
.local-course-archiver-report .card { 
    border: none; 
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); 
}

/* Style for action buttons */
.d-flex.justify-content-center {
    gap: 0.25rem;
}

/* Ensure buttons have consistent size */
.btn-sm {
    min-width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem 0.5rem;
}

/* Ensure icons are properly centered */
.btn-sm .icon {
    margin: 0;
    padding: 0;
}
</style>';


$field = null;

// البحث في جميع الفئات عن الحقل المطلوب
$categories = $handler->get_categories_with_fields();
foreach ($categories as $category) {
    $fields = $category->get_fields();
    foreach ($fields as $f) {
        if ($f->get('shortname') === $field_shortname) {
            $field = $f;
            break 2; // الخروج من الحلقتين عند العثور على الحقل
        }
    }
}

if ($field) {
    global $DB;
    
    // 2. البحث عن المقررات المؤرشفة باستخدام استعلام SQL مباشر
    $sql = "SELECT c.*, cc.name as categoryname
            FROM {course} c
            JOIN {customfield_data} cd ON cd.instanceid = c.id
            JOIN {course_categories} cc ON cc.id = c.category
            WHERE cd.fieldid = :fieldid 
            AND cd.value = :archivedvalue
            AND c.id != :siteid
            ORDER BY c.fullname ASC";
            
    $params = [
        'fieldid' => $field->get('id'),
        'archivedvalue' => $archived_value,
        'siteid' => SITEID
    ];
    
    // الحصول على المقررات المؤرشفة مباشرة من قاعدة البيانات
    $archived_courses = $DB->get_records_sql($sql, $params);
    
    // If no courses found using direct query, try the alternative method
    if (empty($archived_courses)) {
        $archived_courses = [];
        $courses = get_courses();
        
        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue; // تخطي الموقع الرئيسي
            }
            
            // الحصول على حالة الأرشفة باستخدام الدالة المساعدة
            $status = local_course_archiver_get_course_status($course->id);
            if ($status == $archived_value) {
                $archived_courses[] = $course;
            }
        }
    }
    
        // 3. عرض النتائج في جدول
    if (!empty($archived_courses)) {
        // رأس الصفحة
        echo html_writer::start_div('container-fluid py-3');
        
        // Report card header
        echo html_writer::start_div('card mb-4');
        echo html_writer::start_div('card-header bg-primary text-white d-flex justify-content-between align-items-center');
        echo html_writer::tag('h4', get_string('report_page_heading', 'local_course_archiver'), ['class' => 'mb-0']);
        
        // Export button in header
        echo html_writer::link(
            new moodle_url($PAGE->url, ['export' => '1', 'sesskey' => sesskey()]),
            $OUTPUT->pix_icon('t/download', '') . ' ' . get_string('export', 'local_course_archiver'),
            ['class' => 'btn btn-light btn-sm']
        );
        
        echo html_writer::end_div(); // Close card-header
        echo html_writer::start_div('card-body');
        
        // Quick statistics
        $count = count($archived_courses);
        echo html_writer::div(
            html_writer::span(get_string('total_archived_courses', 'local_course_archiver', $count), 'badge bg-info mb-3 p-2'),
            'text-center'
        );
        
        // إنشاء الجدول
        $table = new \flexible_table('archived_courses_table');
        $table->define_columns(['fullname', 'shortname', 'visible', 'category', 'actions']);
        $table->define_headers([
            get_string('course'),
            get_string('shortname'),
            get_string('visible'),
            get_string('category'),
            get_string('actions')
        ]);
        
        // تنسيق الجدول
        $table->set_attribute('class', 'table table-hover table-striped table-bordered');
        $table->set_attribute('style', 'width: 100%; margin: 0;');
        $table->column_style_all('vertical-align', 'middle');
        $table->column_style_all('text-align', 'center');
        $table->column_style('fullname', 'text-align', 'right');
        $table->column_style('shortname', 'text-align', 'right');
        $table->column_style('category', 'text-align', 'right');
        
        $table->define_baseurl($PAGE->url);
        $table->setup();
        
        // إضافة البيانات للجدول
        foreach ($archived_courses as $course) {
            $category = core_course_category::get($course->category, IGNORE_MISSING);
            $categoryname = $category ? format_string($category->name) : '';
            
            // أزرار الإجراءات
            $viewurl = new moodle_url('/course/view.php', ['id' => $course->id]);
            $editurl = new moodle_url('/course/edit.php', ['id' => $course->id]);
            
            $actions = [];
            $actions[] = html_writer::link(
                $viewurl,
                $OUTPUT->pix_icon('i/hide', get_string('view_course', 'local_course_archiver')),
                ['title' => get_string('view_course', 'local_course_archiver'), 'class' => 'btn btn-sm btn-outline-primary me-1']
            );
            
            $actions[] = html_writer::link(
                $editurl,
                $OUTPUT->pix_icon('t/edit', get_string('edit')),
                ['title' => get_string('edit'), 'class' => 'btn btn-sm btn-outline-secondary']
            );
            
            // Wrap actions in a div with flex display
            $actions_html = html_writer::div(implode('', $actions), 'd-flex justify-content-center');
            
            // إضافة الصف للجدول
            $table->add_data([
                html_writer::link($viewurl, format_string($course->fullname), ['class' => 'font-weight-bold']),
                format_string($course->shortname),
                $course->visible ? 
                    html_writer::span(get_string('yes'), 'badge bg-success p-2') : 
                    html_writer::span(get_string('no'), 'badge bg-danger p-2'),
                $categoryname,
                $actions_html
            ]);
        }
        
        // عرض الجدول
        $table->finish_output();
        
        // Export button is now handled at the beginning of the file
        
        // إغلاق العناصر
        echo html_writer::end_div(); // Close card-body
        echo html_writer::end_div(); // Close card
        echo html_writer::end_div(); // Close container-fluid
        
        // Add JavaScript for interactivity
        $PAGE->requires->js_amd_inline(
            "require(['jquery'], function($) {
                // Interactive row effects
                $('.generaltable tbody tr').hover(
                    function() { $(this).addClass('table-primary'); },
                    function() { $(this).removeClass('table-primary'); }
                );
                
                // Confirm before editing
                $('a[title^=\"Edit\"]').on('click', function(e) {
                    if (!confirm('" . get_string('confirm_edit_course', 'local_course_archiver') . "')) {
                        e.preventDefault();
                    }
                });
            });"
        );
        
    } else {
        echo $OUTPUT->box_start('generalbox', 'notice');
        echo $OUTPUT->notification(get_string('no_archived_courses', 'local_course_archiver'), 'info');
        
        // إضافة زر للعودة إلى لوحة التحكم
        echo html_writer::link(
            new moodle_url('/admin/category.php', ['category' => 'coursecatmanagement']),
            get_string('back_to_course_management', 'local_course_archiver'),
            ['class' => 'btn btn-primary']
        );
        
        echo $OUTPUT->box_end();
    }
} else {
    echo $OUTPUT->box_start('generalbox', 'error');
    echo $OUTPUT->notification(get_string('customfield_not_found', 'local_course_archiver', $field_shortname), 'error');
    echo $OUTPUT->box_end();
}

// Add any additional JavaScript for the report page
$PAGE->requires->js_call_amd('local_course_archiver/report', 'init');

echo $OUTPUT->footer();