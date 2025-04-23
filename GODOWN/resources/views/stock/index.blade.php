<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Barang</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css">

    <style>
        .table-img {
            max-width: 100px;
            height: auto;
            display: block;
            margin: auto;
        }
        .dt-buttons {
            margin-bottom: 5px;
        }
        .select-checkbox {
            width: 20px;
            height: 20px;
        }
        .export-disclaimer {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            margin-bottom: 15px;
            font-style: italic;
            clear: both;
        }
    </style>
</head>

<body>
<div class="container mt-5">
    <h2>Stock Bahan</h2>
    <h4>(Inventory)</h4>
    <div class="data-tables datatable-dark">
        <div id="export-buttons"></div>
        <div class="export-disclaimer">
            * Kolom gambar hanya tersedia pada export PDF
        </div>
        <table id="datatablesSimple" class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Gambar</th>
                    <th>Deskripsi</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach($homes as $index)
                <tr>
                    <td></td>
                    <td>{{ $index->id }}</td>
                    <td>{{ $index->namaBarang }}</td>
                    <td class="text-center">
                        @php
                            $imagePath = $index->gambar ? 'storage/gambar/' . $index->gambar : 'storage/gambar/no-image.png';
                            $fullImagePath = public_path($imagePath);
                            $imageData = '';
                            $mimeType = 'image/jpeg';
                            $imageSize = [0, 0];
                            
                            if (file_exists($fullImagePath)) {
                                $imageInfo = getimagesize($fullImagePath);
                                if ($imageInfo) {
                                    $mimeType = $imageInfo['mime'];
                                    $imageData = base64_encode(file_get_contents($fullImagePath));
                                    $imageSize = [$imageInfo[0], $imageInfo[1]];
                                }
                            }
                        @endphp
                        <img src="data:{{ $mimeType }};base64,{{ $imageData }}"
                            alt="{{ $index->namaBarang }}" 
                            class="table-img"
                            data-original-width="{{ $imageSize[0] }}"
                            data-original-height="{{ $imageSize[1] }}"
                            onerror="this.src='{{ asset('storage/gambar/no-image.png') }}';">
                    </td>
                    <td>{{ $index->deskripsi }}</td>
                    <td>{{ $index->stok }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Tambahkan tombol kembali di sini -->
    <div class="mt-3 text-left">
        <a href="{{ route('home.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Home
        </a>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>

<!-- DataTables Initialization -->
<script>
$(document).ready(function() {
    var table = $('#datatablesSimple').DataTable({
        dom: 'Bt',
        buttons: {
            dom: {
                container: {
                    className: 'dt-buttons'
                }
            },
            buttons: [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: [1, 2, 4, 5],
                        modifier: function(data) {
                            return table.rows({ selected: true }).indexes().length > 0 
                                ? table.rows({ selected: true }).indexes()
                                : table.rows().indexes();
                        }
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: [1, 2, 4, 5],
                        modifier: function(data) {
                            return table.rows({ selected: true }).indexes().length > 0 
                                ? table.rows({ selected: true }).indexes()
                                : table.rows().indexes();
                        }
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1, 2, 4, 5],
                        modifier: function(data) {
                            return table.rows({ selected: true }).indexes().length > 0 
                                ? table.rows({ selected: true }).indexes()
                                : table.rows().indexes();
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5],
                        modifier: function(data) {
                            return table.rows({ selected: true }).indexes().length > 0 
                                ? table.rows({ selected: true }).indexes()
                                : table.rows().indexes();
                        }
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 10;
                        doc.styles.tableHeader.fontSize = 12;
                        doc.styles.tableHeader.fillColor = '#2196F3';
                        doc.styles.tableHeader.color = '#FFFFFF';
                        
                        // Mengatur ukuran kolom
                        doc.content[1].table.widths = ['8%', '15%', '25%', '42%', '10%'];
                        
                        // Menambahkan border pada tabel
                        doc.content[1].table.body.forEach(function(row, rowIndex) {
                            row.forEach(function(cell, cellIndex) {
                                cell.border = [true, true, true, true];
                                cell.borderColor = '#000000';
                                cell.borderWidth = 1;
                            });
                        });

                        // Memastikan gambar tetap ada dengan ukuran yang proporsional
                        doc.content[1].table.body.forEach(function(row, rowIndex) {
                            if (rowIndex > 0) { // Skip header row
                                var imgElement = $('#datatablesSimple tbody tr').eq(rowIndex-1).find('img')[0];
                                if (imgElement && imgElement.src.startsWith('data:image')) {
                                    var originalWidth = parseInt(imgElement.dataset.originalWidth);
                                    var originalHeight = parseInt(imgElement.dataset.originalHeight);
                                    var maxWidth = 100;
                                    var width = originalWidth;
                                    var height = originalHeight;

                                    if (width > maxWidth) {
                                        var ratio = maxWidth / width;
                                        width = maxWidth;
                                        height = height * ratio;
                                    }

                                    row[2] = {
                                        image: imgElement.src,
                                        width: width,
                                        height: height,
                                        alignment: 'center',
                                        border: [true, true, true, true],
                                        borderColor: '#000000',
                                        borderWidth: 1,
                                        margin: [0, 5, 0, 5]
                                    };
                                }
                            }
                        });

                        doc.pageMargins = [20, 20, 20, 20];
                        
                        doc.styles.tableBodyEven = doc.styles.tableBodyOdd = {
                            alignment: 'center',
                            border: [true, true, true, true],
                            borderColor: '#000000',
                            borderWidth: 1,
                            padding: 5
                        };

                        doc.content[1].table.body.forEach(function(row) {
                            row.forEach(function(cell) {
                                if (typeof cell === 'object' && !cell.image) {
                                    cell.margin = [0, 5, 0, 5];
                                }
                            });
                        });
                    }
                }
            ]
        },
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }],
        select: {
            style: 'multi',
            selector: 'td:first-child'
        },
        order: [[1, 'asc']],
        searching: false,
        paging: false,
        info: false
    });

    // Memindahkan tombol export ke div khusus
    table.buttons().container().appendTo('#export-buttons');

    // Menghapus disabled pada tombol saat tidak ada yang dipilih
    table.on('select deselect', function() {
        table.buttons().enable();
    });
});
</script>

</body>
</html>