@extends('layouts.layout')

@section('title')
Order Complete
@endsection

@section('main')
<section class="section-margin--small">
    <div class="container">
        <div class="order_complete">
            <h3>Thank you for your order!</h3>
            <p>Your order ID is <strong>{{ $order->invoice }}</strong></p>
            <p>Total payment amount is <strong>Rp. {{ number_format($order->subtotal + $order->cost) }}</strong></p>
            <button id="pay-button" class="btn btn-primary">Pay Now</button>
        </div>
    </div>
</section>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function() {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                alert('Payment Success');
                console.log(result);
            },
            onPending: function(result) {
                alert('Payment Pending');
                console.log(result);
            },
            onError: function(result) {
                alert('Payment Failed');
                console.log(result);
            },
            onClose: function() {
                alert('You closed the popup without finishing the payment');
            }
        });
    };
</script>
@endsection
