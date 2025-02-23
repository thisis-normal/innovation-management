<?php

namespace App\Http\Controllers;

use App\Models\GradingSession;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    public function testGrading(): JsonResponse
    {
        // Create a new grading session
        $session = GradingSession::query()->create([
            'year' => 2025,
            'description' => 'Q1 2024 Grading Session',
            'created_by' => auth()->user()->id ?? null
        ]);

        // Add grade ranges
        $session->gradeRanges()->createMany([
            [
                'grade_name' => 'Outstanding',
                'min_score' => 90,
                'max_score' => 100,
                'created_by' => auth()->user()->id ?? null
            ],
            [
                'grade_name' => 'Excellent',
                'min_score' => 80,
                'max_score' => 89.99,
                'created_by' => auth()->user()->id ?? null
            ]
        ]);

        // Query to get all grades for a specific year
        $grades = GradingSession::with('gradeRanges')
            ->where('year', 2025)
            ->get()
            ->map(function ($session) {
                return [
                    'year' => $session->year,
                    'session_number' => $session->session_number,
                    'description' => $session->description,
                    'grades' => $session->gradeRanges->map(function ($grade) {
                        return [
                            'name' => $grade->grade_name,
                            'min_score' => $grade->min_score,
                            'max_score' => $grade->max_score
                        ];
                    })
                ];
            });
        //return the grades
        return response()->json($grades);
    }
    public function testDeleteGrading()
    {
        $testObject = GradingSession::query()->where('id', 5)->first();
        try {
            $testObject->delete();
            return response()->json(['message' => 'Grading session deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete grading session']);
        }
    }
}
