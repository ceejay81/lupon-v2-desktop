<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\LuponCase;
use App\Services\DocumentOrganizer;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentManager extends Component
{
    use WithFileUploads;

    public $case_id;

    public $document_type = '';

    public $file;

    public $remarks = '';

    public $available_docs = [
        'Complaint (KP Form 7)',
        'Notice of Hearing (KP Form 8)',
        'Summons (KP Form 9)',
        'Subpoena (KP Form 13)',
        'Amicable Settlement (KP Form 16)',
        'Repudiation (KP Form 17)',
        'Notice of Execution (KP Form 18)',
        'Certification to File Action (KP Form 20)',
        'Certification to Bar Action (KP Form 21)',
        'Certification to Bar Counterclaim (KP Form 22)',
        'Others',
    ];

    public function mount($case_id = null)
    {
        $this->case_id = $case_id;
    }

    public function getActiveCasesProperty()
    {
        return LuponCase::orderBy('case_number', 'desc')->get();
    }

    public function getSelectedCaseProperty()
    {
        return $this->case_id ? LuponCase::with('documents')->find($this->case_id) : null;
    }

    public function getCompletenessProperty()
    {
        if (! $this->selectedCase) {
            return 0;
        }

        return app(DocumentOrganizer::class)->getCompleteness($this->selectedCase)['percentage'];
    }

    public function updatedCaseId()
    {
        $this->document_type = '';
        $this->file = null;
        $this->remarks = '';
    }

    public function uploadDocument()
    {
        $this->validate([
            'case_id' => 'required|exists:lupon_cases,id',
            'document_type' => 'required|string',
            'file' => 'required|file|max:10240', // 10MB Max
            'remarks' => 'nullable|string|max:255',
        ]);

        /** @var \Illuminate\Http\UploadedFile|null $uploadedFile */
        $uploadedFile = $this->file;

        if (! $uploadedFile) {
            $this->addError('file', 'File failed to upload.');

            return;
        }

        $originalName = $uploadedFile->getClientOriginalName();
        $path = $uploadedFile->store('case-documents', 'public');

        Document::create([
            'lupon_case_id' => $this->case_id,
            'document_type' => $this->document_type,
            'filename' => $originalName,
            'file_path' => $path,
            'uploaded_by' => auth()->id() ?? 1,
            'remarks' => $this->remarks,
        ]);

        $this->file = null;
        $this->document_type = '';
        $this->remarks = '';

        $this->dispatch('toast', type: 'success', message: 'Document uploaded successfully.');
    }

    public function deleteDocument($id)
    {
        $doc = Document::find($id);
        if ($doc) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
            $this->dispatch('toast', type: 'warning', message: 'Document deleted successfully.');
        }
    }

    public function render()
    {
        return view('livewire.document-manager');
    }
}
