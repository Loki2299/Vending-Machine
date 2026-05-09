<?php

namespace App\Contracts;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function find(int $id): Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): void;

    public function purchase(Product $product, int $quantity, int $userId): Transaction;
}
