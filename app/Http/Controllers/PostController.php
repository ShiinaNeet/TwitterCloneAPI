<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use ResponseBuilder;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $post = Post::orderBy("created_at", "desc")->get();
        return ResponseBuilder::buildResponse($post, "Posts retrieved successfully", 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //validate here
      

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        try {
            $post = Post::create($request->validated());
            return ResponseBuilder::buildResponse($post, "You have created a Post!", 201);
        } catch (\Exception $e) {
            return ResponseBuilder::buildResponse(null, "Failed to create post." . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        if ($post) {
            return ResponseBuilder::buildResponse($post, "Post retrieved successfully", 200);
        } else {
            return ResponseBuilder::buildResponse(null, "Post not found", 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, string $id)
    {   
        try {
            $post = Post::find($id);
            if(!$post){
                return ResponseBuilder::buildResponse(null, "Post not found!",404);
            }
            $post->update($request->validated());
            return ResponseBuilder::buildResponse( $post, 'Post updated successfully', 200);
        }
        catch(\Exception $e){
            return ResponseBuilder::buildResponse(null, "Failed to Uodate". $e->getMessage(),500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $post = Post::find($id);
            if(!$post){
                return ResponseBuilder::buildResponse(null, "Post not found!",404);
            }
            $post->destory();
            return ResponseBuilder::buildResponse( $post, 'Post updated successfully', 200);
        }
        catch(\Exception $e){
            return ResponseBuilder::buildResponse(null, "Failed to Uodate". $e->getMessage(),500);
        }
    }
}
