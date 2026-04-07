<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->query('filters');
        if ($request->wantsJson()) {
            $contracts = $request->user()->contracts()
                ->when(isset($filters['search']), function (Builder $q) use ($filters) {
                    $q->where('name', 'like', "%" . $filters['search'] . "%");
                })
                ->orderBy('name')
                ->get();
            return response()->json($contracts);
        }
        return view('pages.reports.index');
    }
}
