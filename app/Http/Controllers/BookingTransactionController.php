<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Services\BookingTransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    private $service;

    public function __construct(BookingTransactionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $transactions = $this->service->getAll();
        return response()->json(TransactionResource::collection($transactions));
    }

    public function show(int $id)
    {
        try {

            $transaction = $this->service->getByIdForManager($id);
            return response()->json(new TransactionResource($transaction));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Transaction not found',

            ], 404);
        }
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);
        try {
            $transaction = $this->service->updateStatus($id, $validated['status']);
            return response()->json([
                'message' => 'Transaction status updated succesfully.',
                'data' => new TransactionResource($transaction)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    }
}
