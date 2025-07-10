<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $reviews = UserReview::latest()->paginate(10);

            // Format each review's profile image URL
            $reviews->getCollection()->transform(function ($review) {
                if ($review->profile_photo_url && !str_starts_with($review->profile_photo_url, 'http')) {
                    $review->profile_photo_url = url('uploads/reviews/' . $review->profile_photo_url);
                }
                return $review;
            });

            return response()->json([
                'success' => true,
                'data' => $reviews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reviews.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'author_name' => 'required|string|max:255',
                'image' => 'nullable|image',
                'rating' => 'required|numeric|min:1|max:5',
                'text' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $profilePhotoUrl = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image = time() . '_profile.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reviews'), $image);
                $profilePhotoUrl = url('uploads/reviews/' . $image);
            }

            $review = UserReview::create([
                'author_name' => $request->author_name,
                'profile_photo_url' => $profilePhotoUrl,
                'rating' => $request->rating,
                'text' => $request->text,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review created successfully.',
                'data' => $review,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create review.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $review = UserReview::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'author_name' => 'sometimes|required|string|max:255',
                'image' => 'nullable|image',
                'rating' => 'sometimes|required|numeric|min:1|max:5',
                'text' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $image = time() . '_profile.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reviews'), $image);
                $review->profile_photo_url = url('uploads/reviews/' . $image);
            }

            $review->update($request->only([
                'author_name', 'rating', 'text'
            ]));

            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully.',
                'data' => $review,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review.',
                'error' => $e->getMessage(),
            ], 500);
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
    public function destroy(string $id)
    {
        try {
            $review = UserReview::findOrFail($id);
            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
