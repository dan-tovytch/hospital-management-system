<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = "administrators";

    protected $fillable = [
        "user_id",
        "first_name",
        "last_name",
        "cpf",
        "cpf_hash",
        "address_id",
        "termination_date",
        "active",
    ];
}
