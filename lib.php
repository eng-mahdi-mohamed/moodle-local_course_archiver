<?php
// This file is part of the Course Archiver plugin.

defined('MOODLE_INTERNAL') || die();

/**
 * Add navigation nodes and handle course access
 *
 * @param global_navigation $navigation The navigation node to extend
 * @return void
 */
function local_course_archiver_extend_navigation(global_navigation $navigation) {
    global $PAGE, $COURSE, $USER, $OUTPUT;

    // Only proceed if we're in a course context and not on the course view page
    if ($PAGE->context->contextlevel !== CONTEXT_COURSE || $COURSE->id <= 1) {
        return;
    }

    // Check if the course is archived
    require_once(__DIR__ . '/locallib.php');
    
    if (local_course_archiver_is_course_archived($COURSE)) {
        // Add a visual indicator that this is an archived course
        $PAGE->set_heading($PAGE->heading . ' ' . $OUTPUT->pix_icon('i/archive', get_string('archived', 'local_course_archiver')));
        
        // Check access permissions
        if (!local_course_archiver_can_access_archived_course($COURSE, $USER->id)) {
            // For API/CLI requests, throw an exception
            if (AJAX_SCRIPT || CLI_SCRIPT) {
                throw new moodle_exception('coursearchived', 'local_course_archiver', new moodle_url('/'));
            }
            
            // For web requests, redirect to site home with a message
            redirect(
                new moodle_url('/'),
                get_string('coursearchived', 'local_course_archiver'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    }
}

/**
 * Callback to check if current course is accessible
 *
 * @param stdClass $course The course object
 * @param stdClass $user The user object
 * @return bool|null True if accessible, false if not, null to use other checks
 */
function local_course_archiver_course_access_check($course, $user) {
    require_once(__DIR__ . '/locallib.php');
    
    // Only handle archived courses
    if (!local_course_archiver_is_course_archived($course)) {
        return null;
    }
    
    // Check if user has permission to access archived courses
    return local_course_archiver_can_access_archived_course($course, $user->id);
}

/**
 * Add settings link to course navigation for users with appropriate permissions
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course object
 * @param context $context The course context
 * @return void
 */
function local_course_archiver_extend_navigation_course(navigation_node $navigation, stdClass $course, context $context) {
    global $PAGE, $USER;
    
    // Only add for users with appropriate permissions
    if (!has_capability('local/course_archiver:manage', $context)) {
        return;
    }
    
    // Add a link to the course archiving settings
    $url = new moodle_url('/local/course_archiver/index.php', ['id' => $course->id]);
    $navigation->add(
        get_string('archive_settings', 'local_course_archiver'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'coursearchiver',
        new pix_icon('i/settings', '')
    );
}
