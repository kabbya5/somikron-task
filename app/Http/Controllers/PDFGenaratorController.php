<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFGenaratorController extends Controller
{
    public function index(){
        $employees = Employee::with([
            'department',
            'salary',
            'attendances',
            'leaves',
            'performances',
            'promotions'
        ])->take(100)->get();
        return view('welcome', compact('employees'));
    }

    public function pdfGenerate(){
        $employees = Employee::with(['department', 'salary', 'attendances', 'leaves'])->take(100)->get();
        $pdf = Pdf::loadView('pdf', compact('employees'));
        return $pdf->stream('employees.pdf');
    }
}
