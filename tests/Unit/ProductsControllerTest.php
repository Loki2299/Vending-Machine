<?php

namespace Tests\Unit;

use App\Contracts\ProductServiceInterface;
use App\Http\Controllers\ProductsController;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_index_uses_service_pagination(): void
    {
        $paginator = new LengthAwarePaginator([], 0, 10);
        $service = Mockery::mock(ProductServiceInterface::class);
        $service->shouldReceive('paginate')
            ->once()
            ->with(['sort' => 'price', 'direction' => 'desc'])
            ->andReturn($paginator);

        $controller = new ProductsController($service);
        $view = $controller->index(Request::create('/products?sort=price&direction=desc'));

        $this->assertSame('products.index', $view->name());
        $this->assertSame($paginator, $view->getData()['products']);
    }

    public function test_store_validates_and_creates_product_through_service(): void
    {
        $service = Mockery::mock(ProductServiceInterface::class);
        $service->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'Sprite',
                'price' => '2.500',
                'quantity_available' => '10',
            ])
            ->andReturn(new Product());

        $controller = new ProductsController($service);
        $request = Request::create('/admin/products', 'POST', [
            'name' => 'Sprite',
            'price' => '2.500',
            'quantity_available' => '10',
        ]);
        $request->setContainer($this->app);

        $response = $controller->store($request);

        $this->assertSame(302, $response->getStatusCode());
    }

    public function test_store_rejects_non_positive_price(): void
    {
        $this->expectException(ValidationException::class);

        $controller = new ProductsController(Mockery::mock(ProductServiceInterface::class));
        $request = Request::create('/admin/products', 'POST', [
            'name' => 'Invalid',
            'price' => '0',
            'quantity_available' => '5',
        ]);
        $request->setContainer($this->app);

        $controller->store($request);
    }

    public function test_purchase_uses_authenticated_user_and_logs_transaction(): void
    {
        $product = new Product(['name' => 'Coke', 'price' => 3.990, 'quantity_available' => 5]);
        $product->id = 7;

        $user = new User(['name' => 'Buyer', 'email' => 'buyer@example.com', 'role' => 'user']);
        $user->id = 99;

        $transaction = new Transaction([
            'user_id' => 99,
            'product_id' => 7,
            'quantity' => 2,
            'unit_price' => 3.990,
            'total_price' => 7.980,
        ]);

        $service = Mockery::mock(ProductServiceInterface::class);
        $service->shouldReceive('purchase')
            ->once()
            ->with($product, 2, 99)
            ->andReturn($transaction);

        $controller = new ProductsController($service);
        $request = Request::create('/products/7/buy', 'POST', ['quantity' => '2']);
        $request->setContainer($this->app);
        $request->setUserResolver(fn () => $user);

        $response = $controller->purchase($request, $product);

        $this->assertSame(302, $response->getStatusCode());
    }

    public function test_purchase_rejects_zero_quantity(): void
    {
        $this->expectException(ValidationException::class);

        $product = new Product(['name' => 'Water', 'price' => 0.500, 'quantity_available' => 5]);
        $product->id = 3;

        $controller = new ProductsController(Mockery::mock(ProductServiceInterface::class));
        $request = Request::create('/products/3/buy', 'POST', ['quantity' => '0']);
        $request->setContainer($this->app);
        $request->setUserResolver(fn () => new User(['role' => 'user']));

        $controller->purchase($request, $product);
    }
}
