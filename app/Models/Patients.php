<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    protected $table = "patients";

    protected $fillable = [
        "user_id",
        "first_name",
        "last_name",
        "cpf",
        "cpf_hash",
        "address_id",
        "phone_number",
        "date_birth",
        "active"
    ];

    protected $casts = [
        'cpf' => 'encrypted'
    ];
}
