<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'review_date', 'score', 'comments', 'reviewer'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
