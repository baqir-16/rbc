<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stream;
use App\User;
use App\Pdfreport;
use Auth;
use Input;
use App;
use Response;
use File;
use Excel;
use DB;
use SnappyPDF;
use Config;
use SSH;
use MongoDB\BSON\UTCDateTime;

class AssetsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // To display the list of all assets
    public function index()
    {
        $assets = DB::connection('mongodb')->collection('asset_mng')->get()->toArray();
        return view('assetmng.index', compact('assets'));
    }

    // To retrieve details of an asset
    public function assetdetail($_id)
    {
        $assets = DB::connection('mongodb')->collection('asset_mng')
            ->where('_id', $_id)
            ->get()->first();

        $hod_signoff_date = NULL;

        $asset = DB::connection('mongodb')->collection('asset_mng')->where('_id', $_id)->get(['url']);

        if(isset($asset[0]['url']))
            $issue = DB::connection('mongodb')->collection('scan_results')->where('host', '=' ,$asset[0]['url'])->whereNotNull('hod_signoff_date')->where('false_positive', 0)->get();
        else
            $issue = [];

        $info_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))
            ->where('rem_pmo_closure_status', 0)
            ->where('host', '=' ,$asset[0]['url'])
            ->count();

        $low_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Low'))
            ->where('rem_pmo_closure_status', 0)
            ->where('host', '=' ,$asset[0]['url'])
            ->count();

        $med_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))
            ->where('host', '=' ,$asset[0]['url'])
            ->count();

        $high_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->where('rem_pmo_closure_status', 0)
            ->where('host', '=' ,$asset[0]['url'])
            ->count();
        $critical_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->where('rem_pmo_closure_status', 0)
            ->where('host', '=' ,$asset[0]['url'])
            ->count();

        $close = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->where('host', '=' ,$asset[0]['url'])
            ->count();
        $open = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->where('host', '=' ,$asset[0]['url'])
            ->count();


        $streamsmdb = DB::connection('mongodb')->collection('scan_results')
            ->where('host', '=' ,$asset[0]['url'])
            ->whereNotNull('hod_signoff_date')
            ->where('false_positive', 0)
            ->get();

        if(isset($streamsmdb[0]['stream_id']))
            $streams = Stream::with('modules', 'tickets')->where('id', '=' ,$streamsmdb[0]['stream_id'] )
                ->orderBy('id')->get();
        else
            $streams = [];

        return view('assetmng.assetdetail', compact('assets', 'issue','med_risk', 'high_risk', 'info_risk', 'low_risk','critical_risk', 'open', 'close', 'streams'));
    }

    // To display findings report of asset
    public function showreport($id)
    {
        $mresults = DB::connection('mongodb')->collection('scan_results')
            ->where('stream_id', (int)$id)
            ->where('false_positive', 0)
            ->groupby('name','risk')
            ->orderBy('risk', 'desc')->get();

        $mdetails = DB::connection('mongodb')->collection('scan_results')
            ->where('stream_id', (int)$id)
            ->where('false_positive', 0)
            ->groupby('url_scheme', 'module_id', 'name','risk', 'description', 'solution', 'synopsis','host', 'cve', 'cvss', 'protocol', 'port', 'img_filename', 'Affects', 'Impact', 'Description', 'Recommendation', 'ModuleName', 'Details', 'Request', 'Response' )
            ->orderBy('risk', 'desc')->get();

        $enums = array_flip(Config::get('enums.severity_status'));

        $sresults = Stream::with('comments', 'opco', 'modules', 'user', 'tickets')->where('id', $id)->get();
        $tester = User::where('id', $sresults[0]->tester_id)->first();
        $analyst = User::where('id', $sresults[0]->analyst_id)->first();
        $qa = User::where('id', $sresults[0]->qa_id)->first();
        $hod = User::where('id', $sresults[0]->hod_id)->first();

        $pdfinfo = Pdfreport::latest()->with('user')->get();

        return view('assetmng.view_report', compact('mresults','sresults','mdetails','tickets','modules','comments','opco','user','enums','tester','analyst','hod','qa', 'pdfinfo'));
    }

    // To return create asset view
    public function create()
    {
        return view('assetmng.create');
    }

    // To return create asset view
    public function createasset(Request $request)
    {
        return view('assetmng.createasset');
    }

    // Insert new asset with duplication check
    public function issueasset(Request $request)
    {
        $request->url = urlencode($request->url);
        DB::beginTransaction();

        $duplicate_check = DB::connection('mongodb')->collection('asset_mng')
          ->where('url', '=', $request->url)
          ->count();

        if ($duplicate_check == 0) {

        try {

            DB::connection('mongodb')->collection('asset_mng')->insert([
                // application-asset and non-application-asset
                'asset_tag_number' => $request->asset_tag_number,
                'pysical_location' => $request->pysical_location,
                'floor' => $request->floor,
                'asset_type' => $request->asset_type,
                'manufacturer' => $request->manufacturer,
                'serial_number' => $request->serial_number,
                'make_and_model' => $request->make_and_model,
                'application_id' => $request->application_id,
                'application_name' => $request->application_name,
                'url' => $request->url,
                'application_type' => $request->application_type,
                'device_name' => $request->device_name,
                'hostname/node_name' => $request->hostnamenode_name,
                'internal_ip_address' => $request->internal_ip_address,
                'public_ip_address' => $request->public_ip_address,
                'web_server_ip_address' => $request->web_server_ip_address,
                'database_ip_address' => $request->database_ip_address,
                'domain_name' => $request->domain_name,
                'device_status' => $request->device_status,
                'operating_system' => $request->operating_system,
                'software_version' => $request->software_version,
                'kernel_version' => $request->kernel_version,
                'device_type' => $request->device_type,
                'cluster/standalone' => $request->clusterstandalone,
                'physical/vm' => $request->physicalvm,
                'asset_age(years)' => $request->asset_age_year,
                'Prod/Non-Prod/DR' => $request->prodnon_proddr,
                'database_component' => $request->database_component,
                'latest_patch_level' => $request->latest_patch_level,
                'monitoring_server_ip_address' => $request->monitoring_server_ip_address,
                'monitoring_tool' => $request->monitoring_tool,
                'hosted_applications' => $request->hosted_applications,
                'app/db_name' => $request->appdb_name,
                'business_criticality' => $request->business_criticality,
                'business_environment' => $request->business_environment,
                'business_description' => $request->business_description,
                'mbss_type' => $request->mbss_type,
                'mbss_compliance' => $request->mbss_compliance,
                'mbss_compliance_percentage' => $request->mbss_compliance_percentage,
                'mbss_owner' => $request->mbss_owner,
                'asset_owner' => $request->asset_owner,
                'group_owner' => $request->group_owner,
                'sub-group_owner' => $request->sub_group_owner,
                'administrator' => $request->administrator,
                'alternate_administrator' => $request->alternate_administrator,
                'hod' => $request->hod,
                'vendor' => $request->vendor,
                'operating_system_responsible' => $request->operating_system_responsible,
                'database_responsible' => $request->database_responsible,
                'web_application_responsible' => $request->web_application_responsible,
                'web_server_responsible' => $request->web_server_responsible,
                'web_service_responsible' => $request->web_service_responsible,
                'remarks' => $request->remarks,

                'status' => 1,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
            ]);

            DB::commit();
            flash()->success('Asset was successfully created');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Asset was NOT created successfully');
        }
    } else if ($duplicate_check > 0){
        $request->session()->flash('alert-danger', 'Asset already existed!');
    }

    return redirect()->action('AssetsController@index');
    }

    // Uploading of assets in batch
    public function store(Request $request)
    {

        if (Input::hasFile('file')) {

            foreach (Input::file('file') as $file) {
                $path = $file->getRealPath();

                $data = Excel::load($path, function ($reader) {
                })->get();

                if (!empty($data) && $data->count()) {
                    foreach ($data as $key => $value) {
                        $value->url = urlencode($value->url);

                        $result = DB::connection('mongodb')->collection('scan_results')->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('risk', '!=', 1)->where('false_positive', 0)->pluck("host")->toArray();
                        $result1 = DB::connection('mongodb')->collection('scan_results')->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)->pluck("host")->toArray();

                        if (in_array($value->url, $result) or in_array($value->url, $result1)) {

                            $duplicate_check = DB::connection('mongodb')->collection('asset_mng')
                                ->where('url', '=', $value->url)
                                ->count();

                            if ($duplicate_check == 0) {

                                $insert[] = [
                                    // application-asset and non-application-asset
                                    'asset_tag_number' => $value->asset_tag_number,
                                    'pysical_location' => $value->pysical_location,
                                    'floor' => $value->floor,
                                    'asset_type' => $value->asset_type,
                                    'manufacturer' => $value->manufacturer,
                                    'serial_number' => $value->serial_number,
                                    'make_and_model' => $value->make_and_model,
                                    'application_id' => $value->application_id,
                                    'application_name' => $value->application_name,
                                    'url' => $value->url,
                                    'application_type' => $value->application_type,
                                    'device_name' => $value->device_name,
                                    'hostname/node_name' => $value->hostnamenode_name,
                                    'internal_ip_address' => $value->internal_ip_address,
                                    'public_ip_address' => $value->public_ip_address,
                                    'web_server_ip_address' => $value->web_server_ip_address,
                                    'database_ip_address' => $value->database_ip_address,
                                    'domain_name' => $value->domain_name,
                                    'device_status' => $value->device_status,
                                    'operating_system' => $value->operating_system,
                                    'software_version' => $value->software_version,
                                    'kernel_version' => $value->kernel_version,
                                    'device_type' => $value->device_type,
                                    'cluster/standalone' => $value->clusterstandalone,
                                    'physical/vm' => $value->physicalvm,
                                    'asset_age(years)' => $value->asset_age_year,
                                    'Prod/Non-Prod/DR' => $value->prodnon_proddr,
                                    'database_component' => $value->database_component,
                                    'latest_patch_level' => $value->latest_patch_level,
                                    'monitoring_server_ip_address' => $value->monitoring_server_ip_address,
                                    'monitoring_tool' => $value->monitoring_tool,
                                    'hosted_applications' => $value->hosted_applications,
                                    'app/db_name' => $value->appdb_name,
                                    'business_criticality' => $value->business_criticality,
                                    'business_environment' => $value->business_environment,
                                    'business_description' => $value->business_description,
                                    'mbss_type' => $value->mbss_type,
                                    'mbss_compliance' => $value->mbss_compliance,
                                    'mbss_compliance_percentage' => $value->mbss_compliance_percentage,
                                    'mbss_owner' => $value->mbss_owner,
                                    'asset_owner' => $value->asset_owner,
                                    'group_owner' => $value->group_owner,
                                    'sub-group_owner' => $value->sub_group_owner,
                                    'administrator' => $value->administrator,
                                    'alternate_administrator' => $value->alternate_administrator,
                                    'hod' => $value->hod,
                                    'vendor' => $value->vendor,
                                    'operating_system_responsible' => $value->operating_system_responsible,
                                    'database_responsible' => $value->database_responsible,
                                    'web_application_responsible' => $value->web_application_responsible,
                                    'web_server_responsible' => $value->web_server_responsible,
                                    'web_service_responsible' => $value->web_service_responsible,
                                    'remarks' => $value->remarks,
                                    'critical_asset' => $value->critical_asset,
                                    'status' => 1,
                                    'updated_at' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000),
                                ];

                            }

                            DB::beginTransaction();

                            try {
                                DB::connection('mongodb')->collection('scan_results')
                                    ->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
                                    ->where('host', $value->url)
                                    ->update([
                                        'critical_asset' => $value->critical_asset,
                                        'asset_owner' => $value->asset_owner,
                                    ]);

                                DB::commit();
                                $request->session()->flash('alert-success', 'Successful!');
                            } catch (Exception $e) {
                                DB::rollback();
                                $request->session()->flash('alert-danger', 'NOT successful!');
                            }

                            DB::beginTransaction();

                            try {
                                DB::connection('mongodb')->collection('scan_results')
                                    ->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
                                    ->where('host', $value->url)
                                    ->update([
                                        'critical_asset' => $value->critical_asset,
                                        'asset_owner' => $value->asset_owner,
                                    ]);

                                DB::commit();
                                $request->session()->flash('alert-success', 'Successful!');
                            } catch (Exception $e) {
                                DB::rollback();
                                $request->session()->flash('alert-danger', 'NOT successful!');
                            }
                        }

                        else{
                            $duplicate_check = DB::connection('mongodb')->collection('asset_mng')
                                ->where('url', '=', $value->url)
                                ->count();

                            if ($duplicate_check == 0) {

                                $insert[] = [
                                    // application-asset and non-application-asset
                                    'asset_tag_number' => $value->asset_tag_number,
                                    'pysical_location' => $value->pysical_location,
                                    'floor' => $value->floor,
                                    'asset_type' => $value->asset_type,
                                    'manufacturer' => $value->manufacturer,
                                    'serial_number' => $value->serial_number,
                                    'make_and_model' => $value->make_and_model,
                                    'application_id' => $value->application_id,
                                    'application_name' => $value->application_name,
                                    'url' => $value->url,
                                    'application_type' => $value->application_type,
                                    'device_name' => $value->device_name,
                                    'hostname/node_name' => $value->hostnamenode_name,
                                    'internal_ip_address' => $value->internal_ip_address,
                                    'public_ip_address' => $value->public_ip_address,
                                    'web_server_ip_address' => $value->web_server_ip_address,
                                    'database_ip_address' => $value->database_ip_address,
                                    'domain_name' => $value->domain_name,
                                    'device_status' => $value->device_status,
                                    'operating_system' => $value->operating_system,
                                    'software_version' => $value->software_version,
                                    'kernel_version' => $value->kernel_version,
                                    'device_type' => $value->device_type,
                                    'cluster/standalone' => $value->clusterstandalone,
                                    'physical/vm' => $value->physicalvm,
                                    'asset_age(years)' => $value->asset_age_year,
                                    'Prod/Non-Prod/DR' => $value->prodnon_proddr,
                                    'database_component' => $value->database_component,
                                    'latest_patch_level' => $value->latest_patch_level,
                                    'monitoring_server_ip_address' => $value->monitoring_server_ip_address,
                                    'monitoring_tool' => $value->monitoring_tool,
                                    'hosted_applications' => $value->hosted_applications,
                                    'app/db_name' => $value->appdb_name,
                                    'business_criticality' => $value->business_criticality,
                                    'business_environment' => $value->business_environment,
                                    'business_description' => $value->business_description,
                                    'mbss_type' => $value->mbss_type,
                                    'mbss_compliance' => $value->mbss_compliance,
                                    'mbss_compliance_percentage' => $value->mbss_compliance_percentage,
                                    'mbss_owner' => $value->mbss_owner,
                                    'asset_owner' => 'N/A',
                                    'group_owner' => $value->group_owner,
                                    'sub-group_owner' => $value->sub_group_owner,
                                    'administrator' => $value->administrator,
                                    'alternate_administrator' => $value->alternate_administrator,
                                    'hod' => $value->hod,
                                    'vendor' => $value->vendor,
                                    'operating_system_responsible' => $value->operating_system_responsible,
                                    'database_responsible' => $value->database_responsible,
                                    'web_application_responsible' => $value->web_application_responsible,
                                    'web_server_responsible' => $value->web_server_responsible,
                                    'web_service_responsible' => $value->web_service_responsible,
                                    'remarks' => $value->remarks,
                                    'critical_asset' => 'N/A',
                                    'status' => 1,
                                    'updated_at' => new UTCDateTime(strtotime(date("Y/m/d")) * 1000),
                                ];
                            }

                            DB::beginTransaction();

                            try {
                                DB::connection('mongodb')->collection('scan_results')
                                    ->whereNotNull('hod_signoff_date')->where('rem_officer_rem_status', 0)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
                                    ->where('host', $value->url)
                                    ->update([
                                        'critical_asset' => 'N/A',
                                        'asset_owner' => 'N/A',
                                    ]);

                                DB::commit();
                                $request->session()->flash('alert-success', 'Successful!');
                            } catch (Exception $e) {
                                DB::rollback();
                                $request->session()->flash('alert-danger', 'NOT successful!');
                            }

                            DB::beginTransaction();

                            try {
                                DB::connection('mongodb')->collection('scan_results')
                                    ->where('rem_officer_rem_status', 1)->where('rem_pmo_closure_status', 0)->where('false_positive', 0)
                                    ->where('host', $value->url)
                                    ->update([
                                        'critical_asset' => 'N/A',
                                        'asset_owner' => 'N/A',
                                    ]);

                                DB::commit();
                                $request->session()->flash('alert-success', 'Successful!');
                            } catch (Exception $e) {
                                DB::rollback();
                                $request->session()->flash('alert-danger', 'NOT successful!');
                            }
                        }
                    }
                }
            }
        }
        if (!empty($insert)) {

            $tempArr = array_unique(array_column($insert, 'url'));
            $match = array_intersect_key($insert, $tempArr);
            $values = array_values($match);

            if (!empty($values)) {
                DB::beginTransaction();

                try {
                    DB::connection('mongodb')->collection('asset_mng')->insert($values);

                    DB::commit();
                    $request->session()->flash('alert-success', 'Upload was successful!');
                } catch (Exception $e) {
                    DB::rollback();
                    $request->session()->flash('alert-danger', 'Upload was NOT successful!');
                }
            }
        }
        return redirect()->action('AssetsController@index');
    }



    // Return selected asset details
    public function show(Request $request, $_id)
    {
        $issue = DB::connection('mongodb')->collection('asset_mng')->where('_id', $_id)->get()->first();
        return view('assetmng.edit', compact('issue'));
    }

    // Update selected asset details
    public function modify(Request $request)
    {
        $request->url = urlencode($request->url);
        DB::beginTransaction();

        try {
            DB::connection('mongodb')->collection('asset_mng')->where('_id', $request->_id)->update([

                'asset_tag_number' => $request->asset_tag_number,
                'pysical_location' => $request->pysical_location,
                'floor' => $request->floor,
                'asset_type'=> $request->asset_type,
                'manufacturer'=> $request-> manufacturer,
                'serial_number' => $request->serial_number,
                'make_and_model' => $request->make_and_model,
                'application_id' => $request->application_id,
                'application_name'=> $request->application_name,
                'url'=> $request-> url,
                'application_type' => $request->application_type,
                'device_name' => $request->device_name,
                'hostname/node_name' => $request->hostnamenode_name,
                'internal_ip_address' => $request->internal_ip_address,
                'public_ip_address'=> $request->public_ip_address,
                'web_server_ip_address'=> $request-> web_server_ip_address,
                'database_ip_address' => $request->database_ip_address,
                'domain_name' => $request->domain_name,
                'device_status'=> $request->device_status,
                'operating_system'=> $request-> operating_system,
                'software_version' => $request->software_version,
                'kernel_version' => $request->kernel_version,
                'device_type' => $request->device_type,
                'cluster/standalone' => $request->clusterstandalone,
                'physical/vm' => $request->physicalvm,
                'asset_age(years)' => $request->asset_age_year,
                'Prod/Non-Prod/DR' => $request->prodnon_proddr,
                'database_component'=> $request->database_component,
                'latest_patch_level'=> $request-> latest_patch_level,
                'monitoring_server_ip_address' => $request->monitoring_server_ip_address,
                'monitoring_tool' => $request->monitoring_tool,
                'hosted_applications' => $request->hosted_applications,
                'app/db_name' => $request->appdb_name,
                'business_criticality'=> $request->business_criticality,
                'business_environment'=> $request-> business_environment,
                'business_description' => $request->business_description,
                'mbss_type' => $request->mbss_type,
                'mbss_compliance' => $request->mbss_compliance,
                'mbss_compliance_percentage'=> $request->mbss_compliance_percentage,
                'mbss_owner'=> $request-> mbss_owner,
                'asset_owner' => $request->asset_owner,
                'group_owner' => $request->group_owner,
                'sub-group_owner' => $request->sub_group_owner,
                'administrator' => $request->administrator,
                'alternate_administrator'=> $request->alternate_administrator,
                'hod'=> $request-> hod,
                'vendor'=> $request-> vendor,
                'operating_system_responsible' => $request->operating_system_responsible,
                'database_responsible' => $request->database_responsible,
                'web_application_responsible' => $request->web_application_responsible,
                'web_server_responsible'=> $request->web_server_responsible,
                'web_service_responsible'=> $request-> web_service_responsible,
                'remarks'=> $request-> remarks,
                'status' => 1,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
            ]);

            DB::commit();
            flash()->success('Asset was successfully updated');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Asset was NOT updated successfully');
        }
        return redirect()->action('AssetsController@index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    // Soft delete selected asset
    public function deleteAsset(Request $request , $_id)
    {

        DB::beginTransaction();

        try {
            DB::connection('mongodb')->collection('asset_mng')->where('_id', $_id)->update([
                'status'    => 0,
                'updated_at'    => new UTCDateTime(strtotime(date("Y/m/d"))*1000),
            ]);

            DB::commit();
            flash()->success('Asset data deleted successfully');
        } catch (Exception $e) {
            DB::rollback();
            $request->session()->flash('alert-danger', 'Asset data are NOT deleted successfully');
        }
        return redirect()->back();
    }

    // Return the count of open findings by asset and severity level
    public function showassets(Request $request) {

        $host = $request->host;

        $issue = DB::connection('mongodb')->collection('scan_results')->where('host', '=' ,$host)
            ->whereNotNull('hod_signoff_date')
            ->where('false_positive', 0)
            ->where(['opco_id' => (int)Auth::user()->opco_id])
            ->get();

        $enums = array_flip(Config::get('enums.severity_status'));

        $info_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Informational'))
            ->where('rem_pmo_closure_status', 0)
            ->where(['opco_id' => (int)Auth::user()->opco_id])
            ->where('host', $host)
            ->count();

        $low_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Low'))
            ->where('rem_pmo_closure_status', 0)
            ->where(['opco_id' => (int)Auth::user()->opco_id])
            ->where('host', $host)
            ->count();

        $med_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Medium'))
            ->where(['opco_id' => (int)Auth::user()->opco_id])
            ->where('host', $host)
            ->count();

        $high_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.High'))
            ->where('rem_pmo_closure_status', 0)
            ->where(['opco_id' => (int)Auth::user()->opco_id])
            ->where('host', $host)
            ->count();
        $critical_risk = DB::connection('mongodb')->collection('scan_results')
            ->where('risk', (int)Config::get('enums.severity_status.Critical'))
            ->where('rem_pmo_closure_status', 0)
            ->where(['opco_id' => (int)Auth::user()->opco_id])
            ->where('host', $host)
            ->count();

        $close = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_pmo_closure_status', 1)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->where(['opco_id' => (int)Auth::user()->opco_id])
            ->where('host', $host)
            ->count();
        $open = DB::connection('mongodb')->collection('scan_results')
            ->where('rem_pmo_closure_status', 0)
            ->where('false_positive', 0)
            ->whereNotNull('hod_signoff_date')
            ->where(['opco_id' => (int)Auth::user()->opco_id])
            ->where('host', $host)
            ->count();

        return view('assetmng.showasset', compact('issue', 'enums', 'host','med_risk', 'high_risk', 'info_risk', 'low_risk','critical_risk', 'open', 'close'));

    }

    // Return findings of selected host and opco
    public function ipindex()
    {
        $result = DB::connection('mongodb')->collection('scan_results')->select('host', 'opco_id')->groupBy('host', 'opco_id')->get();
        return view('assetmng.ipindex')
            ->with('result', $result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
