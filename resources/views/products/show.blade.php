@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product Details</h1>
    <div class="card">
        <div class="card-header">
            <h2>{{ $product->title }}</h2>
        </div>
        <div class="card-body">
            <p><strong>Description:</strong> {{ $product->description }}</p>
            <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
        </div>
    </div>
</div>
@endsection
