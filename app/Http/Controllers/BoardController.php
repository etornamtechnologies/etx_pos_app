<?php

namespace App\Http\Controllers;
use App\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except'=>['show']]);
    }

    //


    public function index()
    {
        return Auth::user();
    }

    public function show($id)
    {
        $board = Board::findOrFail($id);
        if(Auth::user()->id !== $board->user_id) {
            return response()->json(['status'=>'error', 'message'=> 'unauthorized'], 401);
        }
        return $board;
    }

    public function store(Request $request)
    {
        Board::create([
            'name'=> $request->name,
            'user_id'=> Auth::user()->id
        ]);
        return response()->json(['message'=>'success'], 200);
    }

    public  function update(Request $request, $boardId)
    {
        $board = Board::findOrFail($boardId);
        if(Auth::user()->id !== $board->user_id) {
            return response()->json(['status'=>'error', 'message'=> 'unauthorized'], 401);
        }
        $board->update($request->all());
        return response()->json(['message'=>'success', 'board'=>$board], 200);
    }

    public  function delete($boardId)
    {
        $board = Board::findOrFail($boardId);
        if(Auth::user()->id !== $board->user_id) {
            return response()->json(['status'=>'error', 'message'=> 'unauthorized'], 401);
        }
        if(Board::destroy($boardId)) {
            return response()->json(['status'=>'success', 'message'=> 'Borad was deleted'],200);
        }
        return response()->json(['status'=>'error', 'message'=>'Something went wrong']);
    }
}
