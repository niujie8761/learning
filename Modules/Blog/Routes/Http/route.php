<?php
$api = app('Dingo\Api\Routing\Router');
$api->group(['middleware' => 'slidingGrant'], function($api) {
    foreach(glob(__DIR__.'/NotVerify/*.php') as $filename) {
        !empty($filename) && require $filename;
    }
});

$api->group(['middleware' => 'auth.token.jwt:api'], function($api) {
    foreach(glob(__DIR__.'/Verify/*.php') as $filename) {
        !empty($filename)  && require $filename;
    }
});
