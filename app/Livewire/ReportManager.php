<?php

namespace App\Livewire;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ReportManager extends Component
{
    use WithFileUploads, WithPagination;

    public $month;

    public $year;

    public $file = null;

    public string $report_type = '';

    public string $remarks = '';

    public string $search = '';

    public $filter_year = '';

    public $filter_month = '';

    public $filterType = '';

    public $isReplacing = false;

    public $reportToReplace = null;

    public string $replacingReportName = '';

    // Duplicate detection state
    public bool $showDuplicateModal = false;

    public ?int $duplicateReportId = null;

    /** @var array{id:int,filename:string,date:string,uploader:string,is_legacy:bool}|null */
    public ?array $duplicateInfo = null;

    /** @var array{filename:string,size:string}|null */
    public ?array $pendingFileInfo = null;

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterYear(): void
    {
        $this->resetPage();
    }

    public function updatedFilterMonth(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filter_year = '';
        $this->filter_month = '';
        $this->filterType = '';
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
    }

    protected function checkForDuplicateFilename(int $month, int $year, string $filename): ?Report
    {
        return Report::where('month', $month)
            ->where('year', $year)
            ->where('filename', $filename)
            ->first();
    }

    public function uploadReport(): void
    {
        $this->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'report_type' => 'required|string|max:255',
            'file' => [
                'required',
                'file',
                'max:20480',
                'mimes:pdf,docx,odt,doc,xlsx,xls,jpg,jpeg,png,gif,webp',
            ],
            'remarks' => 'nullable|string|max:1000',
        ]);

        if (! $this->file) {
            \Illuminate\Support\Facades\Log::error('ReportManager: file property is null', [
                'session' => session()->getId(),
                'request' => request()->all(),
            ]);
            throw new \Exception('File object missing – upload aborted');
        }

        \Illuminate\Support\Facades\Log::info('ReportManager: file object present', [
            'name' => $this->file->getClientOriginalName(),
            'size' => $this->file->getSize(),
        ]);

        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $this->file;
        $filename = $uploadedFile->getClientOriginalName();

        // Skip duplicate check when explicitly replacing via the table Replace button
        if (! $this->isReplacing) {
            $duplicate = $this->checkForDuplicateFilename($this->month, $this->year, $filename);

            if ($duplicate) {
                $this->duplicateReportId = $duplicate->id;
                $this->duplicateInfo = [
                    'id' => $duplicate->id,
                    'filename' => $duplicate->filename ?? basename($duplicate->file_path ?? ''),
                    'date' => ($duplicate->submitted_at ?? $duplicate->created_at)->format('M d, Y · h:i A'),
                    'uploader' => optional($duplicate->submitter)->name ?? 'Unknown',
                    'is_legacy' => ! $duplicate->file_path && (bool) $duplicate->content,
                ];
                $this->pendingFileInfo = [
                    'filename' => $filename,
                    'size' => $this->formatBytes($uploadedFile->getSize()),
                ];
                $this->showDuplicateModal = true;

                return;
            }
        }

        $this->performUpload($filename);
    }

    public function replaceReport(): void
    {
        if (! $this->duplicateReportId || ! $this->file) {
            return;
        }

        $existing = Report::find($this->duplicateReportId);

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

    public function saveAsNewReport(): void
    {
        if (! $this->file) {
            return;
        }

        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $this->file;
        $filename = $this->generateUniqueFilename($this->month, $this->year, $uploadedFile->getClientOriginalName());

        $this->performUpload($filename);
        $this->showDuplicateModal = false;
    }

    public function cancelDuplicateUpload(): void
    {
        $this->showDuplicateModal = false;
        $this->duplicateReportId = null;
        $this->duplicateInfo = null;
        $this->pendingFileInfo = null;
        $this->file = null;
        $this->dispatch('toast', type: 'warning', message: 'Upload cancelled.');
    }

    protected function performUpload(string $filename): void
    {
        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $this->file;
        $folder = "reports/{$this->year}-".str_pad($this->month, 2, '0', STR_PAD_LEFT);

        try {
            $path = $uploadedFile->storeAs($folder, time().'_'.$filename, 'public');

            if ($this->isReplacing && $this->reportToReplace) {
                $report = Report::find($this->reportToReplace);
                if ($report) {
                    if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                        Storage::disk('public')->delete($report->file_path);
                    }
                    $report->update([
                        'type' => $this->report_type,
                        'filename' => $filename,
                        'file_path' => $path,
                        'file_size' => $uploadedFile->getSize(),
                        'mime_type' => $uploadedFile->getMimeType(),
                        'status' => 'finalized',
                        'remarks' => $this->remarks,
                        'submitted_by' => auth()->id(),
                        'submitted_at' => now(),
                    ]);
                }
                $this->isReplacing = false;
                $this->reportToReplace = null;
                $this->replacingReportName = '';
            } else {
                Report::create([
                    'type' => $this->report_type,
                    'filename' => $filename,
                    'month' => $this->month,
                    'year' => $this->year,
                    'file_path' => $path,
                    'file_size' => $uploadedFile->getSize(),
                    'mime_type' => $uploadedFile->getMimeType(),
                    'status' => 'finalized',
                    'remarks' => $this->remarks,
                    'submitted_by' => auth()->id(),
                    'submitted_at' => now(),
                    'content' => null,
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Report upload failed', [
                'user_id' => auth()->id(),
                'filename' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('toast', type: 'error', message: 'Upload failed due to a server error. Please try again or contact support.');

            return;
        }

        $this->reset(['file', 'report_type', 'remarks']);
        $this->dispatch('toast', type: 'success', message: 'Report uploaded successfully.');
    }

    protected function generateUniqueFilename(int $month, int $year, string $originalFilename): string
    {
        $ext = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $base = pathinfo($originalFilename, PATHINFO_FILENAME);
        $counter = 2;

        $candidate = $originalFilename;
        while (Report::where('month', $month)->where('year', $year)->where('filename', $candidate)->exists()) {
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

    public function startReplace($reportId): void
    {
        $report = Report::find($reportId);
        if ($report) {
            $this->isReplacing = true;
            $this->reportToReplace = $report->id;
            $this->replacingReportName = $report->type;
            $this->month = $report->month;
            $this->year = $report->year;
            $this->report_type = $report->type;
            $this->remarks = $report->remarks ?? '';
        }
    }

    public function cancelReplace(): void
    {
        $this->isReplacing = false;
        $this->reportToReplace = null;
        $this->replacingReportName = '';
        $this->reset(['file', 'report_type', 'remarks']);
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
    }

    public function delete($reportId): void
    {
        $report = Report::find($reportId);
        if ($report) {
            if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                Storage::disk('public')->delete($report->file_path);
            }
            $report->delete();
            $this->dispatch('toast', type: 'success', message: 'Report deleted successfully.');
        }
    }

    public function download($reportId): mixed
    {
        $report = Report::find($reportId);
        if ($report) {
            if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                return Storage::disk('public')->download($report->file_path, basename($report->file_path));
            }
            if ($report->content) {
                $this->dispatch('toast', type: 'error', message: 'Legacy reports cannot be directly downloaded here.');
            } else {
                $this->dispatch('toast', type: 'error', message: 'File not found on disk.');
            }
        }

        return null;
    }

    public function getReportsProperty(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Report::with('submitter')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('type', 'like', '%'.$this->search.'%')
                    ->orWhere('filename', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->filter_year)) {
            $query->where('year', $this->filter_year);
        }

        if (! empty($this->filter_month)) {
            $query->where('month', $this->filter_month);
        }

        return $query->paginate(10);
    }

    public function getAvailableYearsProperty(): array
    {
        return Report::selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.report-manager', [
            'reportsList' => $this->reports,
            'availableYears' => $this->availableYears,
        ]);
    }
}
