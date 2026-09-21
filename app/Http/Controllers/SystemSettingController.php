<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('system.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_phone' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_address' => 'nullable|string',
            'company_npwp' => 'nullable|string|max:255',
            'company_website' => 'nullable|url|max:255',
            'app_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $settings = $request->except(['_token', 'app_logo']);

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        if ($request->hasFile('app_logo')) {
            $path = $request->file('app_logo')->store('logos', 'public');
            Setting::set('app_logo', $path);
        }

        return redirect()->route('system.settings')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
