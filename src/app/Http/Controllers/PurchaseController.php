<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PurchaseController extends Controller
{
    public function __construct()
    {
        if (config('services.stripe.secret')) {
            Stripe::setApiKey(config('services.stripe.secret'));
        }
    }

    public function show($itemId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = Item::findOrFail($itemId);
        $user = Auth::user();

        return view('purchase.show', compact('item', 'user'));
    }

    public function address($itemId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = Item::findOrFail($itemId);
        $user = Auth::user();

        return view('purchase.address', compact('item', 'user'));
    }

    public function updateAddress(Request $request, $itemId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $user->update([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building_name' => $request->building_name,
        ]);

        return redirect()->route('purchase.show', $itemId)->with('success', '住所を更新しました');
    }

    public function store(PurchaseRequest $request, $itemId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = Item::findOrFail($itemId);

        if ($item->buyer_id) {
            return back()->with('error', 'この商品は既に購入されています');
        }

        // Stripe決済処理（簡易版）
        $paymentIntentId = null;
        if (config('services.stripe.secret')) {
            try {
                $paymentIntent = PaymentIntent::create([
                    'amount' => $item->price,
                    'currency' => 'jpy',
                    'payment_method_types' => ['card'],
                ]);
                $paymentIntentId = $paymentIntent->id;
            } catch (\Exception $e) {
                return back()->with('error', '決済処理に失敗しました');
            }
        }

        // 購入情報の保存
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $itemId,
            'payment_method' => $request->payment_method,
            'postal_code' => $request->postal_code ?? Auth::user()->postal_code,
            'address' => $request->address ?? Auth::user()->address,
            'building_name' => $request->building_name ?? Auth::user()->building_name,
            'stripe_payment_intent_id' => $paymentIntentId,
        ]);

        // 商品の購入者を更新
        $item->update(['buyer_id' => Auth::id()]);

        return redirect()->route('items.index')->with('success', '購入が完了しました');
    }
}
