<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nurses extends Model
{
    protected $table = "nurses";

    protected $fillable = [
        "user_id",
        "first_name",
        "last_name",
        "specialtie_id",
        "cpf",
        "cpf_hash",
        "address_id",
        "coren",
        "phone_number",
        "date_birth",
        "termination_date",
        "active",
    ];
}
