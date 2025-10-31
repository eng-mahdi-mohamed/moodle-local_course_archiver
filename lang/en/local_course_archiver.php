<?php
// local/course_archiver/lang/en/local_course_archiver.php

$string['pluginname'] = 'Auto Course Archiver';
$string['coursearchived'] = 'This course has been archived and is no longer accessible.';
$string['archived'] = 'Archived';
$string['archive_settings'] = 'Archive Settings';
$string['categoryname'] = 'Course Status';
$string['fieldname'] = 'Archive Status';

// The values that will be displayed to the user:
$string['status_active'] = 'active'; 
$string['status_archived'] = 'archived';

// The settings strings
$string['setting_enable_autohide_label'] = 'Enable auto hide';
$string['setting_enable_autohide_desc'] = 'If enabled, the course will be hidden (Set Hidden) when the "Archive Status" is changed to "Archived", and vice versa.';

// Capability strings
$string['course_archiver:manage'] = 'Manage course archiving settings';
$string['course_archiver:viewreports'] = 'View course archiving reports';
$string['course_archiver:accessarchived'] = 'Access archived courses';
$string['course_archiver:archivecourse'] = 'Archive/unarchive courses';
$string['course_archiver:restore'] = 'Restore courses from archive';
$string['course_archiver:viewarchived'] = 'View archived courses in course listing';

// The settings strings (for auto archiving)
$string['auto_archive_heading'] = 'Auto Archiving Settings';
$string['auto_archive_heading_desc'] = 'These settings allow automatic archiving of courses based on time conditions.';

$string['setting_enable_auto_archiving_label'] = 'Enable Auto Archiving';
$string['setting_enable_auto_archiving_desc'] = 'If enabled, a daily task will be activated to search for courses that meet the archiving conditions below.';

$string['setting_archive_after_enddate_label'] = 'Archive after course end date (days)';
$string['setting_archive_after_enddate_desc'] = 'Number of days after which the course will be archived automatically. Enter 0 to disable this condition.';

$string['setting_archive_after_noactivity_label'] = 'Archive after no student activity (days)';
$string['setting_archive_after_noactivity_desc'] = 'Number of days after which the course will be archived automatically if there is no student activity. Enter 0 to disable this condition.';

$string['taskname'] = 'Auto Course Archiver Task';

// --- General Settings ---
$string['general_settings'] = 'General Settings';
$string['general_settings_desc'] = 'General configuration settings for the Course Archiver plugin.';
$string['setting_enable_plugin'] = 'Enable Plugin';
$string['setting_enable_plugin_desc'] = 'Enable or disable the Course Archiver functionality.';

// --- Email Settings ---
$string['email_heading'] = 'Email Notifications';
$string['email_heading_desc'] = 'Configure email notification settings for course archiving.';
$string['setting_enable_email_notifications'] = 'Enable Email Notifications';
$string['setting_enable_email_notifications_desc'] = 'Send email notifications when courses are archived.';
$string['setting_email_sender'] = 'Notification Sender Email';
$string['setting_email_sender_desc'] = 'Email address to use as the sender for notifications.';
$string['setting_email_cc'] = 'CC Email Addresses';
$string['setting_email_cc_desc'] = 'Comma-separated list of email addresses to CC on notifications.';

// --- Auto Hide Settings ---
$string['auto_hide_heading'] = 'Auto Hide Settings';
$string['auto_hide_heading_desc'] = 'Configure automatic course visibility settings.';

// Archive Status Field
$string['archive_status'] = 'Archive Status';
$string['archive_status_desc'] = 'Determines if the course is active or archived';
$string['archive_status_help'] = 'Set to "Archived" to hide this course from course listings. Set to "Active" to make it visible again.';

// Management Interface
$string['management_page_label'] = 'Course Archiver';
$string['management_page_heading'] = 'Course Archiver Management';
$string['report_page_label'] = 'Archived Courses Report';
$string['report_page_heading'] = 'Archived Courses Report';
$string['available_courses'] = 'Available Courses';
$string['no_courses_to_manage'] = 'No courses available to manage';

// Bulk Actions
$string['bulk_actions'] = 'Bulk Actions';
$string['select_action'] = 'Select Action';
$string['select_action_placeholder'] = '-- Select Action --';
$string['action_set_archived'] = 'Archive Selected Courses';
$string['action_set_active'] = 'Activate Selected Courses';
$string['apply_action'] = 'Apply Action';
$string['bulk_archive_success'] = 'Successfully archived {$a} courses';
$string['bulk_active_success'] = 'Successfully activated {$a} courses';

// Course Table
$string['archiverstatus'] = 'Archive Status';
$string['id'] = 'ID';
$string['course_full_name'] = 'Course Name';
$string['course_short_name'] = 'Short Name';
$string['current_archive_status'] = 'Archive Status';
$string['visibility'] = 'Visibility';
$string['visible'] = 'Visible';
$string['hidden'] = 'Hidden';

// Messages
$string['field_not_found'] = 'Archive status field not found';
$string['no_archived_courses'] = 'No archived courses found';
$string['confirm_edit_course'] = 'Are you sure you want to edit this course?';

// --- Management and Reports ---
$string['management_page_label'] = 'Bulk Management for Archive Status';
$string['management_page_heading'] = 'Bulk Management for Archive Status';
$string['management_page_info'] = 'Use this page to set the archive status (active/archived) for a large group of courses at once. (Development of search and update interface is required here).';
$string['bulk_actions'] = 'Bulk Actions';

$string['report_page_label'] = 'Archived Courses Report';
$string['report_page_heading'] = 'Archived Courses Report';
$string['no_archived_courses'] = 'No archived courses found.';

// Report Page
$string['export'] = 'Export';
$string['total_archived_courses'] = 'Total Archived Courses: {$a}';
$string['view_course'] = 'View Course';
$string['field_not_found'] = 'The custom field for archive status was not found in the system. Please ensure the plugin is updated correctly.';

// Report Page
$string['archived_courses_report'] = 'Archived Courses Report';
$string['total_archived_courses'] = 'Total Archived Courses: {$a}';
$string['export'] = 'Export';
$string['back_to_course_management'] = 'Back to Course Management';
$string['customfield_not_found'] = 'Custom field "{$a}" not found. Please check plugin installation.';

// Table headers
$string['id'] = 'ID';
$string['course_full_name'] = 'Course Full Name';
$string['course_short_name'] = 'Course Short Name';
$string['visibility'] = 'Visibility';
$string['visible'] = 'Visible';
$string['hidden'] = 'Hidden';

// --- Bulk Management ---
$string['bulk_actions'] = 'Bulk Actions';
$string['current_archive_status'] = 'Current Archive Status';
$string['available_courses'] = 'Available Courses';
$string['no_courses_to_manage'] = 'No courses to manage in the system (except the main site).';

$string['select_action'] = 'Select Action:';
$string['select_action_placeholder'] = '--- Select ---';
$string['action_set_archived'] = 'Set to Archived';
$string['action_set_active'] = 'Set to Active';
$string['apply_action'] = 'Apply Action';

// Success messages
$string['bulk_archive_success'] = 'Archived {$a} courses successfully.';
$string['bulk_active_success'] = 'Set {$a} courses to active successfully.';