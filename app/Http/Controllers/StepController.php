<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StepController extends Controller
{
    public function getSteps(Document $document)
    {
        // Fetch steps related to the document
        $steps = $document->steps()->orderBy('created_at')->get();

        return response()->json($steps);
    }

    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'document_id' => 'required|integer',
            'action' => 'required|string',
        ]);

        // Create the step
        $validatedData['user_id'] = Auth::id();
        $validatedData['is_completed'] = false;
        $step = Step::create($validatedData);

        // Return the saved step as a JSON response
        return response()->json($step);
    }

    public function toggleCompleted(Request $request, Step $step)
    {
        // Validate the request
        $validatedData = $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        // Update the step
        $step->update($validatedData);

        // Return the updated step as JSON
        return response()->json($step);
    }

    public function update(Request $request, Step $step)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'action' => 'nullable|string',  // action can be null if only updating is_completed
            'is_completed' => 'nullable|boolean',  // is_completed can be null if only updating action
        ]);

        // Update only the fields that are present in the request
        if (isset($validatedData['action'])) {
            $step->action = $validatedData['action'];
        }

        if (isset($validatedData['is_completed'])) {
            $step->is_completed = $validatedData['is_completed'];
        }

        // Save the updated step
        $step->save();

        // Return the updated step as JSON
        return response()->json($step);
    }

    public function destroy(Step $step)
    {
        $step->delete();

        return response()->json(['message' => 'Step deleted successfully']);
    }
}
