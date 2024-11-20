<?php

namespace App\Livewire;

use App\Livewire\Forms\DocumentFrom;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Stakeholder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentIndex extends Component
{

    use WithPagination;

    public $filters = [
        'search' => '',
        'contract_ids' => [],
        'stakeholder_ids' => [],
        'follower_ids' => [],
        'tag_ids' => [],
        'types' => [],
        'statuses' => [],
    ];

    public DocumentFrom $documentForm;

    #[Computed()]
    public function authStakeholderId()
    {
        return Auth::user()->stakeholder_id;
    }

    #[Computed()]
    public function documents()
    {
        return Document::query()
            ->whereIn('contract_id', Auth::user()->contracts->pluck('id'))
            ->with('attachments')
            ->with('uncompletedSteps')
            ->with('users:id') // Only fetch user IDs to reduce data load
            ->with('tags:id') // Only fetch user IDs to reduce data load
            ->when($this->filters['search'], function (Builder $q) {
                $q->where(function (Builder $q) {
                    $q->whereAny(
                        [
                            'title',
                            'content',
                            'notes',
                            'ref'
                        ],
                        'LIKE',
                        "%" . $this->filters['search'] . "%"
                    );

                    $q->orWhereRelation('steps', 'action', 'like', "%" . $this->filters['search'] . "%");
                });
            })
            ->when($this->filters['contract_ids'], function (Builder $q) {
                $q->whereIn('contract_id', $this->filters['contract_ids']);
            })
            ->when($this->filters['stakeholder_ids'], function (Builder $q) {
                $q->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('to_id', $this->authStakeholderId)->orWhere('from_id', $this->authStakeholderId);
                    });
                    $query->where(function ($q) {
                        $q->whereIn('to_id', $this->filters['stakeholder_ids'])
                            ->orWhereIn('from_id', $this->filters['stakeholder_ids']);
                    });
                });
            })
            ->when($this->filters['follower_ids'], function (Builder $q) {
                $q->whereHas('users', function ($q) {
                    $q->whereIn('id', $this->filters['follower_ids']);
                });
            })
            ->when($this->filters['tag_ids'], function (Builder $q) {
                $q->whereHas('tags', function ($q) {
                    $q->whereIn('id', $this->filters['tag_ids']);
                });
            })
            ->when($this->filters['types'], function (Builder $q) {
                $q->whereIn('type', $this->filters['types']);
            })
            ->withExists('uncompletedSteps')
            ->orderBy('is_completed') // Sorts documents with uncompleted steps first
            ->orderByDesc('id') // Then sorts by id in descending order
            ->paginate(50)
            ->through(function ($document) {
                $document->follow_ids = $document->users->pluck('id');
                $document->tag_ids = $document->tags->pluck('id');
                return $document;
            });
    }


    #[Computed()]
    public function contracts()
    {
        return Contract::query()
            ->whereRelation('users', 'user_id', '=', Auth::id())
            ->leftJoin('documents', 'contracts.id', '=', 'documents.contract_id')
            ->select('contracts.*')
            ->selectRaw('COUNT(documents.id) as usage_count')
            ->groupBy('contracts.id')
            ->orderByDesc('usage_count')
            ->orderBy('name') // Optional secondary ordering by name
            ->get();
    }

    #[Computed()]
    public function users()
    {
        return User::query()
            ->orderBy('name')
            ->get();
    }

    #[Computed()]
    public function tags()
    {
        return Tag::query()
            ->orderBy('name')
            ->get();
    }

    #[Computed()]
    public function typesList()
    {
        return [
            ['id' => 'incoming', 'name' => __('Incoming')],
            ['id' => 'outgoing', 'name' => __('Outgoing')],
        ];
    }
    #[Computed()]
    public function statusesList()
    {
        return [
            ['id' => 'completed', 'name' => __('Completed')],
            ['id' => 'pending', 'name' => __('Pending')],
        ];
    }

    #[Computed()]
    public function stakeholders()
    {
        return Stakeholder::query()
            ->leftJoin('documents as from_docs', 'stakeholders.id', '=', 'from_docs.from_id')
            ->leftJoin('documents as to_docs', 'stakeholders.id', '=', 'to_docs.to_id')
            ->select('stakeholders.*')
            ->selectRaw('COUNT(from_docs.id) + COUNT(to_docs.id) as usage_count')
            ->where('stakeholders.id', '!=', $this->authStakeholderId) // Exclude ID 1 if necessary
            ->groupBy('stakeholders.id')
            ->orderByDesc('usage_count')
            ->orderBy('name') // Optional secondary ordering by name
            ->get();
    }

    public function toggleCompleted(Document $document, $val)
    {
        $document->update(['is_completed' => $val]);
    }


    public function saveDocument()
    {
        $document = $this->documentForm->updateOrCreate();
        $this->dispatch('documentCreated', $document);
    }

    public function deleteDocument(Document $document)
    {
        $document->delete();
        $this->dispatch('documentDeleted');
    }

    #[Layout('layouts.app')]

    public function render()
    {
        return view('livewire.document-index');
    }
}
