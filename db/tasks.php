<?php
// local/course_archiver/db/tasks.php

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'local_course_archiver\task\archive_courses', // فئة المهمة
        'blocking' => 0,
        'minute' => '0',
        'hour' => '3', // تشغيلها يومياً الساعة 3 صباحاً
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    )
);