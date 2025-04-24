// Konfigurasi Service URLs
const CONFIG = {
    INVENTORY_SERVICE: 'http://localhost:3001',
    PRODUCT_SERVICE: 'http://localhost:3002',
    TRANSACTION_SERVICE: 'http://localhost:3003'
};

// Fungsi untuk menampilkan section
function showSection(sectionName) {
    // Sembunyikan semua section
    document.getElementById('inventory-section').style.display = 'none';
    document.getElementById('products-section').style.display = 'none';
    document.getElementById('transactions-section').style.display = 'none';

    // Tampilkan section yang dipilih
    document.getElementById(`${sectionName}-section`).style.display = 'block';

    // Update active state pada menu
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[onclick="showSection('${sectionName}')"]`).classList.add('active');

    // Refresh data sesuai section
    if (sectionName === 'inventory') loadInventoryData();
    if (sectionName === 'products') loadProductData();
    if (sectionName === 'transactions') loadTransactionData();
}

// Fungsi-fungsi untuk Product Service
async function loadProductData() {
    try {
        const response = await fetch(`${CONFIG.PRODUCT_SERVICE}/products`);
        const products = await response.json();
        
        const productList = document.getElementById('product-list');
        productList.innerHTML = products.map(product => `
            <tr>
                <td>${product.id}</td>
                <td>${product.name}</td>
                <td>${product.description}</td>
                <td>${product.category}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Hapus</button>
                </td>
            </tr>
        `).join('');

        // Update product selects
        updateProductSelects(products);
    } catch (error) {
        console.error('Error loading products:', error);
        alert('Gagal memuat data produk');
    }
}

async function createProduct(event) {
    event.preventDefault();
    
    const productData = {
        name: document.getElementById('product-name').value,
        description: document.getElementById('product-description').value,
        category: document.getElementById('product-category').value
    };

    try {
        const response = await fetch(`${CONFIG.PRODUCT_SERVICE}/products`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(productData)
        });

        if (response.ok) {
            alert('Produk berhasil ditambahkan');
            event.target.reset();
            loadProductData();
        } else {
            throw new Error('Gagal menambahkan produk');
        }
    } catch (error) {
        console.error('Error creating product:', error);
        alert('Gagal menambahkan produk');
    }
}

// Fungsi-fungsi untuk Inventory Service
async function loadInventoryData() {
    try {
        const response = await fetch(`${CONFIG.INVENTORY_SERVICE}/inventory`);
        const inventory = await response.json();
        
        const inventoryList = document.getElementById('inventory-list');
        inventoryList.innerHTML = inventory.map(item => `
            <tr>
                <td>${item.id}</td>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>${item.location}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="deleteInventoryItem(${item.id})">Hapus</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading inventory:', error);
        alert('Gagal memuat data inventaris');
    }
}

async function createInventoryItem(event) {
    event.preventDefault();
    
    const inventoryData = {
        product_id: document.getElementById('product-select').value,
        quantity: document.getElementById('quantity').value,
        location: document.getElementById('location').value
    };

    try {
        const response = await fetch(`${CONFIG.INVENTORY_SERVICE}/inventory`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(inventoryData)
        });

        if (response.ok) {
            alert('Item inventaris berhasil ditambahkan');
            event.target.reset();
            loadInventoryData();
        } else {
            throw new Error('Gagal menambahkan item inventaris');
        }
    } catch (error) {
        console.error('Error creating inventory item:', error);
        alert('Gagal menambahkan item inventaris');
    }
}

// Fungsi-fungsi untuk Transaction Service
async function loadTransactionData() {
    try {
        const response = await fetch(`${CONFIG.TRANSACTION_SERVICE}/transactions`);
        const transactions = await response.json();
        
        const transactionList = document.getElementById('transaction-list');
        transactionList.innerHTML = transactions.map(transaction => `
            <tr>
                <td>${transaction.id}</td>
                <td>${new Date(transaction.date).toLocaleString()}</td>
                <td>${transaction.product_name}</td>
                <td>${transaction.type === 'in' ? 'Masuk' : 'Keluar'}</td>
                <td>${transaction.quantity}</td>
                <td>${transaction.notes}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading transactions:', error);
        alert('Gagal memuat data transaksi');
    }
}

async function createTransaction(event) {
    event.preventDefault();
    
    const transactionData = {
        product_id: document.getElementById('transaction-product').value,
        type: document.getElementById('transaction-type').value,
        quantity: document.getElementById('transaction-quantity').value,
        notes: document.getElementById('transaction-notes').value
    };

    try {
        const response = await fetch(`${CONFIG.TRANSACTION_SERVICE}/transactions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(transactionData)
        });

        if (response.ok) {
            alert('Transaksi berhasil dicatat');
            event.target.reset();
            loadTransactionData();
            // Refresh inventory data karena stok berubah
            loadInventoryData();
        } else {
            throw new Error('Gagal mencatat transaksi');
        }
    } catch (error) {
        console.error('Error creating transaction:', error);
        alert('Gagal mencatat transaksi');
    }
}

// Fungsi helper untuk mengupdate select product
function updateProductSelects(products) {
    const productSelects = ['product-select', 'transaction-product'];
    
    productSelects.forEach(selectId => {
        const select = document.getElementById(selectId);
        select.innerHTML = '<option value="">Pilih Produk</option>' + 
            products.map(product => `
                <option value="${product.id}">${product.name}</option>
            `).join('');
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('product-form').addEventListener('submit', createProduct);
    document.getElementById('inventory-form').addEventListener('submit', createInventoryItem);
    document.getElementById('transaction-form').addEventListener('submit', createTransaction);
    
    // Load initial data
    loadProductData();
}); 

// Dark Mode Toggle
const darkModeSwitch = document.getElementById('darkModeSwitch');

// Check for saved theme preference
if (localStorage.getItem('theme') === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
    darkModeSwitch.checked = true;
}

// Handle theme switch
darkModeSwitch.addEventListener('change', function() {
    if (this.checked) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
        localStorage.setItem('theme', 'light');
    }
});