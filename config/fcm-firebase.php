<?php

return [

    "device_model" => \Modules\DeviceToken\Entities\DeviceToken::class,
    "user_column"   => "user_id",
    "allow_fcm_log" => env("FCM_ALLOW_LOG", true),
    "allow_fcm_token_log" => env("FCM_ALLOW_TOKENS_LOG", true),
    "server_key"   => env("FCM_SERVER_KEY", ""),
    "langues"  => ["ar","en"],
    "allow_morph" => false,
    "morph" => "owner",
    "morph_index" => false,
    "firebase_credentials" => storage_path(env("FIREBASE_CREDENTIALS", "firebase.json")),
    "parse_service_account_in_init" => env("FCM_PARSE_SERVICE_ACCOUNT_IN_INIT", true)
];
