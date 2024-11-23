<?php

namespace App\Observers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class AttachmentObserver
{
    /**
     * Handle the Attachment "created" event.
     */
    public function created(Attachment $attachment): void
    {
        //
    }

    /**
     * Handle the Attachment "updated" event.
     */
    public function updated(Attachment $attachment): void
    {
        //
    }

    /**
     * Handle the Attachment "deleted" event.
     */
    public function deleted(Attachment $attachment)
    {
        try {
            // Delete the file from S3
            $deleted = Storage::disk('s3')->delete($attachment->file);
    
            if (!$deleted) {
                Log::error('Failed to delete attachment file from S3: ' . $attachment->file);
                return response()->json(['error' => 'Failed to delete file from S3'], 500);
            }
    
            Log::info('Attachment file successfully deleted from S3: ' . $attachment->file);
        } catch (\Throwable $th) {
            // Log the exception message and return an error response
            Log::error('Exception occurred while deleting attachment file from S3: ' . $th->getMessage());
        }
    }

    /**
     * Handle the Attachment "restored" event.
     */
    public function restored(Attachment $attachment): void
    {
        //
    }

    /**
     * Handle the Attachment "force deleted" event.
     */
    public function forceDeleted(Attachment $attachment): void
    {
        //
    }
}
