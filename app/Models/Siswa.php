<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswas';

    protected $fillable = [
        'nis','nama','qr_code','kelas_id','status'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function orangTuas()
    {
        return $this->belongsToMany(OrangTua::class, 'orang_tua_siswa', 'siswa_id');
    }
}
