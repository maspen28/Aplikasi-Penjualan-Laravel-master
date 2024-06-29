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
            <form id="filter-form" class="form-inline mb-3">
                <div class="form-group mr-2">
                    <label for="month">Bulan:</label>
                    <select id="month" name="month" class="form-control ml-2">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label for="year">Tahun:</label>
                    <select id="year" name="year" class="form-control ml-2">
                        @for ($i = date('Y'); $i >= 2000; $i--)
                            <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button type="button" class="btn btn-primary" onclick="updateCharts()">Filter</button>
            </form>

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
                                        <small class="text-muted">Total Omset Bulanan</small>
                                        <br>
                                        <strong class="h4">Rp {{ number_format((float)$monthlyRevenue, 0, ',', '.') }}</strong>
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
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Grafik Omset Harian</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="dailyRevenueChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Grafik Omset Bulanan</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyRevenueChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of row for charts -->
        </div>
    </div>
</main>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function updateCharts() {
            var month = $('#month').val();
            var year = $('#year').val();
            $.ajax({
                url: '{{ route("dashboard.data") }}',
                type: 'GET',
                data: {
                    month: month,
                    year: year
                },
                success: function(response) {
                    console.log(response);
                    // Update charts with new data
                    dailyRevenueChart.data.labels = response.dailyLabels;
                    dailyRevenueChart.data.datasets[0].data = response.dailyRevenueValues;
                    dailyRevenueChart.update();

                    monthlyRevenueChart.data.labels = response.monthlyLabels;
                    monthlyRevenueChart.data.datasets[0].data = response.monthlyRevenueValues;
                    monthlyRevenueChart.update();
                }
            });
        }

        $(document).ready(function() {
            var dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
            var dailyRevenueChart = new Chart(dailyRevenueCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailyLabels) !!},
                    datasets: [{
                        label: 'Omset Harian',
                        data: {!! json_encode($dailyRevenueValues) !!},
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            callback: function(value) {
                                return 'Rp ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            }
                        }
                    }
                }
            });

            var monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
            var monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyLabels) !!},
                    datasets: [{
                        label: 'Omset Bulanan',
                        data: {!! json_encode($monthlyRevenueValues) !!},
                        borderColor: 'rgb(54, 162, 235)',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            callback: function(value) {
                                return 'Rp ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop
