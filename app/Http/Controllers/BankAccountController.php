<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\Bank;
use Inertia\Inertia;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = BankAccount::join('banks AS b', 'b.id', 'bank_accounts.bank_id')
            ->join('account_types AS at', 'at.id', 'bank_accounts.account_type_id')
            ->select('bank_accounts.id', 'bank_accounts.name', 'bank_accounts.active',
                'b.name AS bank', 'at.name AS account_type');

        if ($request->search) {
            $data->where('name', 'like', "%$request->search%");
        }

        if ($request->sortBy) {
            $data->orderBy($request->sortBy, $request->sortDesc ? 'DESC' : 'ASC');
        }

        $data = $data->paginate(12);

        return Inertia::render('BankAccount/List', [
            'data' => $data,
            'options' => [
                'page' => $data->currentPage(),
                'search' => $request->search ?? '',
                'sortBy' => $request->sortBy ?? '',
                'sortDesc' => $request->sortDesc,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('BankAccount/Edit', [
            'banks' => Bank::orderBy('name')->select('id', 'name')->get(),
            'accountTypes' => AccountType::orderBy('name')
                ->select('id', 'name', 'isCredit')->get(),
        ]);
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
            'item' => 'array',
            'item.name' => 'required|unique:bank_accounts,name',
            'item.bankId' => 'required',
            'item.accountTypeId' => 'required',
        ]);

        $data = [
            'name' => $request->item['name'],
            'bank_id' => $request->item['bankId'],
            'account_type_id' => $request->item['accountTypeId'],
            'active' => $request->item['active'] ?? false,
        ];

        if (AccountType::find($request->item['accountTypeId'])->isCredit) {
            $data['credit_limit'] = $request->item['creditLimit'];
        }

        $newRow = BankAccount::create($data);
        return response()->json($newRow->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bankAccount)
    {
        return Inertia::render('BankAccount/Edit', [
            'item' => $bankAccount,
            'banks' => Bank::orderBy('name')->select('id', 'name')->get(),
            'accountTypes' => AccountType::orderBy('name')
                ->select('id', 'name', 'isCredit')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'item' => 'array',
            'item.name' => 'required|unique:bank_accounts,name,' . $bankAccount->id . ',id',
            'item.bankId' => 'required',
            'item.accountTypeId' => 'required',
        ]);

        $bankAccount->name = $request->item['name'];
        $bankAccount->bank_id = $request->item['bankId'];
        $bankAccount->account_type_id = $request->item['accountTypeId'];
        $bankAccount->active = $request->item['active'] ?? false;

        if (AccountType::find($request->item['accountTypeId'])->isCredit) {
            $bankAccount->credit_limit = $request->item['creditLimit'];
        }
        $bankAccount->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
    }
}
