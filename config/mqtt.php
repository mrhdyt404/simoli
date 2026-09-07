<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MQTT Broker Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for connecting to Mosquitto / MQTT Broker
    | for IoT data streams (e.g. PKS Sei Galuh pengaliran sensors).
    |
    */

    'host' => env('MQTT_HOST', '127.0.0.1'),
    'port' => (int) env('MQTT_PORT', 1883),
    'username' => env('MQTT_AUTH_USERNAME') ?? env('MQTT_USERNAME', 'simoli'),
    'password' => env('MQTT_AUTH_PASSWORD') ?? env('MQTT_PASSWORD', 'Simoli404$!'),
    'client_id' => env('MQTT_CLIENT_ID', 'simoli_server_' . uniqid()),
    'clean_session' => true,
    'mqtt_version' => env('MQTT_VERSION', \PhpMqtt\Client\MqttClient::MQTT_3_1), // 3.1
    'keep_alive' => (int) env('MQTT_KEEP_ALIVE', 60),
    'timeout' => (int) env('MQTT_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Default Topics
    |--------------------------------------------------------------------------
    | Topics subscribed by default for pengaliran data.
    | Wildcard '+' matches any single level, '#' matches multiple levels.
    */
    'topics' => [
        'pengaliran' => env('MQTT_TOPIC_PENGALIRAN', 'simoli/+/pengaliran'),
        'sei_galuh'  => env('MQTT_TOPIC_SEI_GALUH', 'simoli/seigaluh/pengaliran'),
        'test'       => env('MQTT_TOPIC_TEST', 'simoli/test'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default PKS Mapping for Sei Galuh
    |--------------------------------------------------------------------------
    */
    'default_pks_id' => (int) env('MQTT_DEFAULT_PKS_ID', 7), // 7 = SEI GALUH

    /*
    |--------------------------------------------------------------------------
    | IoT API Security Key
    |--------------------------------------------------------------------------
    */
    'api_key' => env('IOT_API_KEY', 'simoli_iot_secret_galuh_2026_key'),
];

