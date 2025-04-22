const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
const port = 3003;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Data storage (in-memory untuk contoh)
let transactions = [];
let nextId = 1;

// GET /transactions - Mendapatkan semua transaksi
app.get('/transactions', async (req, res) => {
    try {
        // Dapatkan data produk untuk setiap transaksi
        const transactionsWithProducts = await Promise.all(transactions.map(async (transaction) => {
            try {
                const productResponse = await fetch(`http://localhost:3002/products/${transaction.product_id}`);
                const product = await productResponse.json();
                return {
                    ...transaction,
                    product_name: product.name
                };
            } catch (error) {
                return {
                    ...transaction,
                    product_name: 'Produk tidak ditemukan'
                };
            }
        }));
        
        res.json(transactionsWithProducts);
    } catch (error) {
        res.status(500).json({ error: 'Gagal memuat data transaksi' });
    }
});

// POST /transactions - Membuat transaksi baru
app.post('/transactions', async (req, res) => {
    const { product_id, type, quantity, notes } = req.body;
    
    if (!product_id || !type || !quantity) {
        return res.status(400).json({ error: 'product_id, type, dan quantity harus diisi' });
    }

    try {
        // Validasi produk exists
        const productResponse = await fetch(`http://localhost:3002/products/${product_id}`);
        if (!productResponse.ok) {
            return res.status(400).json({ error: 'Produk tidak ditemukan' });
        }

        // Dapatkan inventaris produk
        const inventoryResponse = await fetch(`http://localhost:3001/inventory`);
        const inventory = await inventoryResponse.json();
        const inventoryItem = inventory.find(item => item.product_id === parseInt(product_id));

        // Validasi stok untuk transaksi keluar
        if (type === 'out') {
            if (!inventoryItem || inventoryItem.quantity < parseInt(quantity)) {
                return res.status(400).json({ error: 'Stok tidak mencukupi' });
            }
        }

        // Buat transaksi
        const transaction = {
            id: nextId++,
            product_id: parseInt(product_id),
            type,
            quantity: parseInt(quantity),
            notes: notes || '',
            date: new Date()
        };

        // Update inventaris
        const newQuantity = type === 'in' 
            ? (inventoryItem ? inventoryItem.quantity + parseInt(quantity) : parseInt(quantity))
            : (inventoryItem.quantity - parseInt(quantity));

        if (inventoryItem) {
            // Update existing inventory
            await fetch(`http://localhost:3001/inventory/${inventoryItem.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ quantity: newQuantity })
            });
        } else if (type === 'in') {
            // Create new inventory for incoming transaction
            await fetch('http://localhost:3001/inventory', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id,
                    quantity: parseInt(quantity),
                    location: 'Default'
                })
            });
        }

        transactions.push(transaction);
        res.status(201).json(transaction);
    } catch (error) {
        console.error('Error in transaction:', error);
        res.status(500).json({ error: 'Gagal memproses transaksi' });
    }
});

// Error handling
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: 'Terjadi kesalahan pada server' });
});

// Start server
app.listen(port, () => {
    console.log(`Transaction service berjalan di port ${port}`);
}); 