<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    // GET /api/reminders
    public function index(Request $request)
    {
        $request->validate([
            'status'   => 'sometimes|in:pending,done,cancelled',
            'time'     => 'sometimes|in:upcoming,past',
            'search'   => 'sometimes|string|max:100',
            'sort_by'  => 'sometimes|in:remind_at,created_at,status',
            'sort_dir' => 'sometimes|in:asc,desc',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        try {
            $query = $request->user()->reminders()->with('event');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('time')) {
                $now = now();
                if ($request->time === 'upcoming') {
                    $query->where('remind_at', '>=', $now);
                } else {
                    $query->where('remind_at', '<', $now);
                }
            }

            if ($request->filled('search')) {
                $keyword = $request->search;
                $query->whereHas('event', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
                });
            }

            $sortBy  = $request->input('sort_by', 'created_at');
            $sortDir = $request->input('sort_dir', 'desc');
            $query->orderBy($sortBy, $sortDir);

            $perPage   = $request->input('per_page', 10);
            $reminders = $query->paginate($perPage);

            return response()->json([
                'total'        => $reminders->total(),
                'per_page'     => $reminders->perPage(),
                'current_page' => $reminders->currentPage(),
                'last_page'    => $reminders->lastPage(),
                'reminders'    => $reminders->items(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // POST /api/reminders
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id'  => 'required|exists:events,id',
            'remind_at' => 'required|date|after:now',
        ]);

        $event = $request->user()->events()->find($validated['event_id']);

        if (! $event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        try {
            $reminder = $request->user()->reminders()->create($validated);

            return response()->json([
                'message'  => 'Reminder created successfully',
                'reminder' => $reminder->load('event'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/reminders/{id}
    public function show(Request $request, $id)
    {
        try {
            $reminder = $request->user()->reminders()->with('event')->find($id);

            if (! $reminder) {
                return response()->json(['message' => 'Reminder not found'], 404);
            }

            return response()->json(['reminder' => $reminder]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // PUT /api/reminders/{id}
    public function update(Request $request, $id)
    {
        $reminder = $request->user()->reminders()->with('event')->find($id);

        if (! $reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        if ($reminder->status === 'cancelled') {
            return response()->json([
                'message' => 'Cancelled reminders cannot be edited.',
            ], 422);
        }

        $validated = $request->validate([
            'remind_at' => 'sometimes|date|after:now',
        ]);

        try {
            $reminder->update($validated);

            return response()->json([
                'message'  => 'Reminder updated successfully',
                'reminder' => $reminder,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // DELETE /api/reminders/{id}
    public function destroy(Request $request, $id)
    {
        $reminder = $request->user()->reminders()->find($id);

        if (! $reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        try {
            $reminder->delete();

            return response()->json(['message' => 'Reminder deleted successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}