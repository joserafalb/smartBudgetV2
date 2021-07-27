<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Inertia\Inertia;
use Illuminate\Http\Request;

class GoalController extends Controller
{

    const LIST_VIEW = 'Goal/List';
    const EDIT_VIEW = 'Goal/Edit';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Goal::select('id', 'name', 'amount', 'date', 'active');
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
            'name' => 'required|unique:banks,name',
            'amount' => 'required|min:1|max:10|regex:/^\d+(\.\d{1,2})?$/',
            'date' => 'required'
        ]);

        $newRow = Goal::create([
            'active' => $request->active ?? false,
            'amount' => $request->amount,
            'date' => $request->date,
            'name' => $request->name,
        ]);

        return response()->json($newRow->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\Http\Response
     */
    public function show(Goal $goal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\Http\Response
     */
    public function edit(Goal $goal)
    {
        return Inertia::render(self::EDIT_VIEW, [
            'item' => $goal
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Goal $goal)
    {
        $request->validate([
            'name' => 'required|unique:goals,name,' . $goal->id . ',id',
            'amount' => 'required|min:1|max:10|regex:/^\d+(\.\d{1,2})?$/',
            'date' => 'required'
        ]);

        $goal->active = $request->active ?? false;
        $goal->amount = $request->amount;
        $goal->date = $request->date;
        $goal->name = $request->name;
        $goal->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Goal $goal)
    {
        $goal->delete();
    }
}
