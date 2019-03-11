<?php

namespace App\Http\Controllers;


use App\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $result = [];
        $filter = $request->has('filter') ? $request->query('filter') : "";
        try{
            $manufacturers = [];
            $q = Manufacturer::where('label', 'LIKE', '%'.$filter.'%')->withCount('products');
            if($request->has('paginate')) {
                $manufacturers = $q->paginate(10);
            } else {
                $manufacturers = $q->get();
            }
            $result['code'] = 0;
            $result['manufacturers'] = $manufacturers;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response($result);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
           'label'=> 'required|unique:manufacturers',
        ]);
        $result = [];
        $status = null;
        try{
            $manufacturer = Manufacturer::create([
                'label'=>strtoupper($request->label),
                'phone'=> $request->phone,
                'email'=>$request->email
            ]);
            $result['code'] = 0;
            $result['category'] = $manufacturer;
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result);
    }

    public function update(Request $request, $manufacturerId)
    {
        $request->validate([
           'label'=>'required|unique:manufacturers, label,'.$manufacturerId.'id'
        ]);
        $manufacturer = Manufacturer::findOrFail($manufacturerId);
        $result = [];
        $status = null;
        try{
            $manufacturer->update([
                'label'=>strtoupper($request->label),
                'phone'=> $request->phone,
                'email'=>$request->email
            ]);
            $result['code'] = 0;
            $result['category'] = $manufacturer;
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result);
    }

    public function destroy($manufacturerId)
    {
        $result = [];
        $status = null;
        try{
            Manufacturer::destroy($manufacturerId);
            $result['code'] = 0;
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result);
    }
}