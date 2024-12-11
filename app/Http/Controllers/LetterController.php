<?php

namespace App\Http\Controllers;

use App\Http\Resources\LetterResource;
use App\Models\Letter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;


class LetterController extends Controller
{
    public function index()
    {
        return view('pages.letters.index');
    }

    public function getData(Request $request)
    {

        $filters = $request->query('filters');
        $letters = Letter::query()
        
            ->where('user_id', $request->user()->id)
            ->when(isset($filters['search']), function (Builder $q) use ($filters) {
                $q->where(function (Builder $q) use ($filters) {
                    $q->whereAny(
                        [
                            'subject',
                            'body',
                            'receiver',
                            'sender'
                        ],
                        'LIKE',
                        "%" . $filters['search'] . "%"
                    );
                });
            })
            ->latest()
            ->paginate(30);

        return LetterResource::collection($letters);
    }

    public function update(Request $request, Letter $letter)
    {
        // $request->validate([
        //     'contract_id' => 'required',
        //     'from_id' => 'required',
        //     'to_id' => 'required',
        //     'type' => 'required',
        //     'title' => 'required',
        //     // 'is_completed' => 'required|boolean',
        //     'ref' => 'nullable',
        //     'content' => 'nullable',
        //     'notes' => 'nullable',
        //     'follow_ids' => 'nullable|array',
        //     'tag_ids' => 'nullable|array',
        // ]);

        $letter->update($request->all());
        return new LetterResource($letter);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'contract_id' => 'required',
        //     'from_id' => 'required_without:to_id|nullable',
        //     'to_id' => 'required_without:from_id|nullable',
        //     'type' => 'required',
        //     'title' => 'required',
        //     'is_completed' => 'required|boolean',
        //     'ref' => 'nullable',
        //     'content' => 'nullable',
        //     'notes' => 'nullable',
        //     'follow_ids' => 'nullable|array',
        //     'tag_ids' => 'nullable|array',
        // ]);
        $letter = Letter::create($request->all());
        return new LetterResource($letter);
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();
        return response()->json(['message' => 'Letter deleted successfully']);
    }

    public function originalPDF(Letter $letter) {
        return Pdf::view('pages.letters.original-pdf', ['letter' => $letter])
            ->format('a4')
            ->name('your-invoice.pdf');
    }

    public function normalPDF(Letter $letter) {
        return Pdf::view('pages.letters.normal-pdf', ['letter' => $letter])
            ->format('a4')
            ->name('your-invoice.pdf');
    }
}
