<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class ProfileController extends Controller
{
    public function index()
    {

        $user = Auth::user();

        $orders = Order::with(['car.model.brand', 'car.bodyType', 'creditApplication'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile', compact('user', 'orders'));
    }
}
