<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{

    public function index(Request $request)
    {
        $this->pageTitle = 'Products';
        $this->apiToken = $request->cookie('apiToken');
        return view('product.index', $this->data);
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show(Product $product)
    {
        //
    }


    public function edit(Product $product)
    {
        //
    }


    public function update(Request $request, Product $product)
    {
        //
    }



    public function data(Request $request){
        $data = Product::paginate(10);
        return ProductResource::collection($data);
    }

    public function destroy(Product $product){
        $product->delete();
        return response()->json([
            'type' => 'success',
            'message' => 'The product has been removed',
        ], 200);
    }
}
