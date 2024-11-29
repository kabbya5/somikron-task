<?php

namespace App\Jobs;

use App\Models\PDFJob;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use setasign\Fpdi\Fpdi;
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

            $directory = storage_path('app/pdfs');

            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $totalEmployees = DB::select("
                SELECT COUNT(*) as total FROM employees
            ")[0]->total;


            $chunkSize = 400;

            DB::table('employees')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->leftJoin('salaries', 'employees.id', '=', 'salaries.employee_id')
                ->leftJoin('attendances', 'employees.id', '=', 'attendances.employee_id')
                ->leftJoin('leaves', 'employees.id', '=', 'leaves.employee_id')
                ->leftJoin('performances', 'employees.id', '=', 'performances.employee_id')
                ->leftJoin('promotions', 'employees.id', '=', 'promotions.employee_id')
                ->select([
                    'employees.id',
                    'employees.name',
                    'employees.position',
                    'departments.name AS department_name',
                    'salaries.base_salary',
                    DB::raw("COUNT(CASE WHEN attendances.status = 'Present' THEN attendances.id END) AS attendance_count"),
                    DB::raw("COUNT(CASE WHEN attendances.status = 'Absent' THEN attendances.id END) AS absent_count"),
                    DB::raw("ROUND(AVG(CASE WHEN attendances.status = 'Present' THEN attendances.present_at END), 2) AS average_present"),
                    DB::raw("ROUND(AVG(CASE WHEN attendances.status = 'Present' THEN attendances.leave_at END), 2) AS average_leave"),
                    DB::raw("COUNT(DISTINCT leaves.id) AS leave_count"),
                    DB::raw("ROUND(AVG(performances.score), 2) AS average_score"),
                    DB::raw("COUNT(DISTINCT promotions.id) AS promotion_count"),
                ])
                ->groupBy('employees.id', 'employees.name',
                 'employees.position','departments.name', 'salaries.base_salary')
                ->orderBy('employees.id')
                ->chunk($chunkSize, function ($employees,$chunkIndex) use ($directory,$chunkSize, $pdfJob,$totalEmployees) {
                    $pdfContent = view('pdf', compact('employees'))->render();
                    $pdf = Pdf::loadHTML($pdfContent);
                    $pdfPath = "{$directory}/chunk_{$pdfJob->id}_{$chunkIndex}.pdf";
                    $pdf->save($pdfPath);

                    $processedEmployees = ($chunkIndex * $chunkSize) + count($employees);
                    $progress = ceil(($processedEmployees / $totalEmployees) * 100);
                    $pdfJob->progress = $progress;
                    $pdfJob->save();
                });

            $this->mergePDFs($directory,$pdfJob);

        } catch (Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());

            if (isset($pdfJob)) {
                $pdfJob->status = 'failed';
                $pdfJob->save();
            }
        }
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
        $pdfJob->progress = 100;
        $pdfJob->save();

        foreach ($pdfFiles as $file) {
            unlink($file);
        }
    }
}
