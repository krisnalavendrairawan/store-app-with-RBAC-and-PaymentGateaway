<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    private $title = 'label.product';
    private $icon = 'mdi mdi-account';
    private $dir = 'backend.product.';

    public function index()
    {
        return view($this->dir . 'index', [
            'title' => __($this->title),
            'icon' => $this->icon,
        ]);
    }

    public function dataTable()
    {
        $product = Product::select(['id', 'name', 'price', 'image', 'description', 'quantity', 'category_id']);

        return DataTables::of($product)
            ->addIndexColumn()
            ->addColumn('category_name', function ($product) {
                return $product->category ? $product->category->name : '-';
            })
            ->addColumn('image_url', function ($product) {
                if ($product->image) {
                    // Tambahkan timestamp ke URL gambar
                    return Storage::url($product->image) . '?t=' . now()->timestamp;
                }
                return '';
            })
            ->addColumn('encrypted_id', function ($product) {
                return Crypt::encrypt($product->id);
            })
            ->make(true);
    }

    public function create()
    {
        $category = Category::all();
        return view($this->dir . 'create', [
            'title' => __('label.create') . ' ' . __($this->title),
            'icon' => $this->icon,
            'category' => $category,
        ]);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->all();
        $data['image'] = $request->file('image')->store('images', 'public');
        Product::create($data);
        $message = __('message.create_success');
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('product.index')->with('success', $message);
    }

    public function edit(Product $product)
    {
        $category = Category::all();
        return view($this->dir . 'edit', [
            'title' => __('label.edit') . ' ' . __($this->title),
            'icon' => $this->icon,
            'product' => $product,
            'category' => $category,
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // Upload gambar baru ke direktori yang sama dengan method store
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $product->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Product updated successfully']);
        }

        return redirect()->route('product.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->forceDelete();
            return response()->json(['success' => __('message.delete_success')]);
        } catch (\Exception $e) {
            return response()->json(['error' => __('message.delete_error')], 500);
        }
    }
}
