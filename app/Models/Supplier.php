<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'company_name',
        'business_type',
        'tax_id',
        'contact_person',
        'email',
        'phone',
        'mobile',
        'payment_terms',
        'credit_limit',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot([
                'vendor_product_code',
                'vendor_cost_price',
                'is_preferred',
                'lead_time_days',
            ])
            ->withTimestamps();
    }

    public function goodReceiveNotes()
    {
        return $this->hasMany(GoodReceiveNote::class);
    }
}
