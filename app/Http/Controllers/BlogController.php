<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlogController extends Controller
{


    // public function index(Request $request)
    // {
    //     try {
    //         //  return gettype($request->publish) ;
    //         if ($request->publish == "all") {
    //             $blogs = Blog::latest()->paginate(8); // latest() comes BEFORE paginate()

    //             return response()->json([
    //                 'success'       => true,
    //                 'current_page'  => $blogs->currentPage(),
    //                 'per_page'      => $blogs->perPage(),
    //                 'data'          => $blogs->items(),
    //                 'total_blogs'   => $blogs->total(),
    //                 'total_pages'   => $blogs->lastPage(),
    //             ]);
    //         }

    //         $blogs = Blog::when($request->search, function ($query, $search) {
    //             return $query->where('title', 'like', "%{$search}%");
    //         })

    //             ->when($request->publish !== null, function ($query) use ($request) {
    //                 return $query->where('publish', (bool) $request->publish); // expects true/false or 1/0
    //             })

    //             ->when($request->publish !== null, function ($query) use ($request) {
    //                 return $query->where('publish', (bool) $request->publish); // expects true/false or 1/0
    //             })
    //             ->when($request->date, function ($query, $date) {
    //                 return $query->whereDate('created_at', $date); // expects Y-m-d
    //             })
    //             ->latest()
    //             ->paginate(8);



    //         return response()->json([
    //             'success'      => true,
    //             'current_page' => $blogs->currentPage(), // ✅ current page number
    //             'per_page'     => $blogs->perPage(),     // ✅ blogs per page (should be 8)
    //             'data'         => $blogs->items(),       // 👈 Only return the actual blogs, not metadata
    //             'total_blogs'        => $blogs->total(),       // Optional: total number of blogs
    //             'total_pages' => $blogs->lastPage(),
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch blogs',
    //         ], 500);
    //     }
    // }

    // public function index(Request $request)
    // {
    //     $validated = $request->validate([
    //         'publish' => 'nullable|string|in:all,true,false,0,1',
    //         'search' => 'nullable|string|max:255',
    //         'date' => 'nullable|date_format:Y-m-d',
    //         'paginate_count' => 'nullable|integer|min:1',
    //     ]);

    //     $publish = $validated['publish'] ?? null;
    //     $search = $validated['search'] ?? null;
    //     $date = $validated['date'] ?? null;
    //     $paginate_count = $validated['paginate_count'] ?? 8;

    //     try {
    //         $blogQuery = Blog::query();

    //         if ($publish === 'all') {
    //             $blogs = $blogQuery->latest()->paginate($paginate_count);

    //             return response()->json([
    //                 'success' => true,
    //                 'current_page' => $blogs->currentPage(),
    //                 'per_page' => $blogs->perPage(),
    //                 'data' => $blogs->items(),
    //                 'total_blogs' => $blogs->total(),
    //                 'total_pages' => $blogs->lastPage(),
    //                 'message' => 'Blogs retrieved successfully',
    //             ]);
    //         }

    //         if ($search) {
    //             $blogQuery->where('title', 'like', "%{$search}%");
    //         }

    //         if ($publish !== null && $publish !== 'all') {
    //             $blogQuery->where('publish', filter_var($publish, FILTER_VALIDATE_BOOLEAN));
    //         }

    //         if ($date) {
    //             $blogQuery->whereDate('created_at', $date);
    //         }

    //         $blogs = $blogQuery->latest()->paginate($paginate_count);

    //         return response()->json([
    //             'success' => true,
    //             'current_page' => $blogs->currentPage(),
    //             'per_page' => $blogs->perPage(),
    //             'data' => $blogs->items(),
    //             'total_blogs' => $blogs->total(),
    //             'total_pages' => $blogs->lastPage(),
    //             'message' => 'Blogs retrieved successfully',
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching blogs: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong, please try again later.',
    //         ], 500);
    //     }
    // }
    public function index(Request $request)
    {
        $validated = $request->validate([
            'publish' => 'nullable|string|in:all,true,false,0,1',
            'search' => 'nullable|string|max:255',
            'date' => 'nullable|date_format:Y-m-d',
            'paginate_count' => 'nullable|integer|min:1',
        ]);

        $publish = $validated['publish'] ?? null;
        $search = $validated['search'] ?? null;
        $date = $validated['date'] ?? null;
        $paginate_count = $validated['paginate_count'] ?? 8;

        try {
            $blogQuery = Blog::query();

            // Apply search filter
            if ($search) {
                $blogQuery->where('title', 'like', "%{$search}%");
            }

            // Apply publish filter only if not 'all'
            if ($publish !== null && $publish !== 'all') {
                $blogQuery->where('publish', filter_var($publish, FILTER_VALIDATE_BOOLEAN));
            }

            // Apply date filter
            if ($date) {
                $blogQuery->whereDate('created_at', $date);
            }

            // Paginate and return
            $blogs = $blogQuery->latest()->paginate($paginate_count);

            return response()->json([
                'success' => true,
                'current_page' => $blogs->currentPage(),
                'per_page' => $blogs->perPage(),
                'data' => $blogs->items(),
                'total_blogs' => $blogs->total(),
                'total_pages' => $blogs->lastPage(),
                'message' => 'Blogs retrieved successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching blogs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again later.',
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
                'title'            => 'required|string|max:255',
                'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:10240',
                'details'          => 'required|string',
                'tags'             => 'nullable|string',
                'keywords'          => 'nullable|string',
                'meta_title'       => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'publish'          => 'nullable|boolean',
            ]);

            // Generate slug directly from title without uniqueness check
            $slug = Str::slug($validated['title']);

            // Handle image upload
            $imageName = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = time() . '_blog_image.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/Blogs'), $imageName);
            }

            $blog = Blog::create([
                'title'            => $validated['title'],
                'slug'             => $slug,
                'image'            => $imageName,
                'details'          => $validated['details'],
                'tags'             => $validated['tags'] ?? null,
                'keywords'          => $validated['keywords'] ?? null,
                'meta_title'       => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'publish'          => $validated['publish'] ?? false,
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

    public function update(Request $request, $id)
    {
        // return "hi";

        try {
            $blog = Blog::findOrFail($id);

            // Validate input
            $validated = $request->validate([
                'title'            => 'sometimes|required|string|max:255',
                'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:10240',
                'details'          => 'sometimes|required|string',
                'tags'             => 'nullable|string',
                'keywords'          => 'nullable|string',
                'meta_title'       => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'publish'          => 'sometimes|boolean',
            ]);



            if (!empty($validated['title'])) {
                $validated['slug'] = Str::slug($validated['title']);
            }

            // Handle image upload if present
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = time() . '_blog_image.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/Blogs'), $imageName);
                $validated['image'] = $imageName;
            }

            // Update the blog record
            $blog->update($validated);

            // Format image URL for response
            $blog->image = $blog->image ? url('uploads/Blogs/' . $blog->image) : null;

            return response()->json([
                'success' => true,
                'message' => 'Blog updated successfully',
                'data'    => $blog,
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
    public function updates(Request $request, $id)
    {
        // return response()->json($request->all()); 
        try {
            $blog = Blog::findOrFail($id);

            // Validate input
            $validated = $request->validate([
                'title'            => 'string|max:255',
                'image'            => 'nullable',
                'details'          => 'string',
                'tags'             => 'nullable|string',
                'keyword'          => 'nullable|string',
                'meta_title'       => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'slug' => 'nullable|string|max:500',
                'publish'          => 'boolean',
            ]);

            // Regenerate slug exactly from title if title is provided
            // if (!empty($validated['title'])) {
            //     $validated['slug'] = Str::slug($validated['title']);
            // }

            // Handle image upload if present
            // if ($request->hasFile('image')) {
            //     $file = $request->file('image');
            //     $imageName = time() . '_blog_image.' . $file->getClientOriginalExtension();
            //     $file->move(public_path('uploads/Blogs'), $imageName);
            //     $validated['image'] = $imageName;
            // }

            // Update the blog record
            $blog->update($validated);

            // Attach full image URL for response
            // $blog->image = $blog->image ? url('uploads/Blogs/' . $blog->image) : null;

            return response()->json([
                'success' => true,
                'message' => 'Blog updated successfully',
                'data'    => $blog,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blog',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
