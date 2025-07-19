<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserStatusController extends Controller
{
    public function toggleBreak(Request $request)
    {
        $user = $request->user();

        // Toggle status antara online dan on_break
        $newStatus = $user->status === 'on_break' ? 'online' : 'on_break';

        $user->update([
            'status' => $newStatus,
            'status_updated_at' => now(),
        ]);

        return back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,on_a_call',
        ]);

        $request->user()->update([
            'status' => $request->status,
            'status_updated_at' => now(),
        ]);

        return response()->json(['message' => 'Status updated successfully.']);
    }
}
