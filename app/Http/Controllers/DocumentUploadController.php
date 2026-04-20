<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\LuponCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentUploadController extends Controller
{
    /**
     * Store a newly uploaded document for a case.
     */
    public function store(Request $request, LuponCase $case): RedirectResponse
    {
        $request->validate([
            'document_type' => 'nullable|string|max:100',
            'file' => [
                'required',
                'file',
                'max:20480', // 20 MB
                'mimes:pdf,docx,odt,doc,xlsx,xls,jpg,jpeg,png,gif,webp',
            ],
            'remarks' => 'nullable|string|max:255',
        ]);

        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $request->file('file');

        $directory = "cases/{$case->id}/uploads";
        $path = $uploadedFile->store($directory, 'public');

        Document::create([
            'lupon_case_id' => $case->id,
            'document_type' => $request->input('document_type', 'Other'),
            'filename' => $uploadedFile->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType(),
            'uploaded_by' => auth()->id() ?? 1,
            'remarks' => $request->input('remarks'),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Delete a document and its physical file.
     */
    public function destroy(LuponCase $case, Document $document): RedirectResponse
    {
        abort_if($document->lupon_case_id !== $case->id, 403);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted.');
    }

    /**
     * Download a document file.
     */
    public function download(LuponCase $case, Document $document): BinaryFileResponse
    {
        abort_if($document->lupon_case_id !== $case->id, 403);

        $absolutePath = Storage::disk('public')->path($document->file_path);

        abort_unless(file_exists($absolutePath), 404, 'File not found on disk.');

        return response()->download($absolutePath, $document->filename);
    }

    /**
     * Return the absolute file path as JSON so Electron can open it
     * with the system default application via shell.openPath().
     */
    public function open(LuponCase $case, Document $document): JsonResponse
    {
        abort_if($document->lupon_case_id !== $case->id, 403);

        $absolutePath = Storage::disk('public')->path($document->file_path);

        if (! file_exists($absolutePath)) {
            return response()->json(['error' => 'File not found on disk.'], 404);
        }

        return response()->json([
            'path' => $absolutePath,
            'filename' => $document->filename,
            'mime_type' => $document->mime_type,
        ]);
    }
}
