<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Service\ImageService;
use App\Service\ProductService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{


    public function __construct(private ProductService $productService, private ImageService $imageService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('products.index');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            $uploadImage = $this->imageService->uploadImage($data);
            $data['image'] = $uploadImage;
            $this->productService->create($data);
            return response()->json(['text' => 'Product created successfully.', 'icon' => 'success', 'title' => 'Success']);
        } catch (Exception $error) {
            return response()->json(['text' => $error->getMessage(), 'icon' => 'error', 'title' => 'Error']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return $this->productService->getByUuid($id);
        } catch (Exception $th) {
            return response()->json(['message' => 'Product not found.']);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $data = $request->validated();
        $getImage = Product::where('uuid', $id)->first()->image;
        try {
            if ($request->hasFile('image')) {
                $uploadImage = $this->imageService->uploadImage($data, $getImage);
                $data['image'] = $uploadImage;
            } else {
                $data['image'] = $getImage;
            }
            $this->productService->updateProduct($data, $id);
            return response()->json(['text' => 'Product updated successfully.', 'icon' => 'success', 'title' => 'Success']);
        } catch (Exception $error) {
            return response()->json(['text' => $error->getMessage(), 'icon' => 'error', 'title' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted successfully.']);
    }

    public function serverSideTable(): JsonResponse
    {
        return $this->productService->getDataTable();
    }
}
