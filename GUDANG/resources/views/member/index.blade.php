@extends('layouts.app')

@section('title', 'Member')

@section('content')
    <div class="container-fluid">
        <h1 class="mt-4 mb-4">MEMBER</h1>

        <div class="card">
            <div class="card-header">
                @if (Auth::user()->isAdmin())
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahMember">
                        Tambah Member
                    </button>
                @endif
            </div>

            <!-- Modal Tambah Barang -->
            <div class="modal fade" id="tambahMember">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">TAMBAH MEMBER</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form method="POST" action="{{ route('member.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nama Member</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Email Member</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="number" name="phone" class="form-control" required min="0">
                                </div>

                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="address" class="form-control" required></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="datatablesSimple" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($member as $index)
                                <tr>
                                    <td>{{ $index->id }}</td>
                                    <td class="text-decoration-none text-dark font-weight-bold">
                                        {{ $index->name }}
                                    </td>
                                    <td>{{ $index->email }}</td>
                                    <td>{{ $index->phone }}</td>
                                    <td>{{ $index->address }}</td>
                                    <td>
                                        @if (Auth::user()->isAdmin())
                                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                                data-target="#edit{{ $index->id }}" data-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#delete{{ $index->id }}" data-toggle="tooltip"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        @foreach ($member as $item)
            <div class="modal fade" id="edit{{ $item->id }}">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">EDIT MEMBER</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form method="POST" action="{{ route('member.update', $item->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nama Member</label>
                                    <input type="text" name="name" class="form-control" value="{{ $item->name }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label>Email Member</label>
                                    <input type="email" name="email" class="form-control" value="{{ $item->email }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="number" name="phone" class="form-control" value="{{ $item->phone }}"
                                        required min="0">
                                </div>

                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="address" class="form-control" required>{{ $item->address }}</textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Delete -->
            <div class="modal fade" id="delete{{ $item->id }}">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">HAPUS MEMBER</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form method="POST" action="{{ route('member.destroy', $item->id) }}">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                <p>Apakah anda yakin menghapus <strong>{{ $item->name }}?</strong></p>
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
        <style>
            .image-preview {
                width: 100%;
                min-height: 200px;
                border: 2px dashed #ddd;
                border-radius: 3px;
                position: relative;
                overflow: hidden;
                background-color: #ffffff;
                padding: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            #preview {
                max-width: 100%;
                max-height: 200px;
                display: none;
            }

            #placeholder-text {
                color: #999;
                text-align: center;
            }

            /* Style untuk tombol aksi */
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                margin: 0 2px;
            }

            .btn-sm i {
                font-size: 1rem;
            }

            /* Efek hover untuk tombol */
            .btn-warning:hover {
                background-color: #e0a800;
                border-color: #d39e00;
            }

            .btn-danger:hover {
                background-color: #c82333;
                border-color: #bd2130;
            }
        </style>
    @endpush
@endsection
