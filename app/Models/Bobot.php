<?php

namespace App\Models;

class Bobot extends BaseModel
{
    protected $table = 'bobot';
    protected $primaryKey = ['kd_jurusan', 'kd_matapelajaran'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "kd_jurusan",
        "nama_matapelajan",
        "bobot",
        "created_at",
        "update_at",
    ];
}