<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReminderStatusController extends Controller
{
    // POST /api/reminders/{id}/done
    public function markDone(Request $request, $id)
    {
        $reminder = $request->user()->reminders()->find($id);

        if (! $reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        if ($reminder->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot change status of a cancelled reminder.',
            ], 422);
        }

        if ($reminder->status === 'done') {
            return response()->json([
                'message' => 'Reminder is already marked as done.',
            ], 422);
        }

        $reminder->update(['status' => 'done']);

        return response()->json([
            'message'  => 'Reminder marked as done.',
            'reminder' => $reminder,
        ]);
    }

    // POST /api/reminders/{id}/undone
    public function markUndone(Request $request, $id)
    {
        $reminder = $request->user()->reminders()->find($id);

        if (! $reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        if ($reminder->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot change status of a cancelled reminder.',
            ], 422);
        }

        if ($reminder->status === 'pending') {
            return response()->json([
                'message' => 'Reminder is already pending (not done).',
            ], 422);
        }

        $reminder->update(['status' => 'pending']);

        return response()->json([
            'message'  => 'Reminder marked as not done.',
            'reminder' => $reminder,
        ]);
    }

    // POST /api/reminders/{id}/cancel
    public function cancel(Request $request, $id)
    {
        $reminder = $request->user()->reminders()->find($id);

        if (! $reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        if ($reminder->status === 'cancelled') {
            return response()->json([
                'message' => 'Reminder is already cancelled.',
            ], 422);
        }

        $reminder->update(['status' => 'cancelled']);

        return response()->json([
            'message'  => 'Reminder cancelled successfully.',
            'reminder' => $reminder,
        ]);
    }
}