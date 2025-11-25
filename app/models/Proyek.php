<?php

namespace App\Models;

use CodeIgniter\Model;

class ProyekModel extends Model
{
    protected $table      = 'proyek';
    protected $primaryKey = 'proyek_id';

    protected $allowedFields = [
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];
}
