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
// Routs to redirect to different views

Route::get('/', function () { return view('auth/login');});

Route::get('/welcome', function () { return view('welcome'); });

Auth::routes();
Route::resource('home', 'HomeController');
Route::resource('opcodb', 'OpcoDashboardController');
Route::resource('cisodb', 'CisoController');

// Admin Controllers
Route::resource('users', 'UserController');
Route::resource('roles', 'RoleController');
Route::resource('department_dashboards', 'DepartmentController');
Route::resource('departments', 'DepartmentController');
Route::resource('permissions', 'PermissionController');
Route::resource('posts', 'PostController');
Route::resource('pdfreports', 'PdfreportController');
Route::resource('profiles', 'ProfileController');
Route::resource('vulncategories', 'VulncategoryController');
Route::get('deactivateuser/{id}', ['uses' => 'UserController@deactivate']);
Route::get('activateuser/{id}', ['uses' => 'UserController@activate']);

//PMO Controllers
Route::resource('Ticket', 'TicketController');
Route::resource('PMO', 'PMOController');
Route::get('forward2tester/{id}', [
    'uses' => 'PMOController@update'
]);
Route::get('assign_tasks/{id}', [
    'uses' => 'PMOController@edit'
]);
Route::get('delTic/{id}', [
    'uses' => 'PMOController@deleteTicket'
]);

Route::resource('close', 'CloseController');

Route::get('closed_streams', [
    'uses' => 'CloseController@closed_streams'
]);
Route::get('progress_streams', [
    'uses' => 'CloseController@progress_streams'
]);
Route::get('pdf_files', [
    'uses' => 'CloseController@pdf_files'
]);
// Tester Controllers
Route::resource('Tester', 'TesterController');

Route::get('testerview/{id}', [
    'uses' => 'TesterController@show'
]);

Route::post('storeXML', [
    'uses' => 'TesterController@storeXML'
]);

Route::post('storeCategory', [
    'uses' => 'TesterController@storeCategory'
]);

Route::get('forward2analyst/{id}', [
    'uses' => 'TesterController@update'
]);
Route::resource('Tester', 'TesterController');
Route::get('backward2pmo/{id}', [
    'uses' => 'TesterController@backward'
]);
Route::get('testerdelup/{id}', [
    'uses' => 'TesterController@deleteUploaded'
]);

Route::get('assetdelup/{id}', [
    'uses' => 'AssetsController@deleteAsset'
]);

// Analyst Controller
Route::resource('Analyst', 'AnalystController');
Route::get('forward2qa/{id}', [
    'uses' => 'AnalystController@update'
]);
Route::resource('Analyst', 'AnalystController');
Route::get('backward2tester/{id}', [
    'uses' => 'AnalystController@backward'
]);
Route::post('validateAll', [
    'uses' => 'ReportController@vulnvalidateAll',
    'as' => 'validateAll',
]);
Route::get('validate', [
    'uses' => 'ReportController@vulnvalidate',
    'as' => 'validate',
]);
Route::get('unvalidate', [
    'uses' => 'ReportController@vulnunvalidate',
    'as' => 'unvalidate',
]);
Route::post('/fpAll', [
    'uses' => 'ReportController@fpAll',
    'as' => 'fpAll',
]);
Route::get('fp', [
    'uses' => 'ReportController@fp',
    'as' => 'fp',
]);
Route::get('unfp', [
    'uses' => 'ReportController@unfp',
    'as' => 'unfp',
]);
Route::get('unfp', [
    'uses' => 'ReportController@unfp',
    'as' => 'unfp',
]);
Route::post('/reverifyAll', [
    'uses' => 'ReportController@vulnreverifyAll',
    'as' => 'reverifyAll',
]);
Route::get('reverify', [
    'uses' => 'ReportController@vulnreverify',
    'as' => 'reverify',
]);
Route::get('unreverify', [
    'uses' => 'ReportController@vulnunreverify',
    'as' => 'unreverify',
]);

Route::get('revalidated/{id}', [
    'uses' => 'ReportController@revalidated'
]);
//Route::get('forward', [
//    'uses' => 'RemofficerController@forward',
//    'as' => 'forward',
//]);
Route::post('/forwardAll', [
    'uses' => 'RemofficerController@forwardAll',
    'as' => 'forwardAll',
]);
Route::get('closeone', [
    'uses' => 'RempmoController@closeone',
    'as' => 'closeone',
]);
Route::get('openofficer', [
    'uses' => 'RempmoController@openofficer',
    'as' => 'openofficer',
]);
Route::post('/closeAll', [
    'uses' => 'RempmoController@closeAll',
    'as' => 'closeAll',
]);
Route::post('/openAll', [
    'uses' => 'RempmoController@openAll',
    'as' => 'openAll',
]);
Route::get('closed_findings', [
    'uses' => 'RempmoController@closed_findings'
]);
Route::get('ipindex', [
    'uses' => 'AssetsController@ipindex'
]);
Route::get('analyst_delete_img/{_id}/{img}', [
    'uses' => 'ReportController@deleteImage'
]);

// QA Controller
Route::resource('QA', 'QAController');
Route::resource('Review', 'ReviewController');

Route::get('reverified/{id}', [
    'uses' => 'ReviewController@reverified'
]);


Route::get('forward2hod/{id}', [
    'uses' => 'QAController@update'
]);
Route::resource('QA', 'QAController');
Route::get('backward2analyst/{id}', [
    'uses' => 'QAController@backward'
]);
Route::post('/vulnverifyAll', [
    'uses' => 'ReportController@vulnverifyAll',
    'as' => 'vulnverifyAll',
]);
Route::get('verify', [
    'uses' => 'ReportController@vulnverify',
    'as' => 'verify',
]);
Route::get('unverify', [
    'uses' => 'ReportController@vulnunverify',
    'as' => 'unverify',
]);
Route::post('/vulnrevalidateAll', [
    'uses' => 'ReportController@vulnrevalidateAll',
    'as' => 'vulnrevalidateAll',
]);
Route::get('revalidate', [
    'uses' => 'ReportController@vulnrevalidate',
    'as' => 'revalidate',
]);
Route::get('unrevalidate', [
    'uses' => 'ReportController@vulnunrevalidate',
    'as' => 'unrevalidate',
]);
Route::get('unisrevalidate', [
    'uses' => 'ReportController@vulnisunrevalidate',
    'as' => 'unisrevalidate',
]);
Route::post('/vulnisunrevalidateAll', [
    'uses' => 'ReportController@vulnisunrevalidateAll',
    'as' => 'vulnisunrevalidateAll',
]);

// HoD Controller
Route::resource('HoD', 'HoDController');
Route::get('backward2qa/{id}', [
    'uses' => 'HoDController@backward'
]);
Route::resource('HoD', 'HoDController');
Route::get('forward2sign_off/{id}', [
    'uses' => 'HoDController@update'
]);

// Close Controller
Route::resource('close', 'CloseController');
Route::get('forward2close/{id}', [
    'uses' => 'CloseController@update'
]);


Route::get('/edit', 'StreamController@QAshow');

Route::get('/streams', 'StreamController@PMOshow');

Route::resource('Report', 'ReportController');

//tester file upload
Route::post('import_file', [
    'uses' => 'TesterController@upload'
]);

Route::get('editxml/{id}', [
    'uses' => 'ReportController@showxml'
]);

Route::post('Report/editxml', [
    'uses' => 'AnalystController@modify'
]);

Route::post('assetmng/edit', [
    'uses' => 'AssetsController@modify'
]);

Route::get('create/{id}', [
    'uses' => 'AnalystController@createcsv'
]);

Route::get('createxml/{id}', [
    'uses' => 'AnalystController@createxml'
]);

Route::get('createasset', [
    'uses' => 'AssetsController@createasset'
]);
Route::get('assetdetail/{id}', [
    'uses' => 'AssetsController@assetdetail'
]);

Route::get('view_report/{id}', [
    'uses' => 'AssetsController@showreport'
]);

Route::get('showasset', [
    'uses' => 'AssetsController@showassets'
]);

Route::post('issuecsv', [
    'uses' => 'AnalystController@issuecsv'
]);

Route::post('issuexml', [
    'uses' => 'AnalystController@issuexml'
]);

Route::post('issueasset', [
    'uses' => 'AssetsController@issueasset'
]);

Route::post('storeXML', [
    'uses' => 'TesterController@storeXML'
]);

Route::get('reviewxml/{id}', [
    'uses' => 'ReviewController@showxml'
]);

Route::post('Review/reviewxml', [
    'uses' => 'QAController@modify'
]);

Route::get('remshowxml/{id}', [
    'uses' => 'RemofficerController@showxml'
]);

Route::get('exportOpco', [
    'uses' => 'RemofficerController@exportOpco'
]);

Route::get('export', [
    'uses' => 'TesterController@export'
]);

Route::post('updateRemediated', [
    'uses' => 'RemofficerController@updateRemediated'
]);

Route::post('Remofficer/remshowxml', [
    'uses' => 'RemofficerController@modify'
]);
Route::get('pmoshowxml/{id}', [
    'uses' => 'RempmoController@showxml'
]);
Route::post('Rempmo/pmoshowxml', [
    'uses' => 'RempmoController@modify'
]);

Route::get('closeone1/{id}', [
    'uses' => 'RempmoController@closeone'
]);
Route::get('openofficer1/{id}', [
    'uses' => 'RempmoController@openofficer'
]);

Route::get('remofficer_delete_img/{_id}/{img}/{type}', [
    'uses' => 'RemofficerController@deleteImage'
]);


//System Update Controller
Route::resource('system_update', 'SystemUpdateController');

// PMO officer Controller
Route::resource('Remofficer', 'RemofficerController');

// PMO officer Controller
Route::resource('Rempmo', 'RempmoController');


// Assets Management Controller
Route::resource('assetmng', 'AssetsController');


Route::get('showdetails/{id}', [
    'uses' => 'HomeController@riskdetails'
]);

Route::get('showdetailsbyopco/{id}/{opcoid}', [
    'uses' => 'OpcoDashboardController@riskdetails'
]);


Route::get('showxml/{id}', [
    'uses' => 'RempmoController@showxml'
]);

Route::get('showcsv/{id}', [
    'uses' => 'RempmoController@show'
]);


Route::get('uploadold', [
    'uses' => 'TesterController@viewUploadOld'
]);

Route::post('storeuploadold', [
    'uses' => 'TesterController@storeUploadOld'
]);

// Remediation Reports
Route::get('total_open_closed', [
    'uses' => 'RemReportController@total_open_closed',
    'as' => 'total_open_closed',
]);

Route::get('weekly_stats', [
    'uses' => 'RemReportController@weekly_stats',
    'as' => 'weekly_stats',
]);

Route::get('weekly_stats_submit1', [
    'uses' => 'RemReportController@getTotalOfCritHigh',
    'as' => 'weekly_stats_submit1',
]);

Route::get('weekly_stats_submit2', [
    'uses' => 'RemReportController@getTotalFindingsGroupByClosedAndOpCo',
    'as' => 'weekly_stats_submit2',
]);

Route::get('weekly_stats_submit3', [
    'uses' => 'RemReportController@getTotalFindingsGroupByRevalAndOpCo',
    'as' => 'weekly_stats_submit3',
]);

Route::get('weekly_stats_submit4', [
    'uses' => 'RemReportController@getOpenbyOpCo',
    'as' => 'weekly_stats_submit4',
]);

Route::get('leaderboard', [
    'uses' => 'RemReportController@leaderboard',
    'as' => 'leaderboard',
]);

Route::post('storeAppscan', ['uses' => 'TesterController@storeAppscan']);
Route::get('editappscan/{id}', ['uses' => 'ReportController@shownappscan']);
Route::post('Report/editappscan', ['uses' => 'AnalystController@modifyappscan']);
Route::get('createappscan/{id}', ['uses' => 'AnalystController@createappscan']);
Route::post('issueappscan', ['uses' => 'AnalystController@issueappscan']);
Route::get('reviewappscan/{id}', ['uses' => 'ReviewController@showappscan']);
Route::post('Review/reviewappscan', ['uses' => 'QAController@modifyappscan']);

Route::get('showappscan/{id}', ['uses' => 'RemofficerController@showappscan']);
Route::post('Remofficer/showappscan', ['uses' => 'RemofficerController@modifyappscan']);
Route::get('forward/{id}', ['uses' => 'RemofficerController@forward']);
Route::get('pmoshowappscan/{id}', ['uses' => 'RempmoController@showappscan']);
Route::post('Rempmo/pmoshowappscan', ['uses' => 'RempmoController@modifyappscan']);

Route::post('storeBurpsuite', ['uses' => 'TesterController@storeBurpsuite']);
Route::get('editburp/{id}', ['uses' => 'ReportController@shownburp']);
Route::post('Report/editburp', ['uses' => 'AnalystController@modifyburp']);
Route::get('createburp/{id}', ['uses' => 'AnalystController@createburp']);
Route::post('issueburp', ['uses' => 'AnalystController@issueburp']);
Route::get('reviewburp/{id}', ['uses' => 'ReviewController@showburp']);
Route::post('Review/reviewburp', ['uses' => 'QAController@modifyburp']);

Route::post('storeNexpose', ['uses' => 'TesterController@storeNexpose']);
Route::get('editnexpose/{id}', ['uses' => 'ReportController@shownexpose']);
Route::post('Report/editnexpose', ['uses' => 'AnalystController@modifynexpose']);
Route::get('createnexpose/{id}', ['uses' => 'AnalystController@createnexpose']);
Route::post('issueNexpose', ['uses' => 'AnalystController@issueNexpose']);
Route::get('reviewnexpose/{id}', ['uses' => 'ReviewController@shownexpose']);
Route::post('Review/reviewnexpose', ['uses' => 'QAController@modifyNexpsoe']);




//Route::get('/','CloseController@index');
Route::get('showresults', [
    'uses' => 'CloseController@scanresults'
]);
Route::get('display-search-queries','CloseController@searchData');


//  ========== Ciso routes =============

Route::get('showdetailsbyciso/{id}/{opco_id}', [
    'uses' => 'CisoController@riskdetails'
]);

Route::get('cisoshowxml/{id}', [
    'uses' => 'CisoController@showxml'
]);

Route::get('cisoshowcsv/{id}', [
    'uses' => 'CisoController@show'
]);

// temp
Route::get('hostsbyvulnname', [
    'uses' => 'HomeController@num_of_hosts_by_vuln_name'
]);

Route::get('extdb', ['uses' => 'ExternalDashboardController@index']);
Route::get('extopcodb/{opcoid}', ['uses' => 'ExternalDashboardController@externalOpcoDashboard']);

Route::get('removecomment/{id}', [
    'uses' => 'ReportController@deletecomment'
]);

Route::get('shownexpose/{id}', ['uses' => 'RemofficerController@shownexpose']);
Route::post('Remofficer/shownexpose', ['uses' => 'RemofficerController@modifynexpose']);

Route::get('showburp/{id}', ['uses' => 'RemofficerController@showburp']);
Route::post('Remofficer/showburp', ['uses' => 'RemofficerController@modifyburp']);

Route::get('pmoshownexpose/{id}', [
    'uses' => 'RempmoController@shownexpose'
]);
Route::post('Rempmo/pmoshownexpose', [
    'uses' => 'RempmoController@modifynexpose'
]);

Route::get('pmoshowburp/{id}', [
    'uses' => 'RempmoController@showburp'
]);

Route::post('Rempmo/pmoshowburp', [
    'uses' => 'RempmoController@modifyburp'
]);

// External Findings API
Route::get('rem_officer_external', ['uses' => 'API\RemOfficerAPIController@externalFindings']);
Route::get('externalremshowxml/{id}', ['uses' => 'API\RemOfficerAPIController@showxmlExternalFindings']);
Route::get('externalremedit/{id}', ['uses' => 'API\RemOfficerAPIController@showeditExternalFindings']);
Route::get('rem_ext/{id}', ['uses' => 'API\RemOfficerAPIController@remediateExternalFinding']);
Route::post('update_ext_rem', ['uses' => 'API\RemOfficerAPIController@updateExternalFinding']);