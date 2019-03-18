<?php

namespace App\Http\Controllers;


use App\Purchase;
use App\Supplier;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
    }

    public function indexPage()
    {
        return view('supplier.index');
    }

    public function index(Request $request)
    {
        $result = [];
        $filter = $request->has('filter') ? $filter = $request->query('filter') : "";
        try{
            $q = null;
            $suppliers = [];
            $q = Supplier::where('label', 'LIKE', '%'.$filter.'%');
            if($request->has('paginate')) {
                $suppliers = $q->paginate(10);
            } else {
                $suppliers = $q->get();
            }
            $result['code'] = 0;
            $result['suppliers'] = $suppliers;
        } catch (Exception $e) {
            return $e;
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $result = [];
        $request->validate([
            'name'=>'required|unique:suppliers'
        ]);
        try{
            $supplier = Supplier::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'address'=>$request->address
            ]);
            $result['code'] = 0;
            $result['supplier'] = $supplier;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }

    public function update(Request $request, $supplierId)
    {
        $this->validate($request, [
            'name'=>'required|unique:suppliers, name,'.$supplierId.'id',
            'phone'=> 'required|unique:suppliers, phone,'.$supplierId.'id'
        ]);
        $supplier = Supplier::findOrFail($supplierId);
        $result = [];
        $status = null;
        try{
            $supplier->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'address'=>$request->address
            ]);
            $result['code'] = 0;
            $result['supplier'] = $supplier;
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result);
    }

    public function destroy($supplierId)
    {
        $result = [];
        $status = null;
        try{
            Supplier::destroy($supplierId);
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