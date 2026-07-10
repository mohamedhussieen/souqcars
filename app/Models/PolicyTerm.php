<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Represents a single bilingual clause of the app's terms & conditions. */
class PolicyTerm extends Model
{
    protected $fillable = ['order', 'title_ar', 'title_en', 'body_ar', 'body_en'];
}
