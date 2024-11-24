<?php

namespace App\Jobs;

use App\Models\PDFJob;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class GenerateEmployeePDF implements ShouldQueue
{
    use Queueable;

    private $pdf_id;

    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {
        $this->pdf_id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $pdfJob = PDFJob::find($this->pdf_id);

            if (!$pdfJob) {
                throw new Exception('PDF job not found');
            }

            $totalEmployees = 20;
            $chunkSize = 10;
            $pdfContent = '<h1 style="text-align: center;">Employee Attendance and Performance Report</h1>';
            $pdfContent .= '<table border="1" cellpadding="10" cellspacing="0" width="100%">';
            $pdfContent .= '<thead><tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Salary</th>
                                <th>Attendance Count</th>
                                <th>Absent Count</th>
                                <th>Avg. Present</th>
                                <th>Avg. Leave</th>
                                <th>Leave Count</th>
                                <th>Avg. Score</th>
                                <th>Promotion Count</th>
                            </tr></thead><tbody>';

                    DB::table('employees')
                    ->selectRaw("employees.name, employees.position, departments.name AS department_name, salaries.base_salary,
                                (SELECT COUNT(DISTINCT attendances.id) FROM attendances WHERE attendances.employee_id = employees.id AND attendances.status = 'Present') AS attendance_count,
                                (SELECT COUNT(DISTINCT attendances.id) FROM attendances WHERE attendances.employee_id = employees.id AND attendances.status = 'Absent') AS absent_count,
                                (SELECT ROUND(AVG(present_at), 2) FROM attendances WHERE attendances.employee_id = employees.id AND attendances.status = 'Present') AS average_present,
                                (SELECT ROUND(AVG(leave_at), 2) FROM attendances WHERE attendances.employee_id = employees.id AND attendances.status = 'Present') AS average_leave,
                                (SELECT COUNT(DISTINCT leaves.id) FROM leaves WHERE leaves.employee_id = employees.id) AS leave_count,
                                (SELECT ROUND(AVG(score), 2) FROM performances WHERE performances.employee_id = employees.id) AS average_score,
                                (SELECT COUNT(DISTINCT promotions.id) FROM promotions WHERE promotions.employee_id = employees.id) AS promotion_count")
                    ->join('departments', 'employees.department_id', '=', 'departments.id')
                    ->join('salaries', 'employees.id', '=', 'salaries.employee_id')
                    ->orderBy('employees.id')
                    ->limit(20)
                    ->chunkById($chunkSize, function ($employees) use (&$pdfContent, $pdfJob, $totalEmployees) {
                        foreach ($employees as $employee) {
                            $pdfContent .= '<tr>';
                            $pdfContent .= '<td>' . $employee->name . '</td>';
                            $pdfContent .= '<td>' . $employee->position . '</td>';
                            $pdfContent .= '<td>' . $employee->department_name . '</td>';
                            $pdfContent .= '<td>' . number_format($employee->base_salary, 2) . '</td>';
                            $pdfContent .= '<td>' . $employee->attendance_count . '</td>';
                            $pdfContent .= '<td>' . $employee->absent_count . '</td>';
                            $pdfContent .= '<td>' . $employee->average_present . '</td>';
                            $pdfContent .= '<td>' . $employee->average_leave . '</td>';
                            $pdfContent .= '<td>' . $employee->leave_count . '</td>';
                            $pdfContent .= '<td>' . $employee->average_score . '</td>';
                            $pdfContent .= '<td>' . $employee->promotion_count . '</td>';
                            $pdfContent .= '</tr>';
                        }

                        $processedEmployees = $pdfJob->progress + count($employees);
                        $pdfJob->progress = ceil(($processedEmployees / $totalEmployees) * 100);
                        $pdfJob->save();
                    });

                $pdfContent .= '</tbody></table>';

                // Create PDF
                $pdf = Pdf::loadHTML($pdfContent);
                $filePath = 'pdf_reports/employee_report_' . time() . '.pdf';
                $pdf->save(storage_path('app/' . $filePath));

                // Update the job with file path and status
                $pdfJob->status = 'completed';
                $pdfJob->file_path = $filePath;
                $pdfJob->save();

            } catch (Exception $e) {
                Log::error('Error generating PDF: ' . $e->getMessage());

                if (isset($pdfJob)) {
                    $pdfJob->status = 'failed';
                    $pdfJob->save();
                }
            }
    }
}
