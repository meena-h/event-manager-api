<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // GET /api/events
    public function index(Request $request)
    {
        // Validate all query parameters upfront
        $request->validate([
            'status'   => 'sometimes|in:pending,done,cancelled',
            'time'     => 'sometimes|in:upcoming,past',
            'search'   => 'sometimes|string|max:100',
            'sort_by'  => 'sometimes|in:event_date,created_at,status',
            'sort_dir' => 'sometimes|in:asc,desc',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $query = $request->user()->events();

        // --- Filter by status ---
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // --- Filter by time ---
        if ($request->filled('time')) {
            $now = now();
            if ($request->time === 'upcoming') {
                $query->where('event_date', '>=', $now);
            } else {
                $query->where('event_date', '<', $now);
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
        $sortBy  = $request->input('sort_by', 'created_at');  // default: newest first
        $sortDir = $request->input('sort_dir', 'desc');        // default: descending
        $query->orderBy($sortBy, $sortDir);

        // --- Pagination ---
        $perPage = $request->input('per_page', 10); // default: 10 per page
        $events  = $query->paginate($perPage);

        return response()->json([
            'total'        => $events->total(),
            'per_page'     => $events->perPage(),
            'current_page' => $events->currentPage(),
            'last_page'    => $events->lastPage(),
            'events'       => $events->items(),
        ]);
    }

    // POST /api/events
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'event_date'  => 'required|date|after:now',
            'location'    => 'nullable|string|max:255',
        ]);

        $event = $request->user()->events()->create($validated);

        return response()->json([
            'message' => 'Event created successfully',
            'event'   => $event,
        ], 201);
    }

    // GET /api/events/{id}
    public function show(Request $request, $id)
    {
        $event = $request->user()->events()->find($id);

        if (! $event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json(['event' => $event]);
    }

    // PUT /api/events/{id}
    public function update(Request $request, $id)
    {
        $event = $request->user()->events()->find($id);

        if (! $event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if ($event->status === 'cancelled') {
            return response()->json([
                'message' => 'Cancelled events cannot be edited.',
            ], 422);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:2000',
            'event_date'  => 'sometimes|date|after:now',
            'location'    => 'nullable|string|max:255',
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Event updated successfully',
            'event'   => $event,
        ]);
    }

    // DELETE /api/events/{id}
    public function destroy(Request $request, $id)
    {
        $event = $request->user()->events()->find($id);

        if (! $event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}