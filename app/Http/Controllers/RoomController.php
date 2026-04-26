<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RoomController extends Controller
{
    public function index()
    {
        return response()->json(Room::orderBy('id', 'asc')->get());
    }

    public function show($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        return response()->json($room);
    }

    public function store(Request $request)
    {
        $room = Room::create([
            'room_number' => $request->room_number,
            'type' => $request->type,
            'price' => $request->price,
            'capacity' => $request->capacity,
            'status' => $request->status ?? 'available',
        ]);

        return response()->json($room, 201);
    }

    public function update(Request $request, $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $room->update($request->only([
            'room_number',
            'type',
            'price',
            'capacity',
            'status'
        ]));

        return response()->json($room);
    }

    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $room->delete();

        return response()->json([
            'message' => 'Room deleted'
        ]);
    }

    public function available()
    {
        $rooms = Room::where('status', 'available')->get();

        return response()->json($rooms);
    }

    public function bookings($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $response = Http::get('http://127.0.0.1:8000/api/bookings?room_id=' . $id);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Booking service error'
            ], 500);
        }

        return response()->json([
            'room' => $room,
            'bookings' => $response->json()
        ]);
    }
    public function reduceStock($id)
{
    $room = Room::find($id);

    if (!$room) {
        return response()->json(['message' => 'Room not found'], 404);
    }

    if ($room->capacity <= 0) {
        return response()->json(['message' => 'Room stock is empty'], 400);
    }

    $room->capacity = $room->capacity - 1;
    $room->save();

    return response()->json([
        'message' => 'Room stock reduced successfully',
        'data' => $room
    ]);
}
}