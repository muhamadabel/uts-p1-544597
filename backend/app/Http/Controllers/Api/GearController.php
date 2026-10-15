<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gear;
use Illuminate\Http\Request;

class GearController extends Controller
{
    /**
     * Menampilkan semua data gear.
     */
    public function index()
    {
        $gears = Gear::latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $gears,
        ], 200);
    }

    /**
     * Menyimpan data gear baru.
     * Mengembalikan status HTTP 201 Created.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'serial_number' => 'required|string|max:100|unique:gears,serial_number',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,in_use,maintenance',
        ]);

        $gear = Gear::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data gear berhasil dibuat.',
            'data' => $gear,
        ], 201);
    }

    /**
     * Menampilkan satu data gear spesifik.
     */
    public function show($id)
    {
        $gear = Gear::find($id);

        if (!$gear) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gear tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $gear,
        ], 200);
    }

    /**
     * Memperbarui data gear.
     */
    public function update(Request $request, $id)
    {
        $gear = Gear::find($id);

        if (!$gear) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gear tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:100',
            'serial_number' => 'sometimes|required|string|max:100|unique:gears,serial_number,' . $id,
            'daily_rate' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:available,in_use,maintenance',
        ]);

        $gear->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data gear berhasil diperbarui.',
            'data' => $gear,
        ], 200);
    }

    /**
     * Menghapus data gear.
     */
    public function destroy($id)
    {
        $gear = Gear::find($id);

        if (!$gear) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data gear tidak ditemukan.',
            ], 404);
        }

        $gear->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data gear berhasil dihapus.',
        ], 200);
    }
}
