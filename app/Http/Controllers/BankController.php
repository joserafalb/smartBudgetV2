<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BankController extends Controller
{

    const LIST_VIEW = 'Bank/List';
    const EDIT_VIEW = 'Bank/Edit';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Bank::select('id', 'name', 'active');
        if ($request->search) {
            $data->where('name', 'like', "%$request->search%");
        }

        if ($request->sortBy) {
            $data->orderBy($request->sortBy, $request->sortDesc ? 'DESC' : 'ASC');
        }

        $data = $data->paginate(12);

        return Inertia::render(self::LIST_VIEW, [
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
        return Inertia::render(self::EDIT_VIEW, []);
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
            'name' => 'required|unique:banks,name'
        ]);

        $newRow = Bank::create([
            'active' => $request->active ?? false,
            'name' => $request->name,
        ]);

        return response()->json($newRow->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        return Inertia::render(self::EDIT_VIEW, [
            'item' => $bank
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|unique:banks,name,' . $bank->id . ',id'
        ]);

        $bank->name = $request->name;
        $bank->active = $request->active ?? false;
        $bank->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();
    }
}
