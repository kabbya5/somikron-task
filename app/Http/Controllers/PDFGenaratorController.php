<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;

class PDFGenaratorController extends Controller
{
    public function index(){
        $employees = DB::select("
            SELECT
                employees.name,
                employees.position,
                departments.name AS department_name,
                salaries.base_salary,
                COUNT(DISTINCT attendances.id) AS attendance_count,
                COUNT(DISTINCT leaves.id) AS leave_count,
                COUNT(DISTINCT performances.id) AS performance_count,
                COUNT(DISTINCT promotions.id) AS promotion_count
            FROM employees
            LEFT JOIN departments ON employees.department_id = departments.id
            LEFT JOIN salaries ON employees.id = salaries.employee_id
            LEFT JOIN attendances ON employees.id = attendances.employee_id AND attendances.status = 'Present'
            LEFT JOIN leaves ON employees.id = leaves.employee_id
            LEFT JOIN performances ON employees.id = performances.employee_id
            LEFT JOIN promotions ON employees.id = promotions.employee_id
            GROUP BY employees.id, departments.name, salaries.base_salary, employees.name,employees.position
        ");

        return view('welcome', compact('employees'));
    }

    public function pdfGenerate(){
        $employees = Employee::with(['department', 'salary', 'attendances', 'leaves'])->get();
        $pdf = Pdf::loadView('pdf', compact('employees'));
        return $pdf->stream('employees.pdf');
    }
}
