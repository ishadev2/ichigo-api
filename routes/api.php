<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'cors'], function () {
    Route::get('users/{id}/rewards', 'API\TestController@rewards');
    Route::patch('users/{id}/rewards/{date}/redeem', 'API\TestController@redeem');
});