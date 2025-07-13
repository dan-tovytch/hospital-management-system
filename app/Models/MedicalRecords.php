<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecords extends Model
{
    protected $table = "medical_records";

    protected $fillable = [
        "querie_id",
        "nurse_id",
        "patient_id",
        "diagnosis",
        "prescriptions",
        "obs",
    ];

    protected $casts = [
        'diagnosis' => 'encrypted',
        'prescriptions' => 'encrypted',
        'obs' => 'encrypted',
    ];
}
