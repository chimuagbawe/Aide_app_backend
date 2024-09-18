<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reviews extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with ServiceProvider
    public function serviceProvider()
    {
        return $this->belongsTo(service_providers::class);
    }

    public function review_images()
    {
        return $this->hasMany(review_images::class);
    }
}