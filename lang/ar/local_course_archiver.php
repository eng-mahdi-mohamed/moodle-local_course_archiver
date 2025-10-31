<?php
// local/course_archiver/lang/ar/local_course_archiver.php

$string['pluginname'] = 'أرشفة المقررات التلقائية';
$string['coursearchived'] = 'تم أرشفة هذا المقرر ولم يعد متاحًا للوصول.';
$string['categoryname'] = 'حالة المقرر';
$string['fieldname'] = 'حالة الأرشفة';

// القيم التي سيتم عرضها للمستخدم:
$string['status_active'] = 'نشط'; 
$string['status_archived'] = 'مؤرشف';
$string['archived'] = 'مؤرشف';
$string['archive_settings'] = 'إعدادات الأرشفة';

// Archive Status Field
$string['archive_status'] = 'حالة الأرشفة';
$string['archive_status_desc'] = 'تحدد ما إذا كان المقرر نشطاً أو مؤرشفاً';
$string['archive_status_help'] = 'اضبط على "مؤرشف" لإخفاء هذا المقرر من قوائم المقررات. اضبط على "نشط" لجعله مرئياً مرة أخرى.';

// سلاسل الصلاحيات
$string['course_archiver:manage'] = 'إدارة إعدادات أرشفة المقررات';
$string['course_archiver:viewreports'] = 'عرض تقارير أرشفة المقررات';
$string['course_archiver:accessarchived'] = 'الوصول إلى المقررات المؤرشفة';
$string['course_archiver:archivecourse'] = 'أرشفة/إلغاء أرشفة المقررات';
$string['course_archiver:restore'] = 'استعادة المقررات من الأرشيف';
$string['course_archiver:viewarchived'] = 'عرض المقررات المؤرشفة في قائمة المقررات';

// سلاسل الإعدادات الجديدة
$string['setting_enable_autohide_label'] = 'تفعيل الإخفاء التلقائي';
$string['setting_enable_autohide_desc'] = 'عند التفعيل، سيتم إخفاء المقرر تلقائياً عند تغيير حالة الأرشفة إلى "مؤرشف"، والعكس صحيح.';

// Management Interface
$string['management_page_label'] = 'أداة أرشفة المقررات';
$string['management_page_heading'] = 'إدارة أداة أرشفة المقررات';
$string['report_page_label'] = 'تقرير المقررات المؤرشفة';
$string['report_page_heading'] = 'تقرير المقررات المؤرشفة';
$string['available_courses'] = 'المقررات المتاحة';
$string['no_courses_to_manage'] = 'لا توجد مقررات متاحة للإدارة';

// Bulk Actions
$string['bulk_actions'] = 'إجراءات جماعية';
$string['select_action'] = 'اختر الإجراء';
$string['select_action_placeholder'] = '-- اختر إجراء --';
$string['action_set_archived'] = 'أرشف المقررات المحددة';
$string['action_set_active'] = 'فعل المقررات المحددة';
$string['apply_action'] = 'تطبيق الإجراء';
$string['bulk_archive_success'] = 'تم أرشفة {$a} مقرر بنجاح';
$string['bulk_active_success'] = 'تم تفعيل {$a} مقرر بنجاح';

// Course Table
$string['archiverstatus'] = 'حالة الأرشفة';
$string['id'] = 'المعرف';
$string['course_full_name'] = 'اسم المقرر';
$string['course_short_name'] = 'الاسم المختصر';
$string['current_archive_status'] = 'حالة الأرشفة الحالية';
$string['visibility'] = 'الظهور';
$string['visible'] = 'مرئي';
$string['hidden'] = 'مخفي';

// Messages
$string['field_not_found'] = 'لم يتم العثور على حقل حالة الأرشفة';
$string['no_archived_courses'] = 'لم يتم العثور على مقررات مؤرشفة';
$string['confirm_edit_course'] = 'هل أنت متأكد من رغبتك في تعديل هذا المقرر؟';

// Auto Archiving Settings
$string['setting_auto_archive_heading'] = 'إعدادات الأرشفة التلقائية';
$string['setting_auto_archive_desc'] = 'إعدادات الأرشفة التلقائية للمقررات';

// Email Settings
$string['email_heading'] = 'إعدادات البريد الإلكتروني';
$string['email_heading_desc'] = 'تكوين إعدادات إشعارات البريد الإلكتروني لأرشفة المقررات';
$string['setting_enable_email_notifications'] = 'تفعيل الإشعارات البريدية';
$string['setting_enable_email_notifications_desc'] = 'إرسال إشعارات بالبريد الإلكتروني عند أرشفة المقررات';
$string['setting_email_sender'] = 'البريد الإلكتروني للمرسل';
$string['setting_email_sender_desc'] = 'عنوان البريد الإلكتروني المستخدم كمرسل للإشعارات';
$string['setting_email_cc'] = 'نسخة إضافية';
$string['setting_email_cc_desc'] = 'قائمة عناوين البريد الإلكتروني لإرسال نسخة إضافية من الإشعارات إليها، مفصولة بفواصل';

// سلاسل الإعدادات الجديدة (للأرشفة التلقائية)
$string['auto_archive_heading'] = 'إعدادات الأرشفة التلقائية';
$string['auto_archive_heading_desc'] = 'تسمح هذه الإعدادات بأرشفة المقررات تلقائياً بناءً على شروط زمنية.';

$string['setting_enable_auto_archiving_label'] = 'تفعيل الأرشفة التلقائية';
$string['setting_enable_auto_archiving_desc'] = 'إذا تم تفعيل هذا الخيار، سيتم تشغيل مهمة يومية للبحث عن المقررات التي تنطبق عليها شروط الأرشفة أدناه.';

$string['setting_archive_after_enddate_label'] = 'الأرشفة بعد انتهاء المقرر بـ (يوم)';
$string['setting_archive_after_enddate_desc'] = 'عدد الأيام التي يجب أن تنقضي بعد تاريخ انتهاء المقرر ليتم أرشفته تلقائياً. أدخل 0 لتعطيل هذا الشرط.';

$string['setting_archive_after_noactivity_label'] = 'الأرشفة بعد عدم وجود نشاط طلابي بـ (يوم)';
$string['setting_archive_after_noactivity_desc'] = 'عدد الأيام التي يجب أن تنقضي دون أي نشاط تسجيل دخول من الطلاب ليتم أرشفة المقرر تلقائياً. أدخل 0 لتعطيل هذا الشرط.';

$string['taskname'] = 'مهمة الأرشفة التلقائية للمقررات';

// --- إعدادات عامة ---
$string['general_settings'] = 'إعدادات عامة';
$string['general_settings_desc'] = 'إعدادات عامة للاضافة مؤرشفة المقررات.';
$string['setting_enable_plugin'] = 'تفعيل الإضافة';
$string['setting_enable_plugin_desc'] = 'تفعيل أو تعطيل وظيفة مؤرشفة المقررات.';

// --- إعدادات البريد الإلكتروني ---
$string['email_heading'] = 'إشعارات البريد الإلكتروني';
$string['email_heading_desc'] = 'إعداد إشعارات البريد الإلكتروني عند أرشفة المقررات.';
$string['setting_enable_email_notifications'] = 'تفعيل إشعارات البريد الإلكتروني';
$string['setting_enable_email_notifications_desc'] = 'إرسال إشعارات البريد الإلكتروني عند أرشفة المقررات.';
$string['setting_email_sender'] = 'بريد إرسال الإشعارات';
$string['setting_email_sender_desc'] = 'عنوان البريد الإلكتروني المستخدم كمرسل للإشعارات';
$string['setting_email_cc'] = 'عنوان البريد الإلكتروني الإضافي';
$string['setting_email_cc_desc'] = 'قائمة عناوين البريد الإلكتروني لإرسال نسخة من الإشعارات إليها، مفصولة بفواصل';

// --- إعدادات الإخفاء التلقائي ---
$string['auto_hide_heading'] = 'إعدادات الإخفاء التلقائي';

// Report Page
$string['export'] = 'تصدير';
$string['total_archived_courses'] = 'إجمالي المقررات المؤرشفة: {$a}';
$string['view_course'] = 'معاينة المقرر';
$string['auto_hide_heading_desc'] = 'إعداد إخفاء المقررات تلقائياً.';

// --- الإدارة والتقارير (Management and Reports) ---
$string['management_page_label'] = 'إدارة جماعية لحالة الأرشفة';
$string['management_page_heading'] = 'إدارة حالة الأرشفة الجماعية للمقررات';
$string['management_page_info'] = 'استخدم هذه الصفحة لتعيين حالة الأرشفة (نشط/مؤرشف) لمجموعة كبيرة من المقررات دفعة واحدة. (تحتاج إلى تطوير واجهة البحث والتحديث هنا).';
$string['bulk_actions'] = 'الإجراءات الجماعية';

$string['report_page_label'] = 'تقرير المقررات المؤرشفة';
$string['report_page_heading'] = 'تقرير المقررات المؤرشفة';
$string['no_archived_courses'] = 'لا يوجد حالياً مقررات مصنفة كمؤرشفة.';
$string['field_not_found'] = 'لم يتم العثور على حقل حالة الأرشفة المخصص في النظام. يرجى التأكد من الترقية الصحيحة للإضافة.';

// رؤوس وأعمدة الجدول
$string['id'] = 'المعرّف';
$string['course_full_name'] = 'الاسم الكامل للمقرر';
$string['course_short_name'] = 'الاسم المختصر';
$string['visibility'] = 'الإتاحة';
$string['visible'] = 'مرئي';
$string['hidden'] = 'مخفي';

// --- إدارة جماعية ---
$string['bulk_actions'] = 'الإجراءات الجماعية';
$string['current_archive_status'] = 'حالة الأرشفة الحالية';
$string['available_courses'] = 'المقررات المتاحة للإدارة';
$string['no_courses_to_manage'] = 'لا يوجد مقررات لإدارتها في النظام (باستثناء الموقع الرئيسي).';

$string['select_action'] = 'اختر الإجراء:';
$string['select_action_placeholder'] = '--- اختر ---';
$string['action_set_archived'] = 'تعيين كمؤرشف';
$string['action_set_active'] = 'تعيين كنشط';
$string['apply_action'] = 'تطبيق الإجراء';

// رسائل النجاح
$string['bulk_archive_success'] = 'تم أرشفة {$a} مقررات بنجاح.';
$string['bulk_active_success'] = 'تم تعيين {$a} مقررات كنشطة بنجاح.';