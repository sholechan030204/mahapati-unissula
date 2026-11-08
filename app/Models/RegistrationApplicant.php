<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationApplicant extends Model
{
    protected $fillable = ['full_name', 'student_number', 'study_program', 'cohort', 'semester', 'email', 'whatsapp'];
}
