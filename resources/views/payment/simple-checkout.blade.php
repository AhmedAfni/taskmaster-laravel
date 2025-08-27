@extends('layouts.app')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Checkout</h4>
                    </div>
                    <div class="card-body">
                        <!-- Product Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded"
                                        alt="Product Image">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5>Order Summary</h5>
                                <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
                                <p><strong>Size:</strong> {{ $product->size }}</p>
                            </div>
                        </div>

                        <!-- Cybersource Form -->
                        <form id="payment_form" action="{{ $action_url }}" method="post" class="mt-4">
                            @csrf
                            @foreach ($params as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Proceed to Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('scripts')
    <script>
        // Ensure the CSRF token is set in all Ajax requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endsection
@endsection
