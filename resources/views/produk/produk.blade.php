@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Kategori</h1>
@stop

@section('content')
<main class="main">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Product</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                List Product
                                <div class="float-right">
                                    <button class="btn btn-primary btn-sm ml-3" data-toggle="modal" data-target="#addProductModal">Tambah</button>
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

                            <form action="{{ route('product.index') }}" method="get">
                                <div class="input-group mb-3 col-md-3 float-right">
                                    <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">Cari</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($product as $row)
                                        <tr>
                                            <td>
                                                <img src="{{ asset('storage/products/' . $row->image) }}" width="100px" height="100px" alt="{{ $row->name }}">
                                            </td>
                                            <td>
                                                <strong>{{ $row->name }}</strong><br>
                                                <label>Kategori: <span class="badge badge-info">{{ $row->category->name }}</span></label><br>
                                                <label>Berat: <span class="badge badge-info">{{ $row->weight }} gr</span></label><br>
                                                <label>Stok: <span class="badge badge-info">{{ $row->stock }}</span></label>
                                            </td>
                                            <td>Rp {{ number_format($row->price) }}</td>
                                            <td>{{ $row->created_at->format('d-m-Y') }}</td>
                                            <td>{!! $row->status_label !!}</td>
                                            <td>
                                                <!-- Button trigger modal -->
                                                <button type="button" class="btn btn-warning btn-sm mb-3" data-toggle="modal" data-target="#editProductModal{{ $row->id }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('product.destroy', $row->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {!! $product->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Tambah Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('product.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Tambah Produk</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Nama Produk</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Deskripsi</label>
                                        <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                                        <p class="text-danger">{{ $errors->first('description') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="1" {{ old('status') == '1' ? 'selected':'' }}>Publish</option>
                                            <option value="0" {{ old('status') == '0' ? 'selected':'' }}>Draft</option>
                                        </select>
                                        <p class="text-danger">{{ $errors->first('status') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="category_id">Kategori</label>
                                        <select name="category_id" class="form-control">
                                            <option value="">Pilih</option>
                                            @foreach ($category as $row)
                                            <option value="{{ $row->id }}" {{ old('category_id') == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-danger">{{ $errors->first('category_id') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Harga</label>
                                        <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
                                        <p class="text-danger">{{ $errors->first('price') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="weight">Berat</label>
                                        <input type="number" name="weight" class="form-control" value="{{ old('weight') }}" required>
                                        <p class="text-danger">{{ $errors->first('weight') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock">Stok</label>
                                        <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" required>
                                        <p class="text-danger">{{ $errors->first('stock') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Foto Produk</label>
                                        <input type="file" name="image" class="form-control" value="{{ old('image') }}" required>
                                        <p class="text-danger">{{ $errors->first('image') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-sm">Tambah</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Produk -->
@foreach ($product as $row)
<div class="modal fade" id="editProductModal{{ $row->id }}" tabindex="-1" aria-labelledby="editProductModalLabel{{ $row->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel{{ $row->id }}">Edit Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('product.update', $row->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Nama Produk</label>
                        <input type="text" name="name" class="form-control" value="{{ $row->name }}" required>
                        <p class="text-danger">{{ $errors->first('name') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control">{{ $row->description }}</textarea>
                        <p class="text-danger">{{ $errors->first('description') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="1" {{ $row->status == '1' ? 'selected' : '' }}>Publish</option>
                            <option value="0" {{ $row->status == '0' ? 'selected' : '' }}>Draft</option>
                        </select>
                        <p class="text-danger">{{ $errors->first('status') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">Pilih</option>
                            @foreach ($category as $cat)
                                <option value="{{ $cat->id }}" {{ $row->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-danger">{{ $errors->first('category_id') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="price">Harga</label>
                        <input type="number" name="price" class="form-control" value="{{ $row->price }}" required>
                        <p class="text-danger">{{ $errors->first('price') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="weight">Berat</label>
                        <input type="number" name="weight" class="form-control" value="{{ $row->weight }}" required>
                        <p class="text-danger">{{ $errors->first('weight') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stok</label>
                        <input type="number" name="stock" class="form-control" value="{{ $row->stock }}" required>
                        <p class="text-danger">{{ $errors->first('stock') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="image">Foto Produk</label>
                        <input type="file" name="image" id="image" class="form-control">
                        <p class="text-danger">{{ $errors->first('image') }}</p>
                        <img id="image_preview" src="{{ asset('storage/products/' . $row->image) }}" alt="Preview Image" style="max-width: 200px; max-height: 200px;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

        $('#addProductModal').on('hidden.bs.modal', function () {
            $(this).find('form').trigger('reset');
        });

        $(document).ready(function() {
            // Saat input file berubah
            $('#image').change(function() {
                previewImage(this);
            });

            // Fungsi untuk menampilkan preview gambar
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#image_preview').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]); // Membaca data URL gambar
                }
            }
        });
    </script>
@stop
