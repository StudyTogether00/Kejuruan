<?php

namespace App\Models;

class Matapelajaran extends BaseModel
{
    protected $table = 'matapelajaran';
    protected $primaryKey = 'kd_matapelajaran';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "kd_matapelajan",
        "nama_matapelajaran",
        "created_at",
        "update_at",
    ];
}