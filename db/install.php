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
 * Course Archiver installation file
 *
 * @package    local_course_archiver
 * @copyright  2025 Mahdi Mohamed <contact@engmahdi.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * التحقق من توافق إصدار Moodle
 */
/**
 * Check if the current Moodle version meets the minimum requirement
 * 
 * @throws moodle_exception if the Moodle version is not compatible
 */
function xmldb_local_course_archiver_check_moodle_version() {
    $version = get_config('moodle', 'version');
    if ($version < 2022112800) { // Moodle 4.5.0
        throw new moodle_exception('moodleversionerror', 'local_course_archiver', '', null, 
            'This plugin requires Moodle 4.5.0 or later');
    }
}

/**
 * الحصول على معرف الفئة أو إنشاؤها إذا لم تكن موجودة
 */
/**
 * Get or create the custom field category
 * 
 * @return stdClass The category object
 * @throws dml_exception On database errors
 */
function local_course_archiver_get_or_create_category() {
    global $DB;
    
    static $category = null;
    
    if ($category !== null) {
        return $category;
    }
    
    $transaction = $DB->start_delegated_transaction();
    
    try {
        // Try to find existing category
        $category = $DB->get_record('customfield_category', 
            ['name' => 'حقول مخصصة للدورات', 'component' => 'core_course', 'area' => 'course'],
            '*',
            IGNORE_MULTIPLE
        );
        
        // Create category if it doesn't exist
        if (!$category) {
            $categorydata = (object)[
                'name' => 'حقول مخصصة للدورات',
                'component' => 'core_course',
                'area' => 'course',
                'sortorder' => 500,
                'timecreated' => time(),
                'timemodified' => time(),
                'description' => 'الحقول المخصصة لإظهار معلومات إضافية عن الدورات',
                'descriptionformat' => FORMAT_HTML,
            ];
            
            $categoryid = $DB->insert_record('customfield_category', $categorydata);
            $category = $DB->get_record('customfield_category', ['id' => $categoryid], '*', MUST_EXIST);
        }
        
        $transaction->allow_commit();
        return $category;
        
    } catch (Exception $e) {
        $transaction->rollback($e);
        debugging('Failed to get or create custom field category: ' . $e->getMessage(), DEBUG_DEVELOPER);
        throw $e;
    }
}

/**
 * إنشاء حقل مخصص إذا لم يكن موجوداً
 */
/**
 * Create or update the archive status custom field
 * 
 * @param stdClass $category The category object
 * @return stdClass The field object
 * @throws dml_exception On database errors
 */
function local_course_archiver_create_custom_field($category) {
    global $DB;
    
    $fieldshortname = 'archive_status';
    $transaction = $DB->start_delegated_transaction();
    
    try {
        // Check if field already exists
        $existingfield = $DB->get_record('customfield_field', 
            ['shortname' => $fieldshortname], 
            '*', 
            IGNORE_MULTIPLE
        );
        
        // Get translated strings
        $statusactive = 'active';
        $statusarchived = 'archived';
        $statusactivelabel = get_string('status_active', 'local_course_archiver');
        $statusarchivedlabel = get_string('status_archived', 'local_course_archiver');
        
        // Format menu options (format: value|label)
        // $menuoptions = "$statusactive|$statusactivelabel\n$statusarchived|$statusarchivedlabel";

        $menuoptions = 
            "$statusactivelabel\n" . 
            "$statusarchivedlabel";
        $field = null;
        
        if ($existingfield) {
            // Update existing field if needed
            $updateneeded = false;
            $configdata = @json_decode($existingfield->configdata, true) ?: [];
            
            // Check if we need to update the field
            if (empty($configdata['options']) || $configdata['options'] !== $menuoptions) {
                $configdata = [
                    'required' => 1,
                    'uniquevalues' => 0,
                    'defaultvalue' => $statusactivelabel,
                    'options' => $menuoptions,
                    'checkbydefault' => 0,
                    'locked' => 0,
                    'visibility' => 2 // Visible to all
                ];
                
                $existingfield->configdata = json_encode($configdata, JSON_UNESCAPED_UNICODE);
                $DB->update_record('customfield_field', $existingfield);
                $field = $existingfield;
            } else {
                $field = $existingfield;
            }
        } else {
            // Create new field if it doesn't exist
            $fielddata = (object)[
                'shortname' => $fieldshortname,
                'name' => get_string('archive_status', 'local_course_archiver'),
                'type' => 'select',
                'description' => get_string('archive_status_desc', 'local_course_archiver'),
                'descriptionformat' => FORMAT_HTML,
                'categoryid' => $category->id,
                'sortorder' => 1,
                'configdata' => json_encode([
                    'required' => 1,
                    'uniquevalues' => 0,
                    'defaultvalue' => $statusactivelabel,
                    'options' => $menuoptions,
                    'checkbydefault' => 0,
                    'locked' => 0,
                    'visibility' => 2 // Visible to all
                ], JSON_UNESCAPED_UNICODE),
                'timecreated' => time(),
                'timemodified' => time()
            ];
            
            $fieldid = $DB->insert_record('customfield_field', $fielddata);
            $field = $DB->get_record('customfield_field', ['id' => $fieldid], '*', MUST_EXIST);
        }
        
        // Clear caches - handle different Moodle versions and cache structures
        try {
            // Method 1: Newer Moodle versions with cache_helper
            if (class_exists('cache_helper') && method_exists('cache_helper', 'purge_all')) {
                try {
                    cache_helper::purge_all();
                } catch (Exception $e) {
                    // Only log if it's not a cache definition error
                    if (strpos($e->getMessage(), 'The requested cache definition does not exist') === false) {
                        debugging('Cache purge_all failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
                    }
                }
            } 
            // Method 2: Older Moodle versions
            else if (function_exists('purge_all_caches')) {
                purge_all_caches();
            }
            
            // Clear specific caches if they exist
            if (class_exists('cache') && method_exists('cache', 'make')) {
                $caches = [];
                
                // Only add customfield caches if the core_customfield API exists
                if (class_exists('core_customfield\\api')) {
                    $caches = ['customfield_config', 'customfield_field'];
                }
                
                foreach ($caches as $cachearea) {
                    try {
                        // Skip if cache definition doesn't exist
                        if (method_exists('cache_helper', 'is_defined') && 
                            !cache_helper::is_defined('core', $cachearea)) {
                            continue;
                        }
                        
                        $cache = cache::make('core', $cachearea);
                        if (method_exists($cache, 'purge')) {
                            $cache->purge();
                        }
                    } catch (Exception $e) {
                        // Only log if it's not a cache definition error
                        if (strpos($e->getMessage(), 'The requested cache definition does not exist') === false) {
                            debugging('Failed to clear cache ' . $cachearea . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
                        }
                    }
                }
                
                // Also clear the config cache
                try {
                    $configcache = cache::make('core', 'config');
                    if (method_exists($configcache, 'purge')) {
                        $configcache->purge();
                    }
                } catch (Exception $e) {
                    debugging('Failed to clear config cache: ' . $e->getMessage(), DEBUG_DEVELOPER);
                }
            }
        } catch (Exception $e) {
            // Only log if it's not a cache definition error
            if (strpos($e->getMessage(), 'The requested cache definition does not exist') === false) {
                debugging('Cache operations failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }
        
        $transaction->allow_commit();
        return $field;
        
    } catch (Exception $e) {
        $transaction->rollback($e);
        debugging('Failed to create/update custom field: ' . $e->getMessage(), DEBUG_DEVELOPER);
        throw $e;
    }
}

/**
 * Installation function that creates the necessary database tables and settings
 * 
 * @return bool True on success, false on failure
 * @throws dml_exception On database errors
 */
function xmldb_local_course_archiver_install() {
    global $DB, $CFG;
    
    // Include required libraries
    require_once($CFG->libdir . '/moodlelib.php');
    require_once($CFG->libdir . '/adminlib.php');
    
    // Check Moodle version
    xmldb_local_course_archiver_check_moodle_version();
    
    // Start transaction
    $transaction = $DB->start_delegated_transaction();
    
    try {
        // 1. Get or create the custom field category
        $category = local_course_archiver_get_or_create_category();
        
        // 2. Create or update the custom field
        $field = local_course_archiver_create_custom_field($category);
        
        if (empty($field) || !isset($field->id)) {
            throw new moodle_exception('error_creating_field', 'local_course_archiver');
        }
        
        // 3. Clear all relevant caches
        try {
            // Method 1: Newer Moodle versions
            if (class_exists('cache_helper') && method_exists('cache_helper', 'purge_all')) {
                try {
                    cache_helper::purge_all();
                } catch (Exception $e) {
                    debugging('Cache purge_all failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
                }
            } 
            // Method 2: Older Moodle versions
            else if (function_exists('purge_all_caches')) {
                purge_all_caches();
            }
            
            // Clear specific caches - only attempt to clear caches that exist
            if (class_exists('cache') && method_exists('cache', 'make')) {
                $caches = ['config'];
                
                // Only add customfield caches if they exist in this Moodle version
                if (class_exists('core_customfield\\api')) {
                    $caches = array_merge($caches, ['customfield_data', 'customfield_config', 'customfield_field']);
                }
                
                foreach ($caches as $cachearea) {
                    try {
                        // Skip if cache definition doesn't exist
                        if (method_exists('cache_helper', 'is_defined') && !cache_helper::is_defined('core', $cachearea)) {
                            continue;
                        }
                        
                        $cache = cache::make('core', $cachearea);
                        if (method_exists($cache, 'purge')) {
                            $cache->purge();
                        }
                    } catch (Exception $e) {
                        // Only log non-critical cache clearing failures
                        if (strpos($e->getMessage(), 'The requested cache definition does not exist') === false) {
                            debugging('Failed to clear cache: ' . $e->getMessage(), DEBUG_DEVELOPER);
                        }
                    }
                }
                
                // Trigger cache event if available and the event exists
                if (method_exists('cache_helper', 'purge_by_event') && 
                    defined('CACHE_EVENT_INVALIDATE') && 
                    in_array('changesincustomfield', 
                        array_column(constant('CACHE_EVENT_INVALIDATE'), 'event'))) {
                    try {
                        cache_helper::purge_by_event('changesincustomfield');
                    } catch (Exception $e) {
                        debugging('Failed to purge cache by event: ' . $e->getMessage(), DEBUG_DEVELOPER);
                    }
                }
            }
        
        // 4. Trigger event for logging
        if (function_exists('\local_course_archiver\event\plugin_installed::create')) {
            $event = \local_course_archiver\event\plugin_installed::create([
                'context' => context_system::instance()
            ]);
            $event->trigger();
        }
        } catch (Exception $e) {
            debugging('Cache clearing failed during installation: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
        
        // Commit the transaction
        $transaction->allow_commit();
        
        // Return true on successful installation
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($transaction) && $transaction->is_active()) {
            try {
                $transaction->rollback($e);
            } catch (Exception $e2) {
                debugging('Error during transaction rollback: ' . $e2->getMessage(), DEBUG_DEVELOPER);
            }
        }
        
        // Log the error
        debugging('Error installing Course Archiver plugin: ' . $e->getMessage(), DEBUG_DEVELOPER);
        
        // Re-throw the exception to stop installation
        throw $e;
    }
}

/**
 * Uninstallation function that removes all plugin data
 * 
 * @return bool True on success, false on failure
 * @throws dml_exception On database errors
 */
function xmldb_local_course_archiver_uninstall() {
    global $DB, $CFG;
    
    // Include required libraries
    require_once($CFG->libdir . '/moodlelib.php');
    require_once($CFG->libdir . '/adminlib.php');
    
    // Start transaction
    $transaction = $DB->start_delegated_transaction();
    
    try {
        // 1. Delete custom field data and field itself if it exists
        $params = [
            'shortname' => 'archive_status',
            'component' => 'core_course',
            'area' => 'course'
        ];
        
        if ($field = $DB->get_record('customfield_field', $params, 'id', IGNORE_MULTIPLE)) {
            // Delete all data associated with this field
            $DB->delete_records('customfield_data', ['fieldid' => $field->id]);
            
            // Delete the field
            $DB->delete_records('customfield_field', ['id' => $field->id]);
        }
        
        // 2. Delete the custom field category if it exists
        $categoryparams = [
            'name' => 'حقول مخصصة للدورات',
            'component' => 'core_course',
            'area' => 'course'
        ];
        
        if ($category = $DB->get_record('customfield_category', $categoryparams, 'id', IGNORE_MULTIPLE)) {
            // Check if there are any fields left in this category
            $fieldcount = $DB->count_records('customfield_field', ['categoryid' => $category->id]);
            
            // Only delete the category if it's empty
            if ($fieldcount == 0) {
                $DB->delete_records('customfield_category', ['id' => $category->id]);
            }
        }
        
        // 3. Delete all plugin settings
        $DB->delete_records('config_plugins', ['plugin' => 'local_course_archiver']);
        
        // 4. Clear all relevant caches
        try {
            // Method 1: Newer Moodle versions
            if (class_exists('cache_helper') && method_exists('cache_helper', 'purge_all')) {
                cache_helper::purge_all();
            } 
            // Method 2: Older Moodle versions
            else if (function_exists('purge_all_caches')) {
                purge_all_caches();
            }
            
            // Clear specific caches
            if (class_exists('cache') && method_exists('cache', 'make')) {
                $caches = ['config', 'customfield_data', 'customfield_config', 'customfield_field'];
                foreach ($caches as $cachearea) {
                    try {
                        $cache = cache::make('core', $cachearea);
                        if (method_exists($cache, 'purge')) {
                            $cache->purge();
                        }
                    } catch (Exception $e) {
                        debugging('Failed to clear cache: ' . $e->getMessage(), DEBUG_DEVELOPER);
                    }
                }
                
                // Trigger cache event if available and the event exists
                if (method_exists('cache_helper', 'purge_by_event') && 
                    defined('CACHE_EVENT_INVALIDATE') && 
                    in_array('changesincustomfield', 
                        array_column(constant('CACHE_EVENT_INVALIDATE'), 'event'))) {
                    try {
                        cache_helper::purge_by_event('changesincustomfield');
                    } catch (Exception $e) {
                        debugging('Failed to purge cache by event: ' . $e->getMessage(), DEBUG_DEVELOPER);
                    }
                }
            }
        
        // 5. Trigger event for logging
        if (function_exists('\local_course_archiver\event\plugin_uninstalled::create')) {
            $event = \local_course_archiver\event\plugin_uninstalled::create([
                'context' => context_system::instance()
            ]);
            $event->trigger();
        }
        } catch (Exception $e) {
            debugging('Cache clearing failed during uninstallation: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
        
        // Commit the transaction
        $transaction->allow_commit();
        
        debugging('Course Archiver plugin uninstalled successfully', DEBUG_DEVELOPER);
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($transaction) && $transaction->is_active()) {
            try {
                $transaction->rollback($e);
            } catch (Exception $e2) {
                debugging('Error during transaction rollback: ' . $e2->getMessage(), DEBUG_DEVELOPER);
            }
        }
        
        // Log the error
        debugging('Error uninstalling Course Archiver plugin: ' . $e->getMessage(), DEBUG_DEVELOPER);
        
        // Re-throw the exception to stop uninstallation
        throw $e;
    }
}