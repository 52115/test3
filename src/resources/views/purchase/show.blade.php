@extends('layouts.app')

@section('title', '商品購入')

@section('content')
<h1>商品購入</h1>
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <div style="margin-bottom: 2rem;">
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width: 200px; height: 200px; object-fit: cover;">
            <h2>{{ $item->name }}</h2>
            <p>¥{{ number_format($item->price) }}</p>
        </div>
        <div style="border-top: 1px solid #ddd; padding-top: 1rem; margin-bottom: 2rem;">
            <h3>支払い方法</h3>
            <form method="POST" action="{{ route('purchase.store', $item->id) }}" id="purchase-form" novalidate>
                @csrf
                <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                <select name="payment_method" id="payment_method" required style="width: 100%; padding: 0.5rem; margin-top: 0.5rem;" class="@error('payment_method') error @enderror">
                    <option value="">選択してください</option>
                    <option value="コンビニ支払い" {{ old('payment_method') == 'コンビニ支払い' ? 'selected' : '' }}>コンビニ支払い</option>
                    <option value="カード支払い" {{ old('payment_method') == 'カード支払い' ? 'selected' : '' }}>カード支払い</option>
                </select>
                @error('payment_method')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <!-- Stripe Elements カード入力欄 -->
                <div id="card-element-container" style="display: none; margin-top: 1rem;">
                    <div id="card-element" style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; background: white;"></div>
                    <div id="card-errors" role="alert" style="color: #dc3545; margin-top: 0.5rem; font-size: 0.875rem;"></div>
                </div>
            </form>
        </div>
        <div style="border-top: 1px solid #ddd; padding-top: 1rem;">
            <h3>配送先</h3>
            <p>〒{{ $user->postal_code ?? 'XXX-YYYY' }} {{ $user->address ?? 'ここには住所と建物が入ります' }} {{ $user->building_name ?? '' }}</p>
            <a href="{{ route('purchase.address', $item->id) }}" style="color: #0066cc;">変更する</a>
            <input type="hidden" name="postal_code" value="{{ $user->postal_code }}" form="purchase-form">
            <input type="hidden" name="address" value="{{ $user->address }}" form="purchase-form">
            <input type="hidden" name="building_name" value="{{ $user->building_name }}" form="purchase-form">
        </div>
    </div>
    <div>
        <div style="border: 1px solid #ddd; padding: 1.5rem; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                <span>商品代金</span>
                <span>¥{{ number_format($item->price) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; border-top: 1px solid #ddd; padding-top: 1rem;">
                <span>支払い方法</span>
                <span id="payment-method-display">-</span>
            </div>
        </div>
        <button type="submit" form="purchase-form" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">購入する</button>
    </div>
</div>
@if(config('services.stripe.key'))
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ config('services.stripe.key') }}');
let elements;
let cardElement;
let paymentIntentClientSecret = null;
let paymentIntentId = null;

document.getElementById('payment_method').addEventListener('change', function() {
    const paymentMethod = this.value;
    document.getElementById('payment-method-display').textContent = paymentMethod || '-';
    const cardElementContainer = document.getElementById('card-element-container');
    if (paymentMethod === 'カード支払い') {
        // PaymentIntentを作成
        fetch('{{ route('purchase.createPaymentIntent', $item->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            paymentIntentClientSecret = data.clientSecret;
            paymentIntentId = data.paymentIntentId;
            document.getElementById('payment_intent_id').value = paymentIntentId;
            // Stripe Elementsを初期化
            if (!elements) {
                elements = stripe.elements();
                cardElement = elements.create('card', {
                    style: {
                        base: {
                            fontSize: '16px',
                            color: '#424770',
                            '::placeholder': {
                                color: '#aab7c4',
                            },
                        },
                        invalid: {
                            color: '#9e2146',
                        },
                    },
                });
                cardElement.mount('#card-element');
                cardElement.on('change', function(event) {
                    const displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
            }
            cardElementContainer.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('決済の初期化に失敗しました');
        });
    } else {
        cardElementContainer.style.display = 'none';
        paymentIntentClientSecret = null;
        paymentIntentId = null;
        document.getElementById('payment_intent_id').value = '';
    }
});

// フォーム送信時の処理
document.getElementById('purchase-form').addEventListener('submit', async function(e) {
    const paymentMethod = document.getElementById('payment_method').value;
    if (paymentMethod === 'カード支払い') {
        e.preventDefault();
        if (!paymentIntentClientSecret) {
            alert('決済情報が準備できていません。もう一度お試しください。');
            return;
        }
        const submitButton = document.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = '処理中...';
        try {
            // PaymentIntentを確認
            const {error, paymentIntent} = await stripe.confirmCardPayment(paymentIntentClientSecret, {
                payment_method: {
                    card: cardElement,
                }
            });
            if (error) {
                submitButton.disabled = false;
                submitButton.textContent = '購入する';
                document.getElementById('card-errors').textContent = error.message;
                return;
            }
            if (paymentIntent.status === 'succeeded') {
                document.getElementById('purchase-form').submit();
            } else {
                submitButton.disabled = false;
                submitButton.textContent = '購入する';
                alert('決済が完了していません。ステータス: ' + paymentIntent.status);
            }
        } catch (err) {
            submitButton.disabled = false;
            submitButton.textContent = '購入する';
            console.error('Error:', err);
            alert('決済処理中にエラーが発生しました');
        }
    }
});
</script>
@else
<script>
// Stripeが設定されていない場合
document.getElementById('payment_method').addEventListener('change', function() {
    const paymentMethod = this.value;
    document.getElementById('payment-method-display').textContent = paymentMethod || '-';
    if (paymentMethod === 'カード支払い') {
        alert('Stripeが設定されていません。カード支払いは利用できません。');
        this.value = '';
        document.getElementById('payment-method-display').textContent = '-';
    }
});
</script>
@endif
@endsection

