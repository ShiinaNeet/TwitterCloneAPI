<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\SharedFunctions\ResponseBuilder;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commentsdata = Comment::orderBy("created_at", "desc")->get();
        return ResponseBuilder::buildResponse($commentsdata, "Comments retrieved successfully", 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $post_Id)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:500',
                'user_id' => 'required|exists:users,id', // Make sure user_id exists in users table
            ]);

            $post = Post::findOrFail($post_Id);

            $comment = $post->comments()->create([
                'content' => $request->content,
                'user_id' => $request->user_id,
                'post_id' => $post_Id
            ]);

            return ResponseBuilder::buildResponse($comment, "You have created a Comment!", 201);
        } catch (\Exception $e) {
            return ResponseBuilder::buildResponse(null, "Failed to create comment. " . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $post, string $comment)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:500',
                'user_id' => 'required|exists:users,id', // Make sure user_id exists in users table
            ]);
            if(!$post || !$comment){
                return ResponseBuilder::buildResponse(null, 'Missing Query parameter', 404);
            }
            $comment = Comment::where('post_id', $post)->findOrFail($comment);
            $comment->update([
                'content' => $request->content
            ]);

            return ResponseBuilder::buildResponse($comment, "Successfully modified a comment!", 200);
        } catch (\Exception $e) {
            return ResponseBuilder::buildResponse(null, "Failed to update comment. " . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $validation = $request->validate([
                'comment_id' => 'required|exists:comments,id',
                'user_id' => 'required|exists:users,id',
            ]);

            $comment = Comment::findOrFail($request->comment_id);

            if ($request->user_id != $comment->user_id) {
                return ResponseBuilder::buildResponse(null, "You are not authorized to delete this comment", 401);
            }

            $comment->delete();

            return ResponseBuilder::buildResponse(null, "Successfully deleted the comment!", 200);
        } catch (\Exception $e) {
            return ResponseBuilder::buildResponse(null, "Failed to delete comment. " . $e->getMessage(), 500);
        }
    }

    // Custom method for most liked comments on a post
    public function mostLiked($postId)
    {
        // Logic for retrieving the most liked comments for a post
        $comments = Post::findOrFail($postId)->comments()->orderBy('likes', 'desc')->get();
        return ResponseBuilder::buildResponse($comments, "Most liked comments retrieved successfully", 200);
    }

    // Custom method for recent comments on a post
    public function recent($postId)
    {
        // Logic for retrieving recent comments for a post
        $comments = Post::findOrFail($postId)->comments()->orderBy('created_at', 'desc')->get();
        return ResponseBuilder::buildResponse($comments, "Recent comments retrieved successfully", 200);
    }
}
