<?php
// local/course_archiver/settings.php

defined('MOODLE_INTERNAL') || die();

// التحقق من أن المستخدم لديه الصلاحيات الكافية
if ($hassiteconfig) {
    
    // 1. إنشاء صفحة الإعدادات الرئيسية
    $settings = new admin_settingpage('local_course_archiver', 
        get_string('pluginname', 'local_course_archiver'),
        'moodle/site:config'
    );
    
    // 2. إضافة صفحة الإعدادات إلى قائمة الإدارة
    $ADMIN->add('courses', $settings);
    
    // 3. إضافة إعدادات الإضافية فقط إذا كانت الصفحة موجودة
    if ($ADMIN->fulltree) {
        
        // ----------------------------------------------------
        // أ. إعدادات عامة
        // ----------------------------------------------------
        $settings->add(new admin_setting_heading(
            'local_course_archiver/general_settings',
            get_string('general_settings', 'local_course_archiver'),
            get_string('general_settings_desc', 'local_course_archiver')
        ));

        // تفعيل/تعطيل الإضافة
        $settings->add(new admin_setting_configcheckbox(
            'local_course_archiver/enable_plugin',
            get_string('setting_enable_plugin', 'local_course_archiver'),
            get_string('setting_enable_plugin_desc', 'local_course_archiver'),
            1
        ));
        
        // ----------------------------------------------------
        // ب. إعدادات الإخفاء التلقائي (التحكم في الـ Observer)
        // ----------------------------------------------------
        $settings->add(new admin_setting_heading(
            'local_course_archiver/auto_hide_heading',
            get_string('auto_hide_heading', 'local_course_archiver'),
            get_string('auto_hide_heading_desc', 'local_course_archiver')
        ));
        
        $settings->add(new admin_setting_configcheckbox(
            'local_course_archiver/enable_auto_hide', 
            get_string('setting_enable_autohide_label', 'local_course_archiver'),
            get_string('setting_enable_autohide_desc', 'local_course_archiver'),
            0
        ));

        // ----------------------------------------------------
        // ج. إعدادات الأرشفة التلقائية (التحكم في المهام المجدولة)
        // ----------------------------------------------------
        $settings->add(new admin_setting_heading(
            'local_course_archiver/auto_archive_heading',
            get_string('auto_archive_heading', 'local_course_archiver'),
            get_string('auto_archive_heading_desc', 'local_course_archiver')
        ));

        // تفعيل الأرشفة التلقائية
        $settings->add(new admin_setting_configcheckbox(
            'local_course_archiver/enable_auto_archiving',
            get_string('setting_enable_auto_archiving_label', 'local_course_archiver'),
            get_string('setting_enable_auto_archiving_desc', 'local_course_archiver'),
            0
        ));
        
        // الأرشفة بعد انتهاء المقرر
        $settings->add(new admin_setting_configtext(
            'local_course_archiver/archive_after_enddate',
            get_string('setting_archive_after_enddate_label', 'local_course_archiver'),
            get_string('setting_archive_after_enddate_desc', 'local_course_archiver'),
            '30',
            PARAM_INT
        ));

        // الأرشفة بعد انتهاء النشاط
        $settings->add(new admin_setting_configtext(
            'local_course_archiver/archive_after_noactivity',
            get_string('setting_archive_after_noactivity_label', 'local_course_archiver'),
            get_string('setting_archive_after_noactivity_desc', 'local_course_archiver'),
            '180',
            PARAM_INT
        ));
        
        // إشعارات البريد الإلكتروني
        $settings->add(new admin_setting_heading(
            'local_course_archiver/email_heading',
            get_string('email_heading', 'local_course_archiver'),
            get_string('email_heading_desc', 'local_course_archiver')
        ));
        
        // إرسال إشعارات البريد الإلكتروني
        $settings->add(new admin_setting_configcheckbox(
            'local_course_archiver/enable_email_notifications',
            get_string('setting_enable_email_notifications', 'local_course_archiver'),
            get_string('setting_enable_email_notifications_desc', 'local_course_archiver'),
            1
        ));
        
        // عنوان البريد الإلكتروني للمرسل
        $settings->add(new admin_setting_configtext(
            'local_course_archiver/email_sender',
            get_string('setting_email_sender', 'local_course_archiver'),
            get_string('setting_email_sender_desc', 'local_course_archiver'),
            '',
            PARAM_EMAIL
        ));
        
        // نسخة من البريد الإلكتروني
        $settings->add(new admin_setting_configtext(
            'local_course_archiver/email_cc',
            get_string('setting_email_cc', 'local_course_archiver'),
            get_string('setting_email_cc_desc', 'local_course_archiver'),
            '',
            PARAM_RAW
        ));
    }
    
    // 4. إضافة صفحات الإدارة
    
    // أ. صفحة إدارة الأرشفة
    $managelink = new moodle_url('/local/course_archiver/manage.php');
    $managementpage = new admin_externalpage(
        'local_course_archiver_manage',
        get_string('management_page_label', 'local_course_archiver'),
        $managelink,
        'local/course_archiver:manage'
    );
    $ADMIN->add('courses', $managementpage);

    // ب. صفحة التقارير
    $reportlink = new moodle_url('/local/course_archiver/report.php');
    $reportpage = new admin_externalpage(
        'local_course_archiver_report',
        get_string('report_page_label', 'local_course_archiver'),
        $reportlink,
        'local/course_archiver:viewreports'
    );
    $ADMIN->add('reports', $reportpage);
}