<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Step;
use Illuminate\Console\View\Components\Task;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function index(Document $document)
    {
        $steps = $document->steps()
            ->orderBy('id', 'asc')
            ->orderBy('order', 'asc')
            ->get();
        return response()->json($steps);
    }

    public function store(Request $request, Document $document)
    {
        $position = $document->steps()->max('order') + 1;
        $validatedData = $request->validate([
            'action' => 'required|string',
        ]);
        $step = $document->steps()->create($validatedData);
        $step->order = $position;
        $step->save();
        return response()->json($step);
    }

    public function update(Request $request, Document $document, Step $step)
    {
        $request->validate([
            'action' => 'required|string',
        ]);
        $step->update($request->only('action'));
        return response()->json($step);
    }

    public function destroy(Document $document, Step $step)
    {
        // Delete the specified step
        $step->delete();

        // Update the order for all steps that come after the deleted step
        Step::where('document_id', $document->id)
            ->where('order', '>', $step->order)
            ->decrement('order');

        return response()->json(['message' => 'Step deleted and reordered successfully']);
    }

    public function toggleCompleted(Request $request, Document $document, Step $step)
    {
        $validatedData = $request->validate([
            'is_completed' => 'required|boolean',
        ]);
        $step->update($validatedData);
        return response()->json($step);
    }

    public function reorderSteps(Request $request, Document $document)
    {
        $step = Step::findOrFail($request->id);
        $currentPosition = $step->order;
        $newPosition = $request->newPosition + 1;

        if ($currentPosition === $newPosition) {
            return response()->json(['message' => 'No reordering necessary'], 200);
        }

        // Temporarily set the order to -1 to avoid conflicts
        $step->update(['order' => -1]);

        // Determine the direction of movement and update other steps accordingly
        if ($currentPosition < $newPosition) {
            Step::where('document_id', $step->document_id)
                ->whereBetween('order', [$currentPosition, $newPosition])
                ->decrement('order');
        } else {
            Step::where('document_id', $step->document_id)
                ->whereBetween('order', [$newPosition, $currentPosition])
                ->increment('order');
        }

        // Update the moved step to its new position
        $step->update(['order' => $newPosition]);

        // Reorder all steps to ensure they start from 1 and have no gaps or duplications
        $steps = Step::where('document_id', $document->id)
            ->orderBy('order')
            ->get();

        foreach ($steps as $index => $step) {
            $step->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Steps reordered successfully'], 200);
    }
}
