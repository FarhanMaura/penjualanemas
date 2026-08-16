<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldPrice;
use App\Services\GoldPriceService;
use Illuminate\Http\Request;

class GoldPriceController extends Controller
{
    public function __construct(private GoldPriceService $service) {}

    public function index()
    {
        $prices  = GoldPrice::latest('price_date')->paginate(30);
        $today   = $this->service->getTodayPrice();
        return view('admin.gold-prices.index', compact('prices', 'today'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'price_date'          => ['required','date'],
            'buy_price_per_gram'  => ['required','numeric','min:100000'],
            'sell_price_per_gram' => ['required','numeric','min:100000'],
            'source'              => ['nullable','string','max:100'],
            'notes'               => ['nullable','string'],
        ]);

        GoldPrice::updateOrCreate(
            ['price_date' => \Carbon\Carbon::parse($request->price_date)->toDateString()],
            [
                'buy_price_per_gram'  => $request->buy_price_per_gram,
                'sell_price_per_gram' => $request->sell_price_per_gram,
                'source'              => $request->source ?? 'Manual',
                'recorded_by'         => auth()->id(),
                'notes'               => $request->notes,
            ]
        );

        $this->service->clearCache();

        return redirect()->route('admin.gold-prices.index')
            ->with('success', 'Harga emas berhasil disimpan.');
    }

    /**
     * Fetch harga dari API eksternal dan simpan ke DB.
     */
    public function fetchExternal()
    {
        $data = $this->service->fetchFromExternal();

        if (! $data) {
            return back()->with('error', 'Gagal mengambil harga dari sumber eksternal.');
        }

        GoldPrice::updateOrCreate(
            ['price_date' => today()->toDateString()],
            [
                'buy_price_per_gram'  => $data['buy'],
                'sell_price_per_gram' => $data['sell'],
                'source'              => $data['source'],
                'recorded_by'         => auth()->id(),
                'notes'               => 'Auto-fetch dari API.',
            ]
        );

        $this->service->clearCache();

        return back()->with('success', "Harga berhasil diambil dari {$data['source']}.");
    }
}
