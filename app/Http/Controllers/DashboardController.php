<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Tickets por status
        $ticketsByStatus = Ticket::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Tickets por prioridad
        $ticketsByPriority = Ticket::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->pluck('total', 'priority');

        // Usuarios que abrieron más tickets
        $ticketsByUser = Ticket::select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->with('user:id,name')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->user ? $item->user->name : 'Usuario eliminado',
                    'total' => $item->total,
                ];
            });

        // Cantidad de tickets abiertos hoy, semana y mes
        $today = Ticket::whereDate('created_at', Carbon::today())->count();
        $week = Ticket::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $month = Ticket::whereMonth('created_at', Carbon::now()->month)->count();

        return view('dashboard', [
            'ticketsByStatus' => $ticketsByStatus,
            'ticketsByPriority' => $ticketsByPriority,
            'ticketsByUser' => $ticketsByUser,
            'today' => $today,
            'week' => $week,
            'month' => $month,
            'breadcrumbs' => [
                'Dashboard' => route('dashboard'),
            ]
        ]);

    }
}
