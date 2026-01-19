<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanAbsenRow extends Model
{
    protected $table = 'laporan_absen_rows'; // virtual (fromSub)
    
    protected $primaryKey = 'row_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;
}
