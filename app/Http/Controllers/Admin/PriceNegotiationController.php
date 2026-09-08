<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceNegotiation;
use Illuminate\Http\Request;

class PriceNegotiationController extends Controller
{
    public function index(Request $request)
    {
        $query = PriceNegotiation::with(['user.profile', 'product.category'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('negotiation_code', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($userQ) => $userQ->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
                  ->orWhereHas('product', fn($prodQ) => $prodQ->where('name', 'like', "%{$s}%"));
            });
        }

        $negotiations = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => PriceNegotiation::count(),
            'pending'  => PriceNegotiation::where('status', 'pending')->count(),
            'approved' => PriceNegotiation::where('status', 'approved')->count(),
            'rejected' => PriceNegotiation::where('status', 'rejected')->count(),
        ];

        return view('admin.negotiations.index', compact('negotiations', 'stats'));
    }

    public function show(PriceNegotiation $negotiation)
    {
        $negotiation->load(['user.profile', 'product.category', 'respondedByAdmin']);

        return view('admin.negotiations.show', compact('negotiation'));
    }

    public function approve(Request $request, PriceNegotiation $negotiation)
    {
        if ($negotiation->status !== 'pending') {
            return back()->with('error', 'Pengajuan tawar harga tidak dalam status pending.');
        }

        $request->validate([
            'agreed_price' => ['required', 'numeric', 'min:10000'],
            'admin_notes'  => ['nullable', 'string', 'max:500'],
        ]);

        $negotiation->update([
            'status'       => 'approved',
            'agreed_price' => $request->agreed_price,
            'admin_notes'  => $request->admin_notes,
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        // Notifikasi Customer
        \App\Models\Notification::create([
            'user_id' => $negotiation->user_id,
            'type'    => 'negotiation.approved',
            'title'   => "Tawar Harga Disetujui (#{$negotiation->negotiation_code})",
            'message' => "Penawaran harga Anda untuk " . ($negotiation->product->name ?? 'produk') . " telah disetujui seharga Rp " . number_format($request->agreed_price, 0, ',', '.') . ".",
            'data'    => ['negotiation_id' => $negotiation->id],
        ]);

        return back()->with('success', "Pengajuan tawar harga #{$negotiation->negotiation_code} berhasil disetujui pada harga Rp " . number_format($request->agreed_price, 0, ',', '.') . ".");
    }

    public function reject(Request $request, PriceNegotiation $negotiation)
    {
        if ($negotiation->status !== 'pending') {
            return back()->with('error', 'Pengajuan tawar harga tidak dalam status pending.');
        }

        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $negotiation->update([
            'status'       => 'rejected',
            'admin_notes'  => $request->admin_notes,
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        // Notifikasi Customer
        \App\Models\Notification::create([
            'user_id' => $negotiation->user_id,
            'type'    => 'negotiation.rejected',
            'title'   => "Tawar Harga Ditolak (#{$negotiation->negotiation_code})",
            'message' => "Penawaran harga Anda untuk " . ($negotiation->product->name ?? 'produk') . " telah ditolak.",
            'data'    => ['negotiation_id' => $negotiation->id],
        ]);

        return back()->with('success', "Pengajuan tawar harga #{$negotiation->negotiation_code} telah ditolak.");
    }
}
