<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Jobs\SendOrderSuccessEmail;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $order = $request->user()->orders()->create($request->validated());

        SendOrderSuccessEmail::dispatch($order);

        return response()->json(['order' => $order], 201);
    }
}
