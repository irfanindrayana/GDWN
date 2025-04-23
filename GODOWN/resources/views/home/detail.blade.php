@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
<!-- Tambahkan meta tag CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid px-4">
    <h1 class="mt-4 mb-3">DETAIL BARANG</h1>
    <div class="mb-4">
        <a href="{{ route('home.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        @if(Auth::user()->isAdmin())
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#stokMinimalModal">
                <i class="fas fa-exclamation-triangle"></i> Stok Minimal
            </button>
        @endif
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="image-container mb-3">
                        @if($barang->gambar && Storage::disk('public')->exists('gambar/' . $barang->gambar))
                            <img src="{{ asset('storage/gambar/' . $barang->gambar) }}" 
                                 alt="{{ $barang->namaBarang }}" 
                                 class="img-fluid rounded">
                        @else
                            <img src="{{ asset('storage/gambar/default.png') }}" 
                                 alt="Default" 
                                 class="img-fluid rounded">
                        @endif
                    </div>
                </div>
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">ID Barang</th>
                            <td>{{ $barang->id }}</td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td>{{ $barang->namaBarang }}</td>
                        </tr>
                        <tr>
                            <th>Stok</th>
                            <td>{{ $barang->stok }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $barang->deskripsi }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Histori Perubahan -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Histori Perubahan</h5>
        </div>
        <div class="card-body">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-3" id="historyTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="masuk-tab" data-toggle="tab" href="#masuk" role="tab">
                        Barang Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="keluar-tab" data-toggle="tab" href="#keluar" role="tab">
                        Barang Keluar
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="historyTabContent">
                <!-- Barang Masuk -->
                <div class="tab-pane fade show active" id="masuk" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangMasuk as $masuk)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($masuk->tanggal)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $masuk->jumlah }}</td>
                                    <td>{{ $masuk->deskripsi }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data barang masuk</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Barang Keluar -->
                <div class="tab-pane fade" id="keluar" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangKeluar as $keluar)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($keluar->tanggal)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $keluar->jumlah }}</td>
                                    <td>{{ $keluar->deskripsi }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data barang keluar</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                
            </div>
        </div>
    </div>

    <!-- Modal Stok Minimal -->
    <div class="modal fade" id="stokMinimalModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set Stok Minimal</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="{{ route('home.updateStokMinimal', $barang->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Stok Minimal</label>
                            <input type="number" name="stok_minimal" class="form-control" 
                                   value="{{ $barang->stok_minimal ?? 5 }}" min="1" required>
                            <small class="form-text text-muted">
                                Sistem akan memberikan peringatan ketika stok barang kurang dari nilai ini
                            </small>
                        </div>
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

<style>
.image-container {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
    background: #fff;
}

.image-container img {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: contain;
}

.table th {
    background-color: #f8f9fa;
}

.nav-tabs .nav-link {
    color: #495057;
}

.nav-tabs .nav-link.active {
    font-weight: bold;
}

.badge {
    padding: 0.5em 1em;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const barangId = {{ $barang->id }};
    const attributeFormsContainer = document.getElementById('attributeFormsContainer');
    const attributeFormTemplate = document.getElementById('attributeFormTemplate');
    
    // Variabel untuk menyimpan state
    let variants = [];
    const maxAttributes = 4;
    let existingVariantQuantities = new Map();
});
</script>
@endpush
@endsection
