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
use Illuminate\Support\Facades\Route;

/**
 * Auth routes
 */
Route::group(['namespace' => 'Auth'], function () {

    // Authentication Routes...
    Route::get('welcome_first', 'LoginController@welcome_first')->name('welcome_first');
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
    
    // Admin Settings
                    Route::get('settings', 'SettingsController@index')->name('settings');
    
    // Admin Reports
    Route::get('reports', 'ReportController@index')->name('reports');
    Route::get('reports/costByPlan', 'ReportCostByPlanController@index')->name('reports.costByPlan');
    Route::get('reports/costByStore', 'ReportCostByStoreController@index')->name('reports.costByStore');
    Route::get('reports/costByStatement', 'ReportCostByStatementController@index')->name('reports.costByStatement');
    
    // Plans
    Route::get('settings/plans', 'PlanController@index')->name('settings.plans');
    Route::get('settings/plans/0/form', 'PlanController@create')->name('settings.plans.new');
    Route::get('settings/plans/{plan}/form', 'PlanController@edit')->name('settings.plans.edit');
    Route::put('settings/plans/0/insert', 'PlanController@insert')->name('settings.plans.insert');
    Route::put('settings/plans/{plan}/update', 'PlanController@update')->name('settings.plans.update');
    Route::post('settings/plans/deletePlan', 'PlanController@delete')->name('settings.plans.deletePlan');
    
    // Plans X Objects
    Route::get('settings/plans/getItemsPlans', 'PlanController@getItemsPlans')->name('settings.plans.getItemsPlans');
    Route::get('settings/plans/getItemsObjects', 'PlanController@getItemsObjects')->name('settings.plans.getItemsObjects');
    Route::get('settings/plans/getItemsSelected', 'PlanController@getItemsSelected')->name('settings.plans.getItemsSelected');
    Route::put('settings/plans/updateObjects', 'PlanController@updateObjects')->name('settings.plans.updateObjects');
    Route::get('settings/plans/validPlanXObject', 'PlanController@validPlanXObject')->name('settings.plans.validPlanXObject');
    
    // Plans X Resellers
    Route::get('settings/plansXresellers', 'PlanXResellerController@index')->name('settings.plansXresellers');
    
    // Plans X Store Groups
    Route::get('settings/plansXstoregroups', 'PlanXStoreGroupController@index')->name('settings.plansXstoregroups');
    
    // Plans X Stores
    Route::get('settings/plansXstores', 'PlanXStoreController@index')->name('settings.plansXstores');
    
    //      url will show                function on controller              how to call
    // Route::get('storegroups/0/form', 'StoreGroupController@create')->name('resellers.new');
    
    // Resellers ------------------------------------------------------------------------------ //
    // List View
    Route::get('resellers/{adminId}', 'ResellerController@index')->name('resellers');
    // New Form View
    Route::get('resellers/0/form', 'ResellerController@create')->name('resellers.new');
    // Edit Form View
    Route::get('resellers/{reseller}/form', 'ResellerController@edit')->name('resellers.edit');
    // Show View
    Route::get('resellers/{reseller}/show', 'ResellerController@show')->name('resellers.show');
    // Insert Action
    Route::put('resellers/0/insert', 'ResellerController@insert')->name('resellers.insert');
    // Update Action
    Route::put('resellers/{reseller}', 'ResellerController@update')->name('resellers.update');
    // Delete Action
    Route::delete('resellers/{reseller}', 'ResellerController@destroy')->name('resellers.destroy');
    // View Agreement
    Route::get('resellers/{reseller}/agreement', 'ResellerController@showAgreement')->name('resellers.agreement');
    // Confirm Agreement
    Route::post('resellers/confirm_agreement', 'ResellerController@confirmAgreement')->name('resellers.confirm_agreement');

    // ------------------------------------------------------------------------------ Resellers //

    // Store Groups --------------------------------------------------------------------------- //
    // List View
    Route::get('storegroups/{resellerId}', 'StoreGroupController@index')->name('storegroups');
    // New Form View
    Route::get('storegroups/0/form', 'StoreGroupController@create')->name('storegroups.new');
    // Edit Form View
    Route::get('storegroups/{storegroup}/form', 'StoreGroupController@edit')->name('storegroups.edit');
    // Show View
    Route::get('storegroups/{storegroup}/show', 'StoreGroupController@show')->name('storegroups.show');
    // Insert Action
    Route::put('storegroups/0/insert', 'StoreGroupController@insert')->name('storegroups.insert');
    // Update Action
    Route::put('storegroups/{storegroup}', 'StoreGroupController@update')->name('storegroups.update');
    // Delete Action
    Route::delete('storegroups/{storegroup}', 'StoreGroupController@destroy')->name('storegroups.destroy');
    // Get Apps By StoreGroup
    Route::post('storegroups/getAppsByStoreGroup', 'StoreGroupController@getAppsByStoreGroup')->name('storegroups.getAppsByStoreGroup');
    // Get Environments By StoreGroup
    Route::post('storegroups/getEnvsByStoreGroup', 'StoreGroupController@getEnvsByStoreGroup')->name('storegroups.getEnvsByStoreGroup');
    // --------------------------------------------------------------------------- Store Groups //

    // Stores --------------------------------------------------------------------------------- //
    // List View
    Route::get('stores/{storegroupId}', 'StoreController@index')->name('stores');
    // New Form View
    Route::get('stores/0/form', 'StoreController@create')->name('stores.new');
    // Edit Form View
    Route::get('stores/{store}/form', 'StoreController@edit')->name('stores.edit');
    // Show View
    Route::get('stores/{store}/show', 'StoreController@show')->name('stores.show');
    // Insert Action
    Route::put('stores/0/insert', 'StoreController@insert')->name('stores.insert');
    // Update Action
    Route::put('stores/{store}', 'StoreController@update')->name('stores.update');
    // Config View
    Route::get('stores/{store}/config', 'StoreController@config')->name('stores.config');
    // Config Devices View
    Route::get('stores/{store}/config#devices', 'StoreController@config')->name('stores.config#devices');
    // Marketplace
    Route::get('stores/{store}/config#marketplace', 'StoreController@config')->name('stores.config#marketplace');
    // Update Settings Action
    Route::put('stores/{store}/updateSettings', 'StoreController@updateSettings')->name('stores.updateSettings');
    // Validate Settings
    Route::post('stores/{store}/validateStoreSettings', 'StoreController@validateStoreSettings')->name('stores.validateStoreSettings');
    // Update Twilio
    Route::put('stores/{store}/updateTwilio', 'StoreController@updateTwilio')->name('stores.updateTwilio');
    // Delete Action
    Route::delete('stores/{store}', 'StoreController@destroy')->name('stores.destroy');
    // Report
    Route::get('stores/{store}/report', 'StoreController@report')->name('stores.report');
    Route::get('stores/{store}/reportByStation', 'StoreController@reportByStation')->name('stores.reportByStation');
    // Load Devices Table
    Route::post('stores/{store}/loadDevicesTable', 'StoreController@loadDevicesTable')->name('stores.loadDevicesTable');
    // Get Expeditors
    Route::post('stores/{store}/getExpeditors', 'StoreController@getExpeditors')->name('stores.getExpeditors');
    // Get Parents
    Route::post('stores/{store}/getParentsByFunction', 'StoreController@getParentsByFunction')->name('stores.getParentsByFunction');
    // Get Transfers
    Route::post('stores/{store}/getTransfers', 'StoreController@getTransfers')->name('stores.getTransfers');
    // Get Device Settings
    Route::post('stores/{store}/getDeviceSettings', 'StoreController@getDeviceSettings')->name('stores.getDeviceSettings');
    // Update Device
    Route::post('stores/{store}/updateDevice', 'StoreController@updateDevice')->name('stores.updateDevice');
    // Remove Device
    Route::post('stores/{store}/removeDevice', 'StoreController@removeDevice')->name('stores.removeDevice');

    Route::post('stores/removeStore', 'StoreController@removeStore')->name('stores.removeStore');
    // --------------------------------------------------------------------------------- Stores //
    
    // -- Report ---------------------------------------------------------------------------- --//
    Route::get('reports/getLicensesQuantityByMonth', 'ReportController@getLicensesQuantityByMonth')->name('reports.getLicensesQuantityByMonth');
    Route::get('reports/getStatementListExcelFile', 'ReportController@getStatementListExcelFile')->name('reports.getStatementListExcelFile');
    Route::post('reports/downloadCompleted', 'ReportController@downloadCompleted')->name('reports.downloadCompleted');
    // -- ---------------------------------------------------------------------------- Report --//

    // Users (Users is every system user. Even Admin, Resellers, Storegroups and Stores)
    Route::get('users', 'UserController@index')->name('users');
    Route::get('users/{user}', 'UserController@show')->name('users.show');
    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::put('users/{user}', 'UserController@update')->name('users.update');
    Route::delete('users/{user}', 'UserController@destroy')->name('users.destroy');

    Route::get('permissions', 'PermissionController@index')->name('permissions');
    Route::get('permissions/{user}/repeat', 'PermissionController@repeat')->name('permissions.repeat');

    Route::get('dashboard/main-chart', 'DashboardController@getMainChartData')->name('dashboard.main.chart');
    
    Route::get('dashboard/active_inactive_licenses_graph', 'DashboardController@getActiveInactiveLicensesGraph')
            ->name('dashboard.active_inactive_licenses_graph');
    
    //Route::get('location/get_country_list','APIController@getCountryList');
    Route::get('location/get_state_list','LocationController@getStateList')->name('location.states');
    Route::get('location/get_city_list','LocationController@getCityList')->name('location.cities');
    
    Route::get('forbidden', 'Controller@forbidden')->name('forbidden');

});

Route::get('/', 'HomeController@index');
Route::post('timezonesByCountry', 'Controller@timezonesByCountry')->name('timezonesByCountry');

// Reseller credit card number approval (by Customer Support)
Route::get('external/authorize/{hash}', 'Controller@approvePaymentType')->name('resellers.approve_card');
Route::get('external/authorize/{hash}/{approve}', 'Controller@approvePaymentType')->name('resellers.approve_card');

/**
 * Membership
 */
Route::group(['as' => 'protection.'], function () {
    Route::get('membership', 'MembershipController@index')->name('membership')->middleware('protection:' . config('protection.membership.product_module_number') . ',protection.membership.failed');
    Route::get('membership/access-denied', 'MembershipController@failed')->name('membership.failed');
    Route::get('membership/clear-cache/', 'MembershipController@clearValidationCache')->name('membership.clear_validation_cache');
});






