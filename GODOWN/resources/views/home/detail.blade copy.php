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

    <!-- Card Varian -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="hasVariant" 
                           {{ $barang->has_variant ? 'checked' : '' }}>
                    <label class="custom-control-label" for="hasVariant">Memiliki Varian</label>
                </div>
            </div>

            <!-- Bagian yang muncul ketika switch aktif -->
            <div id="variantSection" style="display: {{ $barang->has_variant ? 'block' : 'none' }};">
                <div class="mb-3">
                    <h6 class="mb-2">Pilihan Atribut</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" id="addAttributeBtn">
                            <i class="fas fa-plus"></i> Tambah Atribut
                        </button>
                        <button type="button" class="btn btn-outline-success" id="saveVariantsBtn" disabled>
                            Simpan
                        </button>
                        <button type="button" class="btn btn-primary" id="addVariantsBtn" disabled>
                            Tambahkan
                        </button>
                    </div>
                </div>

                <!-- Form untuk menambah varian baru -->
                <div id="newVariantForm">
                    <!-- Form Atribut -->
                    <div id="attributeFormsContainer">
                        <!-- Form akan ditambahkan di sini -->
                    </div>

                    <!-- Template untuk form atribut -->
                    <template id="attributeFormTemplate">
                        <div class="attribute-form mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="dropdown">
                                        <input type="text" class="form-control attribute-input" 
                                               placeholder="Nama Atribut"
                                               autocomplete="off">
                                        <div class="dropdown-menu attribute-suggestions w-100">
                                            <!-- Suggestions will be added here -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="input-group">
                                        <input type="text" class="form-control option-input" 
                                               placeholder="Ketik opsi dan tekan Enter">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary add-option-btn" type="button">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tags-container mt-2"></div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-attribute">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Tabel Varian -->
                <div id="variantTableContainer" style="display: {{ $barang->has_variant ? 'block' : 'none' }};">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Varian</th>
                                <th>Kuantitas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="variantTableBody">
                            @foreach($barang->variants as $variant)
                            <tr>
                                <td class="variant-value">
                                    @if($variant->attribute_4)
                                        {{ $variant->value_1 }} • {{ $variant->value_2 }} • {{ $variant->value_3 }} • {{ $variant->value_4 }}
                                    @elseif($variant->attribute_3)
                                        {{ $variant->value_1 }} • {{ $variant->value_2 }} • {{ $variant->value_3 }}
                                    @elseif($variant->attribute_2)
                                        {{ $variant->value_1 }} • {{ $variant->value_2 }}
                                    @else
                                        {{ $variant->value_1 }}
                                    @endif
                                </td>
                                <td>
                                    <input type="number" class="form-control quantity-input" 
                                           value="{{ $variant->quantity }}" min="0"
                                           data-id="{{ $variant->id }}"
                                           {{ !Auth::user()->isAdmin() ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    @if(Auth::user()->isAdmin())
                                    <button type="button" class="btn btn-danger btn-sm delete-variant" 
                                            data-id="{{ $variant->id }}"
                                            data-toggle="tooltip" title="Hapus">
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
                <li class="nav-item">
                    <a class="nav-link" id="pinjam-tab" data-toggle="tab" href="#pinjam" role="tab">
                        Peminjaman
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

                <!-- Peminjaman -->
                <div class="tab-pane fade" id="pinjam" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Peminjam</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($peminjaman as $pinjam)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        @if($pinjam->tanggal_kembali)
                                            {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d/m/Y H:i:s') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $pinjam->nama_peminjam }}</td>
                                    <td>
                                        @if($pinjam->status == 'dipinjam')
                                            <span class="badge bg-warning">Dipinjam</span>
                                        @else
                                            <span class="badge bg-success">Dikembalikan</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data peminjaman</td>
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

/* Style untuk varian */
.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.tag {
    background-color: #e9ecef;
    border-radius: 3px;
    padding: 2px 8px;
    display: flex;
    align-items: center;
    font-size: 0.9em;
}

.tag .remove-tag {
    margin-left: 5px;
    cursor: pointer;
    color: #dc3545;
}

.custom-switch {
    padding-left: 2.25rem;
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #28a745;
    border-color: #28a745;
}

.gap-2 {
    gap: 0.5rem;
}

.dropdown-menu {
    display: none;
    position: absolute;
    background-color: white;
    border: 1px solid #ddd;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const barangId = {{ $barang->id }};
    const hasVariant = {{ $barang->has_variant ? 'true' : 'false' }};
    const attributeFormsContainer = document.getElementById('attributeFormsContainer');
    const attributeFormTemplate = document.getElementById('attributeFormTemplate');
    const hasVariantSwitch = document.getElementById('hasVariant');
    const variantSection = document.getElementById('variantSection');
    
    // Variabel untuk menyimpan state
    let variants = [];
    const maxAttributes = 4;
    let existingVariantQuantities = new Map();

    // Simpan kuantitas varian yang sudah ada
    @foreach($barang->variants as $variant)
        let variantKey = '';
        @if($variant->attribute_4)
            variantKey = `${variant->value_1} • ${variant->value_2} • ${variant->value_3} • ${variant->value_4}`;
        @elseif($variant->attribute_3)
            variantKey = `${variant->value_1} • ${variant->value_2} • ${variant->value_3}`;
        @elseif($variant->attribute_2)
            variantKey = `${variant->value_1} • ${variant->value_2}`;
        @else
            variantKey = `${variant->value_1}`;
        @endif
        existingVariantQuantities.set(variantKey, {{ $variant->quantity }});
    @endforeach

    // Toggle switch handler
    if (hasVariantSwitch) {
        hasVariantSwitch.addEventListener('change', function(e) {
            e.preventDefault(); // Prevent default behavior
            
            if (this.checked) {
                // Kirim request ke server untuk mengaktifkan varian
                fetch(`/home/${barangId}/variants/enable`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        variantSection.style.display = 'block';
                        if (attributeFormsContainer.children.length === 0) {
                            addAttributeForm();
                        }
                    } else {
                        throw new Error(data.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                    this.checked = false;
                });
            } else {
                if (confirm('Apakah Anda yakin ingin menonaktifkan varian?')) {
                    // Kirim request ke server untuk menonaktifkan varian
                    fetch(`/home/${barangId}/variants/disable`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            variantSection.style.display = 'none';
                            resetVariantForms();
                            location.reload();
                        } else {
                            throw new Error(data.message || 'Unknown error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan: ' + error.message);
                        this.checked = true;
                    });
                } else {
                    this.checked = true;
                }
            }
        });
    }

    // Event handler untuk tombol ADD/Update
    const addNewVariantBtn = document.getElementById('addNewVariantBtn');
    const newVariantForm = document.getElementById('newVariantForm');
    
    if (addNewVariantBtn) {
        addNewVariantBtn.addEventListener('click', function() {
            newVariantForm.style.display = 'block';
            this.style.display = 'none';
            
            // Load varian yang sudah ada
            const existingVariants = @json($barang->variants);
            if (existingVariants.length > 0) {
                // Kumpulkan semua nilai unik untuk setiap atribut
                const uniqueValues = {
                    attribute_1: new Set(),
                    attribute_2: new Set(),
                    attribute_3: new Set(),
                    attribute_4: new Set()
                };
                
                existingVariants.forEach(variant => {
                    if (variant.value_1) uniqueValues.attribute_1.add(variant.value_1);
                    if (variant.value_2) uniqueValues.attribute_2.add(variant.value_2);
                    if (variant.value_3) uniqueValues.attribute_3.add(variant.value_3);
                    if (variant.value_4) uniqueValues.attribute_4.add(variant.value_4);
                });
                
                // Tambahkan form untuk setiap atribut yang ada
                Object.keys(uniqueValues).forEach((attr, index) => {
                    if (uniqueValues[attr].size > 0) {
                        addAttributeForm();
                        const form = attributeFormsContainer.children[index];
                        const input = form.querySelector('.attribute-input');
                        input.value = existingVariants[0][`attribute_${index + 1}`];
                        uniqueValues[attr].forEach(value => {
                            addTag(form, value);
                        });
                    }
                });
            }
        });
    }

    // Event handler untuk tombol Save
    const saveVariantsBtn = document.getElementById('saveVariantsBtn');
    if (saveVariantsBtn) {
        saveVariantsBtn.addEventListener('click', function() {
            if (!this.disabled && confirm('Apakah Anda yakin ingin menyimpan perubahan varian?')) {
                updateVariantTable();
                document.getElementById('variantTableContainer').style.display = 'block';
                newVariantForm.style.display = 'none';
                document.getElementById('addAttributeBtn').disabled = true;
                
                // Reset tombol Simpan dan enable tombol Tambahkan
                this.classList.remove('btn-success');
                this.classList.add('btn-outline-success');
                this.disabled = true;
                document.getElementById('addVariantsBtn').disabled = false;
            }
        });
    }

    // Event handler untuk tombol Tambahkan
    const addVariantsBtn = document.getElementById('addVariantsBtn');
    if (addVariantsBtn) {
        addVariantsBtn.addEventListener('click', function() {
            if (!this.disabled && confirm('Apakah Anda yakin ingin menambahkan varian?')) {
                // ... existing update logic ...
            }
        });
    }

    // Fungsi untuk mengecek duplikasi tag
    function isTagDuplicate(container, value) {
        const existingTags = Array.from(container.querySelectorAll('.tag'))
            .map(tag => tag.textContent.trim().replace('×', '').trim());
        return existingTags.some(tag => tag.toLowerCase() === value.toLowerCase());
    }

    // Fungsi untuk menambah form atribut
    function addAttributeForm() {
        const totalForms = attributeFormsContainer.children.length;
        if (totalForms >= maxAttributes) {
            alert('Maksimal 4 atribut yang diperbolehkan');
            return;
        }

        const template = attributeFormTemplate.content.cloneNode(true);
        const form = template.querySelector('.attribute-form');
        
        // Event handlers untuk input atribut
        const attributeInput = form.querySelector('.attribute-input');
        const suggestionsMenu = form.querySelector('.attribute-suggestions');

        attributeInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            const suggestions = existingAttributes.filter(attr => 
                attr.toLowerCase().includes(value)
            );

            // Tampilkan dropdown suggestions
            suggestionsMenu.innerHTML = '';
            if (value && suggestions.length > 0) {
                suggestions.forEach(suggestion => {
                    const item = document.createElement('div');
                    item.className = 'dropdown-item';
                    item.textContent = suggestion;
                    item.addEventListener('click', function() {
                        attributeInput.value = suggestion;
                        suggestionsMenu.classList.remove('show');
                        checkVariantChanges();
                    });
                    suggestionsMenu.appendChild(item);
                });
                suggestionsMenu.classList.add('show');
            } else {
                suggestionsMenu.classList.remove('show');
            }
        });

        // Sembunyikan dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            if (!attributeInput.contains(e.target)) {
                suggestionsMenu.classList.remove('show');
            }
        });

        // Event handlers untuk input opsi
        const optionInput = form.querySelector('.option-input');
        const addOptionBtn = form.querySelector('.add-option-btn');
        const tagsContainer = form.querySelector('.tags-container');

        function addTag(value) {
            if (!value.trim()) return;
            
            // Cek duplikasi (case insensitive)
            if (isTagDuplicate(tagsContainer, value.trim())) {
                alert('Tag ini sudah ada!');
                return;
            }
            
            const tag = document.createElement('span');
            tag.className = 'tag';
            tag.innerHTML = `
                ${value}
                <span class="remove-tag">&times;</span>
            `;
            
            tag.querySelector('.remove-tag').addEventListener('click', function() {
                tag.remove();
                checkVariantChanges();
            });
            
            tagsContainer.appendChild(tag);
            optionInput.value = '';
            checkVariantChanges();
        }

        optionInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTag(this.value);
            }
        });

        addOptionBtn.addEventListener('click', function() {
            addTag(optionInput.value);
        });

        // Event handler untuk tombol hapus atribut
        form.querySelector('.remove-attribute').addEventListener('click', function() {
            form.remove();
            checkVariantChanges();
        });

        attributeFormsContainer.appendChild(form);
    }

    // Update tombol aksi
    function updateActionButtons() {
        const totalForms = attributeFormsContainer.children.length;
        const addMoreAttributeBtn = document.getElementById('addMoreAttributeBtn');
        if (addMoreAttributeBtn) {
            addMoreAttributeBtn.style.display = totalForms < maxAttributes ? 'block' : 'none';
        }
    }

    // Update tabel varian
    function updateVariantTable() {
        const forms = attributeFormsContainer.querySelectorAll('.attribute-form');
        const attributes = [];

        forms.forEach(form => {
            const attributeName = form.querySelector('.attribute-input').value.trim();
            const tags = Array.from(form.querySelectorAll('.tag'))
                .map(tag => tag.textContent.trim().replace('×', '').trim());
            
            if (attributeName && tags.length > 0) {
                attributes.push({ type: attributeName, values: tags });
            }
        });

        if (attributes.length === 0) {
            document.getElementById('variantTableContainer').style.display = 'none';
            return;
        }

        // Generate kombinasi varian
        variants = generateVariants(attributes);
        const tbody = document.getElementById('variantTableBody');
        tbody.innerHTML = '';

        variants.forEach((variant, index) => {
            const variantText = variant.map(v => v.value).join(' • ');
            const existingQuantity = existingVariantQuantities.get(variantText) || 0;
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${variantText}</td>
                <td>
                    <input type="number" class="form-control quantity-input" 
                           min="0" value="${existingQuantity}" data-index="${index}">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm delete-variant" 
                            data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Generate kombinasi varian
    function generateVariants(attributes) {
        if (attributes.length === 0) return [];
        if (attributes.length === 1) {
            return attributes[0].values.map(value => [{
                type: attributes[0].type,
                value: value
            }]);
        }

        const result = [];
        const firstAttr = attributes[0];
        const remainingAttrs = attributes.slice(1);
        const subVariants = generateVariants(remainingAttrs);

        firstAttr.values.forEach(value => {
            if (subVariants.length === 0) {
                result.push([{
                    type: firstAttr.type,
                    value: value
                }]);
            } else {
                subVariants.forEach(subVariant => {
                    result.push([{
                        type: firstAttr.type,
                        value: value
                    }, ...subVariant]);
                });
            }
        });

        return result;
    }

    // Reset form varian
    function resetVariantForms() {
        attributeFormsContainer.innerHTML = '';
        variants = [];
        document.getElementById('variantTableContainer').style.display = 'none';
    }

    // Fungsi untuk menambah tag ke form yang sudah ada
    function addTag(form, value) {
        const tagsContainer = form.querySelector('.tags-container');
        const tag = document.createElement('span');
        tag.className = 'tag';
        tag.innerHTML = `
            ${value}
            <span class="remove-tag">&times;</span>
        `;
        
        tag.querySelector('.remove-tag').addEventListener('click', function() {
            tag.remove();
        });
        
        tagsContainer.appendChild(tag);
    }

    // Fungsi untuk mengecek duplikasi atribut
    function isAttributeDuplicate(value) {
        const existingAttributes = Array.from(attributeFormsContainer.querySelectorAll('.attribute-input'))
            .map(input => input.value.trim());
        return existingAttributes.some(attr => attr.toLowerCase() === value.toLowerCase());
    }

    // Fungsi untuk menambah form atribut
    function addAttributeForm() {
        const totalForms = attributeFormsContainer.children.length;
        if (totalForms >= maxAttributes) {
            alert('Maksimal 4 atribut yang diperbolehkan');
            return;
        }

        const template = attributeFormTemplate.content.cloneNode(true);
        const form = template.querySelector('.attribute-form');
        
        // Event handlers untuk input atribut
        const attributeInput = form.querySelector('.attribute-input');
        const suggestionsMenu = form.querySelector('.attribute-suggestions');

        attributeInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            const suggestions = existingAttributes.filter(attr => 
                attr.toLowerCase().includes(value)
            );

            // Tampilkan dropdown suggestions
            suggestionsMenu.innerHTML = '';
            if (value && suggestions.length > 0) {
                suggestions.forEach(suggestion => {
                    const item = document.createElement('div');
                    item.className = 'dropdown-item';
                    item.textContent = suggestion;
                    item.addEventListener('click', function() {
                        attributeInput.value = suggestion;
                        suggestionsMenu.classList.remove('show');
                        checkVariantChanges();
                    });
                    suggestionsMenu.appendChild(item);
                });
                suggestionsMenu.classList.add('show');
            } else {
                suggestionsMenu.classList.remove('show');
            }
        });

        // Sembunyikan dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            if (!attributeInput.contains(e.target)) {
                suggestionsMenu.classList.remove('show');
            }
        });

        // Event handlers untuk input opsi
        const optionInput = form.querySelector('.option-input');
        const addOptionBtn = form.querySelector('.add-option-btn');
        const tagsContainer = form.querySelector('.tags-container');

        function addTag(value) {
            if (!value.trim()) return;
            
            // Cek duplikasi (case insensitive)
            if (isTagDuplicate(tagsContainer, value.trim())) {
                alert('Tag ini sudah ada!');
                return;
            }
            
            const tag = document.createElement('span');
            tag.className = 'tag';
            tag.innerHTML = `
                ${value}
                <span class="remove-tag">&times;</span>
            `;
            
            tag.querySelector('.remove-tag').addEventListener('click', function() {
                tag.remove();
                checkVariantChanges();
            });
            
            tagsContainer.appendChild(tag);
            optionInput.value = '';
            checkVariantChanges();
        }

        optionInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTag(this.value);
            }
        });

        addOptionBtn.addEventListener('click', function() {
            addTag(optionInput.value);
        });

        // Event handler untuk tombol hapus atribut
        form.querySelector('.remove-attribute').addEventListener('click', function() {
            form.remove();
            checkVariantChanges();
        });

        attributeFormsContainer.appendChild(form);
    }

    // Fungsi untuk mengecek perubahan varian
    function checkVariantChanges() {
        const saveBtn = document.getElementById('saveVariantsBtn');
        const addVariantsBtn = document.getElementById('addVariantsBtn');
        const forms = attributeFormsContainer.querySelectorAll('.attribute-form');
        let hasValidVariants = false;

        forms.forEach(form => {
            const attributeName = form.querySelector('.attribute-input').value.trim();
            const tags = form.querySelectorAll('.tag');
            if (attributeName && tags.length > 0) {
                hasValidVariants = true;
            }
        });

        if (hasValidVariants) {
            saveBtn.classList.remove('btn-outline-success');
            saveBtn.classList.add('btn-success');
            saveBtn.disabled = false;
        } else {
            saveBtn.classList.remove('btn-success');
            saveBtn.classList.add('btn-outline-success');
            saveBtn.disabled = true;
        }

        // Enable tombol Tambahkan hanya jika tabel varian ditampilkan
        addVariantsBtn.disabled = !document.getElementById('variantTableContainer').style.display === 'block';
    }

    // Tambahkan event listener untuk input dan tags
    attributeFormsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('attribute-input') || e.target.classList.contains('option-input')) {
            checkVariantChanges();
        }
    });

    // Modifikasi fungsi addTag untuk memanggil checkVariantChanges
    function addTag(value) {
        if (!value.trim()) return;
        
        const tag = document.createElement('span');
        tag.className = 'tag';
        tag.innerHTML = `
            ${value}
            <span class="remove-tag">&times;</span>
        `;
        
        tag.querySelector('.remove-tag').addEventListener('click', function() {
            tag.remove();
            checkVariantChanges();
        });
        
        tagsContainer.appendChild(tag);
        optionInput.value = '';
        checkVariantChanges();
    }

    // Daftar atribut yang sudah ada di database
    const existingAttributes = {!! json_encode(
        \App\Models\Variant::select('attribute_1', 'attribute_2', 'attribute_3', 'attribute_4')
            ->whereNotNull('attribute_1')
            ->get()
            ->map(function($variant) {
                return array_filter([
                    $variant->attribute_1,
                    $variant->attribute_2,
                    $variant->attribute_3,
                    $variant->attribute_4
                ]);
            })
            ->flatten()
            ->unique()
            ->values()
            ->all()
    ) !!};

    // Event handler untuk tombol Tambah Atribut
    const addAttributeBtn = document.getElementById('addAttributeBtn');
    if (addAttributeBtn) {
        addAttributeBtn.addEventListener('click', function() {
            // Cek apakah tombol bisa diklik
            if (document.getElementById('variantTableContainer').style.display === 'block') {
                return; // Jangan lakukan apa-apa jika tabel sudah ditampilkan
            }
            addAttributeForm();
        });
    }
});
</script>
@endpush
@endsection 