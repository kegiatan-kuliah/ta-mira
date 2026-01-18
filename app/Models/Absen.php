<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    protected $table = 'absens';

    protected $fillable = [
        'tanggal','jam','status','siswa_id','jadwal_pelajaran_id'
    ];
}
