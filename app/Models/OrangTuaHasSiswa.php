<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTuaHasSiswa extends Model
{
    protected $table = 'orang_tuas_has_siswas';

    protected $fillable = ['orang_tua_id, siswa_id'];
}
