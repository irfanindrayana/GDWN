<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Member
{
    protected $table = 'members';

    public static function getAll()
    {
        return DB::table('members')->get();
    }

    public static function find($id)
    {
        return DB::table('members')->where('id', $id)->first();
    }

    public static function create($data)
    {
        return DB::table('members')->insert($data);
    }

    public static function updateData($id, $data)
    {
        return DB::table('members')->where('id', $id)->update($data);
    }

    public static function deleteData($id)
    {
        return DB::table('members')->where('id', $id)->delete();
    }
}
