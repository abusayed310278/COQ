<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SettingController extends Controller
{
    public function show()
    {
        try {
            $setting = Setting::first();

            if ($setting) {
                $setting->icon = $setting->icon ? url('uploads/Settings/' . $setting->icon) : null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings retrieved successfully.',
                'data'    => $setting
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching settings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings.'
            ], 500);
        }
    }

    public function storeOrUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'icon'          => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:10240',
                'system_name'   => 'nullable|string|max:255',
                'system_title'  => 'nullable|string',
                'system_address'=> 'nullable|string',
                'email'         => 'nullable|email|max:255',
                'phone'         => 'nullable|string|max:20',
                'opening_hour'  => 'nullable|string|max:100',
                'description'   => 'nullable|string',
            ]);

            $setting = Setting::first();
            $icon = $setting->icon ?? null;

            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                $icon = time() . '_setting_icon.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/Settings'), $icon);
            }

            $data = [
                'system_name'   => $validated['system_name'] ?? null,
                'system_title'  => $validated['system_title'] ?? null,
                'system_address'=> $validated['system_address'] ?? null,
                'email'         => $validated['email'] ?? null,
                'phone'         => $validated['phone'] ?? null,
                'opening_hour'  => $validated['opening_hour'] ?? null,
                'description'   => $validated['description'] ?? null,
                'icon'          => $icon,
            ];

            if ($setting) {
                $setting->update($data);
            } else {
                $setting = Setting::create($data);
            }

            $setting->icon = $setting->icon ? url('uploads/Settings/' . $setting->icon) : null;

            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully.',
                'data'    => $setting
            ]);
        } catch (Exception $e) {
            Log::error('Error saving settings: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
