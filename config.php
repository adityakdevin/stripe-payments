<?php
require_once 'vendor/autoload.php';
function config($key)
{
    $config = [
        'base_url' => '',
        'title' => '',
        'logo' => '',
        'meta_description' => '',
        'tmc'=>'',
        'public_key' => '',
        'secret_key' => '',
    ];
    return $config[$key];
}