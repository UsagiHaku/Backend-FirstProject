<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /*
     * CREATE-1
     * */
    public function test_client_can_create_a_product()
    {
        // Given
        $productData = [
            "data" => [
                "attributes" => [
                    'name' => 'Product name',
                    'price' => '23.30'
                ]
            ]
        ];

        // When
        $response = $this->json('POST', '/api/products', $productData);

        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(201);

        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'data' => [
                'attributes' => [
                    'name',
                    'price'
                ]
            ]
        ]);

        $body = $response->decodeResponseJson();

        // Assert the product was created
        // with the correct data
        $response->assertJsonFragment([
            'id' => $body['data']['id'],
            "attributes" => [
                'name' => 'Product name',
                'price' => '23.30'
            ]
        ]);



        // Assert product is on the database
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['data']['id'],
                'name' => 'Product name',
                'price' => '23.30'
            ]
        );
    }

    /*
     * CREATE-2
     * */
    public function test_create_product_without_name_return_error()
    {
        $productData = [
            "data" => [
                "attributes" => [
                    'price' => '23.30'
                ]
            ]
        ];

        $response = $this->json('POST', '/api/products', $productData);
        $response->assertStatus(422);

        $response->assertJson([
            'errors' => [[
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity'
            ]]
        ]);
    }

    /*
    * CREATE-3
    * */
    public function test_create_product_without_price_return_error()
    {
        $productData = [
            "data" => [
                "attributes" => [
                    'name' => 'Product name'
                ]
            ]
        ];

        $response = $this->json('POST', '/api/products', $productData);
        $response->assertStatus(422);

        $response->assertJson([
            'errors' => [[
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity'
            ]]
        ]);
    }

    /*
    * CREATE-4
    * */
    public function test_create_product_with_price_not_a_number_return_error()
    {
        $productData = [
            "data" => [
                "attributes" => [
                    'name' => 'Product name',
                    'price' => 'Not a number'
                ]
            ]
        ];

        $response = $this->json('POST', '/api/products', $productData);
        $response->assertStatus(422);

        $response->assertJson([
            'errors' => [[
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity'
            ]]
        ]);
    }

    /*
    * CREATE-5
    * */
    public function test_create_product_with_price_less_than_0_return_error()
    {
        $productData = [
            "data" => [
                "attributes" => [
                    'name' => 'Product name',
                    'price' => '-10'
                ]
        ]
        ];

        $response = $this->json('POST', '/api/products', $productData);
        $response->assertStatus(422);

        $response->assertJson([
            'errors' => [[
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity'
            ]]
        ]);
    }

    /*
     * LIST-1
     */
    public function test_list_with_two_products()
    {
        // Given
        $product1 = factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name 1',
            'price' => '100.30',
        ]);
        $product2 = factory(Product::class)->create([
            'id' => 2,
            'name' => 'Product name 2',
            'price' => '100.30',
        ]);

        $response = $this->json('GET', '/api/products');
        $response->assertStatus(200)
        -> assertJson([
            "data" => [
                [
                    "type" => "products",
                    "id" => $product1->id,
                    "attributes" => [
                        "name" => $product1->name,
                        "price" => $product1->price
                    ],
                    "links" => [
                        "self" => route('products.show', [$product1->id])
                    ]
                ],
                [
                    "type" => "products",
                    "id" => $product2->id,
                    "attributes" => [
                        "name" => $product2->name,
                        "price" => $product2->price
                    ],
                    "links" => [
                        "self" => route('products.show', [$product2->id])
                    ]
                ]
            ]
        ]);
    }

    /*
     * LIST-2
     */
    public function test_list_with_no_values()
    {
        // Given
        factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name 1',
            'price' => '100',
        ]);

        factory(Product::class)->create([
            'id' => 2,
            'name' => 'Product name 2',
            'price' => '200',
        ]);

        $response = $this->json('GET', '/api/products');

        $response->assertStatus(200)
            ->assertJsonFragment([
                "attributes" => [
                    'name' => 'Product name 1',
                    'price' => '100.00'
                ]
            ])
            ->assertJsonFragment([
                "attributes" => [
                    'name' => 'Product name 2',
                    'price' => '200.00'
                ]
            ]);
    }

    /*
     * SHOW-1
     */
    public function test_show_one_product()
    {
        // Given
        factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name 1',
            'price' => '100.30',
        ]);

        $response = $this->json('GET', '/api/products/1');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Product name 1',
                'price' => '100.30',
            ]);

    }

    /*
     * SHOW-2
     */
    public function test_show_with_uncreated_product()
    {
        // Given
        factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name',
            'price' => '100.30',
        ]);

        $response = $this->json('GET','/api/products/2');

        $response->assertStatus(404)
            ->assertJson([
                'errors' => [[
                    'code' => 'ERROR-2',
                    'title' => 'Not Found'
                ]]
            ]);
    }

    /*
     * UPDATE-1
     * */
    public function test_update_one_product()
    {
        $product = factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name',
            'price' => '100.30',
        ]);

        $updatedProduct = [
            "data" => [
                "attributes" => [
                    'name' => 'Updated Product name'
                ]
            ]
        ];

        $response = $this->put(route('products.update', $product->id), $updatedProduct);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Product name',
                'price' => '100.30',
            ]);

    }

    /*
     * UPDATE-2
     * */
    public function test_update_product_with_price_not_a_number_return_error()
    {
        $product = factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name',
            'price' => '100.30',
        ]);

        $updatedProduct = [
            "data" => [
                "attributes" => [
                    'price' => 'Not a number'
                ]
            ]
        ];

        $response = $this->put(route('products.update', $product->id), $updatedProduct);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [[
                    'code' => 'ERROR-1',
                    'title' => 'Unprocessable Entity'
                ]]
            ]);
    }

    /*
     * UPDATE-3
     * */
    public function test_update_product_with_price_less_than_0_return_error()
    {
        $product = factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name',
            'price' => '100.30',
        ]);

        $updatedProduct = [
            "data" => [
                "attributes" => [
                    'price' => '-10'
                ]
            ]
        ];

        $response = $this->put(route('products.update', $product->id), $updatedProduct);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [[
                    'code' => 'ERROR-1',
                    'title' => 'Unprocessable Entity'
                ]]
            ]);
    }

    /*
     * UPDATE-4
     * */
    public function test_update_product_with_unregistered_id_return_error()
    {
        factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name',
            'price' => '100.30',
        ]);

        $updatedProduct = [
            'price' => '23.50'
        ];

        $response = $this->put(route('products.update', 2), $updatedProduct);

        $response->assertStatus(404)
            ->assertJson([
                'errors' => [[
                    'code' => 'ERROR-2',
                    'title' => 'Not Found'
                ]]
            ]);
    }

    /*
     * DELETE-1
     */
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

    /*
     * DELETE-2
     */
    public function test_delete_with_uncreated_product()
    {
        // Given
        factory(Product::class)->create([
            'id' => 1,
            'name' => 'Product name',
            'price' => '100.30',
        ]);

        $response = $this->delete(route('products.delete', 2));

        $response->assertStatus(404)
            ->assertJson([
                'errors' => [[
                    'code' => 'ERROR-2',
                    'title' => 'Not Found'
                ]]
            ]);
    }
}
