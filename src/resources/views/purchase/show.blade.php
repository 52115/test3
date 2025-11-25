@extends('layouts.app')

@section('title', '商品購入')

@section('content')
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
                <select name="payment_method" id="payment_method" required style="width: 100%; padding: 0.5rem; margin-top: 0.5rem;" class="@error('payment_method') error @enderror">
                    <option value="">選択してください</option>
                    <option value="コンビニ支払い" {{ old('payment_method') == 'コンビニ支払い' ? 'selected' : '' }}>コンビニ支払い</option>
                    <option value="カード支払い" {{ old('payment_method') == 'カード支払い' ? 'selected' : '' }}>カード支払い</option>
                </select>
                @error('payment_method')
                    <div class="error-message">{{ $message }}</div>
                @enderror
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

<script>
document.getElementById('payment_method').addEventListener('change', function() {
    document.getElementById('payment-method-display').textContent = this.value || '-';
});
</script>
@endsection

