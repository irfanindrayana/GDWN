@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">BARANG MASUK</h1>
    
    <div class="card">
        <div class="card-header">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahBarangModal">
                Tambah Barang Masuk
            </button>
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

            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-bordered">
                     <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Gambar</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Aktor</th>
                            @if(Auth::user()->isAdmin())
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangMasuk as $index)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($index->tanggal)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $index->nama_barang }}</td>
                            <td>
                                @if($index->gambar)
                                    <img src="{{ asset('storage/gambar/' . $index->gambar) }}" 
                                         alt="{{ $index->nama_barang }}" 
                                         width="100"
                                         onerror="if (!this.getAttribute('data-tried-noimage')) {
                                                     this.setAttribute('data-tried-noimage', 'true');
                                                     this.src='{{ asset('storage/gambar/no-image.png') }}';
                                                 }">
                                @else
                                    <img src="{{ asset('storage/gambar/no-image.png') }}" 
                                         alt="No Image Available" 
                                         width="100">
                                @endif
                            </td>
                            <td>{{ $index->deskripsi }}</td>
                            <td>{{ $index->jumlah }}</td>
                            <td>{{ Auth::user()->name }}</td>
                            @if(Auth::user()->isAdmin())
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detail{{ $index->id }}" data-toggle="tooltip" title="Detail">
                                        <i class='fas fa-file'></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit{{ $index->id }}" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete{{ $index->id }}" data-toggle="tooltip" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                        <!-- Modal Detail -->
                        <div class="modal fade" id="detail{{ $index->id }}">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h4 class="modal-title">Detail Barang Masuk</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="print-area">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Tanggal:</strong> {{ $index->tanggal }}</p>
                                                    <p><strong>Nama Barang:</strong> {{ $index->nama_barang }}</p>
                                                    <p><strong>Deskripsi:</strong> {{ $index->deskripsi }}</p>
                                                    <p><strong>Jumlah:</strong> {{ $index->jumlah }}</p>
                                                    <p><strong>Aktor:</strong> {{ Auth::user()->name }}</p>
                                                </div>
                                                <div class="col-md-6 text-center">
                                                    <p><strong>Gambar:</strong></p>
                                                    <img src="{{ asset('storage/gambar/' . ($index->gambar ?? 'no-image.png')) }}" 
                                                        alt="{{ $index->nama_barang }}" 
                                                        class="img-fluid rounded" 
                                                        onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="text-center">Tracking Peminjaman</h5>
                                        <ul class="timeline">
                                            @foreach($riwayatPeminjaman as $track)
                                            <li>
                                                <p><strong>{{ $track->nama_peminjam }}</strong></p>
                                                <p>{{ \Carbon\Carbon::parse($track->created_at)->diffForHumans() }}</p>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success" onclick="return printDetail({{ $index->id }})">Cetak</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal fade" id="tambahBarangModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">TAMBAH BARANG MASUK</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="{{ route('barangMasuk.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="datetime-local" name="tanggal" class="form-control" 
                                   value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i:s') }}" 
                                   required>
                        </div>

                        <div class="form-group position-relative">
                            <label>Nama Barang</label>
                            <input type="text" 
                                   id="nama_barang" 
                                   name="nama_barang" 
                                   class="form-control" 
                                   placeholder="Cari nama barang..." 
                                   autocomplete="off" 
                                   required>
                            <div id="searchResults" class="autocomplete-suggestions" style="display: none;"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label>Gambar Barang</label>
                            <div class="image-preview mb-2" id="imagePreview">
                                <img id="preview" src="#" alt="Preview" style="display: none; max-width: 100%; max-height: 200px;">
                                <div id="placeholder-text" class="text-center">
                                    <p class="mb-0">Gambar akan muncul saat barang dipilih</p>
                                </div>
                            </div>
                            <input type="file" name="gambar_baru" class="form-control" style="display: none;">
                            <input type="hidden" name="gambar_default" id="selected_gambar">
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="ganti_gambar">
                                Ganti Gambar
                            </button>
                        </div>

                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="number" name="jumlah" class="form-control" required min="1">
                        </div>
                        
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="deskripsi" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <input type="text" name="admin" class="form-control mb-2" 
                                   value="{{ Auth::user()->name }}" readonly>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    @foreach($barangMasuk as $item)
    <div class="modal fade" id="edit{{ $item->id }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">EDIT BARANG MASUK</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="{{ route('barangMasuk.update', $item->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="datetime-local" name="tanggal" 
                                   value="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d\TH:i:s') }}" 
                                   class="form-control" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Nama Barang</label>
                            <select name="nama_barang" class="form-control" required>
                                @foreach($barang as $brg)
                                    <option value="{{ $brg->namaBarang }}" {{ $brg->namaBarang == $item->nama_barang ? 'selected' : '' }}>
                                        {{ $brg->namaBarang }} (Stok: {{ $brg->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="number" name="jumlah" value="{{ $item->jumlah }}" class="form-control" required min="1">
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="deskripsi" class="form-control" required>{{ $item->deskripsi }}</textarea>
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
                    <h4 class="modal-title">HAPUS BARANG MASUK</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="{{ route('barangMasuk.destroy', $item->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Apakah anda yakin ingin menghapus data barang masuk <strong>{{ $item->nama_barang }}</strong>?</p>
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

<style>
.autocomplete-suggestions {
    border: 1px solid #ddd;
    background: #fff;
    cursor: pointer;
    overflow: auto;
    max-height: 200px;
    position: absolute;
    width: 100%;
    z-index: 9999;
}
.autocomplete-suggestion {
    padding: 8px 15px;
    white-space: nowrap;
    overflow: hidden;
}
.autocomplete-suggestion:hover {
    background: #f0f0f0;
}
.not-found {
    padding: 8px 15px;
    color: #dc3545;
}
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

.table img {
    max-width: 100px;
    height: auto;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.table td {
    vertical-align: middle;
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
.btn-info:hover {
    background-color: #559ae0;
    border-color: #d39e00;
    font-size: 15px;
}

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

@push('scripts')
<script>
    function printDetail(id) {
        // Buat window baru untuk pencetakan
        let printWindow = window.open('', '_blank');
        let printContent = document.querySelector(`#detail${id} #print-area`).innerHTML;
        
        // Tambahkan beberapa style untuk hasil cetak yang lebih baik
        printWindow.document.write(`
            <html>
                <head>
                    <title>Detail Barang Masuk</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .row { display: flex; margin: 10px 0; }
                        .col-md-6 { width: 50%; padding: 10px; }
                        img { max-width: 300px; }
                        p { margin: 5px 0; }
                        h4 { margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <h4 class="text-center">Detail Barang Masuk</h4>
                    ${printContent}
                </body>
            </html>
        `);
        
        // Tunggu sampai gambar dimuat
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            printWindow.onafterprint = function() {
                printWindow.close();
            };
        };
        
        return false; // Mencegah refresh halaman
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('nama_barang');
        const searchResults = document.getElementById('searchResults');
        const preview = document.getElementById('preview');
        const placeholderText = document.getElementById('placeholder-text');
        const selectedGambar = document.getElementById('selected_gambar');
        const gantiGambarBtn = document.getElementById('ganti_gambar');
        const gambarBaruInput = document.querySelector('input[name="gambar_baru"]');
        let timeoutId;



        searchInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            const query = this.value;
            
            if (query.length < 1) {
                searchResults.style.display = 'none';
                return;
            }

            timeoutId = setTimeout(() => {
                fetch(`/search-barang?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        searchResults.style.display = 'block';

                        if (data.length === 0) {
                            searchResults.innerHTML = '<div class="not-found">Barang tidak ditemukan</div>';
                            preview.style.display = 'none';
                            placeholderText.style.display = 'block';
                            selectedGambar.value = '';
                            return;
                        }

                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-suggestion';
                            div.textContent = `${item.namaBarang} (Stok: ${item.stok})`;
                            div.style.cursor = 'pointer';
                            
                            div.addEventListener('click', function() {
                                searchInput.value = item.namaBarang;
                                
                                // Update gambar preview
                                if (item.gambar) {
                                    preview.src = `/storage/gambar/${item.gambar}`;
                                    preview.style.display = 'block';
                                    placeholderText.style.display = 'none';
                                    selectedGambar.value = item.gambar;
                                } else {
                                    preview.style.display = 'none';
                                    placeholderText.style.display = 'block';
                                    selectedGambar.value = '';
                                }

                                searchResults.style.display = 'none';
                            });
                            
                            searchResults.appendChild(div);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        searchResults.innerHTML = '<div class="not-found">Terjadi kesalahan</div>';
                    });
            }, 300);
        });

        // Sembunyikan hasil pencarian saat klik di luar
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });

        // Handler untuk tombol ganti gambar
        gantiGambarBtn.addEventListener('click', function() {
            gambarBaruInput.style.display = 'block';
            gambarBaruInput.click();
        });

        // Preview gambar yang diupload
        gambarBaruInput.addEventListener('change', function(e) {
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
    });
</script>
@endpush
@endsection
