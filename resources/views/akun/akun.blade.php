@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Akun Customer</h1>
@stop

@section('content')
<main class="main">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Akun Customer</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                List Akun Customer
                            </h4>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Telepon</th>
                                            <th>Alamat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $index => $customer)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $customer->name }}</td>
                                                <td>{{ $customer->email }}</td>
                                                <td>{{ $customer->phone }}</td>
                                                <td>{{ $customer->address }}</td>
                                                <td>
                                                    <form action="{{ route('customer.destroy', $customer->id) }}" method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if ($customers->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            {!! $customers->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

