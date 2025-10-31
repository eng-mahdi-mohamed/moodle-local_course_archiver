<?php
// This file is part of Moodle - http://moodle.org/
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
 * Course archiver observer.
 *
 * @package    local_course_archiver
 * @copyright  2025 Mahdi Mohamed <contact@engmahdi.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/classes/customfield/course_handler.php');
require_once($CFG->dirroot . '/local/course_archiver/locallib.php');

use core_course\customfield\course_handler;

/**
 * Observer for course archiving events.
 */
class local_course_archiver_observer {
    /**
     * Handle course_updated event
     *
     * @param \core\event\course_updated $event The event
     * @return bool Success status
     */
    public static function course_updated(\core\event\course_updated $event) {
        global $CFG, $DB;

        try {
            // 1. Check if auto-hide is enabled
            if (!get_config('local_course_archiver', 'enable_auto_hide')) {
                return true;
            }

            $courseid = $event->courseid;
            $fieldshortname = 'archive_status';

            // 2. Get the course handler and fields
            $handler = course_handler::create();
            if (!$handler) {
                throw new \moodle_exception('error_couldnotgetcoursehandler', 'local_course_archiver');
            }
            
            $fields = $handler->get_instance_data($courseid, true);
            
            // 3. Find the archive status field
            $archivefield = null;
            foreach ($fields as $field) {
                if ($field->get_field()->get('shortname') === $fieldshortname) {
                    $archivefield = $field;
                    break;
                }
            }
            
            if (!$archivefield) {
                return true; // Field not found, nothing to do
            }
            
            // 4. Get the raw field value directly from the database
            $dbvalue = $DB->get_field('customfield_data', 'value', 
                ['fieldid' => $archivefield->get_field()->get('id'), 'instanceid' => $courseid],
                IGNORE_MISSING
            );
            
            // Convert the value to integer safely
            $fieldvalue = is_numeric($dbvalue) ? (int)$dbvalue : LOCAL_COURSE_ARCHIVER_STATUS_ACTIVE;
            
            // 5. Determine the required visibility using numeric constants
            $isarchived = ($fieldvalue === LOCAL_COURSE_ARCHIVER_STATUS_ARCHIVED);
            $shouldbevisible = !$isarchived; // Visible unless archived
            
            // 6. Get current course data
            $course = $DB->get_record('course', ['id' => $courseid], 'id, visible', MUST_EXIST);
            
            // 7. Check if update is needed
            if ((bool)$course->visible === $shouldbevisible) {
                return true;
            }
            
            // 8. Update the course visibility directly without triggering events
            $DB->set_field('course', 'visible', (int)$shouldbevisible, ['id' => $courseid]);
            
            // Trigger the event manually without causing recursion
            $eventparams = [
                'objectid' => $courseid,
                'context' => context_course::instance($courseid),
                'other' => [
                    'updatedfields' => ['visible' => (int)$shouldbevisible]
                ]
            ];
            $event = \core\event\course_updated::create($eventparams);
            $event->trigger();
            
            // 9. Clear caches
            cache_helper::purge_by_event('changesincourse');
            cache_helper::purge_by_definition('core', 'coursemodinfo', [], [$courseid]);
            course_modinfo::clear_instance_cache($courseid);
            rebuild_course_cache($courseid, true);
            
            return true;
            
        } catch (\Exception $e) {
            debugging('Error in course_archiver observer: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }
}