@extends('layouts.app')

@section('title', '住所の変更')

@section('content')
<div class="form-container">
    <h1>住所の変更</h1>
    
    <form method="POST" action="{{ route('purchase.updateAddress', $item->id) }}">
        @csrf
        
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
        </div>
        
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}">
        </div>
        
        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" id="building_name" name="building_name" value="{{ old('building_name', $user->building_name) }}">
        </div>
        
        <button type="submit" class="btn btn-primary">更新する</button>
    </form>
</div>
@endsection

