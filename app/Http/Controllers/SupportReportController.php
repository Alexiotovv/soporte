<?php

namespace App\Http\Controllers;

use App\Models\SupportReport;
use App\Models\SupportReportSetting;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupportReportController extends Controller
{
    public function create(Ticket $ticket)
    {
        $this->authorizeSupportTicket($ticket);

        if ($ticket->supportReport) {
            return redirect()->route('support-reports.edit', $ticket->supportReport);
        }

        $settings = SupportReportSetting::firstOrCreate([], [
            'report_code_suffix' => 'LARG-OTI-UNAP',
            'sequence_year' => now()->year,
            'last_sequence' => 0,
            'sender_prefix' => 'Tco.',
            'sender_position' => 'Soporte Informatico - OTI',
        ]);

        return view('support-reports.form', [
            'mode' => 'create',
            'ticket' => $ticket,
            'report' => null,
            'settings' => $settings,
        ]);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $this->authorizeSupportTicket($ticket);

        if ($ticket->supportReport) {
            return redirect()->route('support-reports.edit', $ticket->supportReport)
                ->with('error', 'Este ticket ya tiene un informe creado.');
        }

        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $settings = SupportReportSetting::firstOrCreate([], [
            'report_code_suffix' => 'LARG-OTI-UNAP',
            'sequence_year' => now()->year,
            'last_sequence' => 0,
            'sender_prefix' => 'Tco.',
            'sender_position' => 'Soporte Informatico - OTI',
        ]);

        $reportDate = now()->toDateString();
        $reportYear = now()->year;

        if ((int) $settings->sequence_year !== (int) $reportYear) {
            $settings->sequence_year = $reportYear;
            $settings->last_sequence = 0;
        }

        $nextSequence = ((int) $settings->last_sequence) + 1;
        $settings->last_sequence = $nextSequence;
        $settings->save();

        $reportCode = sprintf('%03d-%d- %s', $nextSequence, $reportYear, trim($settings->report_code_suffix));

        $report = SupportReport::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'report_code' => $reportCode,
            'report_sequence' => $nextSequence,
            'report_year' => $reportYear,
            'report_date' => $reportDate,
            'recipient_name' => $settings->recipient_name ?: '---',
            'recipient_position' => $settings->recipient_position ?: '---',
            'sender_name' => trim($settings->sender_prefix . ' ' . auth()->user()->name),
            'sender_position' => $settings->sender_position,
            'subject' => $data['subject'],
            'content' => $data['content'],
            'header_image_path' => $settings->header_image_path,
            'footer_image_path' => $settings->footer_image_path,
        ]);

        return redirect()
            ->route('support-reports.show', $report)
            ->with('success', 'Informe generado correctamente.');
    }

    public function edit(SupportReport $supportReport)
    {
        $this->authorizeSupportReport($supportReport);

        return view('support-reports.form', [
            'mode' => 'edit',
            'ticket' => $supportReport->ticket,
            'report' => $supportReport,
            'settings' => null,
        ]);
    }

    public function update(Request $request, SupportReport $supportReport)
    {
        $this->authorizeSupportReport($supportReport);

        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $supportReport->update([
            'subject' => $data['subject'],
            'content' => $data['content'],
        ]);

        return redirect()
            ->route('support-reports.show', $supportReport)
            ->with('success', 'Informe actualizado correctamente.');
    }

    public function show(SupportReport $supportReport)
    {
        $this->authorizeViewReport($supportReport);

        return view('support-reports.show', [
            'report' => $supportReport->load(['ticket', 'author']),
        ]);
    }

    public function uploadMedia(Request $request)
    {
        abort_unless(
            auth()->check() && (auth()->user()->isSupportUser() || auth()->user()->isAdminUser()),
            403
        );

        $data = $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $path = $request->file('file')->store('support-reports/media', 'public');

        return response()->json([
            'location' => Storage::url($path),
        ]);
    }

    private function authorizeSupportTicket(Ticket $ticket): void
    {
        abort_unless(
            auth()->check() && (auth()->user()->isSupportUser() || auth()->user()->isAdminUser()),
            403
        );

        if (auth()->user()->isAdminUser()) {
            return;
        }

        abort_unless((int) $ticket->assigned_to === (int) auth()->id(), 403);
    }

    private function authorizeSupportReport(SupportReport $report): void
    {
        abort_unless(
            auth()->check() && (auth()->user()->isSupportUser() || auth()->user()->isAdminUser()),
            403
        );

        if (auth()->user()->isAdminUser()) {
            return;
        }

        abort_unless((int) $report->user_id === (int) auth()->id(), 403);
    }

    private function authorizeViewReport(SupportReport $report): void
    {
        abort_unless(auth()->check(), 403);

        if (auth()->user()->isAdminUser()) {
            return;
        }

        if ((int) $report->user_id === (int) auth()->id()) {
            return;
        }

        if ((int) $report->ticket->user_id === (int) auth()->id()) {
            return;
        }

        abort(403);
    }
}
