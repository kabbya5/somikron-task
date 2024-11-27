<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateEmployeePDF;
use App\Models\PDFJob;
use TCPDF;
use DB;
use Illuminate\Http\Request;

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
            LIMIT 40
        ");

        return view('welcome', compact('employees'));
    }
    public function pdfGenerate() {

        $pdf = new TCPDF();

        $pdf->SetCreator('Laravel');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Employee Report');
        $pdf->SetSubject('Employee Report');
        $pdf->SetKeywords('TCPDF, Laravel, PDF, Employee Report');
        $pdf->SetHeaderData('', 0, 'Employee Report', 'Generated using TCPDF');
        $pdf->setFooterData();
        $pdf->SetMargins(10, 20, 10);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();

        $pdfContent = '<h1 style="text-align: center;">Employee Report</h1>';
        $pdfContent .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody>';

        try {
            DB::table('employees')
                ->join('departments', 'employees.department_id', '=', 'departments.id')
                ->select('employees.name', 'employees.position', 'departments.name AS department_name')
                ->chunk(50, function($chunk) use ($pdf, &$pdfContent) {
                    foreach ($chunk as $employee) {
                        $pdfContent .= '<tr>';
                        $pdfContent .= '<td>' . htmlspecialchars($employee->name) . '</td>';
                        $pdfContent .= '<td>' . htmlspecialchars($employee->position) . '</td>';
                        $pdfContent .= '<td>' . htmlspecialchars($employee->department_name) . '</td>';
                        $pdfContent .= '</tr>';
                    }
                });

            $pdfContent .= '</tbody></table>';

            $pdf->writeHTML($pdfContent, true, false, true, false, '');

            $pdf->Output('employee-report.pdf', 'I');

        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            return response()->json(['error' => 'PDF generation failed. Please try again.'], 500);
        }
    }


}
