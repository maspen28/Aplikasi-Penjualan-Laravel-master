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
                                <div class="callout callout-info">
                                    <small class="text-muted">Total Omset Bulanan</small>
                                    <br>
                                    <strong class="h4">Rp {{ number_format($monthlyRevenue) }}</strong>
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
        </div>
    </div>
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

</main>


@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            var dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
            var dailyRevenueChart = new Chart(dailyRevenueCtx, {
                type: 'line',
                data: {
                    labels: {!! $dailyLabels->toJson() !!},
                    datasets: [{
                        label: 'Omset Harian',
                        data: {!! $dailyRevenue->toJson() !!},
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
        });
    </script>

    <script>
        $(document).ready(function() {
            var monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
            var monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
                type: 'line',
                data: {
                    labels: {!! $monthlyLabels->toJson() !!},
                    datasets: [{
                        label: 'Omset Bulanan',
                        data: {!! $monthlyRevenue->toJson() !!},
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
