<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class AttachmentController extends Controller
{

    public function index(Document $document)
    {
        $attachments = $document->attachments()->latest()->get();
        return response()->json($attachments);
    }


    public function store(Request $request, Document $document)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,png,pdf',
            'description' => 'required|string|max:255',
            'document_id' => 'required|integer|exists:documents,id',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('attachments', 'public');

            $attachment = Attachment::create([
                'file' => $path,
                'description' => $validated['description'],
                'document_id' => $validated['document_id'],
            ]);

            return response()->json(['attachment' => $attachment], 201);
        }

        return response()->json(['error' => 'File upload failed'], 500);
    }

    public function update(Request $request, Document $document, Attachment $attachment)
    {
        $validatedData = $request->validate([
            'description' => 'required|string',
        ]);
        $attachment->update($validatedData);
        return response()->json($attachment);
    }

    public function destroy(Document $document,Attachment $attachment)
    {
        $attachment->delete();
        return response()->json(['message' => 'Attachment deleted successfully']);
    }
}
