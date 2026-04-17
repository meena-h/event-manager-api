<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventStatusController extends Controller
{
    // POST /api/events/{id}/done
    public function markDone(Request $request, $id)
    {
        $event = $request->user()->events()->find($id);

        if (! $event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if ($event->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot change status of a cancelled event.',
            ], 422);
        }

        if ($event->status === 'done') {
            return response()->json([
                'message' => 'Event is already marked as done.',
            ], 422);
        }

        $event->update(['status' => 'done']);

        return response()->json([
            'message' => 'Event marked as done.',
            'event'   => $event,
        ]);
    }

    // POST /api/events/{id}/undone
    public function markUndone(Request $request, $id)
    {
        $event = $request->user()->events()->find($id);

        if (! $event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if ($event->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot change status of a cancelled event.',
            ], 422);
        }

        if ($event->status === 'pending') {
            return response()->json([
                'message' => 'Event is already pending (not done).',
            ], 422);
        }

        $event->update(['status' => 'pending']);

        return response()->json([
            'message' => 'Event marked as not done.',
            'event'   => $event,
        ]);
    }

    // POST /api/events/{id}/cancel
    public function cancel(Request $request, $id)
    {
        $event = $request->user()->events()->find($id);

        if (! $event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if ($event->status === 'cancelled') {
            return response()->json([
                'message' => 'Event is already cancelled.',
            ], 422);
        }

        $event->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Event cancelled successfully.',
            'event'   => $event,
        ]);
    }
}