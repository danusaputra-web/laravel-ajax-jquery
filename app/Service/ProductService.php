<?php

namespace App\Service;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ProductService
{

    public function getByUuid(string $id)
    {
        return response()->json(['data' => Product::where('uuid', $id)->first()]);
    }

    public function create(array $data)
    {
        $data['uuid'] = Str::uuid();
        $data['slug'] = Str::slug($data['name']);

        return Product::create($data);
    }

    public function updateProduct(array $data, string $id)
    {
        $data['uuid'] = Str::uuid(); //uuid berganti ketika data diupdate
        $data['slug'] = Str::slug($data['name']);
        return Product::where('uuid', $id)->update($data);
    }

    public function deleteProduct(string $id)
    {
        if (Product::where('uuid', $id)->first()->image) {
            Storage::disk('public')->delete('images/' . Product::where('uuid', $id)->first()->image);
        }
        return Product::where('uuid', $id)->delete();
    }
    public function getDataTable()
    {
        $product = Product::latest()->get();

        return DataTables::of($product)
            ->addIndexColumn()
            ->editColumn('image', function ($row) {
                return '<div class="text-center">
                            <a href="' . asset('storage/images/' . $row->image) . '" target="_blank">
                                <img src="' . asset('storage/images/' . $row->image) . '" alt="' . $row->name . '" class="img-thumbnail" width="300" height="300">
                            </a>
                        </div>';
            })
            ->addColumn('action', function ($row) {
                return ' <div class="text-center">
                            <button class="btn btn-sm btn-success" onclick="editModal(this)" data-id="' . $row->uuid . '">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteModal(this)" data-id="' . $row->uuid . '">Delete</button>
                        </div>';
            })
            ->rawColumns(['image', 'action'])
            ->make();
    }
}
