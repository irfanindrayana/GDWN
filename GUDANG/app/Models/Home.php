<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Home
{
    protected $table = 'home';

    public static function getAll()
    {
        return DB::table('home')->get();
    }

    public static function find($id)
    {
        return DB::table('home')->where('id', $id)->first();
    }

    public static function create($data)
    {
        return DB::table('home')->insert($data);
    }

    public static function updateData($id, $data)
    {
        return DB::table('home')->where('id', $id)->update($data);
    }

    public static function deleteData($id)
    {
        return DB::table('home')->where('id', $id)->delete();
    }

    public static function getWithVariants($id)
    {
        $barang = DB::table('home')->where('id', $id)->first();
        if ($barang) {
            $barang->variants = DB::table('variants')
                ->where('barang_id', $id)
                ->get();
        }
        return $barang;
    }

    public static function getLowStock()
    {
        return DB::table('home')
            ->whereRaw('stok <= stok_minimal')
            ->get();
    }

    public static function updateStok($id, $stok)
    {
        return DB::table('home')
            ->where('id', $id)
            ->update(['stok' => $stok]);
    }

    public static function count()
    {
        return DB::table('home')->count();
    }

    public static function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        return DB::table('home')->where($column, $operator, $value)->get();
    }

    public static function latest()
    {
        return DB::table('home')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function orWhere($column, $operator = null, $value = null)
    {
        return DB::table('home')
            ->orWhere($column, $operator, $value)
            ->get();
    }
}
