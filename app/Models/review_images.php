<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class review_images extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function reviews()
    {
        return $this->belongsTo(reviews::class);
    }
}