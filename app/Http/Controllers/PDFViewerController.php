<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PDFViewerController extends Controller
{
    /**
     * Return the PDF viewer Blade view for a Case Document.
     */
    public function viewDocument(Document $document)
    {
        abort_unless(auth()->check(), 403);

        // Check if file exists before showing viewer
        $absolutePath = Storage::disk('public')->path($document->file_path);
        if (! file_exists($absolutePath)) {
            return back()->with('error', 'Document file not found. It may have been deleted or moved. Please re-upload the document.');
        }

        $payload = [
            'type' => 'document',
            'id' => $document->id,
            'title' => $document->filename ?? 'Case Document',
            'filename' => $document->filename,
            'url' => route('pdf-viewer.stream-document', $document->id),
            'download_url' => route('cases.documents.download', ['case' => $document->lupon_case_id, 'document' => $document->id]),
            'back_url' => route('cases.show', $document->lupon_case_id).'#documents',
            'file_size' => $document->file_size_formatted,
        ];

        return view('documents.pdf-viewer-optimized', compact('payload'));
    }

    /**
     * Return the PDF viewer Blade view for a Monthly Report.
     */
    public function viewReport(Report $report)
    {
        abort_unless(auth()->check(), 403);

        // Check if file exists before showing viewer
        if (! $report->file_path) {
            return back()->with('error', 'No file associated with this report.');
        }

        $absolutePath = Storage::disk('public')->path($report->file_path);
        if (! file_exists($absolutePath)) {
            return back()->with('error', 'Report file not found. It may have been deleted or moved.');
        }

        $dateString = \Carbon\Carbon::create(null, $report->month, 1)->format('F').' '.$report->year;
        $title = $report->type.' - '.$dateString;

        $payload = [
            'type' => 'report',
            'id' => $report->id,
            'title' => $title,
            'filename' => basename($report->file_path),
            'url' => route('pdf-viewer.stream-report', $report->id),
            'download_url' => route('reports.download', $report->id),
            'back_url' => route('reports.index'),
            'file_size' => 'N/A', // Assuming not tracked natively in DB for reports
        ];

        return view('documents.pdf-viewer-optimized', compact('payload'));
    }

    /**
     * Securely stream the PDF document with 'inline' disposition.
     */
    public function streamDocument(Document $document): BinaryFileResponse
    {
        abort_unless(auth()->check(), 403);
        $absolutePath = Storage::disk('public')->path($document->file_path);
        abort_unless(file_exists($absolutePath), 404, 'File not found on disk.');

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => filesize($absolutePath),
            'Content-Disposition' => 'inline; filename="'.$document->filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Securely stream the PDF report with 'inline' disposition.
     */
    public function streamReport(Report $report): BinaryFileResponse
    {
        abort_unless(auth()->check(), 403);
        abort_unless($report->file_path, 404, 'No file associated.');

        $absolutePath = Storage::disk('public')->path($report->file_path);
        abort_unless(file_exists($absolutePath), 404, 'File not found on disk.');

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => filesize($absolutePath),
            'Content-Disposition' => 'inline; filename="'.basename($report->file_path).'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
