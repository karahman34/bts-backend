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
                'msg' => 'Failed to register user',
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $payload = $request->validate([
            'username' => 'required|string',
            'email' => 'required|email',
            'encrypted_password' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'name' => 'required|string',
            'postcode' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
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
