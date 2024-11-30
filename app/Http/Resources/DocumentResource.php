<?php

namespace App\Http\Resources;

use App\Policies\DocumentPolicy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $policy = new DocumentPolicy();
        $user = $request->user();
        return [
            'id' => $this->id,
            'contract_id' => $this->contract_id,
            'from_id' => $this->from_id,
            'to_id' => $this->to_id,
            'type' => $this->type,
            'title' => $this->title,
            'is_completed' => $this->is_completed,
            'ref' => $this->ref,
            'content' => $this->content,
            'notes' => $this->notes,
            'follow_ids' => $this->users()->pluck('id'),
            'tag_ids' => $this->tags()->pluck('id'),
            'uncompleted_steps' => $this->uncompletedSteps()->orderBy('order', 'asc')->get(),
            'can_delete' => ($policy->before($user, 'delete')) ?? $policy->delete($user, $this->resource),
            'can_update' => ($policy->before($user, 'update')) ?? $policy->update($user, $this->resource),
        ];
    }
}
