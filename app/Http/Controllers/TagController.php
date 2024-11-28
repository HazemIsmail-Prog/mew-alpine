<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return view('pages.tags.index');
    }

    public function getData(Request $request)
    {
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
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tag = Tag::create($request->all());

        return response()->json($tag, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tag = Tag::findOrFail($id);
        $tag->update($request->all());

        return response()->json($tag);
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }

    public function toggleActive(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->is_active = $request->input('is_active');
        $tag->save();

        return response()->json($tag);
    }
}
