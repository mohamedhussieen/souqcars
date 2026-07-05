<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Represents an Egyptian city available for selection in the marketplace. */
class City extends Model
{
    protected $fillable = ['name_ar', 'name_en'];
}
