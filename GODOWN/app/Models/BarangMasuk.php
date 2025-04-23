<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class BarangMasuk
{
    protected $table = 'barang_masuks';

    public static function getAll()
    {
        return DB::table('barang_masuks')
            ->select('barang_masuks.*', 'users.name as user_name')
            ->leftJoin('users', 'barang_masuks.user_id', '=', 'users.id')
            ->get();
    }

    public static function find($id)
    {
        return DB::table('barang_masuks')->where('id', $id)->first();
    }

    public static function create($data)
    {
        return DB::table('barang_masuks')->insert($data);
    }

    public static function updateData($id, $data)
    {
        return DB::table('barang_masuks')->where('id', $id)->update($data);
    }

    public static function deleteData($id)
    {
        return DB::table('barang_masuks')->where('id', $id)->delete();
    }

    public static function getByUserId($userId)
    {
        return DB::table('barang_masuks')->where('user_id', $userId)->get();
    }

    public static function getTotalByDate($date)
    {
        return DB::table('barang_masuks')
            ->whereDate('tanggal', $date)
            ->sum('jumlah');
    }

    public static function getByNamaBarang($namaBarang)
    {
        return DB::table('barang_masuks')
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
        return DB::table('barang_masuks')->where($column, $operator, $value)->get();
    }

    public static function count()
    {
        return DB::table('barang_masuks')->count();
    }
}
