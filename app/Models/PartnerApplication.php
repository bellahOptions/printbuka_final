<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerApplication extends Model
{
    protected $fillable = [
        'business_name',
        'contact_name',
        'email',
        'phone',
        'business_type',
        'city',
        'client_volume',
        'services_needed',
        'delivery_packaging_needs',
        'message',
        'status',
    ];
}
