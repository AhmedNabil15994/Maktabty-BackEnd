<?php

return [
    'services' => [
        'datatable' => [
            'created_at' => 'Created At',
            'date_range' => 'Search By Dates',
            'image' => 'Image',
            'options' => 'Options',
            'status' => 'Status',
            'title' => 'Title',
        ],
        'form' => [
            'status' => 'Status',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'image' => 'Image',

            'tabs' => [
                'export' => 'Export Services',
                'general' => 'General Info.',
                'categories' => 'Services Categories',
                'seo' => 'SEO',
                "input_lang" => "Data :lang",
            ],
            'title' => 'Title',
        ],
        'routes' => [
            'clone' => 'Clone & Create Services',
            'create' => 'Create New Service',
            'index' => 'Services',
            'update' => 'Update Service',
        ],
        'validation' => [
            'title' => [
                'required' => 'Please enter the title',
                'unique' => 'This title is taken before',
            ],
        ],
    ],
    'service_orders' => [
        'datatable' => [
            'created_at' => 'Created At',
            'date_range' => 'Search By Dates',
            'options' => 'Options',
            'service' => 'Service',
        ],
        'index' => [
            'title' => 'Service Orders',
        ],
        'show' => [
            'form' => [

            ],
            'title' => 'Show Service Order Details',
            'invoice_customer' => 'Client Order',
            'items' => [
                'title' => 'Service',
                'description' => 'Description',
                'files' => 'Files',
                'file' => 'File',
            ],
            'user' => [
                'data' => 'Client Info',
                'email' => 'E-mail',
                'mobile' => 'Mobile',
                'name' => 'Name',
            ],
        ],
    ],
    'service_categories' => [
        'datatable' => [
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'date_range' => 'Search By Dates',
            'image' => 'Image',
            'options' => 'Options',
            'status' => 'Status',
            'title' => 'Title',
            'type' => 'Type',
        ],
        'form' => [
            'image' => 'Image',
            'banner_image' => 'banner image',
            'cover' => 'Cover',
            'main_category' => 'Main Category',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'status' => 'Status',
            'open_sub_category' => 'Open sub category in site',
            'show_in_home' => 'Show In Home',
            'tabs' => [
                'category_level' => 'Categories Tree',
                'general' => 'General Info.',
                'seo' => 'SEO',
            ],
            'title' => 'Title',
            'color' => 'Color',
            'sort' => 'Sort',
            'color_hint' => 'Hex Color - example: FFFFFF',
            'import_selects' => [
                'title_ar' => 'Arabic Title',
                'title_en' => 'English Title',
                'price' => 'Price',
                'description_ar' => 'Arabic Description',
                'description_en' => 'English Description',
                'qty' => 'Qty',
                'sku' => 'SKU',
                'status' => 'Status',
                'category' => 'Category',
                'offer_price' => 'Offer Price',
                'offer_start_at' => 'Offer Start at',
                'offer_end_at' => 'Offer End at',
                'international_code' => 'International Code',
            ],
        ],
        'routes' => [
            'create' => 'Create Service Categories',
            'index' => 'Service Categories',
            'update' => 'Update Service Category',
        ],
        'validation' => [
            'service_category_id' => [
                'required' => 'Please select category level',
            ],
            'image' => [
                'required' => 'Please select image',
            ],
            'title' => [
                'required' => 'Please enter the title',
                'unique' => 'This title is taken before',
            ],
            'color' => [
                'required_if' => 'Please enter a color for the main category',
            ],
            'image' => [
                'required' => 'Pleas select image',
                'image' => 'Image file should be an image',
                'mimes' => 'Image must be in',
                'max' => 'The image size should not be more than',
            ],
        ],
    ],
];
