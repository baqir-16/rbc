<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

// this file is not in use

class AjaxController extends Controller {
    public function index(){
        $msg = "This is a simple message.";
        return response()->json(array('msg'=> $msg), 200);
    }
}
