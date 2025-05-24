<?php

return [
    'services' => [
        'datatable' => [
            'created_at' => 'تاريخ الآنشاء',
            'date_range' => 'البحث بالتواريخ',
            'image' => 'الصورة',
            'options' => 'الخيارات',
            'status' => 'الحالة',
            'title' => 'العنوان',
        ],
        'form' => [
            'status' => 'الحالة',
            'longitude' => 'خط الطول',
            'latitude' => 'خط العرض',
            'image' => 'الصورة',

            'tabs' => [
                'export' => 'نسخ الوسوم',
                'general' => 'بيانات عامة',
                'categories' => 'أقسام الخدمات',
                'seo' => 'SEO',
                "input_lang" => "بيانات :lang",
            ],
            'title' => 'عنوان',
        ],
        'routes' => [
            'clone' => 'نسخ و اضافة خدمة جديد',
            'create' => 'اضافة خدمة جديدة',
            'index' => 'الخدمات',
            'update' => 'تعديل الخدمة',
        ],
        'validation' => [
            'title' => [
                'required' => 'من فضلك ادخل العنوان',
                'unique' => 'هذا العنوان تم ادخالة من قبل',
            ],
        ],
    ],
    'service_orders' => [
        'datatable' => [
            'created_at' => 'تاريخ الآنشاء',
            'date_range' => 'البحث بالتواريخ',
            'options' => 'الخيارات',
            'service' => 'الخدمة',
        ],
        'index' => [
            'title' => 'طلبات الخدمات',
        ],
        'show' => [
            'form' => [

            ],
            'title' => 'عرض الطلب',
            'invoice_customer' => 'فاتورة العميل',
            'items' => [
                'title' => 'الخدمة',
                'description' => 'الوصف',
                'files' => 'الملفات',
                'file' => 'ملف',
            ],
            'user' => [
                'data' => 'بيانات العميل',
                'email' => 'البريد الالكتروني',
                'mobile' => 'رقم الهاتف',
                'name' => 'اسم العميل',
            ],
        ],
    ],
    'service_categories' => [
        'datatable' => [
            'created_at' => 'تاريخ الآنشاء',
            'date_range' => 'البحث بالتواريخ',
            'image' => 'الصورة',
            'options' => 'الخيارات',
            'status' => 'الحالة',
            'title' => 'العنوان',
            'type' => 'النوع',
        ],
        'form' => [
            'image' => 'الصورة',
            'banner_image' => 'صورة البانر',
            'cover' => 'صورة الغلاف',
            'main_category' => 'قسم رئيسي',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'status' => 'الحالة',
            'open_sub_category' => 'فتح الأقسام الفرعية في الموقع',
            'show_in_home' => 'يظهر فى الرئيسية',
            'tabs' => [
                'category_level' => 'مستوى الاقسام',
                'general' => 'بيانات عامة',
                'seo' => 'SEO',
            ],
            'title' => 'عنوان',
            'color' => 'اللون',
            'sort' => 'الترتيب',
            'color_hint' => 'اللون بطريقة Hex Color - على سبيل المثال: FFFFFF',
            'import_selects' => [
                'title_ar' => 'العنوان بالعربية',
                'title_en' => 'العنوان بالإنجليزية',
                'price' => 'السعر',
                'description_ar' => ' الوصف بالعربية',
                'description_en' => ' الوصف بالإنجليزية',
                'qty' => 'الكمية',
                'sku' => 'كود المنتج',
                'status' => 'الحالة',
                'category' => 'الأقسام',
                'offer_price' => 'السعر بعد العرض',
                'offer_start_at' => 'بداية تاريخ العرض',
                'offer_end_at' => 'نهاية تاريخ العرض',
                'international_code' => ' كود المنتج (international)',
            ],
        ],
        'routes' => [
            'create' => 'اضافة أقسام الخدمات',
            'index' => 'أقسام الخدمات',
            'update' => 'تعديل قسم الخدمات',
        ],
        'validation' => [
            'service_category_id' => [
                'required' => 'من فضلك اختر مستوى القسم',
            ],
            'image' => [
                'required' => 'من فضلك اختر الصورة',
            ],
            'title' => [
                'required' => 'من فضلك ادخل العنوان',
                'unique' => 'هذا العنوان تم ادخالة من قبل',
            ],
            'color' => [
                'required_if' => 'من فضلك ادخل لون للقسم الرئيسى',
            ],
            'image' => [
                'required' => 'من فضلك ادخل الصورة',
                'image' => 'من فضلك ادخل الصورة من نوع صورة',
                'mimes' => 'الصورة يجب ان تكون ضمن',
                'max' => 'حجم الصورة يجب الا يزيد عن',
            ],
        ],
    ],
];
