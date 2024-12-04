<?php

namespace App\Models\MasterData;

use App\Models\BaseModel;

class Siswa extends BaseModel
{
    protected $table = 'siswa';
    protected $primaryKey = 'nisn';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "nisn",
        "nama_siswa",
        "jenis_kelamin",
        "created_at",
        "update_at",
    ];
}
