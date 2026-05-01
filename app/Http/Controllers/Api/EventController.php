<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class EventController extends Controller
{
    // GET /api/events
    public function index(Request $request)
    {
        try {
            $request->validate([
                'status'   => 'sometimes|in:pending,done,cancelled',
                'time'     => 'sometimes|in:upcoming,past',
                'search'   => 'sometimes|string|max:100',
                'sort_by'  => 'sometimes|in:event_date,created_at,status',
                'sort_dir' => 'sometimes|in:asc,desc',
                'per_page' => 'sometimes|integer|min:1|max:100',
            ]);

            $query = $request->user()->events();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('time')) {
                $now = now();
                if ($request->time === 'upcoming') {
                    $query->where('event_date', '>=', $now);
                } else {
                    $query->where('event_date', '<', $now);
                }
            }

            if ($request->filled('search')) {
                $keyword = $request->search;
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
                });
            }

            $sortBy  = $request->input('sort_by', 'created_at');
            $sortDir = $request->input('sort_dir', 'desc');
            $query->orderBy($sortBy, $sortDir);

            $perPage = $request->input('per_page', 10);
            $events  = $query->paginate($perPage);

            return response()->json([
                'total'        => $events->total(),
                'per_page'     => $events->perPage(),
                'current_page' => $events->currentPage(),
                'last_page'    => $events->lastPage(),
                'events'       => $events->items(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch events',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // POST /api/events
    public function store(Request $request)
    {
        try {
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

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/events/{id}
    public function show(Request $request, $id)
    {
        try {
            $event = $request->user()->events()->with('reminders')->find($id);

            if (! $event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            return response()->json(['event' => $event]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch event',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // PUT /api/events/{id}
    public function update(Request $request, $id)
    {
        try {
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

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update event',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // DELETE /api/events/{id}
    public function destroy(Request $request, $id)
    {
        try {
            $event = $request->user()->events()->find($id);

            if (! $event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            $event->delete();

            return response()->json([
                'message' => 'Event deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete event',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
