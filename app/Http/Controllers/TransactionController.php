<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'date' => 'required',
            'categoryId' => 'required',
            'amount' => 'required',
            'bankAccountId' => 'required',
            'status' => 'required',
        ]);

        $data = [
            'description' => $request->description,
            'date' => $request->date,
            'category_id' => $request->categoryId,
            'amount' => $request->amount,
            'status' => $request->status,
            'bank_account_id' => $request->bankAccountId,
        ];

        $newRow = Transaction::create($data);
        return response()->json($newRow->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'description' => 'required',
            'date' => 'required',
            'categoryId' => 'required',
            'amount' => 'required',
            'bankAccountId' => 'required',
            'status' => 'required',
        ]);

        $transaction->description = $request->description;
        $transaction->date = $request->date;
        $transaction->category_id = $request->categoryId;
        $transaction->amount = $request->amount;
        $transaction->status = $request->status;
        $transaction->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     *
     * @return void
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
    }
}
