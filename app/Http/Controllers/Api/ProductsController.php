<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ProductServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductsController extends Controller
{
    public function __construct(private readonly ProductServiceInterface $products)
    {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->products->paginate($request->only(['sort', 'direction'])));
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        
        try {
            $product = $this->products->create($this->validatedProduct($request));
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to create product.'], 500);
        }

        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        try {
            $updatedProduct = $this->products->update($product, $this->validatedProduct($request, $product));
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }

        return response()->json($updatedProduct);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $this->products->delete($product);

        return response()->noContent();
    }

    public function purchase(Request $request, Product $product): JsonResponse
    {
        try {
            $data = $request->validate([
                'quantity' => ['required', 'integer', 'min:1'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }

        return response()->json(
            $this->products->purchase($product, (int) $data['quantity'], (int) $request->user()->id),
            201
        );
    }

    private function validatedProduct(Request $request, ?Product $product = null): array
    {
        $id = $product?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:products,name,'.$id],
            'price' => ['required', 'numeric', 'gt:0'],
            'quantity_available' => ['required', 'integer', 'min:0'],
        ]);
    }
}