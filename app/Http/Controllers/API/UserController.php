<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::all();

            return $users;
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'msg' => 'Failed to load user data',
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $payload = $request->validate([
            'user' => 'required|array',
            'user.username' => 'required|string',
            'user.email' => 'required|email',
            'user.encrypted_password' => 'required|string',
            'user.phone' => 'required|string',
            'user.address' => 'required|string',
            'user.city' => 'required|string',
            'user.country' => 'required|string',
            'user.name' => 'required|string',
            'user.postcode' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $payload = $payload['user'];

            $existUser = User::where([
                'email' => $payload['email']
            ])->first();

            if ($existUser) {
                return response()->json([
                    'ok' => false,
                    'msg' => 'Email already registered',
                ], 422);
            }

            $payload['encrypted_password'] = Hash::make($payload['encrypted_password']);
            $payload['password'] = $payload['encrypted_password'];

            unset($payload['encrypted_password']);

            $user = User::create($payload);
            $token = Auth::login($user);

            DB::commit();

            return response()->json([
                'email' => $user->email,
                'token' => $token,
                'username' => $user->username,
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'msg' => 'Failed to register user',
                'e' => $th->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $payload = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try {
            $token = Auth::attempt($payload);
            $user = Auth::user();

            if (!$token) {
                return response()->json([
                    'ok' => false,
                    'msg' => 'Email or Password is wrong.'
                ], 401);
            }

            return response()->json([
                'email' => $user->email,
                'token' => $token,
                'username' => $user->username,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'msg' => 'Failed to register user',
            ], 500);
        }
    }
}
