<?php
// This file is part of the Course Archiver plugin.

define('LOCAL_COURSE_ARCHIVER_STATUS_ACTIVE', 1);
define('LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED', 2);


/**
 * Get the archive status values with their labels
 * 
 * @return array Array of status values with their labels
 */
function local_course_archiver_get_status_values() {
    return [
        LOCAL_COURSE_ARCHIVER_STATUS_ACTIVE => get_string('status_active', 'local_course_archiver'),
        LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED => get_string('status_archived', 'local_course_archiver')
    ];
}

/**
 * Get the label for a specific status value
 * 
 * @param int $value The status value
 * @return string The status label
 */
function local_course_archiver_get_status_label($value) {
    $value = (int)$value;
    $statuses = local_course_archiver_get_status_values();
    
    // Default to active status if value not found
    return $statuses[$value] ?? get_string('status_active', 'local_course_archiver');
}

/**
 * Get the archive status of a course
 * 
 * @param int $courseid The course ID
 * @return int The archive status value (1 for active, 2 for archived)
 */
function local_course_archiver_get_course_status($courseid) {
    global $DB;
    
    $field = $DB->get_record('customfield_field', ['shortname' => 'archive_status'], '*', IGNORE_MULTIPLE);
    if (!$field) {
        return LOCAL_COURSE_ARCHIVER_STATUS_ACTIVE; // Default to active if field not found
    }
    
    $record = $DB->get_record('customfield_data', 
        ['instanceid' => $courseid, 'fieldid' => $field->id],
        'value',
        IGNORE_MULTIPLE
    );
    
    // Convert to int and validate the value
    $status = $record ? (int)$record->value : LOCAL_COURSE_ARCHIVER_STATUS_ACTIVE;
    return in_array($status, [LOCAL_COURSE_ARCHIVER_STATUS_ACTIVE, LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED]) 
        ? $status 
        : LOCAL_COURSE_ARCHIVER_STATUS_ACTIVE;
}

/**
 * Check if a course is archived
 * 
 * @param int|stdClass $course Course ID or course object
 * @return bool True if the course is archived, false otherwise
 */
function local_course_archiver_is_course_archived($course) {
    global $DB;
    
    if (is_object($course)) {
        $courseid = $course->id;
    } else {
        $courseid = $course;
    }
    
    $status = local_course_archiver_get_course_status($courseid);
    return ($status === LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED);
}

/**
 * Check if a user can access an archived course
 * 
 * @param stdClass $course The course object
 * @param int $userid The user ID (0 for current user)
 * @return bool True if the user can access the archived course
 */
function local_course_archiver_can_access_archived_course($course, $userid = 0) {
    global $USER;
    
    // If the course is not archived, return true (let other access controls handle it)
    if (!local_course_archiver_is_course_archived($course)) {
        return true;
    }
    
    // Get the user ID if not provided
    if (empty($userid)) {
        $userid = $USER->id;
    }
    
    // Check if the user has the capability to access archived courses
    $context = context_course::instance($course->id);
    return has_capability('local/course_archiver:accessarchived', $context, $userid);
}
