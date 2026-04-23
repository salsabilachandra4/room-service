<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RoomController extends Controller
{
    public function index(): JsonResponse
    {
        $rooms = Room::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kamar berhasil diambil',
            'data' => $rooms
        ], 200);
    }

    public function show($id): JsonResponse
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kamar berhasil diambil',
            'data' => $room
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number',
            'type' => 'required|string',
            'price' => 'required|integer',
            'capacity' => 'required|integer',
            'status' => 'required|string|in:available,booked'
        ]);

        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil disimpan',
            'data' => $room
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $id,
            'type' => 'required|string',
            'price' => 'required|integer',
            'capacity' => 'required|integer',
            'status' => 'required|string|in:available,booked'
        ]);

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil diperbarui',
            'data' => $room
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar tidak ditemukan'
            ], 404);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil dihapus'
        ], 200);
    }

    public function available(): JsonResponse
    {
        $rooms = Room::where('status', 'available')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kamar tersedia berhasil diambil',
            'data' => $rooms
        ], 200);
    }

    public function bookings($id): JsonResponse
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar tidak ditemukan'
            ], 404);
        }

        $bookingServiceUrl = env('BOOKING_SERVICE_URL', 'http://127.0.0.1:8003');

        try {
            $response = Http::get($bookingServiceUrl . '/api/bookings');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data booking dari booking-service'
                ], 500);
            }

            $bookings = $response->json('data', []);

            $roomBookings = collect($bookings)
                ->where('room_id', (int) $id)
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'message' => 'Data booking kamar berhasil diambil',
                'room' => $room,
                'data' => $roomBookings
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking service tidak dapat diakses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
