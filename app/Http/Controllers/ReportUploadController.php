<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportUploadController extends Controller
{
    /**
     * Endpoint for downloading an uploaded report.
     */
    public function download(Report $report): StreamedResponse
    {
        if (! $report->file_path || ! Storage::disk('public')->exists($report->file_path)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk('public')->download($report->file_path, basename($report->file_path));
    }

    /**
     * Endpoint used via IPC/Electron to trigger native shell file open.
     */
    public function open(Report $report): JsonResponse
    {
        if ($report->content && ! $report->file_path) {
            return response()->json(['status' => 'legacy', 'message' => 'Legacy report cannot be directly opened via Electron.'], 400);
        }

        if (! $report->file_path || ! Storage::disk('public')->exists($report->file_path)) {
            return response()->json(['error' => 'File missing'], 404);
        }

        return response()->json([
            'path' => Storage::disk('public')->path($report->file_path),
        ]);
    }
}
