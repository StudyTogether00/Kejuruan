<?php

namespace App\Models\MasterData;

use App\Models\BaseModel;

class Jurusan extends BaseModel
{
    protected $table = 'jurusan';
    protected $primaryKey = 'kd_jurusan';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "kd_jurusan",
        "nama_jurusan",
        "created_at",
        "update_at",
    ];
}
