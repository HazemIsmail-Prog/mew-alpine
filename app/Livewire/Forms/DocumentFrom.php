<?php

namespace App\Livewire\Forms;

use App\Models\Document;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DocumentFrom extends Form
{
    public $id;
    public $created_by;
    public $follow_ids = [];
    public $tag_ids = [];
    #[Validate('required')]
    public $contract_id;
    #[Validate('required')]
    public $from_id;
    #[Validate('required')]
    public $to_id;
    #[Validate('required')]
    public $type;
    #[Validate('required')]
    public $title;
    public $ref;
    public $content;
    public $notes;
    public bool $is_completed;

    public function updateOrCreate()
    {
        $this->validate();
        $document = Document::updateOrCreate(['id' => $this->id], $this->except('follow_ids','tag_ids'));
        $document->users()->sync($this->follow_ids);
        $document->tags()->sync($this->tag_ids);

        $document->load('users:id'); // Only fetch user IDs to reduce data load
        $document->load('tags:id'); // Only fetch user IDs to reduce data load
        $document->follow_ids = $document->users->pluck('id');
        $document->tag_ids = $document->tags->pluck('id');
        return $document;
    }
}
