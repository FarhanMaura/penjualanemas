<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\GoldPrice;
use App\Services\GoldPriceService;
use Illuminate\Http\Request;

class GoldPriceController extends Controller
{
    public function __construct(private GoldPriceService $goldPriceService) {}

    public function index()
    {
        $todayPrice = $this->goldPriceService->getTodayPrice();
        $history = GoldPrice::orderBy('price_date', 'desc')
            ->paginate(15);

        return view('customer.gold-prices.index', compact('todayPrice', 'history'));
    }
}
