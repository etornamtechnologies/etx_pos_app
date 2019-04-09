<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;


class CustomerController extends Controller
{
    public function __construct()
    {
       $this->middleware('api_auth');
    }

    public function indexPage()
    {
        return view('customer.index');
    }

    public function index(Request $request)
    {
        $result = [];
        $filter = "";
        $status = null;
        if($request->has('filter')) {
            $filter = $request->query('filter');
        }
        try{
            $customers = Customer::where('name','LIKE','%'.$filter.'%')->get();
            $result['code'] = 0;
            $result['customers'] = $customers;
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status = 402;
        }
        return response($result, $status);
    }

    public function debtors()
    {
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
           'name'=>'required',
           'phone'=> 'required|unique:customers'
        ]);
        $result = [];
        $status = null;
        try{
            $Customer = Customer::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'address'=>$request->address
            ]);
            $result['code'] = 0;
            $result['customer'] = $Customer;
            $result['message'] = "Customer created successfully";
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result);
    }

    public function update(Request $request, $customerId)
    {
        $this->validate($request, [
            'name'=>'required',
            'phone'=> 'required|unique:customers,phone,'.$customerId.'id'
        ]);
        $customer = Customer::findOrFail($customerId);
        $result = [];
        $status = null;
        try{
            $customer->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'address'=>$request->address
            ]);
            $result['code'] = 0;
            $result['customers'] = Customer::all();
            $result['message'] = "Customer updated successfully";
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result);
    }

    public function destroy($CustomerId)
    {
        $result = [];
        $status = null;
        try{
            Customer::destroy($CustomerId);
            $result['code'] = 0;
            $status = 200;
            $result['message'] = "Customer deleted successfully";
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=500;
        }
        return response()->json($result, $status);
    }
}