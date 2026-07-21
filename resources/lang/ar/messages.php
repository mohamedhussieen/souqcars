<?php

return [
    'success'          => 'تمت العملية بنجاح.',
    'error'            => 'حدث خطأ ما.',
    'not_found'        => 'المورد غير موجود.',
    'unauthorized'     => 'غير مصرح. يرجى تسجيل الدخول.',
    'validation_error' => 'فشل التحقق من صحة البيانات.',
    'server_error'     => 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.',
    'throttled'        => 'طلبات كثيرة جدًا. يرجى المحاولة مرة أخرى لاحقًا.',

    'auth' => [
        'registered'          => 'تم إنشاء الحساب بنجاح.',
        'login_success'       => 'تم تسجيل الدخول بنجاح.',
        'login_failed'        => 'بيانات الاعتماد غير صحيحة.',
        'logout_success'      => 'تم تسجيل الخروج بنجاح.',
        'otp_sent'            => 'تم إرسال رمز التحقق بنجاح.',
        'otp_verified'        => 'تم التحقق من الرمز بنجاح.',
        'otp_invalid'         => 'رمز التحقق غير صالح أو منتهي الصلاحية.',
        'otp_expired'         => 'انتهت صلاحية رمز التحقق. يرجى طلب رمز جديد.',
        'otp_too_many_attempts' => 'محاولات فاشلة كثيرة جدًا. يرجى طلب رمز تحقق جديد.',
        'reset_token_invalid' => 'رمز إعادة التعيين غير صالح أو منتهي الصلاحية.',
        'password_reset'      => 'تم إعادة تعيين كلمة المرور بنجاح.',
        'account_inactive'    => 'تم تعطيل حسابك.',
        'otp_mail_subject'    => 'رمز التحقق الخاص بك',
        'otp_mail_greeting'   => 'رمز التحقق الخاص بك هو:',
        'otp_mail_expiry'     => 'هذا الرمز صالح لمدة 5 دقائق فقط.',
        'password_reset_otp_mail_subject' => 'رمز إعادة تعيين كلمة المرور الخاص بك',
        'password_reset_otp_mail_expiry'  => 'هذا الرمز صالح لمدة 10 دقائق فقط.',
        'policy_not_accepted' => 'يجب الموافقة على سياسة استخدام التطبيق قبل المتابعة.',
        'otp_throttled'       => 'يرجى الانتظار :seconds ثانية قبل طلب رمز تحقق جديد.',
    ],

    'profile' => [
        'fetched'             => 'تم استرداد الملف الشخصي بنجاح.',
        'updated'             => 'تم تحديث الملف الشخصي بنجاح.',
        'password_changed'    => 'تم تغيير كلمة المرور بنجاح.',
        'preferences_updated' => 'تم تحديث التفضيلات بنجاح.',
        'deleted'             => 'تم حذف الحساب بنجاح.',
        'wrong_password'      => 'كلمة المرور الحالية غير صحيحة.',
        'policy_accepted'     => 'تمت الموافقة على السياسة بنجاح.',
    ],

    'lookup' => [
        'cities_fetched'  => 'تم استرداد المدن بنجاح.',
        'brands_fetched'  => 'تم استرداد الماركات بنجاح.',
        'models_fetched'  => 'تم استرداد موديلات السيارات بنجاح.',
        'colors_fetched'  => 'تم استرداد الألوان بنجاح.',
    ],

    'home' => [
        'fetched' => 'تم استرداد بيانات الصفحة الرئيسية بنجاح.',
    ],

    'cars' => [
        'fetched'              => 'تم استرداد السيارات بنجاح.',
        'detail_fetched'       => 'تم استرداد تفاصيل السيارة بنجاح.',
        'my_listings_fetched'  => 'تم استرداد إعلاناتك بنجاح.',
        'image_limit_exceeded' => 'لا يمكن أن تحتوي السيارة على أكثر من 10 صور.',
    ],

    'favorites' => [
        'fetched' => 'تم استرداد المفضلة بنجاح.',
        'added'   => 'تمت إضافة السيارة إلى المفضلة.',
        'removed' => 'تمت إزالة السيارة من المفضلة.',
    ],

    'ratings' => [
        'fetched' => 'تم استرداد التقييمات بنجاح.',
        'saved'   => 'تم حفظ التقييم بنجاح.',
    ],

    'showrooms' => [
        'fetched'        => 'تم استرداد المعارض بنجاح.',
        'detail_fetched' => 'تم استرداد المعرض بنجاح.',
    ],

    'core' => [
        'app_config_fetched' => 'تم العرض بنجاح',
        'terms_fetched'      => 'تم استرداد الشروط والأحكام بنجاح.',
    ],

    'admin' => [
        'forbidden'            => 'هذا الإجراء مقتصر على المسؤولين فقط.',
        'users_fetched'        => 'تم استرداد المستخدمين بنجاح.',
        'user_fetched'         => 'تم استرداد المستخدم بنجاح.',
        'user_status_updated'  => 'تم تحديث حالة المستخدم بنجاح.',
        'user_role_updated'    => 'تم تحديث دور المستخدم بنجاح.',
        'user_deleted'         => 'تم حذف المستخدم بنجاح.',

        'cities_fetched' => 'تم استرداد المدن بنجاح.',
        'city_created'   => 'تم إنشاء المدينة بنجاح.',
        'city_updated'   => 'تم تحديث المدينة بنجاح.',
        'city_deleted'   => 'تم حذف المدينة بنجاح.',

        'brands_fetched' => 'تم استرداد الماركات بنجاح.',
        'brand_created'  => 'تم إنشاء الماركة بنجاح.',
        'brand_updated'  => 'تم تحديث الماركة بنجاح.',
        'brand_deleted'  => 'تم حذف الماركة بنجاح.',

        'car_models_fetched' => 'تم استرداد موديلات السيارات بنجاح.',
        'car_model_created'  => 'تم إنشاء موديل السيارة بنجاح.',
        'car_model_updated'  => 'تم تحديث موديل السيارة بنجاح.',
        'car_model_deleted'  => 'تم حذف موديل السيارة بنجاح.',

        'cars_fetched'        => 'تم استرداد السيارات بنجاح.',
        'car_fetched'         => 'تم استرداد السيارة بنجاح.',
        'car_created'         => 'تم إنشاء السيارة بنجاح.',
        'car_updated'         => 'تم تحديث السيارة بنجاح.',
        'car_deleted'         => 'تم حذف السيارة بنجاح.',
        'car_images_uploaded' => 'تم رفع صور السيارة بنجاح.',
        'car_image_deleted'   => 'تم حذف صورة السيارة بنجاح.',
        'inspection_uploaded' => 'تم رفع تقرير الفحص بنجاح.',
        'car_marked_sold'     => 'تم تحديد السيارة كمباعة بنجاح.',
        'car_status_updated'  => 'تم تحديث حالة السيارة بنجاح.',

        'showroom_fetched'       => 'تم استرداد المعرض بنجاح.',
        'showroom_updated'       => 'تم تحديث المعرض بنجاح.',
        'showroom_logo_uploaded' => 'تم رفع شعار المعرض بنجاح.',

        'ads_fetched' => 'تم استرداد الإعلانات بنجاح.',
        'ad_created'  => 'تم إنشاء الإعلان بنجاح.',
        'ad_updated'  => 'تم تحديث الإعلان بنجاح.',
        'ad_deleted'  => 'تم حذف الإعلان بنجاح.',

        'colors_fetched' => 'تم استرداد الألوان بنجاح.',
        'color_created'  => 'تم إنشاء اللون بنجاح.',
        'color_updated'  => 'تم تحديث اللون بنجاح.',
        'color_deleted'  => 'تم حذف اللون بنجاح.',
        'color_in_use'   => 'هذا اللون مستخدم في سيارة واحدة أو أكثر ولا يمكن حذفه.',

        'stats_fetched'     => 'تم استرداد الإحصائيات بنجاح.',
        'analytics_fetched' => 'تم استرداد التحليلات بنجاح.',

        'maintenance_centers_fetched' => 'تم استرداد مراكز الصيانة بنجاح.',
        'maintenance_center_fetched'  => 'تم استرداد مركز الصيانة بنجاح.',
        'maintenance_center_created'  => 'تم إنشاء مركز الصيانة بنجاح.',
        'maintenance_center_updated'  => 'تم تحديث مركز الصيانة بنجاح.',
        'maintenance_center_deleted'  => 'تم حذف مركز الصيانة بنجاح.',
        'maintenance_center_logo_uploaded' => 'تم رفع شعار مركز الصيانة بنجاح.',
        'maintenance_center_has_bookings'  => 'يحتوي مركز الصيانة هذا على حجوزات معلقة أو مؤكدة ولا يمكن حذفه.',

        'maintenance_service_created' => 'تم إنشاء الخدمة بنجاح.',
        'maintenance_service_updated' => 'تم تحديث الخدمة بنجاح.',
        'maintenance_service_deleted' => 'تم حذف الخدمة بنجاح.',
        'maintenance_service_has_bookings' => 'تحتوي هذه الخدمة على حجوزات معلقة أو مؤكدة ولا يمكن حذفها.',

        'bookings_fetched'       => 'تم استرداد الحجوزات بنجاح.',
        'booking_status_updated' => 'تم تحديث حالة الحجز بنجاح.',

        'ad_toggled'    => 'تم تبديل حالة الإعلان بنجاح.',
        'ads_reordered' => 'تم إعادة ترتيب الإعلانات بنجاح.',

        'brand_logo_uploaded' => 'تم رفع شعار الماركة بنجاح.',
        'brand_has_cars'      => 'هذه الماركة لديها سيارات ولا يمكن حذفها.',
        'car_model_has_cars'  => 'موديل السيارة هذا لديه سيارات ولا يمكن حذفه.',
        'city_has_cars'       => 'هذه المدينة لديها سيارات ولا يمكن حذفها.',

        'watch_requests_fetched' => 'تم استرداد طلبات المتابعة بنجاح.',
    ],

    'maintenance' => [
        'centers_fetched' => 'تم استرداد مراكز الصيانة بنجاح.',
        'center_fetched'  => 'تم استرداد مركز الصيانة بنجاح.',
    ],

    'bookings' => [
        'created'   => 'تم إنشاء الحجز بنجاح.',
        'fetched'   => 'تم استرداد الحجوزات بنجاح.',
        'detail_fetched' => 'تم استرداد الحجز بنجاح.',
        'cancelled' => 'تم إلغاء الحجز بنجاح.',
        'conflict'  => 'لديك بالفعل حجز في هذا المركز بنفس التاريخ.',
        'not_cancellable' => 'يمكن إلغاء الحجوزات المعلقة أو المؤكدة فقط.',
        'invalid_transition' => 'تغيير حالة الحجز هذا غير مسموح به.',
        'forbidden' => 'غير مسموح لك بالوصول إلى هذا الحجز.',
    ],

    'notifications' => [
        'fetched'      => 'تم استرداد الإشعارات بنجاح.',
        'marked_read'  => 'تم تحديد الإشعار كمقروء.',
        'all_marked_read' => 'تم تحديد جميع الإشعارات كمقروءة.',
        'unread_count' => 'تم استرداد عدد الإشعارات غير المقروءة بنجاح.',
        'forbidden'    => 'غير مسموح لك بالوصول إلى هذا الإشعار.',

        'car_match_title'         => 'عرض جديد يناسب اهتمامك',
        'car_match_body'          => 'هناك إعلان جديد يناسب اهتماماتك.',
        'booking_confirmed_title' => 'تم تأكيد الحجز',
        'booking_confirmed_body'  => 'تم تأكيد حجزك.',
        'booking_created_title'   => 'حجز جديد',
        'booking_created_body'    => 'تم إنشاء حجز جديد للتو.',
        'booking_cancelled_title' => 'تم إلغاء الحجز',
        'booking_cancelled_body'  => 'تم إلغاء أحد الحجوزات.',
        'booking_completed_title' => 'تم إتمام الحجز',
        'booking_completed_body'  => 'تم تحديد حجزك كمكتمل.',
        'price_drop_title'        => 'انخفض سعر سيارة في مفضلتك',
        'price_drop_body'         => 'انخفض سعر سيارة موجودة في مفضلتك.',
        'listing_approved_title'  => 'تم قبول إعلانك',
        'listing_approved_body'   => 'تم قبول إعلان سيارتك.',
        'listing_rejected_title'  => 'تم رفض إعلانك',
        'listing_rejected_body'   => 'تم رفض إعلان سيارتك.',
        'car_available_title'     => 'عربية من نوع اهتمامك نزلت',
        'car_available_body'      => 'أصبحت سيارة تطابق طلب المتابعة الخاص بك متاحة الآن.',
    ],

    'watch_requests' => [
        'watched'   => 'سيتم إعلامك عند توفر هذه السيارة.',
        'unwatched' => 'تمت إزالة طلب المتابعة.',
        'fetched'   => 'تم استرداد طلبات المتابعة بنجاح.',
        'not_sold'  => 'يمكنك متابعة الماركة/الموديل فقط بعد بيع هذا الإعلان.',
    ],

    'profile_stats' => [
        'fetched' => 'تم استرداد إحصائيات الملف الشخصي بنجاح.',
    ],
];
