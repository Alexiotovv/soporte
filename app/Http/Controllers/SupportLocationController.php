<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\User;
use App\Models\UserLocation;
use App\Models\UserLocationLog;
use Illuminate\Http\Request;

class SupportLocationController extends Controller
{
    public function supportPanel()
    {
        abort_unless(auth()->check() && auth()->user()->isSupportUser(), 403);

        $location = UserLocation::firstOrCreate(
            ['user_id' => auth()->id()],
            ['is_sharing' => false]
        );

        return view('support.location', compact('location'));
    }

    public function setSharing(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->isSupportUser(), 403);

        $data = $request->validate([
            'is_sharing' => 'required|boolean',
        ]);

        $location = UserLocation::firstOrCreate(['user_id' => auth()->id()]);
        $location->is_sharing = (bool) $data['is_sharing'];

        if (!$location->is_sharing) {
            $location->latitude = null;
            $location->longitude = null;
            $location->last_seen_at = null;
        }

        $location->save();

        return redirect()
            ->route('support.location.panel')
            ->with('success', $location->is_sharing ? 'Ubicacion habilitada.' : 'Ubicacion deshabilitada.');
    }

    public function updateCoordinates(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->isSupportUser(), 403);

        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $location = UserLocation::firstOrCreate(['user_id' => auth()->id()]);

        if (!$location->is_sharing) {
            return response()->json([
                'message' => 'La ubicacion esta deshabilitada para este usuario.',
            ], 403);
        }

        $location->update([
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'last_seen_at' => now(),
        ]);

        UserLocationLog::create([
            'user_id' => auth()->id(),
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Ubicacion actualizada.',
        ]);
    }

    public function adminIndex()
    {
        abort_unless(auth()->check() && auth()->user()->isAdminUser(), 403);

        $offices = Office::query()->orderBy('name')->get();

        return view('admin.support-locations.index', compact('offices'));
    }

    public function adminData(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->isAdminUser(), 403);

        $officeId = $request->query('office_id');

        $query = User::query()
            ->where('is_admin', User::ROLE_SUPPORT)
            ->where('status', 1)
            ->with(['location', 'office'])
            ->orderBy('name');

        if (!empty($officeId)) {
            $query->where('office_id', $officeId);
        }

        $supportUsers = $query->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'office_name' => $user->office->name ?? 'Sin oficina',
                    'is_sharing' => (bool) optional($user->location)->is_sharing,
                    'latitude' => optional($user->location)->latitude,
                    'longitude' => optional($user->location)->longitude,
                    'last_seen_at' => $user->location?->last_seen_at?->toDateTimeString(),
                ];
            })
            ->values();

        return response()->json([
            'users' => $supportUsers,
        ]);
    }

    public function adminHistory(User $user)
    {
        abort_unless(auth()->check() && auth()->user()->isAdminUser(), 403);
        abort_unless((int) $user->is_admin === User::ROLE_SUPPORT, 404);

        $history = UserLocationLog::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(15)
            ->get(['latitude', 'longitude', 'created_at'])
            ->map(function (UserLocationLog $log) {
                return [
                    'latitude' => (float) $log->latitude,
                    'longitude' => (float) $log->longitude,
                    'created_at' => $log->created_at->toDateTimeString(),
                ];
            })
            ->values();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'history' => $history,
        ]);
    }
}
