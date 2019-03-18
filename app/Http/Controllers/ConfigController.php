<?php

namespace App\Http\Controllers;

use App\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function index(Request $request)
    {
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
    }

    public function store(Request $request)
    {
        $result = [];
        try{
            $config = Config::create([
                'shop_name'=> $request->shop_name,
                'shop_phone'=> $request->shop_phone,
                'shop_address'=> $request->shop_address,
                'shop_email'=> $request->shop_email,
                'shop_message'=>$request->shop_message,
                'profit_margin'=> $request->profit_margin
            ]);
            $result['code']=0;
            $result['message'] = "Config created successfully";
            $result['config'] = $config;
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Failed to load config data";
        }
        return response()->json($result);
    }


    public function update(Request $request)
    {
        $result = [];
        try{
            $config = Config::findOrFail($request->id);
            $config->update([
                'shop_name'=> $request->shop_name,
                'shop_phone'=> $request->shop_phone,
                'shop_address'=> $request->shop_address,
                'shop_email'=> $request->shop_email,
                'shop_message'=>$request->shop_message,
                'profit_margin'=> $request->profit_margin,
            ]);
            $result['code']=0;
            $result['config'] = $config;
            $result['message'] = "SHop info updated successfully";
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Failed to load config data";
        }
        return response()->json($result);
    }

}
