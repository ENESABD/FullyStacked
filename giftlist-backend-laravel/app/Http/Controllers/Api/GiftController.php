<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Gift::whereHas('recipient', function ($query) {
            $query->where('user_id', Auth::id());
        });

        if ($request->has('recipientId')) {
            $query->where('recipient_id', $request->recipientId);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:recipients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'url' => 'nullable|url',
            'purchased' => 'boolean',
        ]);

        // Verify ownership of recipient
        $recipient = Auth::user()->recipients()->findOrFail($validated['recipient_id']);

        $gift = $recipient->gifts()->create($validated);

        return response()->json($gift, 201);
    }

    public function show(string $id)
    {
        $gift = Gift::whereHas('recipient', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        return response()->json($gift);
    }

    public function update(Request $request, string $id)
    {
        $gift = Gift::whereHas('recipient', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'url' => 'nullable|url',
            'purchased' => 'boolean',
        ]);

        $gift->update($validated);

        return response()->json($gift);
    }

    public function destroy(string $id)
    {
        $gift = Gift::whereHas('recipient', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        $gift->delete();

        return response()->json(null, 204);
    }
}
