<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportReportSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupportReportSettingController extends Controller
{
    public function edit()
    {
        $this->authorizeAdmin();

        $settings = SupportReportSetting::firstOrCreate([], [
            'report_code_suffix' => 'LARG-OTI-UNAP',
            'sequence_year' => now()->year,
            'last_sequence' => 0,
            'sender_prefix' => 'Tco.',
            'sender_position' => 'Soporte Informatico - OTI',
        ]);

        return view('admin.settings.support-reports', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'report_code_suffix' => 'required|string|max:80',
            'sequence_year' => 'required|integer|min:2000|max:2100',
            'last_sequence' => 'required|integer|min:0|max:9999',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_position' => 'nullable|string|max:255',
            'sender_prefix' => 'required|string|max:80',
            'sender_position' => 'required|string|max:255',
            'header_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'footer_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'remove_header_image' => 'nullable|boolean',
            'remove_footer_image' => 'nullable|boolean',
        ]);

        $settings = SupportReportSetting::firstOrCreate([]);

        if ($request->boolean('remove_header_image') && $settings->header_image_path) {
            Storage::disk('public')->delete($settings->header_image_path);
            $settings->header_image_path = null;
        }

        if ($request->boolean('remove_footer_image') && $settings->footer_image_path) {
            Storage::disk('public')->delete($settings->footer_image_path);
            $settings->footer_image_path = null;
        }

        if ($request->hasFile('header_image')) {
            if ($settings->header_image_path) {
                Storage::disk('public')->delete($settings->header_image_path);
            }

            $settings->header_image_path = $request->file('header_image')->store('report-letterhead', 'public');
        }

        if ($request->hasFile('footer_image')) {
            if ($settings->footer_image_path) {
                Storage::disk('public')->delete($settings->footer_image_path);
            }

            $settings->footer_image_path = $request->file('footer_image')->store('report-letterhead', 'public');
        }

        $settings->report_code_suffix = trim($data['report_code_suffix']);
        $settings->sequence_year = (int) $data['sequence_year'];
        $settings->last_sequence = (int) $data['last_sequence'];
        $settings->recipient_name = $data['recipient_name'] ?? null;
        $settings->recipient_position = $data['recipient_position'] ?? null;
        $settings->sender_prefix = trim($data['sender_prefix']);
        $settings->sender_position = trim($data['sender_position']);
        $settings->save();

        return redirect()
            ->route('admin.settings.support-reports.edit')
            ->with('success', 'Configuracion de informes actualizada correctamente.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdminUser(), 403);
    }
}
