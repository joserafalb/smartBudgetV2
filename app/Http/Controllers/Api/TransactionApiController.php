<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class TransactionApiController extends Controller
{
    public function Add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'date' => 'required',
            'categoryId' => 'required',
            'amount' => 'required',
            'bankAccountId' => 'required',
            'status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'description' => $request->description,
            'date' => $request->date,
            'category_id' => $request->categoryId,
            'amount' => $request->amount,
            'status' => $request->status,
            'bank_account_id' => $request->bankAccountId,
        ];

        $transaction = Transaction::create($data);

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ], 201);
    }
}
