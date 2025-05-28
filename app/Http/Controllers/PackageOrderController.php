<?php

namespace App\Http\Controllers;

use App\Models\PackageOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;

class PackageOrderController extends Controller
{
    public function store(Request $request, $slug)
    {
        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'email'        => 'required|email|max:255',
                // 'phone'        => 'required|string|max:20|phone:*',
                'phone'        => 'required|string|max:20',
                'postal_code'  => 'required|string|max:20',
                'address'      => 'required|string',
                'location'     => 'required|string|max:255',
                'status'       => 'boolean|nullable',

            ]);

            // Default status to false if not provided
            $validated['status'] = $validated['status'] ?? false;

            // The slug acts as package_name
            $data = array_merge($validated, ['package_name' => $slug]);

            $order = PackageOrder::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Package order submitted successfully.',
                'data'    => $order
            ]);
        } catch (Exception $e) {
            Log::error('Package order submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit package order.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    // public function index()
    // {
    //     $data = PackageOrder::select('id','package_name', 'email','status', 'created_at')
    //         // ->where('status', false) // optional: uncomment if needed
    //         ->orderBy('created_at', 'desc') // ensures latest by created_at
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data'    => $data
    //     ]);
    // }

    public function index(Request $request)
    {
        $query = PackageOrder::select('id', 'package_name', 'email', 'status', 'created_at')
            ->orderBy('created_at', 'desc');

        // Search by package_name
        if ($request->has('package_name') && !empty($request->package_name)) {
            $query->where('package_name', 'like', '%' . $request->package_name . '%');
        }

        // Search by date (assuming format: YYYY-MM-DD)
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('created_at', $request->date);
        }

        // Generic search across multiple fields
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('package_name', 'like', "%$searchTerm%")
                    ->orWhere('email', 'like', "%$searchTerm%");
            });
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }




    public function allShow()
    {

        // return 'ok';

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to view orders.',
            ], 401);
        }

        $orders = PackageOrder::latest()
            ->take(10)
            ->get(['package_name', 'email', 'company_name', 'location', 'created_at']);

        $orders->transform(function ($order) {
            return [
                'package_name' => $order->package_name,
                'company_name' => $order->company_name,
                'email'        => $order->email,
                'location'     => $order->location,
                'created_at'   => $order->created_at->format('d/m/Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }




    public function silverAllShow()
    {
        $silverOrders = PackageOrder::where('package_name', 'silver')
            ->latest()
            ->take(10)
            ->get(['package_name', 'email', 'company_name', 'location', 'created_at']);

        $silverOrders->transform(function ($order) {
            return [
                'package_name' => $order->package_name,
                'email'        => $order->email,
                'company_name' => $order->company_name,
                'location'     => $order->location,
                'created_at'   => $order->created_at->format('d/m/Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'silverOrders' => $silverOrders,
        ]);
    }

    public function bronzeAllShow()
    {
        $bronzeOrders = PackageOrder::where('package_name', 'bronze')
            ->latest()
            ->take(10)
            ->get(['package_name', 'email', 'company_name', 'location', 'created_at']);

        $bronzeOrders->transform(function ($order) {
            return [
                'package_name' => $order->package_name,
                'email'        => $order->email,
                'company_name' => $order->company_name,
                'location'     => $order->location,
                'created_at'   => $order->created_at->format('d/m/Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'bronzeOrders' => $bronzeOrders,
        ]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'     => 'required|exists:package_orders,id',
                'status' => 'required|boolean',
            ]);

            $order = PackageOrder::findOrFail($validated['id']);
            $order->status = $validated['status'];
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.',
                'data'    => $order
            ]);
        } catch (Exception $e) {
            Log::error('Order status update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
