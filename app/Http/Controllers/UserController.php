<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
    public function store(UserRequest $request)
    {
        try {
            // Validasi data yang diinput
            $data = $request->validated();

            // Encrypt password
            $data['password'] = Hash::make($data['password']);

            // Buat user
            $user = User::create($data);

            // Buat token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Buat sektor baru sebagai default
            $sector = $user->sector()->create([
                'name' => 'Home',
                'user_id' => $user->id,
            ])->toArray();

            return response()->json([
                'status' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'token' => $token,
                    ],
                    'sector' => [$sector],
                ],
            ], Response::class::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Kesalahan server, silahkan coba lagi dan hubungi costumer service' .$th
            ], Response::class::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function Login(UserRequest $request)
    {
        try {
            // Validasi data yang diinput
            $data = $request->validated();

            // Check apakah user sudah terdaftar
            $user = User::where('email', $data['email'])->first();

            if (!$user || !\Hash::check($data['password'], $user->password)) {
                if (!$user) {
                    $error = ['email' => ["Email tidak ditemukan"]];
                    $res = Response::class::HTTP_NOT_FOUND;
                } else {
                    $error = ['password' => ["Password salah"]];
                    $res = Response::class::HTTP_UNAUTHORIZED;
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Kesalahan login',
                    'errors' => $error,
                ], $res);
            }

            // Buat token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Ambil data user: sektor dan perangkat
            $sector = $user->sector()->get();
            $device = Device::where('sector_id', $sector[0]->id)->get();

            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'token' => $token,
                    ],
                    'sector' => $sector,
                    'device' => $device
                ]
            ], Response::class::HTTP_OK);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
    public function destroy(string $id)
    {
        //
    }
}
