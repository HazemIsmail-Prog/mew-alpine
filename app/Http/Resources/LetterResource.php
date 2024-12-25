<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LetterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'sender' => $this->sender,
            'prefix' => $this->prefix,
            'receiver' => $this->receiver,
            $this->mergeWhen($request->user()->role != 'user', [
                'creator' => $this->whenLoaded('creator')->name,
            ]),
            'body' => $this->body,
            'official' => $this->official,
            'internal' => $this->internal,
            'copyTo' => $this->copyTo,
            'code' => $this->code,
            'address' => $this->address,
        ];
    }
}
