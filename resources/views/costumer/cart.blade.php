@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/linericon/style.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendors/nouislider/nouislider.min.css')}}">
@endsection

@section('title')
    Home
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
                                                <form action="/costumer/cartupdate/{{$row->id}}" method="post">
                                                    @method('PATCH')
                                                    @csrf
                                                    <input type="text" name="qty" id="sst{{ $row->id }}" maxlength="12" value="{{$row->qty}}" title="Quantity:" class="input-text qty">
                                                    <input type="hidden" name="{{$row->id}}" value="{{$row->id}}">
                                                    <button onclick="var result = document.getElementById('sst{{$row->id}}'); var sst = result.value; if( !isNaN( sst )) result.value++;return false;" class="increase items-count" type="button"><i class="lnr lnr-chevron-up"></i></button>
                                                    <button onclick="var result = document.getElementById('sst{{$row->id}}'); var sst = result.value; if( !isNaN( sst ) &amp;&amp; sst > 0 ) result.value--;return false;" class="reduced items-count" type="button"><i class="lnr lnr-chevron-down"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Rp.{{ number_format($row->cart_price * $row->qty) }}</h5>
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

                        <tr class="bottom_button">
                            <td colspan="5" class="text-right">
                                <button class="button" type="submit">Update</button>
                            </td>
                        </tr>
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
                                        <a class="primary-btn ml-2" href="{{ route('home.checkout') }}">Checkout</a>
                                    @else
                                        <a class="primary-btn ml-2" href="{{ route('home.product') }}">Lanjutkan Berbelanja</a>
                                        <a href="{{ route('home.checkout') }}"><button class="gray_btn" disabled="disabled">Checkout</button></a>
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
@endsection

@section('js')
<script src="{{ asset('assets/vendors/nice-select/jquery.nice-select.min.js') }}"></script>
@endsection
