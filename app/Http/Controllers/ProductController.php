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
