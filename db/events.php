<?php
// local/course_archiver/db/events.php

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\\core\\event\\course_updated',
        'callback' => 'local_course_archiver_observer::course_updated',
        'includefile' => null,
        'internal' => true,
        'priority' => 1000,
    ],
];