<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_method_returns_paginated_products()
    {
        // Create some products manually
        $products = [
            Product::factory()->create(),
            Product::factory()->create(),
            Product::factory()->create(),
            Product::factory()->create(),
            Product::factory()->create(),
        ];

        // Make a request to the index endpoint
        $response = $this->get('/products');

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the response contains the paginated products
        foreach ($products as $product) {
            $response->assertJsonFragment($product->toArray());
        }
    }

    public function test_productsPrice_method_returns_profit_and_sales()
    {
        // Create some products manually
        $products = [
            Product::factory()->create(['initPrice' => 10, 'price' => 20, 'quantity' => 10]),
            Product::factory()->create(['initPrice' => 10, 'price' => 20, 'quantity' => 5]),
            Product::factory()->create(['initPrice' => 10, 'price' => 20, 'quantity' => 8]),
        ];

        // Make a request to the productsPrice endpoint
        $response = $this->get('/products/price');

        // Calculate expected profit and sales
        $expectedProfit = ($products[0]->price - $products[0]->initPrice) * $products[0]->quantity +
            ($products[1]->price - $products[1]->initPrice) * $products[1]->quantity +
            ($products[2]->price - $products[2]->initPrice) * $products[2]->quantity;

        $expectedSales = $products[0]->initPrice * $products[0]->quantity +
            $products[1]->initPrice * $products[1]->quantity +
            $products[2]->initPrice * $products[2]->quantity;

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the response contains the expected profit and sales
        $response->assertJson(['profit' => $expectedProfit, 'sales' => $expectedSales]);
    }

    public function test_store_method_creates_product_and_updates_budget()
    {
        // Create a budget
        $budget = Budget::factory()->create(['budget' => 100]);

        // Make a request to store a new product
        $response = $this->postJson('/products', [
            'name' => 'New Product',
            'description' => 'Description of new product',
            'initPrice' => 10,
            'price' => 20,
            'quantity' => 5,
            'image' => UploadedFile::fake()->image('product.jpg'),
        ]);

        // Assert that the product was created successfully
        $response->assertStatus(201)
            ->assertJson(['message' => 'Product created successfully']);

        // Assert that the product was stored in the database
        $this->assertDatabaseHas('products', ['name' => 'New Product']);

        // Assert that the budget was updated
        $this->assertDatabaseHas('budgets', ['budget' => 50]); // 100 - (10 * 5)
    }

    public function test_show_method_returns_product()
    {
        // Create a product
        $product = Product::factory()->create();

        // Make a request to the show endpoint
        $response = $this->get("/products/{$product->id}");

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the response contains the product data
        $response->assertJson($product->toArray());
    }

    public function test_update_method_updates_product_and_budget()
    {
        // Create a product
        $product = Product::factory()->create(['initPrice' => 10, 'price' => 20, 'quantity' => 5]);

        // Create a budget
        $budget = Budget::factory()->create(['budget' => 100]);

        // Make a request to update the product
        $response = $this->putJson("/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'initPrice' => 15,
            'price' => 25,
            'quantity' => 10,
            'image' => UploadedFile::fake()->image('updated_product.jpg'),
        ]);

        // Assert that the product was updated successfully
        $response->assertStatus(200)
            ->assertJson(['message' => 'Product updated successfully']);

        // Assert that the product was updated in the database
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);

        // Assert that the budget was updated
        $this->assertDatabaseHas('budgets', ['budget' => 50]); // 100 - (15 * 10)
    }

    public function test_destroy_method_deletes_product_and_updates_budget()
    {
        // Create a product
        $product = Product::factory()->create(['initPrice' => 10, 'price' => 20, 'quantity' => 5]);

        // Create a budget
        $budget = Budget::factory()->create(['budget' => 100]);

        // Make a request to delete the product
        $response = $this->delete("/products/{$product->id}");

        // Assert that the product was deleted successfully
        $response->assertStatus(200)
            ->assertJson(['message' => 'Product deleted successfully']);

        // Assert that the product was deleted from the database
        $this->assertDatabaseMissing('products', ['name' => $product->name]);

        // Assert that the budget was updated
        $this->assertDatabaseHas('budgets', ['budget' => 150]); // 100 + (10 * 5)
    }

    public function test_indexByClient_method_returns_products_for_client()
    {
        // Create a client
        $client = Client::factory()->create();

        // Create products for the client
        $products = [
            Product::factory()->create(['client_id' => $client->id]),
            Product::factory()->create(['client_id' => $client->id]),
        ];

        // Make a request to the indexByClient endpoint
        $response = $this->get("/clients/{$client->id}/products");

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the response contains the products for the client
        $response->assertJson(['data' => $products]);
    }

    public function test_searchProducts_method_returns_matching_products()
    {
        // Create products
        $products = [
            Product::factory()->create(['name' => 'Product A']),
            Product::factory()->create(['name' => 'Product B']),
            Product::factory()->create(['name' => 'Product C']),
        ];

        // Make a request to the searchProducts endpoint with keyword 'Product'
        $response = $this->get('/products/search?keyword=Product');

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the response contains matching products
        foreach ($products as $product) {
            $response->assertJsonFragment($product->toArray());
        }
    }
}
