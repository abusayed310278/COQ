<?php

namespace App\Http\Controllers;

use App\Models\PackageOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

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
            ]);

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

    public function index()
    {
        $emails = PackageOrder::latest()->pluck('email');

        return response()->json([
            'success' => true,
            'emails'  => $emails
        ]);
    }

    // public function allShow()
    // {

    //     if (!Auth::check()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Login first',
    //         ], 401);
    //     }

    //     $orders = PackageOrder::latest()
    //         ->take(10)
    //         ->get(['package_name', 'email', 'company_name', 'location', 'created_at']);

    //     $orders->transform(function ($order) {
    //         return [
    //             'package_name' => $order->package_name,
    //             'company_name' => $order->company_name,
    //             'email'        => $order->email,
    //             'location'     => $order->location,
    //             'created_at'   => $order->created_at->format('d/m/Y'),
    //         ];
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'orders' => $orders,
    //     ]);
    // }



    // public function allShow(Request $request)
    // {
    //     try {
    //         // Check if token is provided in the Authorization header
    //         $token = $request->bearerToken();

    //         if (!$token) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Authorization token not found. Please login first.',
    //             ], 401);
    //         }

    //         // Try to authenticate user from token
    //         $user = JWTAuth::setToken($token)->authenticate();

    //         if (!$user) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User not found. Please login again.',
    //             ], 401);
    //         }
    //     } catch (TokenExpiredException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Token has expired. Please login again.',
    //         ], 401);
    //     } catch (TokenInvalidException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Token is invalid. Please login again.',
    //         ], 401);
    //     } catch (JWTException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Token parsing failed. Please provide a valid token.',
    //         ], 401);
    //     } catch (\Exception $e) {
    //         Log::error('JWT Auth error in allShow(): ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An unexpected error occurred. Please try again.',
    //         ], 500);
    //     }

    //     // Retrieve the latest 10 package orders
    //     $orders = PackageOrder::latest()
    //         ->take(10)
    //         ->get(['package_name', 'email', 'company_name', 'location', 'created_at']);

    //     // Format the created_at date for each order
    //     $orders->transform(function ($order) {
    //         return [
    //             'package_name' => $order->package_name,
    //             'company_name' => $order->company_name,
    //             'email'        => $order->email,
    //             'location'     => $order->location,
    //             'created_at'   => $order->created_at->format('d/m/Y'),
    //         ];
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'orders'  => $orders,
    //     ]);
    // }

    public function allShow(Request $request)
    {
        try {
            // Check and authenticate token
            $token = $request->bearerToken();

            if (!$token || !JWTAuth::setToken($token)->authenticate()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login first.',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first.',
            ], 401);
        }

        // Fetch latest 10 package orders
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
            'orders'  => $orders,
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
}
