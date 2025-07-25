<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CleaningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CleaningServiceController extends Controller
{
    // Helper to format service data with full image URLs

    public function index()
    {
        try {
            $services = CleaningService::with('category')->latest()->get();

            $formatted = $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'category_id' => $service->category_id,
                    'category_name' => $service->category ? $service->category->name : null,
                    'cover_image' => $service->cover_image ? url($service->cover_image) : null,
                    'title' => $service->title,
                    'subtitle' => $service->subtitle,
                    'left_image' => $service->left_image ? url($service->left_image) : null,
                    'what_we_offer_content' => $service->what_we_offer_content,
                    'what_we_offer_content_tags' => $service->what_we_offer_content_tags,
                    'why_choose_us_content' => $service->why_choose_us_content,
                    'why_choose_us_content_tags' => $service->why_choose_us_content_tags,
                    'right_image' => $service->right_image ? url($service->right_image) : null,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formatted,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cleaning services.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'required|image|max:2048',
            'title' => 'required|string',
            'subtitle' => 'required|string',
            'left_image' => 'required|image|max:2048',
            'what_we_offer_content' => 'required|string',
            'what_we_offer_content_tags' => 'nullable|array',
            'why_choose_us_content' => 'required|string',
            'why_choose_us_content_tags' => 'nullable|array',
            'right_image' => 'required|image|max:2048',
        ]);

        try {
            $uploadsPath = 'uploads/cleaning_services';

            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $filename = time() . '_cover.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadsPath), $filename);
                $validated['cover_image'] = $uploadsPath . '/' . $filename;
            }

            if ($request->hasFile('left_image')) {
                $file = $request->file('left_image');
                $filename = time() . '_left.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadsPath), $filename);
                $validated['left_image'] = $uploadsPath . '/' . $filename;
            }

            if ($request->hasFile('right_image')) {
                $file = $request->file('right_image');
                $filename = time() . '_right.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadsPath), $filename);
                $validated['right_image'] = $uploadsPath . '/' . $filename;
            }

            $validated['what_we_offer_content_tags'] = $request->input('what_we_offer_content_tags')
                ? array_filter(array_map('trim', $request->input('what_we_offer_content_tags')))
                : null;

            $validated['why_choose_us_content_tags'] = $request->input('why_choose_us_content_tags')
                ? array_filter(array_map('trim', $request->input('why_choose_us_content_tags')))
                : null;

            $service = CleaningService::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cleaning service created successfully.',
                'data' => [
                    'id' => $service->id,
                    'category_id' => $service->category_id,
                    'category_name' => $service->category ? $service->category->name : null,
                    'cover_image' => $service->cover_image ? url($service->cover_image) : null,
                    'title' => $service->title,
                    'subtitle' => $service->subtitle,
                    'left_image' => $service->left_image ? url($service->left_image) : null,
                    'what_we_offer_content' => $service->what_we_offer_content,
                    'what_we_offer_content_tags' => $service->what_we_offer_content_tags,
                    'why_choose_us_content' => $service->why_choose_us_content,
                    'why_choose_us_content_tags' => $service->why_choose_us_content_tags,
                    'right_image' => $service->right_image ? url($service->right_image) : null,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Creation failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $service = CleaningService::with('category')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $service->id,
                    'category_id' => $service->category_id,
                    'category_name' => $service->category ? $service->category->name : null,
                    'cover_image' => $service->cover_image ? url($service->cover_image) : null,
                    'title' => $service->title,
                    'subtitle' => $service->subtitle,
                    'left_image' => $service->left_image ? url($service->left_image) : null,
                    'what_we_offer_content' => $service->what_we_offer_content,
                    'what_we_offer_content_tags' => $service->what_we_offer_content_tags,
                    'why_choose_us_content' => $service->why_choose_us_content,
                    'why_choose_us_content_tags' => $service->why_choose_us_content_tags,
                    'right_image' => $service->right_image ? url($service->right_image) : null,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cleaning service not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $service = CleaningService::findOrFail($id);

            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'cover_image' => 'nullable|image|max:2048',
                'title' => 'required|string',
                'subtitle' => 'required|string',
                'left_image' => 'nullable|image|max:2048',
                'what_we_offer_content' => 'required|string',
                'what_we_offer_content_tags' => 'nullable|array',
                'why_choose_us_content' => 'required|string',
                'why_choose_us_content_tags' => 'nullable|array',
                'right_image' => 'nullable|image|max:2048',
            ]);

            $uploadsPath = 'uploads/cleaning_services';

            // Replace cover_image if uploaded
            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $filename = time() . '_cover.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadsPath), $filename);
                $validated['cover_image'] = $uploadsPath . '/' . $filename;
            }

            // Replace left_image if uploaded
            if ($request->hasFile('left_image')) {
                $file = $request->file('left_image');
                $filename = time() . '_left.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadsPath), $filename);
                $validated['left_image'] = $uploadsPath . '/' . $filename;
            }

            // Replace right_image if uploaded
            if ($request->hasFile('right_image')) {
                $file = $request->file('right_image');
                $filename = time() . '_right.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadsPath), $filename);
                $validated['right_image'] = $uploadsPath . '/' . $filename;
            }

            // Handle tag arrays
            $validated['what_we_offer_content_tags'] = $request->input('what_we_offer_content_tags')
                ? array_filter(array_map('trim', $request->input('what_we_offer_content_tags')))
                : null;

            $validated['why_choose_us_content_tags'] = $request->input('why_choose_us_content_tags')
                ? array_filter(array_map('trim', $request->input('why_choose_us_content_tags')))
                : null;

            $service->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cleaning service updated successfully.',
                'data' => [
                    'id' => $service->id,
                    'category_id' => $service->category_id,
                    'category_name' => $service->category ? $service->category->name : null,
                    'cover_image' => $service->cover_image ? url($service->cover_image) : null,
                    'title' => $service->title,
                    'subtitle' => $service->subtitle,
                    'left_image' => $service->left_image ? url($service->left_image) : null,
                    'what_we_offer_content' => $service->what_we_offer_content,
                    'what_we_offer_content_tags' => $service->what_we_offer_content_tags,
                    'why_choose_us_content' => $service->why_choose_us_content,
                    'why_choose_us_content_tags' => $service->why_choose_us_content_tags,
                    'right_image' => $service->right_image ? url($service->right_image) : null,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $service = CleaningService::findOrFail($id);

            // Optional: Delete associated image files from storage
            $imagePaths = [
                $service->cover_image,
                $service->left_image,
                $service->right_image,
            ];

            foreach ($imagePaths as $path) {
                if ($path && file_exists(public_path($path))) {
                    unlink(public_path($path));
                }
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cleaning service deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cleaning service.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
