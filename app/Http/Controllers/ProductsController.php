<?php

namespace App\Http\Controllers;

use App\Contracts\ProductServiceInterface;
use App\Routing\Post;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductsController extends Controller
{
    public function __construct(private readonly ProductServiceInterface $products)
    {
    }

    public function index(Request $request): View
    {
        return view('products.index', [
            'products' => $this->products->paginate($request->only(['sort', 'direction'])),
            'sort' => $request->query('sort', 'name'),
            'direction' => $request->query('direction', 'asc'),
        ]);
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->products->create($this->validatedProduct($request));

        return redirect()->route('products.index')->with('status', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->products->update($product, $this->validatedProduct($request, $product));

        return redirect()->route('products.index')->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->products->delete($product);

        return redirect()->route('products.index')->with('status', 'Product deleted successfully.');
    }

    #[Post('/products/{product}/buy', name: 'products.purchase.store')]
    public function purchase(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $this->products->purchase($product, (int) $data['quantity'], (int) $request->user()->id);

        return redirect()->route('products.show', $product)->with('status', 'Purchase completed successfully.');
    }

    public function purchaseForm(Product $product): View
    {
        return view('products.purchase', compact('product'));
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
