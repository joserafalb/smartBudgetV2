<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\CategoryType;
use Illuminate\Http\Request;

class CategoryTypeController extends Controller
{

    private const LIST_VIEW = 'CategoryType/List';
    private const EDIT_VIEW = 'CategoryType/Edit';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = CategoryType::select('id', 'name', 'active');
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
            'name' => 'required|unique:category_types,name'
        ]);

        $newRow = CategoryType::create([
            'active' => $request->active ?? false,
            'name' => $request->name,
        ]);

        return response()->json($newRow->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CategoryType  $categoryType
     * @return \Illuminate\Http\Response
     */
    public function show(CategoryType $categoryType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CategoryType  $categoryType
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryType $categoryType)
    {
        return Inertia::render(self::EDIT_VIEW, [
            'item' => $categoryType
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CategoryType  $categoryType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoryType $categoryType)
    {
        $request->validate([
            'name' => 'required|unique:category_types,name,' . $categoryType->id . ',id'
        ]);

        $categoryType->name = $request->name;
        $categoryType->active = $request->active ?? false;
        $categoryType->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CategoryType  $categoryType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CategoryType $categoryType)
    {
        $categoryType->delete();
    }
}
