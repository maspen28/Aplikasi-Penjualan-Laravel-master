@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<main class="main">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            
            <!-- Filter Form -->
            <div class="row">
                <div class="col-md-12">
                    <form id="filterForm" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="month" class="mr-2">Bulan:</label>
                            <select id="month" name="month" class="form-control">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == $currentMonth ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <label for="year" class="mr-2">Tahun:</label>
                            <select id="year" name="year" class="form-control">
                                @for ($i = 2020; $i <= now()->year; $i++)
                                    <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End of Filter Form -->
            
            <!-- Dashboard Content -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Aktivitas Toko</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="callout callout-danger">
                                        <small class="text-muted">Total Pelanggan</small>
                                        <br>
                                        <strong class="h4">{{ $totalCustomers }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="callout callout-success">
                                        <small class="text-muted">Total Produk</small>
                                        <br>
                                        <strong class="h4">{{ $totalProducts }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="callout callout-info">
                                        <small class="text-muted">Total Omset Bulan ini</small>
                                        <br>
                                        <strong class="h4">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="callout callout-primary">
                                        <small class="text-muted">Perlu Dikirim</small>
                                        <br>
                                        <strong class="h4">{{ $ordersToShip }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row for charts -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Grafik Jumlah Produk Terjual</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="soldProductsChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of row for charts -->
        </div>
    </div>
</main>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Ambil data yang diberikan oleh controller
            var soldQuantities = @json($chartData['soldQuantities']);
            var productNames = @json($chartData['productNames']);

            // Inisialisasi grafik bar
            var soldProductsCtx = document.getElementById('soldProductsChart').getContext('2d');
            var soldProductsChart = new Chart(soldProductsCtx, {
                type: 'bar',
                data: {
                    labels: productNames,
                    datasets: [{
                        label: 'Jumlah Terjual',
                        data: soldQuantities,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value;
                                }
                            }
                        }
                    }
                }
            });

        function submitFilterForm() {
            var month = $('#month').val().padStart(2, '0');
            var year = $('#year').val();

            console.log("Filtering for month: " + month + " and year: " + year);

            $.ajax({
                url: '{{ route("dashboard.filter") }}',
                type: 'GET',
                data: {
                    month: month,
                    year: year
                },
                success: function(data) {
                    // Update data pada halaman dashboard
                    $('.callout-danger .h4').text(data.totalCustomers);
                    $('.callout-success .h4').text(data.totalProducts);
                    $('.callout-info .h4').text('Rp ' + new Intl.NumberFormat().format(data.monthlyRevenue));
                    $('.callout-primary .h4').text(data.ordersToShip);

                    // Update data pada grafik
                    soldProductsChart.data.labels = data.chartData.productNames;
                    soldProductsChart.data.datasets[0].data = data.chartData.soldQuantities;
                    soldProductsChart.update();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        $('#month, #year').change(function() {
            submitFilterForm();
        });

        // Submit form saat halaman pertama kali dimuat untuk mendapatkan data awal
        submitFilterForm();
    });    
    </script>
@stop

