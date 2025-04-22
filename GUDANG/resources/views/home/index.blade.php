@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4 mb-4">GUDANG</h1>
    
    <div class="card">
        <div class="card-header">
            @if(Auth::user()->isAdmin())
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahBarangModal">
                    Tambah Barang
                </button>
                <a href="{{ route('home.export') }}" class="btn btn-info">Export Data</a>
            @endif
        </div>

        <!-- Modal Tambah Barang -->
        <div class="modal fade" id="tambahBarangModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">TAMBAH BARANG</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('home.store') }}" enctype="multipart/form-data" id="tambahBarangForm">
                        @csrf
                        <div class="alert alert-success d-none" role="alert"></div>
                        <div class="alert alert-danger d-none" role="alert"></div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text" name="namaBarang" class="form-control" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Gambar Barang</label>
                                <div class="image-preview mb-2" id="imagePreview">
                                    <img id="preview" src="#" alt="Preview" style="display: none; max-width: 100%; max-height: 200px;">
                                    <div id="placeholder-text" class="text-center">
                                        <p class="mb-0">Preview gambar akan muncul di sini</p>
                                    </div>
                                </div>
                                <input type="file" name="gambar" class="form-control" id="gambar">
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Stok Awal</label>
                                <input type="number" name="stok" class="form-control" required min="0">
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @foreach($datastok as $item)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>PERHATIAN!</strong> Stok <strong>{{ $item->namaBarang }}</strong> saat ini ({{ $item->stok }}) telah mencapai batas minimal ({{ $item->stok_minimal }}).
            </div>
            @endforeach

            {{-- Filtering --}}

            <form method="GET" action="{{ route('home.index') }}" class="form-inline mb-2">
                <label for="filter_namaBarang" class="mr-2">Filter Barang:</label>
                <select name="filter_namaBarang" id="filter_namaBarang" class="form-control mr-2">
                    <option value="all" {{ request()->input('filter_namaBarang') == 'all' ? 'selected' : '' }}>All
                    </option>
                    @foreach ($allNamaBarang as $nama)
                        <option value="{{ $nama }}"
                            {{ request()->input('filter_namaBarang') == $nama ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            {{-- End of Filtering --}}
            
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-bordered">
                    <thead>
                        <tr>
                            <!-- <th>ID</th> -->
                            <th>Nama</th>
                            <th>Gambar</th>
                            <th>Deskripsi</th>
                            <th>Stok</th>
                            @if(Auth::user()->isAdmin())
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($homes as $index)
                        <tr>
                            <!-- <td>{{ $index->id }}</td> -->
                            <td>
                                <a href="{{ route('home.detail', $index->id) }}" 
                                   class="text-decoration-underline text-dark">
                                    {{ $index->namaBarang }}
                                </a>
                            </td>
                            <td>
                                @if($index->gambar && Storage::disk('public')->exists('gambar/' . $index->gambar))
                                    <img src="{{ asset('storage/gambar/' . $index->gambar) }}" 
                                         alt="{{ $index->namaBarang }}" 
                                         width="100"
                                         class="img-thumbnail"
                                         data-toggle="modal" data-target="#imageModal{{ $index->id }}">
                                @else
                                    <img src="{{ asset('storage/gambar/default.png') }}" 
                                         alt="Default" 
                                         width="100"
                                         class="img-thumbnail">
                                @endif
                            </td>
                            <td>{{ $index->deskripsi }}</td>
                            <td>{{ $index->stok }}</td>
                            @if(Auth::user()->isAdmin())
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit{{ $index->id }}" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete{{ $index->id }}" data-toggle="tooltip" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                                @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    

    <!-- Modal Image -->     
    @foreach($homes as $index)
     <div class="modal fade" id="imageModal{{ $index->id }}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel{{ $index->id }}" aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="imageModalLabel{{ $index->id }}">{{ $index->namaBarang }}</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <img src="{{ asset('storage/gambar/' . $index->gambar) }}" alt="{{ $index->namaBarang }}" class="img-fluid">
                 </div>
                 <div class="modal-footer">
                     <a href="{{ asset('storage/gambar/' . $index->gambar) }}" download class="btn btn-primary">Download</a>
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                 </div>
             </div>
         </div>
     </div>
     @endforeach
    <!-- Modal Edit -->
    @foreach($homes as $item)
    <div class="modal fade" id="edit{{ $item->id }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">EDIT BARANG</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="{{ route('home.update',$item->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" name="namaBarang" value="{{ $item->namaBarang }}" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Gambar Barang</label>
                            <div class="image-preview mb-2" id="imagePreview{{ $item->id }}">
                                <img id="preview{{ $item->id }}" 
                                     src="{{ $item->gambar && Storage::disk('public')->exists('gambar/' . $item->gambar) 
                                           ? asset('storage/gambar/' . $item->gambar) 
                                           : asset('storage/gambar/default.png') }}" 
                                     alt="Preview" 
                                     style="display: block; max-width: 100%; max-height: 200px;">
                            </div>
                            <input type="file" name="gambar" class="form-control" id="gambar{{ $item->id }}">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" required>{{ $item->deskripsi }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Stok</label>
                            <input type="number" name="stok" value="{{ $item->stok }}" class="form-control" required min="0">
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
                    <h4 class="modal-title">HAPUS BARANG</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="{{ route('home.destroy', $item->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Apakah anda yakin menghapus <strong>{{ $item->namaBarang }}?</strong></p>
                        <div class="text-center">
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
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
    color:white;
}

.btn-sm i {
    font-size: 1rem;
}

/* Efek hover untuk tombol */
.btn-warning:hover {
    background-color: #559ae0;
    border-color: #d39e00;
    font-size: 15px;
}
     
.btn-danger:hover {
    background-color: #559ae0;
    border-color: #bd2130;
    font-size: 15px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi tooltip
    $('[data-toggle="tooltip"]').tooltip();

    const gambarInput = document.getElementById('gambar');
    const preview = document.getElementById('preview');
    const placeholderText = document.getElementById('placeholder-text');

    gambarInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholderText.style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    @foreach($homes as $item)
        const editGambarInput{{ $item->id }} = document.getElementById('gambar{{ $item->id }}');
        const editPreview{{ $item->id }} = document.getElementById('preview{{ $item->id }}');

        editGambarInput{{ $item->id }}.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    editPreview{{ $item->id }}.src = e.target.result;
                    editPreview{{ $item->id }}.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    @endforeach
});
</script>
@endpush
@push('scripts')
<script>
$(document).ready(function() {
    // Load data
    function loadInventory() {
        $.ajax({
            url: '/api/inventory',
            method: 'GET',
            success: function(data) {
                let tableBody = '';
                data.forEach(function(item) {
                    tableBody += `
                        <tr>
                            <td>
                                <a href="/home/detail/${item.id}" class="text-decoration-underline text-dark">
                                    ${item.namaBarang}
                                </a>
                            </td>
                            <td>
                                <img src="${item.gambar ? '/storage/gambar/' + item.gambar : '/storage/gambar/default.png'}" 
                                     alt="${item.namaBarang}" 
                                     width="100"
                                     class="img-thumbnail">
                            </td>
                            <td>${item.deskripsi}</td>
                            <td>${item.stok}</td>
                            ${isAdmin ? `
                            <td>
                                <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="${item.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${item.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            ` : ''}
                        </tr>
                    `;
                });
                $('#datatablesSimple tbody').html(tableBody);
            }
        });
    }

    // Add new item
    $('#tambahBarangModal form').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#tambahBarangModal').modal('hide');
                loadInventory();
                $('.alert-success').html('Barang berhasil ditambahkan!').removeClass('d-none').show();
                setTimeout(function() {
                    $('.alert-success').fadeOut();
                }, 3000);
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menambah barang';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('.alert-danger').html(errorMessage).removeClass('d-none').show();
                setTimeout(function() {
                    $('.alert-danger').fadeOut();
                }, 3000);
            }
        });
    });

    // Update item
    $(document).on('submit', '.edit-form', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let formData = new FormData(this);
        formData.append('_method', 'PUT');

        $.ajax({
            url: `/api/inventory/${id}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $(`#edit${id}`).modal('hide');
                loadInventory();
                alert('Item updated successfully');
            },
            error: function(xhr) {
                alert('Error updating item');
            }
        });
    });

    // Delete item
    $(document).on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this item?')) {
            let id = $(this).data('id');
            $.ajax({
                url: `/api/inventory/${id}`,
                method: 'DELETE',
                success: function(response) {
                    loadInventory();
                    alert('Item deleted successfully');
                },
                error: function(xhr) {
                    alert('Error deleting item');
                }
            });
        }
    });

    // Initial load
    loadInventory();
});
</script>
@endpush
@endsection
