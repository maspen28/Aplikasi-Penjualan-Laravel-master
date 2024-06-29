@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/linericon/style.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendors/nouislider/nouislider.min.css')}}">

@endsection

@section('title')
    Kontak
@endsection

@section('kontak')
    active
@endsection

@section('main')
<!-- ================ start banner area ================= -->
<section class="blog-banner-area" id="contact">
    <div class="container h-100">
        <div class="blog-banner">
            <div class="text-center">
                <h1>Contact Us</h1>
                <nav aria-label="breadcrumb" class="banner-breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
        </ol>
      </nav>
            </div>
        </div>
</div>
</section>
<!-- ================ end banner area ================= -->

<!-- ================ contact section start ================= -->
<section class="section-margin--small">
<div class="container">
  <div class="d-none d-sm-block mb-5 pb-4">
    <div id="map" style="height: 420px;"></div>
    <script>
      function initMap() {
        var uluru = {lat: -25.363, lng: 131.044};
        var grayStyles = [
          {
            featureType: "all",
            stylers: [
              { saturation: -90 },
              { lightness: 50 }
            ]
          },
          {elementType: 'labels.text.fill', stylers: [{color: '#A3A3A3'}]}
        ];
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: -31.197, lng: 150.744},
          zoom: 9,
          styles: grayStyles,
          scrollwheel:  false
        });
      }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDpfS1oRGreGSBU5HHjMmQ3o5NLw7VdJ6I&callback=initMap"></script>

  </div>


  <div class="row">
    <div class="col-md-4 col-lg-3 mb-4 mb-md-0">
      <div class="media contact-info">
        <span class="contact-info__icon"><i class="ti-home"></i></span>
        <div class="media-body">
          <h3>KEMBANG TELON STORE</h3>
          <p>Kaos Bernuansa Jawa Modern</p>
        </div>
      </div>
      <div class="media contact-info">
        <span class="contact-info__icon"><i class="ti-headphone"></i></span>
        <div class="media-body">
          <h3><a href="tel:454545654">085335144432</a></h3>
          <p>Buka Terus Menerima Pesanan</p>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<!-- ================ contact section end ================= -->
@endsection

@section('js')
    <script src="{{asset('assets/vendors/nice-select/jquery.nice-select.min.js')}}"></script>
    {{-- <script type="text/javascript">
        $('#province_id').on('change', function() {
            $.ajax({
                url: "{{ url('/api/city') }}",
                type: "GET",
                data: { province_id: $(this).val() },
                success: function(html){

                    $('#city_id').empty()
                    $('#city_id').append('<option value="">Pilih Kabupaten/Kota</option>')
                    $.each(html.data, function(key, item) {
                        $('#city_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                    })
                }
            });
        })

        $('#courier').on('change', function() {
            $('#service').empty()
            $('#service').append('<option value="">Loading...</option>')
            $.ajax({
                url:"{{ route('home.cekongkir') }}",
                type: "POST",
                data: {
                        _token:              $("meta[name='csrf-token']").attr("content"),
                        city_origin:         $('input[name=city_origin]').val(),
                        city_destination:    $('select[name=city_destination]').val(),
                        courier:             $('select[name=courier]').val(),
                        weight:              $('#weight').val(),
                    },

                success: function(response){
                    $('#service').empty();
                    $('#service').append('<option value="">Pilih service</option>')
                    $.each(response[0]['costs'], function (key, value) {
                        $('#service').append('<option>'+response[0].code.toUpperCase()+' : <strong>'+value.service+'</strong>, '+value.cost[0].value+', ('+value.cost[0].etd+' hari)</option>')
                    });
                }
            });
        })

        $('#service').on('change', function() {
            let split = $(this).val().split(',')
            $('#ongkir').text('Ongkir : Rp. ' + split[1])
            $('#resi').text('Perkiraan waktu sampai : ' + split[2])

            let subtotal = "{{ $subtotal }}"
            let total = parseInt(subtotal) + parseInt(split[1])
            $('#total').text('Rp. ' + total)
            $('#subtotal').append('<input type="text" name="subtotal" value="'+total+'" disabled required>')
        })

    </script>     --}}

@endsection

