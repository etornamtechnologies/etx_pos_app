<?php

namespace App\Http\Controllers;

use App\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()) {
            $result = [];
            try{
                $config = Config::findOrFail(1);
                $result['code']=0;
                $result['config'] = $config;
            } catch(Exception $e) {
                $result['code'] = 1;
                $result['message'] = "Failed to load config data";
            }
            return response()->json($result);
        } else {
            return view('config.index');
        }
    }

    public function update(Request $request, $id)
    {
        $result = [];
        try{
            $config = Config::findOrFail($id);
            $config->update([
                'shop_name'=> $request->shop_name,
                'shop_phone'=> $request->shop_phone,
                'shop_address'=> $request->shop_address,
                'shop_email'=> $request->shop_email,
                'shop_message'=>$request->shop_message
            ]);
            $result['code']=0;
            $result['config'] = $config;
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Failed to load config data";
        }
        return response()->json($result);
    }

}
