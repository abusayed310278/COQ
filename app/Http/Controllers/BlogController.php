<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    // GET /api/blogs
    public function index()
    {
        try {
            $blogs = Blog::latest()->paginate(10);

            // Map over each blog to convert image path to full URL
            $blogs->getCollection()->transform(function ($blog) {
                $blog->image = $blog->image ? url('uploads/Blogs/' . $blog->image) : null;
                return $blog;
            });

            return response()->json(['success' => true, 'data' => $blogs]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blogs',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    // GET /api/blogs/{id}
    public function show($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            return response()->json(['success' => true, 'data' => $blog]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Blog not found', 'error' => $e->getMessage()], 404);
        }
    }




    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'    => 'required|string|max:255',
                'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:10240',
                'details'  => 'required|string',
                'tags'     => 'nullable|string',
                'keyword'  => 'nullable|string',
                'publish'  => 'nullable|boolean',
            ]);

            // Generate unique slug from title
            $slug = Str::slug($validated['title']);
            $count = Blog::where('slug', $slug)->count();


            // Handle image upload
            $imageName = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = time() . '_blog_image.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/Blogs'), $imageName);
            }

            $blog = Blog::create([
                'title'    => $validated['title'],
                'slug'     => $slug,
                'image'    => $imageName,
                'details'  => $validated['details'],
                'tags'     => $validated['tags'] ?? null,
                'keyword'  => $validated['keyword'] ?? null,
                'publish'  => $request->has('publish') ? (bool) $validated['publish'] : false,
            ]);

            // Attach full image URL for response
            $blog->image = $blog->image ? url('uploads/Blogs/' . $blog->image) : null;

            return response()->json([
                'success' => true,
                'message' => 'Blog created successfully',
                'data'    => $blog
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blog',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    // PUT /api/blogs/{id}
    // public function update(Request $request, $id)
    // {
    //     try {
    //         $blog = Blog::findOrFail($id);

    //         $validated = $request->validate([
    //             'title'    => 'sometimes|required|string',
    //             'slug'     => 'sometimes|required|string|max:255|unique:blogs,slug,' . $id,
    //             'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:10240',
    //             'details'  => 'sometimes|required|string',
    //             'tags'     => 'nullable|string',
    //             'keyword'  => 'nullable|string',
    //             'publish'  => 'sometimes|boolean',
    //         ]);

    //         if ($request->hasFile('image')) {
    //             $file = $request->file('image');
    //             $imageName = time() . '_blog_image.' . $file->getClientOriginalExtension();
    //             $file->move(public_path('uploads/Blogs'), $imageName);
    //             $validated['image'] = $imageName;
    //         }

    //         $blog->update($validated);

    //         // Convert image filename to full URL
    //         $blog->image = $blog->image ? url('uploads/Blogs/' . $blog->image) : null;

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Blog updated successfully',
    //             'data'    => $blog
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to update blog',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }



    // PUT /api/blogs/{id}
    

    public function update(Request $request, $id)
{
    try {
        $blog = Blog::findOrFail($id);

        // Validate request input
        $validated = $request->validate([
            'title'    => 'sometimes|required|string|max:255',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:10240',
            'details'  => 'sometimes|required|string',
            'tags'     => 'nullable|string',
            'keyword'  => 'nullable|string',
            'publish'  => 'sometimes|boolean',
        ]);

        // If title is present, regenerate slug
        if (!empty($validated['title'])) {
            $slug = Str::slug($validated['title']);

            // Check if slug already exists for a different blog
            $exists = Blog::where('slug', $slug)->where('id', '!=', $id)->exists();
            if ($exists) {
                $slug .= '-' . Str::random(5); // Add suffix if not unique
            }

            $validated['slug'] = $slug;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_blog_image.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/Blogs'), $imageName);
            $validated['image'] = $imageName;
        }

        // Update blog
        $updated = $blog->update($validated);

        // Attach full image URL for response
        $blog->image = $blog->image ? url('uploads/Blogs/' . $blog->image) : null;

        return response()->json([
            'success' => true,
            'message' => $updated ? 'Blog updated successfully' : 'No changes made',
            'data'    => $blog
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update blog',
            'error'   => $e->getMessage()
        ], 500);
    }
}









    // DELETE /api/blogs/{id}
    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->delete();

            return response()->json(['success' => true, 'message' => 'Blog deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete blog', 'error' => $e->getMessage()], 500);
        }
    }

    public function getBlogData()
    {
        try {
            $blogs = Blog::all();

            // Append full image URLs
            foreach ($blogs as $blog) {
                $blog->image = $blog->image ? url('uploads/Blogs/' . $blog->image) : null;
            }

            return response()->json(['success' => true, 'data' => $blogs]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blog data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
