<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class BarangKeluar
{
    protected $table = 'barang_keluars';

    public static function getAll()
    {
        return DB::table('barang_keluars')
            ->select('barang_keluars.*', 'users.name as user_name')
            ->leftJoin('users', 'barang_keluars.user_id', '=', 'users.id')
            ->get();
    }

    public static function find($id)
    {
        return DB::table('barang_keluars')->where('id', $id)->first();
    }

    public static function create($data)
    {
        return DB::table('barang_keluars')->insert($data);
    }

    public static function updateData($id, $data)
    {
        return DB::table('barang_keluars')->where('id', $id)->update($data);
    }

    public static function deleteData($id)
    {
        return DB::table('barang_keluars')->where('id', $id)->delete();
    }

    public static function getByUserId($userId)
    {
        return DB::table('barang_keluars')->where('user_id', $userId)->get();
    }

    public static function getTotalByDate($date)
    {
        return DB::table('barang_keluars')
            ->whereDate('tanggal', $date)
            ->sum('jumlah');
    }

    public static function getByNamaBarang($namaBarang)
    {
        return DB::table('barang_keluars')
            ->where('nama_barang', $namaBarang)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public static function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        return DB::table('barang_keluars')->where($column, $operator, $value)->get();
    }

    public static function count()
    {
        return DB::table('barang_keluars')->count();
    }
}
