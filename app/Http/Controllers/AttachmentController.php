<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class AttachmentController extends Controller
{

    public function getAttachments(Document $document)
    {
        // Fetch attachments related to the document
        $attachments = $document->attachments()->orderBy('created_at')->get();

        return response()->json($attachments);
    }


    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'file' => 'required|file|mimes:jpg,png,pdf',
                'description' => 'required|string|max:255',
                'document_id' => 'required|integer|exists:documents,id',
            ]);

            // Store the file
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
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function update(Request $request, Attachment $attachment)
    {
        $validatedData = $request->validate([
            'description' => 'required|string',
        ]);
        $attachment->description = $validatedData['description'];
        $attachment->save();
        return response()->json($attachment);
    }

    public function destroy(Attachment $attachment)
    {
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully']);
    }
}
