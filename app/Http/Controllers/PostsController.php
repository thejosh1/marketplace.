<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PostResource;
use Illuminate\Support\MessageBag;
use Illuminate\Mail\Message;

class PostsController extends Controller
{
    /**
     * store a newly created resource in storage
     * 
     * @param \Illuminate|Http Request $request
     * @return \Illuminate|Http Response
     */
    public function createPost(Request $request)
    {
        $errors = new MessageBag();
        $errors->add('401', 'Sorry you are not logged in yet');

        $validationMessages = [
            'required' => 'The :attribute field id required',
            'exists' => 'The specified :attribute reference_id does not exist',
            'integer' => 'The :attribute is of invalid type',
        ];


        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exist|integer|exist: users, id',
            'post' => 'required|string'
        ], $validationMessages);

        if ($validator->fails()) {
            return response()->json($validator->messages, 422);
        }

        $data = collect(request()->all())->toArray();
        $data['user_id'] = Auth::user()->id;

        if (!Auth::user) {
            return response()->json([
                'errors' => $errors
            ], 401);
        }
        $result = new PostResource($data);

        if ($result) {
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json(false, 500);
        }
    }

    /**
     * update the specified resource in storage
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePost(Request $request)
    {
        $errors = new MessageBag();
        $errors->add('404', 'you can only edit your own post');

        $validationMessages = [
            'required' => 'The :attribute field id required',
            'exists' => 'The specified :attribute reference_id does not exist',
            'integer' => 'The :attribute is of invalid type',
        ];


        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exist|integer|exist: users, id',
            'post' => 'required|string'
        ], $validationMessages);

        if ($validator->fails()) {
            return response()->json($validator->messages, 422);
        }

        $data = collect(request()->all())->toArray();
        $data['user_id'] = Auth::user()->id;

        if ($data['user_id'] != Auth::user()->id) {
            return response()->json([
                'errors' => $errors
            ], 404);
        }
        $post = Post;
        $user = Auth::user();
        $result = $post()->$user()->update($data);
        if ($result) {
            return response()->json([
                'data' => true
            ], 201);
        } else {
            return response()->json(false, 500);
        }
    }

    /**
     * show a single post
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response 
     */

    public function showPost(Request $request)
    {
        $id = (int) $request->route('id');
        $post = Post::find($id);

        if ($post) {
            return response()->json([
                'data' => true
            ], 200);
        } else {
            return response()->json(false, 500);
        }
    }

    /**
     * delete a specified post
     * @param \Illuminate\Http\Request $request
     * @param \Auth\user
     * @return \Illuminate\Http\Response
     */
    public function deletePost(Request $request)
    {
        $errors = new MessageBag();
        $errors->add('404', 'post not found');
        $id = (int) $request->route('id');
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            return response()->json([
                'data' => true
            ], 204);
        } else {
            return response()->json([
                'errors' => $errors
            ], 404);
        }
    }

    public function list(Request $request)
    { 
        
    }
}
