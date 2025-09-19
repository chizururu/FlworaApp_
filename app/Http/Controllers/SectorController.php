<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectorRequest;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SectorRequest $request)
    {
        try {
            // Validasi data yang diinput
            $data = $request->validated();

            // Buat dan simpan data sektor
            $sector = Sector::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Sektor ' . $sector->name . ' berhasil ditambahkan'
            ], Response::class::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Kesalahan server, silahkan coba lagi dan hubungi costumer service'
            ], Response::class::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sector $sector)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SectorRequest $request, $id)
    {
        try {
            // Validasi data yang diinput
            $data = $request->validated();

            // Update sektor berdasarkan id
            $sector = Sector::findOrFail($id);
            $sector->update($data);
            $sector->save();

            return response()->json([
                'status' => true,
                'message' => 'Sektor berhasil diubah'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Kesalahan server, silahkan coba lagi dan hubungi costumer service'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        //
    }
}
