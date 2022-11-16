<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\AccountService;
use App\Services\TransactionManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TransactionController extends Controller
{
    private TransactionManager $manager;

    public function __construct()
    {
        $this->manager = app(TransactionManager::class);
    }

    public function event(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            $response = $this->manager->persist($payload);
            return response()->json($response, Response::HTTP_CREATED);
        } catch (Throwable) {
            return response()->json(0, Response::HTTP_NOT_FOUND);
        }
    }

    public function balance(Request $request): JsonResponse
    {
        try {
            $accountId = $request->get('account_id');
            $balance = $this->manager->getBalance($accountId);
            return response()->json($balance, Response::HTTP_OK);
        } catch (Throwable) {
            return response()->json(0, Response::HTTP_NOT_FOUND);
        }
    }
}
