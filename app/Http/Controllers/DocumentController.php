<?php

namespace App\Http\Controllers;

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
        $contracts = Contract::query()
            ->whereRelation('users', 'user_id', '=', Auth::id())
            ->leftJoin('documents', 'contracts.id', '=', 'documents.contract_id')
            ->select('contracts.*')
            ->selectRaw('COUNT(documents.id) as usage_count')
            ->groupBy('contracts.id')
            ->orderByDesc('usage_count')
            ->orderBy('name') // Optional secondary ordering by name
            ->get();

        $stakeholders = Stakeholder::query()
            ->leftJoin('documents as from_docs', 'stakeholders.id', '=', 'from_docs.from_id')
            ->leftJoin('documents as to_docs', 'stakeholders.id', '=', 'to_docs.to_id')
            ->select('stakeholders.*')
            ->selectRaw('COUNT(from_docs.id) + COUNT(to_docs.id) as usage_count')
            ->where('stakeholders.id', '!=', Auth::user()->stakeholder_id)
            ->groupBy('stakeholders.id')
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

        $authStakeholderId = Auth::user()->stakeholder_id;

        $filters = $request->query('filters');
        $documents = Document::query()
            ->with('tags:id')
            ->with('users:id')
            ->with('uncompletedSteps',function($q){
                $q->orderBy('order', 'asc');

            })
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
                $q->whereIn('contract_id', $filters['contract_ids']);
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
            // ->orderBy('is_completed') // Sorts documents with uncompleted steps first
            ->latest() // Then sorts by id in descending order
            ->paginate(50);
        return response()->json($documents);
    }

    public function store(Request $request)
    {
        $request->validate([
            'contract_id' => 'required',
            'from_id' => 'required',
            'to_id' => 'required',
            'type' => 'required',
            'title' => 'required',
            'is_completed' => 'required|boolean',
            'ref' => 'nullable',
            'content' => 'nullable',
            'notes' => 'nullable',
            'follow_ids' => 'nullable|array',
            'tag_ids' => 'nullable|array',
        ]);
        $document = Document::create($request->except('follow_ids', 'tag_ids'));
        $document->users()->sync($request->follow_ids);
        $document->tags()->sync($request->tag_ids);
        return response()->json($document, 201);
    }

    public function update(Request $request, Document $document)
    {
        $request->validate([
            'contract_id' => 'required',
            'from_id' => 'required',
            'to_id' => 'required',
            'type' => 'required',
            'title' => 'required',
            'is_completed' => 'required|boolean',
            'ref' => 'nullable',
            'content' => 'nullable',
            'notes' => 'nullable',
            'follow_ids' => 'nullable|array',
            'tag_ids' => 'nullable|array',
        ]);

        $document->update($request->except('follow_ids', 'tag_ids'));
        $document->users()->sync($request->follow_ids);
        $document->tags()->sync($request->tag_ids);
        return response()->json($document->load('uncompletedSteps'));
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        return response()->json(['message' => 'Document deleted successfully']);
    }

    public function toggleCompleted(Request $request, Document $document)
    {
        $document->is_completed = $request->input('is_completed');
        $document->save();
        return response()->json($document->load('uncompletedSteps'));
    }
}