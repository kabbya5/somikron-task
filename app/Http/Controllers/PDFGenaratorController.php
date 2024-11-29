<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateEmployeePDF;
use App\Models\PDFJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Tcpdf\Fpdi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;

class PDFGenaratorController extends Controller
{
    public function index() {
        $cacheKey = 'employees_with_attendance_performance';
        $employees = Cache::remember($cacheKey, 60, function () {
            return DB::select("
                SELECT
                    employees.name,
                    employees.position,
                    departments.name AS department_name,
                    salaries.base_salary,
                    (
                        SELECT COUNT(DISTINCT attendances.id)
                        FROM attendances
                        WHERE attendances.employee_id = employees.id
                            AND attendances.status = 'Present'
                    ) AS attendance_count,
                    (
                        SELECT COUNT(DISTINCT attendances.id)
                        FROM attendances
                        WHERE attendances.employee_id = employees.id
                            AND attendances.status = 'Absent'
                    ) AS absent_count,
                    (
                        SELECT ROUND(AVG(present_at), 2)
                        FROM attendances
                        WHERE attendances.employee_id = employees.id
                            AND attendances.status = 'Present'
                    ) AS average_present,
                    (
                        SELECT ROUND(AVG(leave_at), 2)
                        FROM attendances
                        WHERE attendances.employee_id = employees.id
                            AND attendances.status = 'Present'
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
                LIMIT 100
            ");
        });
        return view('welcome', compact('employees'));
    }
    public function pdfGenerate() {
        $pdfJob = PDFJob::create([
            'status' => 'pending',
            'progress' => 0,
        ]);

        GenerateEmployeePDF::dispatch($pdfJob->id);

        return response()->json(['status' => 'PDF generation started', 'job_id' => $pdfJob->id]);
    }

    public function checkStatus($jobId)
    {
        $job = PdfJob::find($jobId);

        if ($job) {
            return response()->json([
                'status' => $job->status,
                'file_path' => $job->file_path,
                'progress' => $job->progress,
            ]);
        }

        return response()->json(['status' => 'Job not found'], 404);
    }

    private function mergePDFs($directory, $pdfJob)
    {
        $pdfMerger = new Fpdi();

        $pdfFiles = glob("{$directory}/chunk_{$pdfJob->id}_*.pdf");

        foreach ($pdfFiles as $file) {
            $pageCount = $pdfMerger->setSourceFile($file);

            for ($page = 1; $page <= $pageCount; $page++) {
                $templateId = $pdfMerger->importPage($page);
                $pdfMerger->AddPage();
                $pdfMerger->useTemplate($templateId);
            }
        }

        $mergedPdfPath = "{$directory}/merged_report_{$pdfJob->id}.pdf";
        $pdfMerger->Output($mergedPdfPath, 'F');

        $pdfJob->status = 'completed';
        $pdfJob->file_path = $mergedPdfPath;
        $pdfJob->save();

        foreach ($pdfFiles as $file) {
            unlink($file);
        }
    }
}
