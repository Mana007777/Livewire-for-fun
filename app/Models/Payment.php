<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'employee_id',
        'salary_id',
        'amount',
        'payment_date',
        'method',
        'notes',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function scopeInCompany($query)
    {
        return $query->whereHas('employee', function ($q) {
            $q->inCompany();
        });
    }
}