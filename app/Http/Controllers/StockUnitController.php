<?php

namespace App\Http\Controllers;
use App\StockUnit;
use Illuminate\Http\Request;


class StockUnitController extends Controller
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
            $stockUnits = [];
            $q = StockUnit::where('label', 'LIKE', '%'.$filter.'%');
            if($request->has('paginate')) {
                $stockUnits = $q->paginate(10);
            } else {
                $stockUnits = $q->get();
            }
            $result['code'] = 0;
            $result['stock_units'] = $stockUnits;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response($result);
    }

    public function store(Request $request)
    {
        $request->validate([
            'label'=> 'required|unique:stock_units'
        ]);
        $result = [];
        try{
            $stockUnit = StockUnit::create([
                'label'=>strtoupper($request->label)
            ]);
            $result['code'] = 0;
            $result['stock_unit'] = $stockUnit;
            $result['message'] = "stock unit created successfully"; 
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result, $status);
    }

    public function update(Request $request, $stockUnitId)
    {
        $stockUnit = StockUnit::findOrFail($stockUnitId);
        $result = [];
        $status = null;
        try{
            $stockUnit->update([
                'label'=> strtoupper($request->label)
            ]);
            $result['code'] = 0;
            $result['stock_units'] = StockUnit::all();
            $result['message'] = "stock unit updated successfully"; 
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result);
    }

    public function destroy($stockUnitId)
    {
        $result = [];
        $status = null;
        try{
            if(StockUnit::destroy($stockUnitId)) {
                $result['code'] = 0;
            } else {
                $result['code'] = 1;
                $result['message'] = 'Cannot not delete';
            }
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }
}