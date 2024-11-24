<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'base_salary', 'bonus', 'deductions', 'effective_date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
