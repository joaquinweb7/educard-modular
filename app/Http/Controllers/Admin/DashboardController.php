<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneratedCard;
use App\Models\Student;
use App\Models\StudentRequest;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index', [
            'totalRequests' => StudentRequest::count(),
            'pendingRequests' => StudentRequest::whereIn('status', ['pending', 'resubmitted'])->count(),
            'approvedRequests' => StudentRequest::where('status', 'approved')->count(),
            'students' => Student::count(),
            'generatedCards' => GeneratedCard::count(),
            'latestRequests' => StudentRequest::with(['career', 'semester'])->latest()->take(8)->get(),
        ]);
    }
}
