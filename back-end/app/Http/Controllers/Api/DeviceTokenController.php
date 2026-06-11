<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $req)
    {
        $req->validate([
            'token' => 'required|string',
            'platform' => 'nullable|string',
        ]);

        $user = $req->user(); // مستخدم مصادق عليه
        if (! $user) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        DeviceToken::updateOrCreate(
            ['token' => $req->token], // فقط التوكن هو المفتاح
            ['user_id' => $user->id, 'platform' => $req->platform]
        );

        return response()->json(['ok' => true, 'message' => 'Token saved successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $req->validate(['token' => 'required|string']);

        $user = $req->user();
        if (! $user) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        DeviceToken::where('token', $req->token)
            ->where('user_id', $user->id)
            ->delete();

        return response()->json(['ok' => true, 'message' => 'Token deleted successfully']);
    }
}
