<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function index()
    {
        if (Auth::user()->cannot('viewAny', Tag::class)) {
            abort(404);
        }
        return view('pages.tags.index');
    }

    public function getData(Request $request)
    {
        if (Auth::user()->cannot('viewAny', Tag::class)) {
            abort(403, 'Unauthorized action.');
        }
        $limit = 10;
        $filters = $request->query('filters');

        $query = Tag::query()
            ->latest();

        if ($filters['search']) {
            $query->where('name', 'like', "%" . $filters['search'] . "%");
        }

        $tags = $query->paginate($limit);
        return response()->json($tags);
    }

    public function store(Request $request)
    {
        if (Auth::user()->cannot('create', Tag::class)) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tag = Tag::create($request->all());

        return response()->json($tag, 201);
    }

    public function update(Request $request, Tag $tag)
    {
        if (Auth::user()->cannot('update', $tag)) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tag->update($request->all());

        return response()->json($tag);
    }

    public function destroy(Tag $tag)
    {
        if (Auth::user()->cannot('delete', $tag)) {
            abort(403, 'Unauthorized action.');
        }
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }

    public function toggleActive(Request $request, Tag $tag)
    {
        if (Auth::user()->cannot('update', $tag)) {
            abort(403, 'Unauthorized action.');
        }
        $tag->is_active = $request->input('is_active');
        $tag->save();

        return response()->json($tag);
    }
}
