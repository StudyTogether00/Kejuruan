<?php

namespace App\Models\Process;

use App\Models\BaseModel;

class HeaderNilai extends BaseModel
{
    protected $table = 'nilai_header';
    protected $primaryKey = ['tahun', 'nisn'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        "tahun",
        "nisn",
        "nilai_rata-rata",
        "created_at",
        "update_at",
    ];
}
