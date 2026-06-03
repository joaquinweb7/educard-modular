<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AcademicAssignment;

class DropdownController extends Controller
{
    public function getSemesters(Request $request)
    {
        $careerId = $request->get('career_id');
        if (!$careerId) return response()->json([]);

        $semestersIds = collect(\App\Models\Student::where('career_id', $careerId)->whereNotNull('semester_id')->pluck('semester_id'))
            ->merge(\App\Models\StudentRequest::where('career_id', $careerId)->whereNotNull('semester_id')->pluck('semester_id'))
            ->merge(\App\Models\AcademicAssignment::where('career_id', $careerId)->where('status', 'active')->pluck('semester_id'))
            ->unique()
            ->filter();

        $semesters = \App\Models\Semester::whereIn('id', $semestersIds)->orderBy('number')->get(['id', 'name']);
        
        return response()->json($semesters);
    }

    public function getGestions(Request $request)
    {
        $careerId = $request->get('career_id');
        $semesterId = $request->get('semester_id');
        if (!$careerId || !$semesterId) return response()->json([]);

        $gestions = collect(\App\Models\Student::where('career_id', $careerId)->where('semester_id', $semesterId)->whereNotNull('gestion')->pluck('gestion'))
            ->merge(\App\Models\StudentRequest::where('career_id', $careerId)->where('semester_id', $semesterId)->whereNotNull('gestion')->pluck('gestion'))
            ->merge(\App\Models\AcademicAssignment::where('career_id', $careerId)->where('semester_id', $semesterId)->where('status', 'active')->pluck('gestion'))
            ->unique()
            ->sort()
            ->values();

        return response()->json($gestions);
    }

    public function getTurnos(Request $request)
    {
        $careerId = $request->get('career_id');
        $semesterId = $request->get('semester_id');
        $gestion = $request->get('gestion');
        if (!$careerId || !$semesterId || !$gestion) return response()->json([]);

        $turnos = collect(\App\Models\Student::where('career_id', $careerId)->where('semester_id', $semesterId)->where('gestion', $gestion)->whereNotNull('turno')->pluck('turno'))
            ->merge(\App\Models\StudentRequest::where('career_id', $careerId)->where('semester_id', $semesterId)->where('gestion', $gestion)->whereNotNull('turno')->pluck('turno'))
            ->merge(\App\Models\AcademicAssignment::where('career_id', $careerId)->where('semester_id', $semesterId)->where('gestion', $gestion)->where('status', 'active')->pluck('turno'))
            ->unique()
            ->sort()
            ->values();

        return response()->json($turnos);
    }

    public function getGrupos(Request $request)
    {
        $careerId = $request->get('career_id');
        $semesterId = $request->get('semester_id');
        $gestion = $request->get('gestion');
        $turno = $request->get('turno');
        if (!$careerId || !$semesterId || !$gestion || !$turno) return response()->json([]);

        $grupos = collect(\App\Models\Student::where('career_id', $careerId)->where('semester_id', $semesterId)->where('gestion', $gestion)->where('turno', $turno)->whereNotNull('grupo')->pluck('grupo'))
            ->merge(\App\Models\StudentRequest::where('career_id', $careerId)->where('semester_id', $semesterId)->where('gestion', $gestion)->where('turno', $turno)->whereNotNull('grupo')->pluck('grupo'))
            ->merge(\App\Models\AcademicAssignment::where('career_id', $careerId)->where('semester_id', $semesterId)->where('gestion', $gestion)->where('turno', $turno)->where('status', 'active')->pluck('grupo'))
            ->unique()
            ->sort()
            ->values();

        return response()->json($grupos);
    }
}
