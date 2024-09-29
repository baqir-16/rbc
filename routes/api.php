<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('open', 'API\DashboardAPIController@open');
Route::get('num_of_hosts_by_vuln_name', 'API\DashboardAPIController@num_of_hosts_by_vuln_name');
Route::get('db_num_of_hosts_by_cat', 'API\DashboardAPIController@db_num_of_hosts_by_cat');
Route::get('total_open_close', 'API\DashboardAPIController@total_open_close');
Route::get('vuln_exposure_past_four_months', 'API\DashboardAPIController@vuln_exposure_past_four_months');
Route::get('opco_findings', 'API\DashboardAPIController@opco_findings');
Route::get('aging_and_kpi', 'API\DashboardAPIController@aging_and_kpi');
