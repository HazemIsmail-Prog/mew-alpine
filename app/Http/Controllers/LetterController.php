<?php

namespace App\Http\Controllers;

use App\Http\Resources\LetterResource;
use App\Models\Letter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


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

            ->when($request->user()->role == 'user', function (Builder $q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })

            ->when(isset($filters['id']), function (Builder $q) use ($filters) {
                $q->where('id',$filters['id']);
            })
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
        $request->validate([
            'sender' => 'required',
            'receiver' => 'required',
            'subject' => 'required',
            'copyTo' => 'required',
            'body' => 'required',
        ]);

        $letter->update($request->all());
        return new LetterResource($letter);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sender' => 'required',
            'receiver' => 'required',
            'subject' => 'required',
            'copyTo' => 'required',
            'body' => 'required',
        ]);
        $letter = Letter::create($request->all());
        return new LetterResource($letter);
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();
        return response()->json(['message' => 'Letter deleted successfully']);
    }

    public function originalPDF(Letter $letter)
    {
        $cover = asset('images/cover.jpeg');
        return view('pages.letters.print', compact('letter', 'cover'));
    }

    public function normalPDF(Letter $letter)
    {
        $cover = false;
        return view('pages.letters.print', compact('letter', 'cover'));
    }
}
