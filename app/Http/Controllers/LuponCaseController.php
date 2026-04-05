<?php

namespace App\Http\Controllers;

use App\Models\LuponCase;

class LuponCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('cases.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cases.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(LuponCase $case): \Illuminate\View\View
    {
        $case->load([
            'complainantCitizen',
            'respondentCitizen',
            'pangkats.chairperson',
            'pangkats.secretary',
            'pangkats.member',
            'hearings.attendances',
            'documents',
            'statusHistories.changedBy',
        ]);

        $slaData = $case->sla_data;

        $citizensLinked = $case->complainant_id !== null || $case->respondent_id !== null;

        if (! $citizensLinked) {
            $relatedCases = collect();
            $relatedCasesTotal = 0;
        } else {
            $relatedCasesQuery = LuponCase::query()
                ->select(['id', 'case_number', 'nature_of_case', 'status', 'filed_date'])
                ->where(function ($query) use ($case) {
                    $query->where('complainant_id', $case->complainant_id)
                        ->orWhere('respondent_id', $case->respondent_id);
                })
                ->where('id', '!=', $case->id)
                ->orderBy('filed_date', 'desc');

            $relatedCasesTotal = $relatedCasesQuery->count();
            $relatedCases = $relatedCasesQuery->limit(5)->get();
        }

        return view('cases.show', compact('case', 'slaData', 'relatedCases', 'relatedCasesTotal', 'citizensLinked'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LuponCase $case)
    {
        return view('cases.edit', compact('case'));
    }

    /**
     * Update case fields from the document editor (AJAX).
     */
    public function updateFromDocument(\Illuminate\Http\Request $request, LuponCase $case): \Illuminate\Http\JsonResponse
    {
        $fields = $request->input('fields', []);

        if (empty($fields)) {
            return response()->json(['success' => false, 'message' => 'No fields provided.'], 400);
        }

        $allowedColumns = [
            'case_number', 'complainant', 'complainant_address',
            'respondent', 'respondent_address', 'nature_of_case',
        ];

        foreach ($fields as $field => $value) {
            if (in_array($field, $allowedColumns)) {
                $case->{$field} = $value;
            }
        }

        $case->save();

        return response()->json(['success' => true, 'message' => 'Case updated successfully.']);
    }

    /**
     * Toggle a progress step as completed (AJAX).
     */
    public function toggleStepCompletion(\Illuminate\Http\Request $request, LuponCase $case): \Illuminate\Http\JsonResponse
    {
        $step = $request->input('step');
        $isDone = $request->input('done', true);

        if (! $step) {
            return response()->json(['success' => false, 'message' => 'No step provided.'], 400);
        }

        $completedSteps = $case->completed_steps ?? [];

        $stepToDocType = [
            'status-of-case' => 'Status of Case',
            'invitation-notice' => 'Invitation Notice',
            'notice-of-hearing' => 'Notice of Hearing (KP Form 8)',
            'summon' => 'Summons (KP Form 9)',
            'amicable-settlement' => 'Amicable Settlement (KP Form 16)',
            'kasabutan' => 'Kasabutan',
            'certificate-to-file-action' => 'Certification to File Action (KP Form 20)',
        ];

        if ($isDone) {
            $completedSteps[$step] = now()->toDateTimeString();

            if (isset($stepToDocType[$step])) {
                \App\Models\Document::firstOrCreate(
                    ['lupon_case_id' => $case->id, 'document_type' => $stepToDocType[$step]],
                    [
                        'filename' => $stepToDocType[$step],
                        'file_path' => 'digital_record',
                        'uploaded_by' => auth()->id() ?? 1,
                    ]
                );
            }
        } else {
            unset($completedSteps[$step]);

            if (isset($stepToDocType[$step])) {
                \App\Models\Document::where('lupon_case_id', $case->id)
                    ->where('document_type', $stepToDocType[$step])
                    ->delete();
            }
        }

        $case->completed_steps = $completedSteps;
        $case->save();

        return response()->json([
            'success' => true,
            'message' => 'Step updated.',
            'completed_at' => $isDone ? ($completedSteps[$step] ?? null) : null,
        ]);
    }
}
