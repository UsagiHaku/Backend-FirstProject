<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_client_can_create_a_product()
    {
        // Given
        $productData = [
            'name' => 'Super Product',
            'price' => '23.30'
        ];

        // When
        $response = $this->json('POST', '/api/products', $productData); 

        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(201);
        
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'id',
            'name',
            'price'
        ]);

        // Assert the product was created
        // with the correct data
        $response->assertJsonFragment([
            'name' => 'Super Product',
            'price' => '23.30'
        ]);
            
        $body = $response->decodeResponseJson();

        // Assert product is on the database
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['id'],
                'name' => 'Super Product',
                'price' => '23.30'
            ]
        );
    }

    public function test_list_products()
    {
        // Given
        $product = factory(Product::class)->create([
            'name' => 'Product name',
            'price' => '100.30',
        ]);
        $response = $this->json('GET', '/api/products'); 

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'Product name',
                    'price' => '100.30',
                ]);
                
    }

    public function test_show_one_product()
    {
        // Given
        $product = factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name',
            'price' => '100.30',
        ]);

        $response = $this->json('GET', '/api/products/1'); 

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'Product name',
                    'price' => '100.30',
                ]);
                
    }

    public function test_update_one_product()
    {
        // Given
        $product = factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name',
            'price' => '100.30',
        ]);

        $updatedProduct = [
            'name' => 'Updated Product name'
        ];

        $response = $this->put(route('products.update', $product->id), $updatedProduct);

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'Updated Product name',
                    'price' => '100.30',
                ]);
                
    }

    public function test_delete_one_product()
    {
        // Given
        $product = factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name',
            'price' => '100.30',
        ]);

        $response = $this->delete(route('products.delete', $product->id));

        $response->assertStatus(204);
    }
}
