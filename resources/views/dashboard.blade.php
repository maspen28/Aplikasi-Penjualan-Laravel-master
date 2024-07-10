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
            <!-- Dashboard Content -->
            <div class="row">
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
            <div class="row">
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
            var soldQuantities = @json($soldQuantities);
            var productNames = @json($productNames);

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
        });
    </script>
@stop
