<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EmployeeSalary extends Model
{
    protected $table = 'employee_salaries';

    protected $fillable = [
        'user_id',
        'basic_salary',
        'epf',
        'etf',
        'ot_hours',
        'ot_rate',
        'total_salary',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
