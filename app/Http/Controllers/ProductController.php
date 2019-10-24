<?php

namespace App\Http\Controllers;

use App\ErrorField;
use App\ErrorResponse;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $products = ProductResource::collection(Product::all());
        return $products;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        // Create a new product
        $product = Product::create([
            "name" => $request->input('data.attributes.name'),
            "price" => $request->input('data.attributes.price')
        ]);

        // Return a response with a product json
        // representation and a 201 status code
        return response()->json(["data" => new ProductResource($product)], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Product $product
     * @return ProductResource|\Illuminate\Http\JsonResponse
     */
    public function show(int $id){
        $responseError = new ErrorResponse();
        $product = Product::find($id);

        if(!$product) {
            array_push($responseError->errors, new ErrorField(
                "ERROR-2", "Not Found"
            ));

            return response()->json($responseError, 404);
        }

        return response()->json(new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, int $id)
    {
        $responseError = new ErrorResponse();
        $product = Product::find($id);

        if(!$product) {
            array_push($responseError->errors, new ErrorField(
                "ERROR-2", "Not Found"
            ));

            return response()->json($responseError, 404);
        }

        $product->update([
            "name" => $request->input('data.attributes.name') ?: $product->name,
            "price" => $request->input('data.attributes.price') ?: $product->price
        ]);
        return response()->json(new ProductResource($product), 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $responseError = new ErrorResponse();
        $product = Product::find($id);

        if(!$product) {
            array_push($responseError->errors, new ErrorField(
                "ERROR-2", "Not Found"
            ));

            return response()->json($responseError, 404);
        }
        $product->delete();

        return response()->json(null, 204);
    }
}
