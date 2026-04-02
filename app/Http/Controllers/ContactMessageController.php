<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::query()->create($validated);

        return response()->json([
            'message' => 'Message sent successfully.',
        ]);
    }
}
