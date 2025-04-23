<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Variant
{
    protected $table = 'variants';

    public static function getAll()
    {
        return DB::table('variants')
            ->select('variants.*', 'homes.nama_barang')
            ->leftJoin('homes', 'variants.barang_id', '=', 'homes.id')
            ->get();
    }

    public static function find($id)
    {
        return DB::table('variants')->where('id', $id)->first();
    }

    public static function create($data)
    {
        return DB::table('variants')->insert($data);
    }

    public static function updateData($id, $data)
    {
        return DB::table('variants')->where('id', $id)->update($data);
    }

    public static function deleteData($id)
    {
        return DB::table('variants')->where('id', $id)->delete();
    }

    public static function getByBarangId($barangId)
    {
        return DB::table('variants')->where('barang_id', $barangId)->get();
    }

    public static function updateQuantity($id, $quantity)
    {
        return DB::table('variants')
            ->where('id', $id)
            ->update(['quantity' => $quantity]);
    }
} 