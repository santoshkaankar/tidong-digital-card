<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitingCard extends Model
{
    use HasFactory;

    // Sabhi fields ko ek sath save (Mass Assignment) karne ke liye
    protected $guarded = [];
}