<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    // GET /api/reminders
    public function index(Request $request)
    {
        // Validate all query parameters upfront
        $request->validate([
            'status'   => 'sometimes|in:pending,done,cancelled',
            'time'     => 'sometimes|in:upcoming,past',
            'search'   => 'sometimes|string|max:100',
            'sort_by'  => 'sometimes|in:remind_at,created_at,status',
            'sort_dir' => 'sometimes|in:asc,desc',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $query = $request->user()->reminders();

        // --- Filter by status ---
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // --- Filter by time ---
        if ($request->filled('time')) {
            $now = now();
            if ($request->time === 'upcoming') {
                $query->where('remind_at', '>=', $now);
            } else {
                $query->where('remind_at', '<', $now);
            }
        }

        // --- Search by keyword in title or description ---
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // --- Sorting ---
        $sortBy  = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // --- Pagination ---
        $perPage   = $request->input('per_page', 10);
        $reminders = $query->paginate($perPage);

        return response()->json([
            'total'        => $reminders->total(),
            'per_page'     => $reminders->perPage(),
            'current_page' => $reminders->currentPage(),
            'last_page'    => $reminders->lastPage(),
            'reminders'    => $reminders->items(),
        ]);
    }

    // POST /api/reminders
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'remind_at'   => 'required|date|after:now',
        ]);

        $reminder = $request->user()->reminders()->create($validated);

        return response()->json([
            'message'  => 'Reminder created successfully',
            'reminder' => $reminder,
        ], 201);
    }

    // GET /api/reminders/{id}
    public function show(Request $request, $id)
    {
        $reminder = $request->user()->reminders()->find($id);

        if (! $reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        return response()->json(['reminder' => $reminder]);
    }

    // PUT /api/reminders/{id}
    public function update(Request $request, $id)
    {
        $reminder = $request->user()->reminders()->find($id);

        if (! $reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        if ($reminder->status === 'cancelled') {
            return response()->json([
                'message' => 'Cancelled reminders cannot be edited.',
            ], 422);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:2000',
            'remind_at'   => 'sometimes|date|after:now',
        ]);

        $reminder->update($validated);

        return response()->json([
            'message'  => 'Reminder updated successfully',
            'reminder' => $reminder,
        ]);
    }

    // DELETE /api/reminders/{id}
    public function destroy(Request $request, $id)
    {
        $reminder = $request->user()->reminders()->find($id);

        if (! $reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        $reminder->delete();

        return response()->json(['message' => 'Reminder deleted successfully']);
    }
}