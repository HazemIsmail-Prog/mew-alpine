<?php

namespace App\Models;

use App\Observers\AttachmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
#[ObservedBy(AttachmentObserver::class)]
class Attachment extends Model
{
    protected $guarded = [];
    protected $appends = ['file_path'];

    public function getFilePathAttribute()
    {
        $bucketName = config('filesystems.disks.s3.bucket');
        return "https://" . $bucketName . ".s3.amazonaws.com/" . $this->file;
    }
}
