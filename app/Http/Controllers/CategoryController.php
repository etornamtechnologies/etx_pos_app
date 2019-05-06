<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 28-Jan-19
 * Time: 11:37 PM
 */

namespace App\Http\Controllers;


use App\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role:supervisor,admin,manager')->except(['index']);
    }

    public function index(Request $request)
    {
        $result = [];
        $filter = "";
        if($request->has('filter')) {
            $filter = $request->query('filter');
        }
        try{
            $categories = [];
            $query = Category::where('label', 'LIKE', '%'.$filter.'%')
                            ->withCount('products')->orderBy('label', 'ASC');
            if($request->has('paginate')) {
                $categories = $query->paginate(10);
            }  else {
                $categories = $query->get();
            }               
            $result['code'] = 0;
            $result['categories'] = $categories;
        } catch (Exception $e) {
            return $e;
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $request->validate([
            'label'=>'required|unique:categories'
        ]);
        $result = [];
        try{
            $category = Category::create([
                'label'=>strtoupper($request->label)
            ]);
            $result['code'] = 0;
            $result['message'] = "category created successfully";
            $result['category'] = Category::where('id', $category->id)->withCount('products')->first();
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "server error";
        }
        return response()->json($result);
    }

    public function update(Request $request, $categoryId)
    {
        $request->validate([
           'label'=> 'required|unique:categories,label,'.$categoryId.'id'
        ]);
        $category = Category::findOrFail($categoryId);
        $result = [];
        try{
            $category->update([
                'label'=>strtoupper($request->label)
            ]);
            $result['code'] = 0;
            $result['message'] = 'category updated successfully';
            $result['categories'] = Category::withCount('products')->get();
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }

    public function destroy($categoryId)
    {
        $result = [];
        $status = null;
        try{
            Category::destroy($categoryId);
            $result['code'] = 0;
            $status = 200;
            $result['message'] = "category deleted successfully";
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=500;
        }
        return response()->json($result, $status);
    }
}
