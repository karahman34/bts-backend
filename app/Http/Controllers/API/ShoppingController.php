<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShoppingResource;
use App\Models\Shopping;
use Illuminate\Http\Request;

class ShoppingController extends Controller
{
    public function index()
    {
        try {
            $shoppings = Shopping::all();

            return ShoppingResource::collection($shoppings);
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'msg' => 'Failed to load shopping data',
            ], 500);
        }
    }

    public function detail($shoppingId)
    {
        try {
            $shopping = Shopping::find($shoppingId);

            if (!$shopping) {
                return response()->json([
                    'ok' => false,
                    'msg' => 'Shopping data not found',
                ], 404);
            }

            return new ShoppingResource($shopping);
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'msg' => 'Failed to load shopping data',
            ], 500);
        }
    }

    public function create(Request $request)
    {
        $payload = $request->validate([
            'shopping' => 'required|array',
            'shopping.name' => 'required|string',
            'shopping.createddate' => 'required|date'
        ]);

        try {
            $shopping = Shopping::create([
                'Name' => $payload['shopping']['name'],
                'CreatedDate' => $payload['shopping']['createddate'],
            ]);

            return response()->json([
                'data' => new ShoppingResource($shopping)
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'msg' => 'Failed to create shopping data',
            ], 500);
        }
    }

    public function update(Request $request, $shoppingId)
    {
        $payload = $request->validate([
            'shopping' => 'required|array',
            'shopping.name' => 'required|string',
            'shopping.createddate' => 'required|date'
        ]);

        try {
            $shopping = Shopping::find($shoppingId);

            if (!$shopping) {
                return response()->json([
                    'ok' => false,
                    'msg' => 'Shopping not found',
                ], 404);
            }

            $shopping->update([
                'Name' => $payload['shopping']['name'],
                'CreatedDate' => $payload['shopping']['createddate'],
            ]);

            return new ShoppingResource($shopping);
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'msg' => 'Failed to load shopping data',
            ], 500);
        }
    }

    public function destroy($shoppingId)
    {
        try {
            $shopping = Shopping::find($shoppingId);

            if (!$shopping) {
                return response()->json([
                    'ok' => false,
                    'msg' => 'Shopping not found',
                ], 404);
            }

            $shopping->delete();

            return response()->json('Success', 204);
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'msg' => 'Failed to load shopping data',
            ], 500);
        }
    }
}
