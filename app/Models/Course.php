<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['code','title','title_hi','duration','fee_amount','fee_note','level','summary','eligibility','modules','careers','is_active','sort_order'];
    protected $casts = ['fee_amount'=>'decimal:2','modules'=>'array','careers'=>'array','is_active'=>'boolean'];
}
