<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\LuponCase;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentManager extends Component
{
    use WithFileUploads;

    public int $case_id;

    public string $document_type = '';

    public $file = null;

    public string $remarks = '';

    public bool $showUploadForm = false;

    // Duplicate detection state
    public bool $showDuplicateModal = false;

    public ?int $duplicateDocumentId = null;

    /** @var array{id:int,filename:string,size:string,date:string,uploader:string}|null */
    public ?array $duplicateInfo = null;

    /** @var array{filename:string,size:string}|null */
    public ?array $pendingFileInfo = null;

    /** Common document type suggestions shown in the datalist. */
    public array $typeSuggestions = [
        'Complaint',
        'Notice of Hearing',
        'Summons',
        'Invitation Notice',
        'Amicable Settlement',
        'Kasabutan',
        'Certification to File Action',
        'Minutes of Hearing',
        'Attendance Sheet',
        'Barangay Certificate',
        'Subpoena',
        'Evidence / Exhibit',
        'Affidavit',
        'Other',
    ];

    public function mount(int $case_id): void
    {
        $this->case_id = $case_id;
    }

    public function getSelectedCaseProperty(): ?LuponCase
    {
        return LuponCase::with(['documents.uploader'])->find($this->case_id);
    }

    public function toggleUploadForm(): void
    {
        $this->showUploadForm = ! $this->showUploadForm;

        if (! $this->showUploadForm) {
            $this->resetUploadForm();
        }
    }

    public function resetUploadForm(): void
    {
        $this->document_type = '';
        $this->file = null;
        $this->remarks = '';
        $this->showDuplicateModal = false;
        $this->duplicateDocumentId = null;
        $this->duplicateInfo = null;
        $this->pendingFileInfo = null;
    }

    protected function checkForDuplicateFilename(int $caseId, string $filename): ?Document
    {
        return Document::where('lupon_case_id', $caseId)
            ->where('filename', $filename)
            ->first();
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'document_type' => 'required|string|max:100',
            'file' => [
                'required',
                'file',
                'max:20480',
                'mimes:pdf,docx,odt,doc,xlsx,xls,jpg,jpeg,png,gif,webp',
            ],
            'remarks' => 'nullable|string|max:255',
        ]);

        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $this->file;
        $filename = $uploadedFile->getClientOriginalName();

        $duplicate = $this->checkForDuplicateFilename($this->case_id, $filename);

        if ($duplicate) {
            $this->duplicateDocumentId = $duplicate->id;
            $this->duplicateInfo = [
                'id' => $duplicate->id,
                'filename' => $duplicate->filename,
                'size' => $duplicate->file_size_formatted,
                'date' => $duplicate->created_at->format('M d, Y · h:i A'),
                'uploader' => optional($duplicate->uploader)->name ?? 'Unknown',
            ];
            $this->pendingFileInfo = [
                'filename' => $filename,
                'size' => $this->formatBytes($uploadedFile->getSize()),
            ];
            $this->showDuplicateModal = true;

            return;
        }

        $this->performUpload($filename);
    }

    public function replaceDocument(): void
    {
        if (! $this->duplicateDocumentId || ! $this->file) {
            return;
        }

        $existing = Document::find($this->duplicateDocumentId);

        if ($existing) {
            if ($existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $existing->delete();
        }

        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $this->file;
        $this->performUpload($uploadedFile->getClientOriginalName());
        $this->showDuplicateModal = false;
    }

    public function saveAsNewDocument(): void
    {
        if (! $this->file) {
            return;
        }

        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $this->file;
        $filename = $this->generateUniqueFilename($this->case_id, $uploadedFile->getClientOriginalName());

        $this->performUpload($filename);
        $this->showDuplicateModal = false;
    }

    public function cancelDuplicateUpload(): void
    {
        $this->showDuplicateModal = false;
        $this->duplicateDocumentId = null;
        $this->duplicateInfo = null;
        $this->pendingFileInfo = null;
        $this->file = null;
        $this->dispatch('toast', type: 'warning', message: 'Upload cancelled.');
    }

    protected function performUpload(string $filename): void
    {
        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $this->file;
        $directory = "cases/{$this->case_id}/uploads";

        try {
            $path = $uploadedFile->store($directory, 'public');

            Document::create([
                'lupon_case_id' => $this->case_id,
                'document_type' => $this->document_type,
                'filename' => $filename,
                'file_path' => $path,
                'file_size' => $uploadedFile->getSize(),
                'mime_type' => $uploadedFile->getMimeType(),
                'uploaded_by' => auth()->id() ?? 1,
                'remarks' => $this->remarks,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Document upload failed', [
                'user_id' => auth()->id(),
                'case_id' => $this->case_id,
                'filename' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('toast', type: 'error', message: 'Upload failed due to a server error. Please try again or contact support.');

            return;
        }

        $this->resetUploadForm();
        $this->showUploadForm = false;
        $this->dispatch('toast', type: 'success', message: 'Document uploaded successfully.');
    }

    protected function generateUniqueFilename(int $caseId, string $originalFilename): string
    {
        $ext = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $base = pathinfo($originalFilename, PATHINFO_FILENAME);
        $counter = 2;

        $candidate = $originalFilename;
        while (Document::where('lupon_case_id', $caseId)->where('filename', $candidate)->exists()) {
            $candidate = $ext ? "{$base} {$counter}.{$ext}" : "{$base} {$counter}";
            $counter++;
        }

        return $candidate;
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '—';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }

    public function deleteDocument(int $id): void
    {
        $doc = Document::find($id);

        if ($doc && $doc->lupon_case_id === $this->case_id) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
            $this->dispatch('toast', type: 'warning', message: 'Document deleted.');
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.document-manager');
    }
}
