<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class service_provider_availability extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function serviceProvider()
    {
        return $this->belongsTo(service_providers::class);
    }
}