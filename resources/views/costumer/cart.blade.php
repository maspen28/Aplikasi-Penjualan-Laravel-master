@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendors/linericon/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/nouislider/nouislider.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css">
@endsection

@section('title')
    Shopping Cart
@endsection

@section('main')
<!-- ================ start banner area ================= -->
<section class="blog-banner-area" id="category">
    <div class="container h-100">
        <div class="blog-banner">
            <div class="text-center">
                <h1>Shopping Cart</h1>
                <nav aria-label="breadcrumb" class="banner-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>
<!-- ================ end banner area ================= -->

<!--================Cart Area =================-->
<section class="cart_area">
    <div class="container">
        <div class="cart_inner">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Action</th>
                            <th scope="col">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($cart->count())
                            @foreach ($cart as $row)
                                @if ($row->product)
                                    <tr>
                                        <td>
                                            <form method="POST" action="{{ url('/costumer/cart/delete/'.$row->id)}}" accept-charset="UTF-8" style="display:inline">
                                                @method('delete')
                                                @csrf
                                                <button class="btn btn-danger" type="submit"><i class="ti-trash"></i></button>
                                            </form>

                                        </td>
                                        <td>
                                            <div class="media">
                                                <div class="d-flex">
                                                    <img src="{{ asset('storage/products/' . $row->product->image) }}" style="height: 100px;" alt="">
                                                </div>
                                                <div class="media-body">
                                                    <p>{{ $row->product->name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Rp.{{ number_format($row->product->price) }}</h5>
                                        </td>
                                        <td>
                                            <div class="product_count">
                                                <form action="{{ url('/customer/cart/update/'.$row->id)}}" method="post">
                                                    @method('PATCH')
                                                    @csrf
                                                    <input type="text" name="qty" id="sst{{ $row->id }}" maxlength="12" value="{{$row->qty}}" title="Quantity:" class="input-text qty">
                                                    <button onclick="var result = document.getElementById('sst{{$row->id}}'); var sst = result.value; if( !isNaN( sst )) result.value++;return false;" class="increase items-count" type="button"><i class="lnr lnr-chevron-up"></i></button>
                                                    <button onclick="var result = document.getElementById('sst{{$row->id}}'); var sst = result.value; if( !isNaN( sst ) && sst > 0 ) result.value--;return false;" class="reduced items-count" type="button"><i class="lnr lnr-chevron-down"></i></button>
                                                    <button type="submit">Update</button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Rp.{{ number_format($row->product->price * $row->qty) }}</h5>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-warning">Product not found for this cart item.</div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5">
                                    <div class="cupon_text d-flex align-items-center">
                                        <h4>Belanjaan masih kosong, klik <a href="{{ route('home.product') }}">disini!</a> untuk mulai belanja.</h4>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td colspan="3"></td>
                            <td>
                                <h5>Subtotal</h5>
                            </td>
                            <td>
                                <h5>Rp.{{ number_format($subtotal) }}</h5>
                            </td>
                        </tr>
                        <tr class="out_button_area">
                            <td class="d-none-l"></td>
                            <td colspan="3"></td>
                            <td>
                                <div class="checkout_btn_inner d-flex align-items-center">
                                    @if ($cart->count())
                                        <a class="gray_btn" href="{{ route('home.product') }}">Lanjutkan Berbelanja</a>
                                        <button class="primary-btn ml-2" data-toggle="modal" data-target="#orderSummaryModal">Checkout</button>
                                    @else
                                        <a class="primary-btn ml-2" href="{{ route('home.product') }}">Lanjutkan Berbelanja</a>
                                        <button class="gray_btn" disabled="disabled">Checkout</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<!--================End Cart Area =================-->

<!-- Order Summary Modal -->
<div class="modal fade" id="orderSummaryModal" tabindex="-1" role="dialog" aria-labelledby="orderSummaryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderSummaryModalLabel">Order Summary</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Product</th>
              <th scope="col">Price</th>
              <th scope="col">Quantity</th>
              <th scope="col">Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($cart as $row)
              <tr>
                <td>{{ $row->product->name }}</td>
                <td>Rp.{{ number_format($row->product->price) }}</td>
                <td>{{ $row->qty }}</td>
                <td>Rp.{{ number_format($row->product->price * $row->qty) }}</td>
              </tr>
            @endforeach
            <tr>
              <td colspan="3"><strong>Subtotal</strong></td>
              <td><strong>Rp.{{ number_format($subtotal) }}</strong></td>

              <text>Silahkan melanjutkan transaksi di Aplikasi Mobile</text>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary" id="proceedToCheckout">Proceed to Checkout</button> -->
      </div>
    </div>
  </div>
</div>

<!-- Message Modal -->
<!-- <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Silahkan melanjutkan transaksi di Aplikasi Mobile
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> -->
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle the "Proceed to Checkout" button click
        $('#orderSummaryModal').on('shown.bs.modal', function () {
            $('#proceedToCheckout').click(function() {
                // Show the message modal
                $('#messageModal').modal('show');
            });
        });
    });
</script>
@endsection

