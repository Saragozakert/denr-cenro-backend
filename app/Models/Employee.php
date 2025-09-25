<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'department',
        'position'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            // Generate employee ID if not provided
            if (empty($employee->employee_id)) {
                $employee->employee_id = 'DENR-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}