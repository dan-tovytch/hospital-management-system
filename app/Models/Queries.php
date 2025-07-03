<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queries extends Model
{
    protected $table = "queries";

    protected $fillable = [
        "nurses_id",
        "patients_id",
        "date",
        "query_type",
    ];
}
