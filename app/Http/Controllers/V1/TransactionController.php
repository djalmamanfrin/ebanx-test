<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\TransactionManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function event(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = new Event($payload);
        $event->save();
        $manager = new TransactionManager($event);
        return response()->json($manager->persist(), Response::HTTP_CREATED);
    }

    public function balance(Request $request, int $accountId): JsonResponse
    {
        $payload = $request->all();
        return response()->json();
    }
}
