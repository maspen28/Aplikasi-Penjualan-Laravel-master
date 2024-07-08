@extends('layouts.layout')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/linericon/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/nouislider/nouislider.min.css') }}">
@endsection

@section('title', 'Checkout')

@section('main')
<section class="blog-banner-area" id="category">
    <div class="container h-100">
        <div class="blog-banner">
            <div class="text-center">
                <h1>Product Checkout</h1>
                <nav aria-label="breadcrumb" class="banner-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="checkout_area section-margin--small">
    <div class="container">
        <div class="billing_details">
            <div class="row">
                @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="col-lg-8">
                    <h3>Billing Details</h3>
                    <form class="row contact_form" action="{{ route('home.checkoutproses') }}" method="post" novalidate="novalidate">
                        @csrf
                        <div class="col-md-6 form-group p_star">
                            <label for="customer_name">Nama</label>
                            <input type="hidden" name="invoice" value="{{ Str::random(4) . '-' . time() }}" required>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ Auth::guard('costumer')->user()->name }}" required>
                            <span class="placeholder" data-placeholder="Name"></span>
                        </div>
                        <div class="col-md-6 form-group p_star">
                            <label for="customer_phone">NO HP Aktif</label>
                            <input type="text" class="form-control" id="customer_phone" name="customer_phone" value="{{ Auth::guard('costumer')->user()->phone_number }}" required>
                            <span class="placeholder" data-placeholder="Phone"></span>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="customer_address">Alamat</label>
                            <input type="text" class="form-control" id="customer_address" name="customer_address" value="{{ Auth::guard('costumer')->user()->address }}" required>
                            <label for="province_id">Provinsi</label>
                            <select class="form-control" name="province_id" id="province_id" required>
                                <option value="">Select Province</option>
                                @foreach ($provinces as $province)
                                <option value="{{ $province->id }}" {{ $province->id == old('province_id') ? 'selected' : '' }}>{{ $province->name }}</option>
                                @endforeach
                            </select>
                            <label for="city_id">Kota</label>
                            <select class="form-control" name="city_id" id="city_id" required>
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ $city->id == old('city_id') ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            <label for="district_id">Kecamatan</label>
                            <select class="form-control" name="district_id" id="district_id" required>
                                <option value="">Select District</option>
                                @foreach ($districts as $district)
                                <option value="{{ $district->id }}" {{ $district->id == old('district_id') ? 'selected' : '' }}>{{ $district->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" class="form-control" name="subtotal" value="{{ $subtotal }}" required>
                            <input type="hidden" class="form-control" name="cost" value="" required>
                            <input type="hidden" class="form-control" name="shipping" value="" required>
                            <input type="hidden" class="form-control" name="status" value="0" required>
                        </div>
                </div>
                <div class="col-lg-4">
                    <div class="order_box">
                        <h2>Your Order</h2>
                        <ul class="list">
                            <li><a href="#"><h4>Product <span>Total</span></h4></a></li>
                            @foreach ($cart as $row)
                            <li><a href="#">{{ $row->product->name }} <span class="middle">x {{ $row->qty }}</span> <span class="last">Rp. {{ number_format($row->cart_price * $row->qty) }}</span></a></li>
                            @endforeach
                        </ul>
                        <ul class="list list_2">
                            <li><a href="#">Subtotal <span>Rp. {{ number_format($subtotal) }}</span></a></li>
                            <li><a href="#">Shipping <span id="shipping_cost">Rp. 0</span></a></li>
                            <li><a href="#">Total <span id="total_cost">Rp. {{ number_format($subtotal) }}</span></a></li>
                        </ul>
                        <button type="submit" class="btn btn-primary">Proceed to Checkout</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#province_id').on('change', function() {
            var province_id = $(this).val();
            if (province_id) {
                $.ajax({
                    url: '/cities/' + province_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#city_id').empty();
                        $('#city_id').append('<option value="">Select City</option>');
                        $.each(data, function(id, name) {
                            $('#city_id').append('<option value="' + id + '">' + name + '</option>');
                        });
                        // Setelah mengisi ulang kota, pilih kota pertama jika ada
                        var defaultCity = data[0];
                        if (defaultCity) {
                            $('#city_id').val(defaultCity.id);
                            $('#city_id').trigger('change');
                        }
                    }
                });
            } else {
                $('#city_id').empty();
                $('#district_id').empty();
            }
        });

        $('#city_id').on('change', function() {
            var province_id = $('#province_id').val();
            var city_id = $(this).val();
            if (city_id) {
                $.ajax({
                    url: '/districts/' + province_id + '/' + city_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#district_id').empty();
                        $('#district_id').append('<option value="">Select District</option>');
                        $.each(data, function(id, name) {
                            $('#district_id').append('<option value="' + id + '">' + name + '</option>');
                        });
                    }
                });
            } else {
                $('#district_id').empty();
            }
        });

        $('#district_id').on('change', function() {
            var district_id = $(this).val();
            var weight = {{ $weight }};
            if (district_id) {
                $.ajax({
                    url: '/calculate-shipping-cost',
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        district_id: district_id,
                        weight: weight
                    },
                    success: function(data) {
                        if (data.length > 0) {
                            var cost = data[0]['costs'][0]['cost'][0]['value'];
                            $('#shipping_cost').text('Rp. ' + cost.toLocaleString('id-ID'));
                            $('#total_cost').text('Rp. ' + ({{ $subtotal }} + cost).toLocaleString('id-ID'));
                            $('input[name="cost"]').val(cost);
                            $('input[name="shipping"]').val(cost);
                        }
                    }
                });
            }
        });

        // Skrip untuk memilih kota pertama saat halaman dimuat
        var initialProvinceId = $('#province_id').val();
        if (initialProvinceId) {
            $.ajax({
                url: '/cities/' + initialProvinceId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#city_id').empty();
                    $('#city_id').append('<option value="">Select City</option>');
                    $.each(data, function(id, name) {
                        $('#city_id').append('<option value="' + id + '">' + name + '</option>');
                    });
                    // Setelah mengisi ulang kota, pilih kota pertama jika ada
                    var defaultCity = data[0];
                    if (defaultCity) {
                        $('#city_id').val(defaultCity.id);
                        $('#city_id').trigger('change');
                    }
                }
            });
        }
    });
</script>

@endsection
