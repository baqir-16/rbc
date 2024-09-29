<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Validator;
use DB;
use Config;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\Encrypter as Encrypter;
use Auth;

// To perform remediation of particular OpCo through S2S VPN
class RemOfficerAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Pull remediation officer data of particular OpCo from GCDP
    public function externalFindings(){
        $departments = DB::table('departments')->pluck('department', 'id');
        $enums = array_flip(\Illuminate\Support\Facades\Config::get('enums.severity_status'));
        $enums1 = array_flip(Config::get('enums.mdb_stream_status'));

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/rem_officer_findings?opcoid='.$_ENV['APP_OPCO_ID'], [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode((string)$response->getBody());

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $res1 = $encrypter->decrypt($data->results1);
        $res2 = $encrypter->decrypt($data->results2);

        $vul_categories = DB::table('vul_categories')->pluck('name', 'id')->toArray();

        return view('Remofficer.external.index')
            ->with('result1', $res1)
            ->with('result2', $res2)
            ->with('departments', $departments)
            ->with('enums', $enums)
            ->with('enums1', $enums1)
            ->with('vul_categories', $vul_categories);
    }

    // Pull and show remediation officer data of particular OpCo from GCDP
    public function showxmlExternalFindings($_id)
    {
        $departments = DB::table('departments')->pluck('department', 'id');
        $enums = array_flip(Config::get('enums.severity_status'));
        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/rem_officer_finding_details?id='.$_id, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode((string)$response->getBody());

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $issue = $encrypter->decrypt($data->result);
        $comments = $encrypter->decrypt($data->comments);

        return view('Remofficer.external.remshowxml', compact('issue','comments', 'enums','vul_categories', 'departments'));
    }

    // Show remediation officer findings in details
    public function showeditExternalFindings($_id)
    {
        $departments = DB::table('departments')->pluck('department', 'id');
        $enums = array_flip(Config::get('enums.severity_status'));
        $vul_categories = DB::table('vul_categories')->pluck('name', 'id');

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/rem_officer_finding_details?id='.$_id, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode((string)$response->getBody());

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $issue = $encrypter->decrypt($data->result);
        $comments = $encrypter->decrypt($data->comments);

        return view('Remofficer.external.edit', compact('issue','comments', 'enums','vul_categories', 'departments'));
    }

    // Remediate remediation officer data pulled from GCDP
    public function remediateExternalFinding($_id)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/rem_ext?id='.$_id, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);
        json_decode((string)$response->getBody());

        return redirect('rem_officer_external');
    }

    // Update remediation officer data and push to GCDP
    public function updateExternalFinding(Request $request)
    {
        $imageArray = [];
        if(isset($request->img_filename)){
            foreach($request->img_filename as $key=>$file){
                $data = file_get_contents($file);
                $base64 = 'data:image/png;base64,'.base64_encode($data);
                array_push($imageArray, $base64);
            }
        }

        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $request->_id = $encrypter->encrypt($request->_id);
        $request->user_id = $encrypter->encrypt(Auth::user()->id);
        $request->status = $encrypter->encrypt($request->status);
        $request->risk = $encrypter->encrypt($request->risk);
        $request->vul_category_id = $encrypter->encrypt($request->vul_category_id);
        $request->datetime = $encrypter->encrypt($request->datetime);
        $request->comment = $encrypter->encrypt($request->comment);
        $imageArray = $encrypter->encrypt($imageArray);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST',$_ENV['GCDP_URL'].'/api/update_ext_rem', [
            'form_params' => [
                'id' => $request->_id,
                'user_id' => $request->user_id,
                'user_name' => $encrypter->encrypt(Auth::user()->username),
                'opco_id' => $encrypter->encrypt(Auth::user()->opco_id),
                'status' => $request->status,
                'risk' => $request->risk,
                'vul_category_id' => $request->vul_category_id,
                'target_fix_date' => $request->datetime,
                'comment' => $request->comment,
                'image' => $imageArray
            ],
            'verify' => false,
        ]);

//        $response = json_decode((string)$response->getBody());

        return redirect('rem_officer_external');
    }
}
