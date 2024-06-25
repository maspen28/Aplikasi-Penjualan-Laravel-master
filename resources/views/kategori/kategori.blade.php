@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Kategori</h1>
@stop

@section('content')
<main class="main">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Kategori</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
              
                <!-- MODAL UNTUK FORM INPUT NEW CATEGORY -->
                <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="categoryModalLabel">Kategori Baru</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('category.store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Kategori</label>
                                        <input type="text" name="name" class="form-control" required>
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-sm">Tambah</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL UNTUK FORM INPUT NEW CATEGORY -->
              
                <!-- MODAL UNTUK FORM EDIT CATEGORY -->
                <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCategoryModalLabel">Edit Kategori</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editCategoryForm" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="edit_name">Kategori</label>
                                        <input type="text" name="name" class="form-control" id="edit_name" required>
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-sm">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL UNTUK FORM EDIT CATEGORY -->
              
                <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">List Kategori
                                <button class="btn btn-primary btn-sm float-right ml-2" data-toggle="modal" data-target="#categoryModal">Tambah Kategori</button>
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
                                            <th>Kategori</th>
                                            <th>Parent</th>
                                            <th>Created At</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($category as $val)
                                        <tr>
                                            <td></td>
                                            <td><strong>{{ $val->name }}</strong></td>
                                            <td>{{ $val->parent ? $val->parent->name:'-' }}</td>
                                            <td>{{ $val->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                <form action="{{ route('category.destroy', $val->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:void(0)" class="btn btn-warning btn-sm edit-category" data-id="{{ $val->id }}" data-name="{{ $val->name }}">Edit</a>
                                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {!! $category->links() !!}
                        </div>
                    </div>
                </div>
                <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY -->
            </div>
        </div>
    </div>
</main>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function(){
            $('.edit-category').click(function(){
                var id = $(this).data('id');
                var name = $(this).data('name');
                var url = '{{ route("category.update", ":id") }}';
                url = url.replace(':id', id);

                $('#editCategoryModal').modal('show');
                $('#editCategoryForm').attr('action', url);
                $('#edit_name').val(name);
            });
        });
    </script>
@stop
