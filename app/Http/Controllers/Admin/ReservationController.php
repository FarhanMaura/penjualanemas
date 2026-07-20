<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'product.category'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$s}%")
                                                   ->orWhere('email', 'like', "%{$s}%"));
        }

        $reservations = $query->paginate(20)->withQueryString();

        $stats = [
            'today'     => Reservation::whereDate('created_at', today())->count(),
            'pending'   => Reservation::where('status', 'pending')->count(),
            'confirmed' => Reservation::where('status', 'confirmed')->count(),
            'cancelled' => Reservation::whereIn('status', ['cancelled','expired'])->count(),
        ];

        return view('admin.reservations.index', compact('reservations', 'stats'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['user.profile', 'product.category', 'transaction']);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function confirm(Reservation $reservation)
    {
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Reservasi tidak dalam status pending.');
        }

        $reservation->update([
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('success', "Reservasi #{$reservation->reservation_code} dikonfirmasi.");
    }

    public function reject(Request $request, Reservation $reservation)
    {
        $request->validate(['admin_notes' => ['nullable','string','max:500']]);

        if (! in_array($reservation->status, ['pending','confirmed'])) {
            return back()->with('error', 'Reservasi tidak dapat ditolak.');
        }

        $reservation->update([
            'status'      => 'cancelled',
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', "Reservasi #{$reservation->reservation_code} ditolak.");
    }
}
