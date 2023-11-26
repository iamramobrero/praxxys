<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Carbon\Carbon;
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
        $request->validate([
            'name' => ['required'],
            'description' => ['required'],
            'category_id' => ['required','exists:product_categories,id'],
            'datetime' => ['required']
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'datetime_at' => Carbon::parse($request->datetime),
        ]);

        return new ProductResource($product);
    }


    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function imageUpload(Product $product, Request $request){
        return $request;
    }


    public function edit(Product $product, Request $request)
    {
        $this->pageTitle = 'Edit Product';
        $this->apiToken = $request->cookie('apiToken');
        $this->product = $product;
        return view('product.edit', $this->data);
    }


    public function update(Request $request, Product $product)
    {
        //
    }



    public function data(Request $request){
        $data = Product::select('products.*');

        if(isset($request->category) && $request->category != '')
            $data = $data->where('category_id', $request->category);

        if(isset($request->keyword) && $request->keyword != ''){
            $keywords = array_map('trim', explode(',', $request->keyword));

            $data = $data->where(function($query) use ($keywords){
                foreach($keywords as $keyword)
                    $query->orWhere('name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if(isset($request->sort_by) && $request->sort_by != ''){
            $sortBy ='id';
            $sortOrder = $request->input('sort_order','ASC');
            if($request->sort_by=='date') $sortBy = 'datetime_at';
            elseif($request->sort_by=='name') $sortBy = 'products.name';
            elseif($request->sort_by=='category'){
                $sortBy = 'product_categories.name';
                $data = $data->leftJoin('product_categories','product_categories.id','products.category_id');
            };
            $data = $data->orderBy($sortBy,$sortOrder);
        }


        $data = $data->paginate(10);
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
