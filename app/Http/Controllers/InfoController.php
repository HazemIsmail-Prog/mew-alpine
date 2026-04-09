<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Info;

class InfoController extends Controller
{
    public function index(Contract $contract)
    {
        $infos = $contract->infos()->orderBy('index')->get();
        return response()->json($infos);
    }

    public function store(Request $request, Contract $contract)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
        ]);
        $index = $contract->infos()->max('index') + 1;
        $info = $contract->infos()->create([
            'key' => $request->key,
            'value' => $request->value,
            'index' => $index,
        ]);
        return response()->json($info);
    }

    public function update(Request $request, Info $info)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
        ]);
        $info->update([
            'key' => $request->key,
            'value' => $request->value,
        ]);
        return response()->json($info);
    }

    public function destroy(Info $info)
    {
        $info->delete();
        return response()->json($info);
    }
}
