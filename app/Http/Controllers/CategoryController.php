<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use App\Models\CategoryType;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    const LIST_VIEW = 'Category/List';
    const EDIT_VIEW = 'Category/Edit';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Category::join('category_types AS ct', 'ct.id', 'categories.category_type_id')
        ->select('categories.id', 'categories.name', 'ct.name AS type', 'categories.active');

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
        return Inertia::render(self::EDIT_VIEW, [
            'categoryTypes' => CategoryType::orderBy('name')->select('id', 'name')->get(),
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
            'item.name' => 'required|unique:categories,name',
            'item.categoryTypeId' => 'required',
        ]);

        $data = [
            'name' => $request->item['name'],
            'category_type_id' => $request->item['categoryTypeId'],
            'active' => $request->item['active'] ?? false,
        ];

        $newRow = Category::create($data);
        return response()->json($newRow->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return Inertia::render(self::EDIT_VIEW, [
            'item' => $category,
            'categoryTypes' => CategoryType::orderBy('name')->select('id', 'name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'item' => 'array',
            'item.name' => 'required|unique:categories,name,' . $category->id . ',id',
            'item.categoryTypeId' => 'required',
        ]);

        $category->name = $request->item['name'];
        $category->category_type_id = $request->item['categoryTypeId'];
        $category->active = $request->item['active'] ?? false;
        $category->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
    }
}
