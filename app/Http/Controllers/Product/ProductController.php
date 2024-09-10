<?php

declare(strict_types=1);

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\SearchRequest;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\Product;
use App\Models\Task;
use App\Repositories\Products\ProductRepository;
use App\Resource\Products\ProductResource;
use App\Services\Tasks\TaskService;
use Illuminate\Http\JsonResponse;


class ProductController extends Controller
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function index(SearchRequest $request): JsonResponse
    {
        return ProductResource::collection($this->productRepository->paginate($request->validated()))->response();
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return responseSuccess(new ProductResource($this->productRepository->create($request->validated())));
    }

    public function update(Product $product, UpdateRequest $request): JsonResponse
    {
        return responseSuccess($product->update($request->validated()));
    }

    public function destroy(Product $product): JsonResponse
    {
        return responseSuccess($this->productRepository->delete($product->id));
    }

    public function show(Product $product): JsonResponse
    {
        return responseSuccess(new ProductResource($product));
    }

}
