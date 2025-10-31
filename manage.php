<?php
// This file is part of the Course Archiver plugin.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course archiver management interface.
 *
 * @package    local_course_archiver
 * @copyright  2025 Mahdi Mohamed <contact@engmahdi.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');
require_once(__DIR__ . '/locallib.php');

// Set up the page and check permissions.
admin_externalpage_setup('local_course_archiver_manage');

$title = get_string('management_page_label', 'local_course_archiver');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Constants for archive status
$fieldshortname = 'archive_status';
$activevalue = LOCAL_COURSE_ARCHIVER_STATUS_ACTIVE;
$archivedvalue = LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED;

// ----------------------------------------------------
// 1. معالجة الإجراءات الجماعية (Bulk Action Processing)
// ----------------------------------------------------

$action = optional_param('action', '', PARAM_ALPHA);
$courseids = optional_param_array('course_ids', array(), PARAM_INT);
$message = '';

if (($data = data_submitted()) && confirm_sesskey() && !empty($courseids) && in_array($data->action, ['set_active', 'set_archived'])) {
    $newstatus = ($data->action === 'set_archived') ? $archivedvalue : $activevalue;
    $count = 0;
    
    if ($customfield = $DB->get_record('customfield_field', ['shortname' => $fieldshortname], '*', IGNORE_MULTIPLE)) {
        $handler = \core_course\customfield\course_handler::create();
        $transaction = $DB->start_delegated_transaction();
        
        try {
            foreach ($courseids as $courseid) {
                $context = \context_course::instance($courseid);
                
                // Get and update the custom field
                $fielddata = null;
                foreach ($handler->get_instance_data($courseid, true) as $item) {
                    if ($item->get_field()->get('shortname') === $fieldshortname) {
                        $fielddata = $item;
                        break;
                    }
                }
                
                if ($fielddata) {
                    // Update custom field
                    $fielddata->set('contextid', $context->id)
                             ->set('valueformat', FORMAT_PLAIN)
                             ->set('value', (int)$newstatus)
                             ->save();
                    
                    // Update course visibility if needed
                    $shouldbevisible = ((int)$newstatus !== LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED);
                    $course = get_course($courseid);
                    
                    if ($course->visible != $shouldbevisible) {
                        course_change_visibility($courseid, $shouldbevisible);
                    }
                    
                    $count++;
                }
            }
            
            $transaction->allow_commit();
            
            // Clear caches for all affected courses
            foreach ($courseids as $courseid) {
                rebuild_course_cache($courseid, true);
            }
            
            // Set success message
            $messagekey = ($newstatus == $archivedvalue) ? 'bulk_archive_success' : 'bulk_active_success';
            $message = get_string($messagekey, 'local_course_archiver', $count);
            
        } catch (\Exception $e) {
            if (isset($transaction) && $transaction->is_active()) {
                $transaction->rollback($e);
            }
            \core\notification::error(get_string('updateerror', 'local_course_archiver', $e->getMessage()));
            debugging('Error updating courses: ' . $e->getMessage(), DEBUG_DEVELOPER);
            $message = get_string('updateerror', 'local_course_archiver', $e->getMessage());
        }
    } else {
        $message = get_string('field_not_found', 'local_course_archiver');
    }
}

// ----------------------------------------------------
// 2. إخراج الصفحة والعنوان والرسائل
// ----------------------------------------------------
$PAGE->set_title($title);
echo $OUTPUT->header();

// عرض رسالة النجاح أو الخطأ
if (!empty($message)) {
    echo $OUTPUT->notification($message, 'success');
}

// ----------------------------------------------------
// 3. بناء جدول عرض المقررات (Filtering and Display)
// ----------------------------------------------------

// تعريف الجدول القابل للتحديد
$table = new \html_table();
$table->id = 'archiver_courses_table';

// تعريف الأعمدة
$table->head = array(
    get_string('id', 'local_course_archiver'),
    get_string('course_full_name', 'local_course_archiver'),
    get_string('course_short_name', 'local_course_archiver'),
    get_string('current_archive_status', 'local_course_archiver'),
    get_string('visibility', 'local_course_archiver')
);

// تحديد العرض
$table->align = array('left', 'left', 'left', 'center', 'center');
$table->attributes = ['class' => 'generaltable'];

// Get all courses (excluding site course)
$courses = $DB->get_records_sql(
    'SELECT id, fullname, shortname, visible FROM {course} WHERE id <> ? ORDER BY fullname ASC',
    [SITEID],
    0, 0  // No limits
);

$rows = [];
$courseids = array_keys($courses);

if (!empty($courseids)) {
    // Get all custom field values in one query to reduce DB load
    $cfdata = [];
    list($insql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
    $sql = "SELECT * FROM {customfield_data} WHERE instanceid $insql";
    $records = $DB->get_records_sql($sql, $params);
    foreach ($records as $record) {
        $cfdata[$record->instanceid] = $record;
    }
    
    $customfield = $DB->get_record('customfield_field', ['shortname' => $fieldshortname], '*', IGNORE_MULTIPLE);
    $customfieldid = $customfield ? $customfield->id : 0;
    
    // Build table rows
    foreach ($courses as $course) {
        if ($course->id == SITEID) {
            continue; // Skip site course
        }

        // Get current archive status
        $currentstatus = $activevalue; // Default value
        if (isset($cfdata[$course->id]) && $cfdata[$course->id]->fieldid == $customfieldid) {
            $currentstatus = (int)$cfdata[$course->id]->value;
        }

        // Get visibility status
        $visibility = (int)$course->visible === 1 
            ? get_string('visible', 'local_course_archiver') 
            : get_string('hidden', 'local_course_archiver');

        // Get status label
        $statuslabel = ($currentstatus === $archivedvalue) 
            ? get_string('status_archived', 'local_course_archiver')
            : get_string('status_active', 'local_course_archiver');
            
        $rows[] = [
            $course->id,
            html_writer::link(
                new moodle_url('/course/view.php', ['id' => $course->id]),
                format_string($course->fullname)
            ),
            format_string($course->shortname),
            $statuslabel,
            $visibility
        ];
    }
}

// 4. بناء النموذج (Form) للإجراءات الجماعية
echo html_writer::start_tag('form', [
    'action' => new moodle_url('/local/course_archiver/manage.php'),
    'method' => 'post',
    'id' => 'course_archiver_form'
]);
echo html_writer::empty_tag('input', [
    'type' => 'hidden',
    'name' => 'sesskey',
    'value' => sesskey()
]);

// إنشاء جدول المقررات القابل للتحديد (Selectable Table)
// Set up the table with improved styling
$table = new \flexible_table('course_archiver_table');
$table->define_columns(['select', 'id', 'fullname', 'shortname', 'status', 'visible']);
$table->define_headers([
    html_writer::checkbox('selectall', 1, false, '', ['id' => 'selectall', 'class' => 'm-1']),
    get_string('id', 'local_course_archiver'),
    get_string('fullnamecourse'),
    get_string('shortname'),
    get_string('archiverstatus', 'local_course_archiver'),
    get_string('visible')
]);

// Add responsive container and proper spacing
echo html_writer::start_div('container-fluid py-3');
echo html_writer::start_div('card');
echo html_writer::start_div('card-body');

$table->set_attribute('class', 'generaltable table-hover table-striped');
$table->set_attribute('id', 'archiver-courses-select-form');
$table->set_attribute('style', 'width: 100%; margin: 0;');
$table->column_style_all('text-align', 'center');
$table->column_style('fullname', 'text-align', 'right');
$table->column_style('shortname', 'text-align', 'right');
$table->column_style('status', 'text-align', 'center');
$table->column_style('visible', 'text-align', 'center');

$table->define_baseurl($PAGE->url);
$table->setup();

// إضافة الصفوف إلى الجدول
foreach ($rows as $row) {
    $checkbox = html_writer::checkbox('course_ids[]', $row[0], false, '', ['class' => 'm-1 course-select']);
    $table->add_data([
        $checkbox,
        $row[0], // ID
        $row[1], // Course name with link
        $row[2], // Short name
        $row[3], // Status
        $row[4]  // Visibility
    ]);
}

if (!empty($rows)) {
    echo html_writer::tag('h3', get_string('available_courses', 'local_course_archiver'), ['class' => 'mb-4']);
    $table->finish_output();
    
    // Add select all/none functionality and form validation
    $PAGE->requires->js_amd_inline(
        "require(['jquery'], function($) {
            // Toggle all checkboxes when select all is clicked
            $('#selectall').change(function() {
                $('.course-select').prop('checked', $(this).prop('checked')).trigger('change');
            });
            
            // Function to validate form and update button state
            function validateForm() {
                var anyChecked = $('.course-select:checked').length > 0;
                var actionSelected = $('#action_select').val() !== '';
                $('#apply_action_btn').prop('disabled', !(anyChecked && actionSelected));
                return anyChecked && actionSelected;
            }
            
            // Update form state when any checkbox changes
            $(document).on('change', '.course-select', function() {
                // Update select all checkbox state
                var allChecked = $('.course-select:not(:checked)').length === 0;
                $('#selectall').prop('checked', allChecked);
                validateForm();
            });
            
            // Update form state when action selection changes
            $('#action_select').change(function() {
                validateForm();
            });
            
            // Prevent form submission if validation fails
            $('#course_archiver_form').on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    if ($('.course-select:checked').length === 0) {
                        alert(M.util.get_string('no_courses_selected', 'local_course_archiver'));
                    } else if ($('#action_select').val() === '') {
                        alert(M.util.get_string('no_action_selected', 'local_course_archiver'));
                    }
                    return false;
                }
                return true;
            });
            
            // Initial form state
            validateForm();
        });"
    );
    
    // Close card and container divs
    echo html_writer::end_div(); // Close card-body
    echo html_writer::end_div(); // Close card
    echo html_writer::end_div(); // Close container-fluid
} else {
    echo $OUTPUT->notification(get_string('no_courses_to_manage', 'local_course_archiver'), 'info');
    echo html_writer::end_div(); // Close card-body
    echo html_writer::end_div(); // Close card
    echo html_writer::end_div(); // Close container-fluid
}

// 5. قسم الإجراءات الجماعية
echo $OUTPUT->box_start();
echo html_writer::tag('h3', get_string('bulk_actions', 'local_course_archiver'));

echo html_writer::label(get_string('select_action', 'local_course_archiver'), 'action_select');
echo html_writer::start_tag('select', [
    'name' => 'action',
    'id' => 'action_select',
    'class' => 'select custom-select',
    'required' => 'required'
]);
echo html_writer::tag('option', get_string('select_action_placeholder', 'local_course_archiver'), ['value' => '']);
echo html_writer::tag('option', get_string('action_set_archived', 'local_course_archiver'), ['value' => 'set_archived']);
echo html_writer::tag('option', get_string('action_set_active', 'local_course_archiver'), ['value' => 'set_active']);
echo html_writer::end_tag('select');
echo html_writer::tag('input', '', [
    'type' => 'submit', 
    'value' => get_string('apply_action', 'local_course_archiver'), 
    'class' => 'btn btn-primary ml-2',
    'id' => 'apply_action_btn',
    'disabled' => 'disabled' // Initially disabled until a course is selected
]);

echo $OUTPUT->box_end();

echo html_writer::end_tag('form');

echo $OUTPUT->footer();