<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentResource;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Stakeholder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        if (Auth::user()->cannot('viewAny', Document::class)) {
            abort(404);
        }
        $contracts = Contract::query()
            ->whereRelation('users', 'user_id', '=', Auth::id())
            ->leftJoin('documents', 'contracts.id', '=', 'documents.contract_id')
            ->select('contracts.id', 'contracts.name')
            ->selectRaw('COUNT(documents.id) as usage_count')
            ->groupBy('contracts.id', 'contracts.name')
            ->orderByDesc('usage_count')
            ->orderBy('name') // Optional secondary ordering by name
            ->get();

        $stakeholders = Stakeholder::query()
            ->where('stakeholders.id', '!=', Auth::user()->stakeholder_id)
            ->leftJoin('documents as from_docs', 'stakeholders.id', '=', 'from_docs.from_id')
            ->leftJoin('documents as to_docs', 'stakeholders.id', '=', 'to_docs.to_id')
            ->select('stakeholders.id', 'stakeholders.name')
            ->selectRaw('COUNT(from_docs.id) + COUNT(to_docs.id) as usage_count')
            ->groupBy('stakeholders.id', 'stakeholders.name')
            ->orderByDesc('usage_count')
            ->orderBy('name') // Optional secondary ordering by name
            ->get();

        $users = User::query()
            ->orderBy('name')
            ->get();

        $tags = Tag::query()
            ->orderBy('name')
            ->get();

        $typesList = [
            ['id' => 'incoming', 'name' => __('Incoming')],
            ['id' => 'outgoing', 'name' => __('Outgoing')],
        ];

        $statusesList = [
            ['id' => 'completed', 'name' => __('Completed')],
            ['id' => 'pending', 'name' => __('Pending')],
        ];

        return view('pages.documents.index', [
            'contracts' => $contracts,
            'stakeholders' => $stakeholders,
            'users' => $users,
            'tags' => $tags,
            'typesList' => $typesList,
            'statusesList' => $statusesList,
        ]);
    }

    public function getData(Request $request)
    {
        if (Auth::user()->cannot('viewAny', Document::class)) {
            abort(403, 'Unauthorized action.');
        }
        $authStakeholderId = Auth::user()->stakeholder_id;

        $filters = $request->query('filters');
        $documents = Document::query()
            ->with('contracts')
            ->whereHas('contracts', function (Builder $q) use ($request) {
                $q->whereIn('id', $request->user()->contracts()->pluck('id'));
            })
            // ->whereIn('contract_id', $request->user()->contracts()->pluck('id'))
            ->when(isset($filters['search']), function (Builder $q) use ($filters) {
                $q->where(function (Builder $q) use ($filters) {
                    $q->whereAny(
                        [
                            'title',
                            'content',
                            'notes',
                            'ref'
                        ],
                        'LIKE',
                        "%" . $filters['search'] . "%"
                    );

                    $q->orWhereRelation('steps', 'action', 'like', "%" . $filters['search'] . "%");
                });
            })
            ->when(isset($filters['contract_ids']), function (Builder $q) use ($filters) {
                $q->whereHas('contracts', function (Builder $q) use ($filters) {
                    $q->whereIn('id', $filters['contract_ids']);
                });
            })
            ->when(isset($filters['stakeholder_ids']), function (Builder $q) use ($filters, $authStakeholderId) {
                $q->where(function ($query) use ($filters, $authStakeholderId) {
                    $query->where(function ($q) use ($authStakeholderId) {
                        $q->where('to_id', $authStakeholderId)->orWhere('from_id', $authStakeholderId);
                    });
                    $query->where(function ($q) use ($filters) {
                        $q->whereIn('to_id', $filters['stakeholder_ids'])
                            ->orWhereIn('from_id', $filters['stakeholder_ids']);
                    });
                });
            })
            ->when(isset($filters['follower_ids']), function (Builder $q) use ($filters) {
                $q->whereHas('users', function ($q) use ($filters) {
                    $q->whereIn('id', $filters['follower_ids']);
                });
            })
            ->when(isset($filters['tag_ids']), function (Builder $q) use ($filters) {
                $q->whereHas('tags', function ($q) use ($filters) {
                    $q->whereIn('id', $filters['tag_ids']);
                });
            })
            ->when(isset($filters['types']), function (Builder $q) use ($filters) {
                $q->whereIn('type', $filters['types']);
            })
            ->when(isset($filters['statuses']), function (Builder $q) use ($filters) {
                $q->where(function (Builder $q) use ($filters) {
                    if (in_array('completed', $filters['statuses'])) {
                        $q->orDoesntHave('uncompletedSteps');
                    }
                    if (in_array('pending', $filters['statuses'])) {
                        $q->orHas('uncompletedSteps');
                    }
                });
            })
            // ->orderBy('is_completed') // Sorts documents with uncompleted steps first
            ->latest()
            ->paginate(30);

        return DocumentResource::collection($documents);
    }

    public function show(Document $document)
    {
        $currentDocument = Document::query()
            ->where('id', $document->id)
            ->first();
        return new DocumentResource($currentDocument);
    }

    public function store(Request $request)
    {
        if (Auth::user()->cannot('create', Document::class)) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'contract_ids' => 'required|array',
            'from_id' => 'required_without:to_id|nullable',
            'to_id' => 'required_without:from_id|nullable',
            'type' => 'required',
            'title' => 'required',
            'is_completed' => 'required|boolean',
            'ref' => 'nullable',
            'content' => 'nullable',
            'notes' => 'nullable',
            'follow_ids' => 'nullable|array',
            'tag_ids' => 'nullable|array',
        ]);
        $document = Document::create($request->except('follow_ids', 'tag_ids', 'contract_ids', 'can_update', 'can_delete'));
        $document->contracts()->sync($request->contract_ids);
        $document->users()->sync($request->follow_ids);
        $document->tags()->sync($request->tag_ids);
        return new DocumentResource($document);
    }

    public function update(Request $request, Document $document)
    {
        if (Auth::user()->cannot('update', $document)) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'contract_ids' => 'required|array',
            'from_id' => 'required',
            'to_id' => 'required',
            'type' => 'required',
            'title' => 'required',
            // 'is_completed' => 'required|boolean',
            'ref' => 'nullable',
            'content' => 'nullable',
            'notes' => 'nullable',
            'follow_ids' => 'nullable|array',
            'tag_ids' => 'nullable|array',
        ]);

        $document->update($request->except('follow_ids', 'tag_ids', 'contract_ids', 'is_completed', 'can_update', 'can_delete'));
        $document->contracts()->sync($request->contract_ids);
        $document->users()->sync($request->follow_ids);
        $document->tags()->sync($request->tag_ids);
        return new DocumentResource($document);
    }

    public function destroy(Document $document)
    {
        if (Auth::user()->cannot('delete', $document)) {
            abort(403, 'Unauthorized action.');
        }
        $document->delete();
        return response()->json(['message' => 'Document deleted successfully']);
    }

    // public function toggleCompleted(Request $request, Document $document)
    // {
    //     if (Auth::user()->cannot('update', $document)) {
    //         abort(403, 'Unauthorized action.');
    //     }
    //     $document->is_completed = $request->input('is_completed');
    //     $document->save();
    //     return new DocumentResource($document);
    // }
}
