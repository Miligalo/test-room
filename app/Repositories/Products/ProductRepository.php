<?php

namespace App\Repositories\Products;

use App\Models\Product;
use App\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ProductRepository extends Repository
{
    const PAGINATE_COUNT = 30;

    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function paginate(array $validated): LengthAwarePaginator
    {
        return Product::query()
            ->when(isset($validated['title']), function (Builder $query) use ($validated): void {
                $query->where('products.title', 'like', '%' . $validated['title'] . '%');
            })
            ->when(isset($validated['price']), function (Builder $query) use ($validated): void {
                $query->where('products.description', 'like', '%' . $validated['price'] . '%');
            })
            ->orderBy('products.id')
            ->paginate(self::PAGINATE_COUNT);
    }
}
