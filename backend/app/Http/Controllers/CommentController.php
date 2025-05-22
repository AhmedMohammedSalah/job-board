<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCommentRequest;

class CommentController extends Controller
{
    public function index($jobId)
    {
        $comments = Comment::with(['user', 'replies.user'])
            ->where('job_id', $jobId)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return response()->json($comments);
    }
    public function getAllComments(){
        // Get all comments with job and user
        $comments = Comment::all() -> load(['user', 'job']);
        return response()->json($comments);
    }

    public function store(StoreCommentRequest $request)
    {
        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'job_id' => $request->job_id,
            'parent_id' => $request->parent_id ?? null
        ]);

        return response()->json($comment->load('user'));
    }

    public function update(StoreCommentRequest $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->update([
            'content' => $request->content
        ]);

        return response()->json($comment->load('user'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $comment = Comment::findOrFail($id);

        if ($user->role!="admin" && $comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }
}
