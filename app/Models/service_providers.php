<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class service_providers extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function availability()
    {
        return $this->hasMany(service_provider_availability::class);
    }

    public function reviews()
    {
        return $this->hasMany(Reviews::class);
    }
}