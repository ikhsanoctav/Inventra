<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'app_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'login_bg' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'remove_login_bg' => 'nullable|boolean',
            'remove_app_logo' => 'nullable|boolean',
            'currency_symbol' => 'nullable|string|max:10',
            'app_timezone' => 'nullable|string|max:100',
            'date_format' => 'nullable|string|max:50',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'min_stock_alert_threshold' => 'nullable|integer|min:0',
            'notification_email' => 'nullable|email|max:255',
        ]);

        $settings = $request->except(['_token', 'app_logo', 'login_bg', 'remove_login_bg', 'remove_app_logo']);

        foreach ($settings as $key => $value) {
            Setting::set($key, (string) $value);
        }

        if ($request->hasFile('app_logo')) {
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('app_logo')->store('logos', 'public');
            Setting::set('app_logo', $path);
        } elseif ($request->boolean('remove_app_logo')) {
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('app_logo', null);
        }

        if ($request->hasFile('login_bg')) {
            $oldBg = Setting::get('login_bg');
            if ($oldBg && Storage::disk('public')->exists($oldBg)) {
                Storage::disk('public')->delete($oldBg);
            }
            $path = $request->file('login_bg')->store('backgrounds', 'public');
            Setting::set('login_bg', $path);
        } elseif ($request->boolean('remove_login_bg')) {
            $oldBg = Setting::get('login_bg');
            if ($oldBg && Storage::disk('public')->exists($oldBg)) {
                Storage::disk('public')->delete($oldBg);
            }
            Setting::set('login_bg', null);
        }

        AuditLog::record('SYSTEM_SETTINGS_UPDATED', 'Setting', null, null, [
            'company_name' => $request->company_name,
            'updated_by' => auth()->user()->name,
        ]);

        return redirect()->route('system.settings')->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
