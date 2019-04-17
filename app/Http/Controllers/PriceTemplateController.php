<?php

namespace App\Http\Controllers;

use App\PriceTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PriceTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role:admin,manager');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = PriceTemplate::with(['category'])->get();
        return response()->json(['code'=> 0, 'templates'=> $templates], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
           'label'=> 'required|unique:price_templates',
           'description'=> 'required',
           'base_value'=> 'required'
        ]);
        $priceTemplate = PriceTemplate::create([
            'label'=> $request->get('label'),
            'description'=> $request->get('description'),
            'base_value'=> $request->get('base_value'),
            'percent_value'=> $request->get('percent_value'),
            'constant_value'=> $request->get('constant_value')*100,
            'apply_on'=> $request->get('apply_on'),
            'category_id'=> $request->get('category_id')
        ]);
        return response()->json(['code'=> 0, 'template'=> $priceTemplate
                                    , 'message'=> 'Price template created successsfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
