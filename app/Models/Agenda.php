<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = "agendas";

    protected $fillable = [
        "nurses_id",
        "days_week",
        "start_time",
        "end_time",
        "active",
    ];
}
