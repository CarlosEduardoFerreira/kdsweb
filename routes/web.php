<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/**
 * Auth routes
 */
Route::group(['namespace' => 'Auth'], function () {

    // Authentication Routes...
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout')->name('logout');

    // Registration Routes...
    if (config('auth.users.registration')) {
        Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'RegisterController@register');
    }

    // Password Reset Routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset');

    // Confirmation Routes...
    if (config('auth.users.confirm_email')) {
        Route::get('confirm/{user_by_code}', 'ConfirmController@confirm')->name('confirm');
        Route::get('confirm/resend/{user_by_email}', 'ConfirmController@sendEmail')->name('confirm.send');
    }

    // Social Authentication Routes...
    Route::get('social/redirect/{provider}', 'SocialLoginController@redirect')->name('social.redirect');
    Route::get('social/login/{provider}', 'SocialLoginController@login')->name('social.login');
});

/**
 * Backend routes
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'admin'], function () {

    // Dashboard
    Route::get('/', 'DashboardController@index')->name('dashboard');

    // Resellers
    Route::get('resellers/{adminId}', 'ResellerController@index')->name('resellers');
    Route::get('resellers/{reseller}', 'ResellerController@show')->name('resellers.show');
    Route::get('resellers/{reseller}/edit', 'ResellerController@edit')->name('resellers.edit');
    Route::put('resellers/{reseller}', 'ResellerController@update')->name('resellers.update');
    Route::delete('resellers/{reseller}', 'ResellerController@destroy')->name('resellers.destroy');

    // Store Groups
    Route::get('storegroups/{resellerId}', 'StoreGroupController@index')->name('storegroups');
    Route::get('storegroups/{storegroup}', 'StoreGroupController@show')->name('storegroups.show');
    Route::get('storegroups/{storegroup}/edit', 'StoreGroupController@edit')->name('storegroups.edit');
    Route::put('storegroups/{storegroup}', 'StoreGroupController@update')->name('storegroups.update');
    Route::delete('storegroups/{storegroup}', 'StoreGroupController@destroy')->name('storegroups.destroy');

    // Stores
    Route::get('stores/{storegroupId}', 'StoreController@index')->name('stores');
    Route::get('stores/{store}', 'StoreController@show')->name('stores.show');
    Route::get('stores/{store}/edit', 'StoreController@edit')->name('stores.edit');
    Route::put('stores/{store}', 'StoreController@update')->name('stores.update');
    Route::delete('stores/{store}', 'StoreController@destroy')->name('stores.destroy');
    Route::delete('stores/{store}/config', 'StoreController@destroy')->name('stores.config');

    // Users (Users is every system user. Even Admin)
    Route::get('users', 'UserController@index')->name('users');
    Route::get('users/{user}', 'UserController@show')->name('users.show');
    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::put('users/{user}', 'UserController@update')->name('users.update');
    Route::delete('users/{user}', 'UserController@destroy')->name('users.destroy');

    Route::get('permissions', 'PermissionController@index')->name('permissions');
    Route::get('permissions/{user}/repeat', 'PermissionController@repeat')->name('permissions.repeat');

    Route::get('dashboard/log-chart', 'DashboardController@getLogChartData')->name('dashboard.log.chart');
    Route::get('dashboard/registration-chart', 'DashboardController@getRegistrationChartData')->name('dashboard.registration.chart');
});


Route::get('/', 'HomeController@index');

/**
 * Membership
 */
Route::group(['as' => 'protection.'], function () {
    Route::get('membership', 'MembershipController@index')->name('membership')->middleware('protection:' . config('protection.membership.product_module_number') . ',protection.membership.failed');
    Route::get('membership/access-denied', 'MembershipController@failed')->name('membership.failed');
    Route::get('membership/clear-cache/', 'MembershipController@clearValidationCache')->name('membership.clear_validation_cache');
});
