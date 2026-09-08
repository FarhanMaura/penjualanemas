<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        // Auto-expire pending reservations past expired_at
        Reservation::where('status', 'pending')
            ->where('expired_at', '<', now())
            ->update(['status' => 'expired']);

        $query = Reservation::with(['user', 'product.category', 'priceNegotiation', 'paymentMethodDetail'])
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
        $reservation->load(['user.profile', 'product.category', 'priceNegotiation', 'transaction', 'paymentMethodDetail']);

        return view('admin.reservations.show', compact('reservation'));
    }

    public function confirm(Reservation $reservation, Request $request)
    {
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Reservasi tidak dalam status pending.');
        }

        $reservation->update([
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        // Notifikasi ke Customer
        \App\Models\Notification::create([
            'user_id' => $reservation->user_id,
            'type'    => 'reservation.confirmed',
            'title'   => "Reservasi Dikonfirmasi (#{$reservation->reservation_code})",
            'message' => "Reservasi #{$reservation->reservation_code} Anda telah dikonfirmasi oleh toko. Silakan datang ke toko.",
            'data'    => ['reservation_id' => $reservation->id],
        ]);

        $isPickupReservation = str_starts_with($reservation->reservation_code, 'RSV-PKP-');

        if (! $isPickupReservation && ($request->boolean('process_transaction') || $request->has('process_transaction'))) {
            return redirect()->route('admin.transactions.create', ['reservation_id' => $reservation->id])
                ->with('success', "Reservasi #{$reservation->reservation_code} dikonfirmasi. Form transaksi telah otomatis terisi.");
        }

        return back()->with('success', "Reservasi #{$reservation->reservation_code} dikonfirmasi.");
    }

    public function complete(Reservation $reservation)
    {
        if (! in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Reservasi tidak dapat diselesaikan.');
        }

        $reservation->update([
            'status'       => 'completed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => $reservation->confirmed_at ?? now(),
        ]);

        $isBuyback = $reservation->type === 'buyback';

        // Notifikasi ke Customer
        \App\Models\Notification::create([
            'user_id' => $reservation->user_id,
            'type'    => 'reservation.completed',
            'title'   => $isBuyback ? "Buyback Selesai (#{$reservation->reservation_code})" : "Serah Terima Emas Selesai (#{$reservation->reservation_code})",
            'message' => $isBuyback
                ? "Transaksi buyback emas di toko untuk reservasi #{$reservation->reservation_code} telah selesai dilakukan. Terima kasih atas kunjungan Anda di Toko Emas Sinar Baru II!"
                : "Serah terima perhiasan emas fisik untuk reservasi #{$reservation->reservation_code} telah selesai dilakukan. Terima kasih telah bertransaksi di Toko Emas Sinar Baru II!",
            'data'    => ['reservation_id' => $reservation->id],
        ]);

        return back()->with('success', $isBuyback
            ? "Reservasi buyback #{$reservation->reservation_code} telah ditandai Selesai (Kunjungan & Transaksi di Toko Selesai)."
            : "Reservasi #{$reservation->reservation_code} telah ditandai Selesai (Serah Terima Emas Fisik Selesai).");
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

        // Notifikasi ke Customer
        \App\Models\Notification::create([
            'user_id' => $reservation->user_id,
            'type'    => 'reservation.rejected',
            'title'   => "Reservasi Dibatalkan (#{$reservation->reservation_code})",
            'message' => "Reservasi #{$reservation->reservation_code} Anda telah dibatalkan oleh toko.",
            'data'    => ['reservation_id' => $reservation->id],
        ]);

        return back()->with('success', "Reservasi #{$reservation->reservation_code} ditolak.");
    }

    public function destroy(Reservation $reservation)
    {
        // Putuskan relasi transaksi jika ada
        if ($reservation->transaction_id) {
            $reservation->update(['transaction_id' => null]);
        }

        // Putuskan relasi di transaksi yang merujuk reservasi ini
        \App\Models\Transaction::where('reservation_id', $reservation->id)->update(['reservation_id' => null]);

        // Putuskan relasi di cicilan jika ada
        \App\Models\InstallmentPlan::where('pickup_reservation_id', $reservation->id)->update(['pickup_reservation_id' => null]);

        // Hapus notifikasi terkait reservasi ini
        \App\Models\Notification::whereJsonContains('data->reservation_id', $reservation->id)->delete();

        $code = $reservation->reservation_code;
        $reservation->delete();

        return redirect()->route('admin.reservations.index')
            ->with('success', "Reservasi #{$code} berhasil dihapus permanen.");
    }
}
