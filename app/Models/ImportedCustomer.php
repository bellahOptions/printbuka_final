<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportedCustomer extends Model
{
    protected $fillable = [
        'external_customer_id',
        'customer_number',
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_code',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_code',
        'source',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function displayName(): string
    {
        return $this->name ?: trim($this->first_name.' '.$this->last_name) ?: 'Imported Customer';
    }
}
