<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateEmployeePDF;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;

class PDFGenaratorController extends Controller
{
    public function index() {
        $employees = DB::select("
            SELECT
                employees.name,
                employees.position,
                departments.name AS department_name,
                salaries.base_salary,
                (
                    SELECT COUNT(DISTINCT attendances.id)
                    FROM attendances
                    WHERE attendances.employee_id = employees.id AND attendances.status = 'Present'
                ) AS attendance_count,
                (
                    SELECT COUNT(DISTINCT attendances.id)
                    FROM attendances
                    WHERE attendances.employee_id = employees.id AND attendances.status = 'Absent'
                ) AS absent_count,
                (
                    SELECT ROUND(AVG(present_at), 2)
                    FROM attendances
                    WHERE attendances.employee_id = employees.id AND attendances.status = 'Present'
                ) AS average_present,
                (
                    SELECT ROUND(AVG(leave_at), 2)
                    FROM attendances
                    WHERE attendances.employee_id = employees.id AND attendances.status = 'Present'
                ) AS average_leave,
                (
                    SELECT COUNT(DISTINCT leaves.id)
                    FROM leaves
                    WHERE leaves.employee_id = employees.id
                ) AS leave_count,

                (
                    SELECT ROUND(AVG(score), 2)
                    FROM performances
                    WHERE performances.employee_id = employees.id
                ) AS average_score,
                (
                    SELECT COUNT(DISTINCT promotions.id)
                    FROM promotions
                    WHERE promotions.employee_id = employees.id
                ) AS promotion_count
            FROM employees
            LEFT JOIN departments ON employees.department_id = departments.id
            LEFT JOIN salaries ON employees.id = salaries.employee_id
        ");

        return view('welcome', compact('employees'));
    }

    public function pdfGenerate(){

        // GenerateEmployeePDF::dispatch();
        $employees = DB::select("
        SELECT
            employees.name,
            employees.position,
            departments.name AS department_name,
            salaries.base_salary,
            (
                SELECT COUNT(DISTINCT attendances.id)
                FROM attendances
                WHERE attendances.employee_id = employees.id AND attendances.status = 'Present'
            ) AS attendance_count,
            (
                SELECT COUNT(DISTINCT attendances.id)
                FROM attendances
                WHERE attendances.employee_id = employees.id AND attendances.status = 'Absent'
            ) AS absent_count,
            (
                SELECT ROUND(AVG(present_at), 2)
                FROM attendances
                WHERE attendances.employee_id = employees.id AND attendances.status = 'Present'
            ) AS average_present,
            (
                SELECT ROUND(AVG(leave_at), 2)
                FROM attendances
                WHERE attendances.employee_id = employees.id AND attendances.status = 'Present'
            ) AS average_leave,
            (
                SELECT COUNT(DISTINCT leaves.id)
                FROM leaves
                WHERE leaves.employee_id = employees.id
            ) AS leave_count,

            (
                SELECT ROUND(AVG(score), 2)
                FROM performances
                WHERE performances.employee_id = employees.id
            ) AS average_score,
            (
                SELECT COUNT(DISTINCT promotions.id)
                FROM promotions
                WHERE promotions.employee_id = employees.id
            ) AS promotion_count
        FROM employees
        LEFT JOIN departments ON employees.department_id = departments.id
        LEFT JOIN salaries ON employees.id = salaries.employee_id

        LIMIT 400
    ");

        $pdf = Pdf::loadView('pdf', compact('employees'));

        return $pdf->stream('employees.pdf');
    }
}
