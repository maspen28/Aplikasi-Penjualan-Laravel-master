@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Diskon</h1>
@stop

@section('content')
<main class="main">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Diskon</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                List Diskon
                                <div class="float-right">
                                    <button class="btn btn-primary btn-sm ml-3" data-toggle="modal" data-target="#addDiscountModal">Tambah</button>
                                </div>
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
                                            <th>Nama Diskon</th>
                                            <th>Besar Diskon (%)</th>
                                            <th>Created At</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($discounts as $discount)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $discount->discount_name }}</td>
                                            <td>{{ $discount->besar_diskon }}</td>
                                            <td>{{ $discount->created_at->format('d-m-Y H:i:s') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editDiscountModal{{ $discount->id }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('discount.destroy', $discount->id) }}" method="post" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data diskon</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Diskon -->
<div class="modal fade" id="addDiscountModal" tabindex="-1" aria-labelledby="addDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDiscountModalLabel">Tambah Diskon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('discount.store') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="discount_name">Nama Diskon</label>
                        <input type="text" name="discount_name" class="form-control" value="{{ old('discount_name') }}" required>
                        <p class="text-danger">{{ $errors->first('discount_name') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="besar_diskon">Besar Diskon (%)</label>
                        <input type="number" name="besar_diskon" class="form-control" value="{{ old('besar_diskon') }}" required>
                        <p class="text-danger">{{ $errors->first('besar_diskon') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Diskon -->
@foreach ($discounts as $discount)
<div class="modal fade" id="editDiscountModal{{ $discount->id }}" tabindex="-1" aria-labelledby="editDiscountModalLabel{{ $discount->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDiscountModalLabel{{ $discount->id }}">Edit Diskon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('discount.update', $discount->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="discount_name">Nama Diskon</label>
                        <input type="text" name="discount_name" class="form-control" value="{{ $discount->discount_name }}" required>
                        <p class="text-danger">{{ $errors->first('discount_name') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="product_id">Produk</label>
                        <select name="product_id" class="form-control" required>
                            <option value="">Pilih Produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ $discount->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-danger">{{ $errors->first('product_id') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="besar_diskon">Besar Diskon (%)</label>
                        <input type="number" name="besar_diskon" class="form-control" value="{{ $discount->besar_diskon }}" required>
                        <p class="text-danger">{{ $errors->first('besar_diskon') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Hi!');

        $('#addDiscountModal').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');
        });
    </script>
@stop
