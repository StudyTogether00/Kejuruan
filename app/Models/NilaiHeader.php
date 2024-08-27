<?php

namespace App\Models;

class NilaiHeader extends BaseModel
{
    protected $table = 'nilai_header';
    protected $primaryKey = ['tahun', 'nisn'];
    protected $keyType = ['integer', 'string'];
    public $incrementing = false;

    protected $fillable = [
        "tahun",
        "nisn",
        "nilai_rata-rata",
        "created_at",
        "update_at",
    ];
}