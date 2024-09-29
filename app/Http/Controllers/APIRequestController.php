<?php

namespace App\Http\Controllers;

use Request;
use DB;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use Config;

class APIRequestController extends Controller
{
    // Not in use for now
    public function checkToken()
    {
        return DB::table('oauth_tokens')->first();
    }

    // Not in use for now
    public function requestNewToken()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $_ENV['GCDP_URL'].'/oauth/token', [
            'form_params' => [
                'client_id' => 2,
                'client_secret' => $_ENV['GCDP_API_CLIENT_SECRET'],
                'grant_type' => 'client_credentials',
                'scope' => '*',
            ]
        ]);

        $auth = json_decode((string)$response->getBody());

        DB::table('oauth_tokens')->update([
                'token' => $auth->access_token,
                'updated_at' => date('Y-m-d H:i:s', time()),
            ]
        );
        return $auth->access_token;
    }

    // To start the API request for tfetchint internal findings from LCDP
    public function requestAPI()
    {
//        $checkToken = $this->checkToken();

//        $tokenExpiry = Carbon::parse($checkToken->updated_at)->addMonth(11);
//        $now = Carbon::now();

//        if(!isset($checkToken->token))
//            $access_token = $this->requestNewToken();
//        elseif($now > $tokenExpiry)
//            $access_token = $this->requestNewToken();
//        else
//            $access_token = $checkToken->token;

        $client = new \GuzzleHttp\Client();
        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        $opco_enums = array_flip(Config::get('enums.opco_switch'));

//===== OPEN =====
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/open?opcoid='.$_ENV['APP_OPCO_ID'], [
            'headers' => [
//                'Authorization' => 'Bearer '.$access_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode((string)$response->getBody());

        $data->critical_risk = $encrypter->decrypt($data->critical_risk);
        $data->high_risk = $encrypter->decrypt($data->high_risk);
        $data->med_risk = $encrypter->decrypt($data->med_risk);
        $data->low_risk = $encrypter->decrypt($data->low_risk);
        $data->pending_rem_c = $encrypter->decrypt($data->pending_rem_c);
        $data->pending_rem_h = $encrypter->decrypt($data->pending_rem_h);
        $data->pending_rem_m = $encrypter->decrypt($data->pending_rem_m);
        $data->pending_rem_l = $encrypter->decrypt($data->pending_rem_l);

        $db_open = DB::connection('mongodb')->collection('external_db_open')->first();

        DB::connection('mongodb')->collection('external_db_open')
            ->where('_id', $db_open['_id'])
            ->update([
                'critical_risk' => $data->critical_risk,
                'high_risk' => $data->high_risk,
                'med_risk' => $data->med_risk,
                'low_risk' => $data->low_risk,
                'pending_rem_c' => $data->pending_rem_c,
                'pending_rem_h' => $data->pending_rem_h,
                'pending_rem_m' => $data->pending_rem_m,
                'pending_rem_l' => $data->pending_rem_l,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);

//===== OPEN & CLOSE =====
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/total_open_close?opcoid='.$_ENV['APP_OPCO_ID'], [
            'headers' => [
//                'Authorization' => 'Bearer '.$access_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode((string)$response->getBody());

        $data->close = $encrypter->decrypt($data->close);
        $data->open = $encrypter->decrypt($data->open);

        $db_open_close_findings = DB::connection('mongodb')->collection('external_db_open_close_findings')->first();

        DB::connection('mongodb')->collection('external_db_open_close_findings')
            ->where('_id', $db_open_close_findings['_id'])
            ->update([
                'close' => $data->close,
                'open' => $data->open,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);

//===== NUM OF HOSTS BY CAT =====
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/db_num_of_hosts_by_cat?opcoid='.$_ENV['APP_OPCO_ID'], [
            'headers' => [
//                'Authorization' => 'Bearer '.$access_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode((string)$response->getBody());

        $data->hosts_per_cat_array = $encrypter->decrypt($data->hosts_per_cat_array);
        $data->cat_array = $encrypter->decrypt($data->cat_array);

        $db_num_of_hosts_by_cat = DB::connection('mongodb')->collection('external_db_num_of_hosts_by_cat')->first();

        DB::connection('mongodb')->collection('external_db_num_of_hosts_by_cat')
            ->where('_id', $db_num_of_hosts_by_cat['_id'])
            ->update([
                'hosts_per_cat_array' => $data->hosts_per_cat_array,
                'cat_array' => $data->cat_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);

//===== NUM OF HOSTS BY VULN NAME =====
//         $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/num_of_hosts_by_vuln_name?opcoid='.$_ENV['APP_OPCO_ID'], [
//             'headers' => [
// //                'Authorization' => 'Bearer '.$access_token,
//                 'Content-Type' => 'application/json',
//                 'Accept' => 'application/json',
//             ],
//             'verify' => false,
//         ]);

//         $data = json_decode((string)$response->getBody());

//         $data->num_of_hosts_by_vuln_name = $encrypter->decrypt($data->num_of_hosts_by_vuln_name);
//         $db_num_of_hosts_by_vuln_name = DB::connection('mongodb')->collection('external_db_num_of_hosts_by_vuln_name')->first();

//         DB::connection('mongodb')->collection('external_db_num_of_hosts_by_vuln_name')
//             ->where('_id', $db_num_of_hosts_by_vuln_name['_id'])
//             ->update([
//                 'num_of_hosts_by_vuln_name' => $data->num_of_hosts_by_vuln_name,
//                 'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
//             ]);

//===== AGING & KPI =====
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/aging_and_kpi?opcoid='.$_ENV['APP_OPCO_ID'], [
            'headers' => [
//                'Authorization' => 'Bearer '.$access_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode((string)$response->getBody());

        $data->pdfinfo = $encrypter->decrypt($data->pdfinfo);
        $data->aging_opco_risk_array = $encrypter->decrypt($data->aging_opco_risk_array);
        $data->kpi_opco_risk_array = $encrypter->decrypt($data->kpi_opco_risk_array);

        $db_aging_and_kpi = DB::connection('mongodb')->collection('external_db_aging_and_kpi')->first();

        DB::connection('mongodb')->collection('external_db_aging_and_kpi')
            ->where('_id', $db_aging_and_kpi['_id'])
            ->update([
                'pdfinfo' => $data->pdfinfo,
                'aging_opco_risk_array' => $data->aging_opco_risk_array,
                'kpi_opco_risk_array' => $data->kpi_opco_risk_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);

//===== VULN EXPOSURE FOR THE PAST FOUR MONTHS =====
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/vuln_exposure_past_four_months?opcoid='.$_ENV['APP_OPCO_ID'], [
            'headers' => [
//                'Authorization' => 'Bearer '.$access_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode((string)$response->getBody());

        $data->open_array = $encrypter->decrypt($data->open_array);
        $data->close_array = $encrypter->decrypt($data->close_array);

        $db_vuln_exposure_past_four_months = DB::connection('mongodb')->collection('external_db_vuln_exposure_past_four_months')->first();

        DB::connection('mongodb')->collection('external_db_vuln_exposure_past_four_months')
            ->where('_id', $db_vuln_exposure_past_four_months['_id'])
            ->update([
                'open_array' => $data->open_array,
                'close_array' => $data->close_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
            ]);

//===== OPCO FINDINGS =====
        $response = $client->request('GET',$_ENV['GCDP_URL'].'/api/opco_findings?opcoid='.$_ENV['APP_OPCO_ID'], [
            'headers' => [
//                'Authorization' => 'Bearer '.$access_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $data = json_decode((string)$response->getBody());
        $data->opco_risk_array = $encrypter->decrypt($data->opco_risk_array);

        $db_opco_findings = DB::connection('mongodb')->collection('external_db_opco_findings')->first();

        DB::connection('mongodb')->collection('external_db_opco_findings')
            ->where('_id', $db_opco_findings['_id'])
            ->update([
                'opco_risk_array' => $data->opco_risk_array,
                'updated_at' => new UTCDateTime(strtotime(date("Y/m/d H:i:s"))*1000),
        ]);
    }
}
