<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Home;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        try {
            $homes = Home::all();
            return response()->json([
                'status' => 'success',
                'data' => $homes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch inventory items'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'namaBarang' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'stok' => 'required|numeric|min:0',
                'gambar' => 'nullable|image|max:2048'
            ]);

            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('public/gambar');
                $validated['gambar'] = basename($path);
            }

            $item = Home::create($validated);
            return response()->json([
                'status' => 'success',
                'message' => 'Item created successfully',
                'data' => $item
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create item'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = Home::findOrFail($id);
            $validated = $request->validate([
                'namaBarang' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'stok' => 'required|numeric|min:0',
                'gambar' => 'nullable|image|max:2048'
            ]);

            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('public/gambar');
                $validated['gambar'] = basename($path);
            }

            $item->update($validated);
            return response()->json([
                'status' => 'success',
                'message' => 'Item updated successfully',
                'data' => $item
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update item'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = Home::findOrFail($id);
            $item->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Item deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete item'
            ], 500);
        }
    }
}
