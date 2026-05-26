<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingSettingController extends Controller
{
    public function edit()
    {
        $this->authorizeAdmin();

        $brandingSetting = BrandingSetting::firstOrCreate([]);

        return view('admin.settings.branding', compact('brandingSetting'));
    }

    public function update(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'navbar_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'login_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'remove_navbar_logo' => 'nullable|boolean',
            'remove_login_image' => 'nullable|boolean',
        ]);

        $brandingSetting = BrandingSetting::firstOrCreate([]);

        if ($request->boolean('remove_navbar_logo') && $brandingSetting->navbar_logo_path) {
            Storage::disk('public')->delete($brandingSetting->navbar_logo_path);
            $brandingSetting->navbar_logo_path = null;
        }

        if ($request->boolean('remove_login_image') && $brandingSetting->login_image_path) {
            Storage::disk('public')->delete($brandingSetting->login_image_path);
            $brandingSetting->login_image_path = null;
        }

        if ($request->hasFile('navbar_logo')) {
            if ($brandingSetting->navbar_logo_path) {
                Storage::disk('public')->delete($brandingSetting->navbar_logo_path);
            }

            $brandingSetting->navbar_logo_path = $request->file('navbar_logo')->store('branding', 'public');
        }

        if ($request->hasFile('login_image')) {
            if ($brandingSetting->login_image_path) {
                Storage::disk('public')->delete($brandingSetting->login_image_path);
            }

            $brandingSetting->login_image_path = $request->file('login_image')->store('branding', 'public');
        }

        $brandingSetting->save();

        return redirect()
            ->route('admin.settings.branding.edit')
            ->with('success', 'Configuracion visual actualizada correctamente.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && (int) auth()->user()->is_admin === 1, 403);
    }
}
