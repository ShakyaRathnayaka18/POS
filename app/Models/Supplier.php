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
}
