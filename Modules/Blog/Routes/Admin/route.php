<?php
$apiAdmin = app('Dingo\Api\Routing\Router');
$apiAdmin->group([], function($apiAdmin) {
    foreach(glob(__DIR__.'/NotVerify/*.php') as $filename) {
        !empty($filename) && require $filename;
    }
});

$apiAdmin->group(['namespace' => 'auth.token.jwt:admin'], function($apiAdmin) {
   foreach(glob(__DIR__.'/Verify/*.php') as $filename) {
       !empty($filename) && require $filename;
   }
});
