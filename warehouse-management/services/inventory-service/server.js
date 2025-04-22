const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
const port = 3001;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Data storage (in-memory untuk contoh)
let inventory = [];
let nextId = 1;

// GET /inventory - Mendapatkan semua item inventaris
app.get('/inventory', async (req, res) => {
    try {
        // Dapatkan data produk untuk setiap item inventaris
        const inventoryWithProducts = await Promise.all(inventory.map(async (item) => {
            try {
                const productResponse = await fetch(`http://localhost:3002/products/${item.product_id}`);
                const product = await productResponse.json();
                return {
                    ...item,
                    product_name: product.name
                };
            } catch (error) {
                return {
                    ...item,
                    product_name: 'Produk tidak ditemukan'
                };
            }
        }));
        
        res.json(inventoryWithProducts);
    } catch (error) {
        res.status(500).json({ error: 'Gagal memuat data inventaris' });
    }
});

// GET /inventory/:id - Mendapatkan item inventaris berdasarkan ID
app.get('/inventory/:id', (req, res) => {
    const item = inventory.find(i => i.id === parseInt(req.params.id));
    if (!item) {
        return res.status(404).json({ error: 'Item tidak ditemukan' });
    }
    res.json(item);
});

// POST /inventory - Menambah item inventaris baru
app.post('/inventory', async (req, res) => {
    const { product_id, quantity, location } = req.body;
    
    if (!product_id || !quantity || !location) {
        return res.status(400).json({ error: 'Semua field harus diisi' });
    }

    try {
        // Validasi produk exists
        const productResponse = await fetch(`http://localhost:3002/products/${product_id}`);
        if (!productResponse.ok) {
            return res.status(400).json({ error: 'Produk tidak ditemukan' });
        }

        const item = {
            id: nextId++,
            product_id: parseInt(product_id),
            quantity: parseInt(quantity),
            location,
            createdAt: new Date(),
            updatedAt: new Date()
        };

        inventory.push(item);
        res.status(201).json(item);
    } catch (error) {
        res.status(500).json({ error: 'Gagal menambahkan item inventaris' });
    }
});

// PUT /inventory/:id - Update jumlah stok
app.put('/inventory/:id', (req, res) => {
    const { quantity } = req.body;
    const item = inventory.find(i => i.id === parseInt(req.params.id));
    
    if (!item) {
        return res.status(404).json({ error: 'Item tidak ditemukan' });
    }

    item.quantity = parseInt(quantity);
    item.updatedAt = new Date();
    
    res.json(item);
});

// DELETE /inventory/:id - Menghapus item inventaris
app.delete('/inventory/:id', (req, res) => {
    const index = inventory.findIndex(i => i.id === parseInt(req.params.id));
    if (index === -1) {
        return res.status(404).json({ error: 'Item tidak ditemukan' });
    }

    inventory.splice(index, 1);
    res.status(204).send();
});

// Error handling
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: 'Terjadi kesalahan pada server' });
});

// Start server
app.listen(port, () => {
    console.log(`Inventory service berjalan di port ${port}`);
}); 