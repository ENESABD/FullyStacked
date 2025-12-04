<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipientController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->recipients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $recipient = Auth::user()->recipients()->create($validated);

        return response()->json($recipient, 201);
    }

    public function show(string $id)
    {
        $recipient = Auth::user()->recipients()->findOrFail($id);
        return response()->json($recipient);
    }

    public function update(Request $request, string $id)
    {
        $recipient = Auth::user()->recipients()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $recipient->update($validated);

        return response()->json($recipient);
    }

    public function destroy(string $id)
    {
        $recipient = Auth::user()->recipients()->findOrFail($id);
        $recipient->delete();

        return response()->json(null, 204);
    }
    
    public function gifts(string $id)
    {
        $recipient = Auth::user()->recipients()->findOrFail($id);
        return response()->json($recipient->gifts);
    }
}
