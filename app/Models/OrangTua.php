<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tuas';

    protected $fillable = [
        'nama', 'no_hp', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'orang_tua_siswa','orang_tua_id');
    }
}
