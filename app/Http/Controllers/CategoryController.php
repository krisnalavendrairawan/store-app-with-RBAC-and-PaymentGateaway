<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    private $title = 'label.category';
    private $icon = 'mdi mdi-account';
    private $dir = 'backend.category.';

    public function index()
    {
        return view($this->dir . 'index', [
            'title' => __($this->title),
            'icon' => $this->icon,
        ]);
    }

    public function dataTable()
    {
        $categories = Category::select(['id', 'name', 'description']);

        return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('encrypted_id', function ($category) {
                return Crypt::encrypt($category->id);
            })
            ->make(true);
    }

    public function create()
    {
        return view($this->dir . 'create', [
            'title' => __('label.create') . ' ' . __($this->title),
            'icon' => $this->icon,
        ]);
    }

    public function store(CategoryRequest $request)
    {
        // dd($request->all());
        $data = $request->all();
        Category::create($data);
        $message = __('message.create_success');
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('categories.index')->with('success', $message);
    }

    public function edit(Category $category)
    {
        return view($this->dir . 'edit', [
            'title' => __('label.edit') . ' ' . __($this->title),
            'icon' => $this->icon,
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->all());
        if ($request->ajax()) {
            return response()->json(['success' => __('message.update_success')]);
        }

        return redirect()->route('categories.index')->with('success', __('message.update_success'));
    }

    public function destroy(Category $category)
    {
        try {
            // $user->delete();
            $category->forceDelete();
            return response()->json(['success' => __('message.delete_success')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('message.delete_error')], 500);
        }
    }
}
