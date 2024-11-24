<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'promotion_date', 'new_position', 'salary_increment'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
