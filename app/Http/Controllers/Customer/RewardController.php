<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\RewardService;

class RewardController extends Controller
{
    public function __construct(private RewardService $rewardService) {}

    public function index()
    {
        $summary = $this->rewardService->getRewardSummary(auth()->user());
        return view('customer.rewards.index', compact('summary'));
    }
}
