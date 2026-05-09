<?php

namespace App\Services;

use App\Contracts\ProductServiceInterface;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductService implements ProductServiceInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $allowedSorts = ['id', 'name', 'price', 'quantity_available', 'created_at'];
        $sort = in_array($filters['sort'] ?? '', $allowedSorts, true) ? $filters['sort'] : 'name';
        $direction = ($filters['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        return Product::query()
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();
    }

    public function find(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function purchase(Product $product, int $quantity, int $userId): Transaction
    {
        return DB::transaction(function () use ($product, $quantity, $userId): Transaction {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);

            if ($lockedProduct->quantity_available < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Not enough inventory is available for this purchase.',
                ]);
            }

            $lockedProduct->decrement('quantity_available', $quantity);

            return Transaction::create([
                'user_id' => $userId,
                'product_id' => $lockedProduct->id,
                'quantity' => $quantity,
                'unit_price' => $lockedProduct->price,
                'total_price' => number_format((float) $lockedProduct->price * $quantity, 3, '.', ''),
            ]);
        });
    }
}
