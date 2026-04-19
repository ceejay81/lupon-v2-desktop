<?php

namespace App\Http\Controllers;

use App\Models\LuponCase;
use App\Models\Setting;

class DocumentExportController extends Controller
{
    private function caseWithData(int $id): LuponCase
    {
        return LuponCase::with([
            'complainants',
            'respondents',
            'pangkats.chairperson',
            'pangkats.secretary',
            'pangkats.member',
            'hearings',
        ])->findOrFail($id);
    }

    private function settings(): array
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    private function getDocumentContent(int $caseId, string $type): ?string
    {
        return \App\Models\Document::where('lupon_case_id', $caseId)
            ->where('document_type', $type)
            ->value('content');
    }

    public function saveContent(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'case_id' => 'required|exists:lupon_cases,id',
            'type' => 'required|string',
            'content' => 'required|string',
        ]);

        $document = \App\Models\Document::firstOrCreate(
            [
                'lupon_case_id' => $request->case_id,
                'document_type' => $request->type,
            ],
            [
                'filename' => $request->type,
                'file_path' => 'digital_record',
                'uploaded_by' => auth()->id() ?? 1,
            ]
        );

        $document->setAttribute('content', $request->input('content'));
        $document->save();

        return response()->json(['success' => true]);
    }

    public function noticeOfHearing(int $caseId)
    {
        $case = $this->caseWithData($caseId);
        $settings = $this->settings();
        $savedContent = $this->getDocumentContent($caseId, 'Notice of Hearing (KP Form 8)');

        return view('documents.notice-of-hearing', compact('case', 'settings', 'savedContent'));
    }

    public function summon(int $caseId)
    {
        $case = $this->caseWithData($caseId);
        $settings = $this->settings();
        $savedContent = $this->getDocumentContent($caseId, 'Summons (KP Form 9)');

        return view('documents.summon', compact('case', 'settings', 'savedContent'));
    }

    public function invitationNotice(int $caseId)
    {
        $case = $this->caseWithData($caseId);
        $settings = $this->settings();
        $savedContent = $this->getDocumentContent($caseId, 'Invitation Notice');

        return view('documents.invitation-notice', compact('case', 'settings', 'savedContent'));
    }

    public function amicableSettlement(int $caseId)
    {
        $case = $this->caseWithData($caseId);
        $settings = $this->settings();
        $savedContent = $this->getDocumentContent($caseId, 'Amicable Settlement (KP Form 16)');

        return view('documents.amicable-settlement', compact('case', 'settings', 'savedContent'));
    }

    public function kasabutan(int $caseId)
    {
        $case = $this->caseWithData($caseId);
        $settings = $this->settings();
        $savedContent = $this->getDocumentContent($caseId, 'Kasabutan');

        return view('documents.kasabutan', compact('case', 'settings', 'savedContent'));
    }

    public function certificateToFileAction(int $caseId)
    {
        $case = $this->caseWithData($caseId);
        $settings = $this->settings();
        $savedContent = $this->getDocumentContent($caseId, 'Certification to File Action (KP Form 20)');

        return view('documents.certificate-to-file-action', compact('case', 'settings', 'savedContent'));
    }

    public function statusOfCase(int $caseId)
    {
        $case = $this->caseWithData($caseId);
        $settings = $this->settings();
        $savedContent = $this->getDocumentContent($caseId, 'Status of Case');

        return view('documents.status-of-case', compact('case', 'settings', 'savedContent'));
    }

    public function endorsement(int $caseId)
    {
        $case = $this->caseWithData($caseId);
        $settings = $this->settings();
        $savedContent = $this->getDocumentContent($caseId, 'endorsement');

        return view('documents.endorsement', compact('case', 'settings', 'savedContent'));
    }
}
