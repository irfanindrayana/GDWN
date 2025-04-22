const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
const port = 3002;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Data storage (in-memory untuk contoh)
let products = [];
let nextId = 1;

// GET /products - Mendapatkan semua produk
app.get('/products', (req, res) => {
    res.json(products);
});

// GET /products/:id - Mendapatkan produk berdasarkan ID
app.get('/products/:id', (req, res) => {
    const product = products.find(p => p.id === parseInt(req.params.id));
    if (!product) {
        return res.status(404).json({ error: 'Produk tidak ditemukan' });
    }
    res.json(product);
});

// POST /products - Membuat produk baru
app.post('/products', (req, res) => {
    const { name, description, category } = req.body;
    
    if (!name || !description || !category) {
        return res.status(400).json({ error: 'Semua field harus diisi' });
    }

    const product = {
        id: nextId++,
        name,
        description,
        category,
        createdAt: new Date()
    };

    products.push(product);
    res.status(201).json(product);
});

// DELETE /products/:id - Menghapus produk
app.delete('/products/:id', (req, res) => {
    const index = products.findIndex(p => p.id === parseInt(req.params.id));
    if (index === -1) {
        return res.status(404).json({ error: 'Produk tidak ditemukan' });
    }

    products.splice(index, 1);
    res.status(204).send();
});

// Error handling
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: 'Terjadi kesalahan pada server' });
});

// Start server
app.listen(port, () => {
    console.log(`Product service berjalan di port ${port}`);
}); 