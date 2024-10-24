<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\Category;
//use App\Models\Product;
//use Illuminate\Http\JsonResponse;
//use Illuminate\Http\Request;
//
//class ProductController extends Controller
//{
//    public function index(): \Illuminate\Http\JsonResponse
//    {
//        $products = Product::all();
//        return response()->json($products);
//    }
//    public function show($id): \Illuminate\Http\JsonResponse
//    {
//        $products = Product::query()->findOrfail($id);
//        return response()->json($products);
//    }
//    public function create(): JsonResponse
//    {
//        $categories = Category::all();
//        $products = Product::all();
//        $product = new Product();
//        return response()->json([
//            'categories' => $categories,
//            'products' => $products,
//            'product' => $product
//        ]);
//    }
//    public function store(Request $request): JsonResponse
//    {
//        $request->validate([
//            'name' => 'required',
//        ],
//            [
//                'name'=>['required'=>'Mahsulot nomi kiritish majburiy']
//            ]
//        );
//        $product = Product::query()->create([
//            'name' => $request->input('name'),
//            'description' => $request->input('description'),
//            'price' => $request->input('price'),
//            'category_id' => $request->input('category_id'),
//            'user_id' => $request->user()->id,
//            'in_stock' => $request->input('in_stock')
//        ]);
//        return response()->json([
//            'message'=>'Mahsulot yaratildi',
//            'product' => $product,
//        ],201);
//    }
//}


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function show($id): JsonResponse
    {
        $product = Product::query()->findOrFail($id);
        return response()->json($product);
    }

    public function create(): JsonResponse
    {
        $categories = Category::all();
        return response()->json([
            'categories' => $categories,
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Foydalanuvchi autentifikatsiya qilinmagan.'
            ], 401);
        }

        $product = Product::query()->create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'category_id' => $request->input('category_id'),
            'user_id' => auth()->id(), // Foydalanuvchi ID sini auth() orqali olish
            'in_stock' => $request->input('in_stock')
        ]);

        return response()->json([
            'message' => 'Mahsulot yaratildi',
            'product' => $product,
        ], 201);
    }
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::query()->findOrFail($id);
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Foydalanuvchi autentifikatsiya qilinmagan.'
            ], 401);
        }
        $product->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'category_id' => $request->input('category_id'),
            'in_stock' => $request->input('in_stock'),
            'user_id' => auth()->id()
        ]);
        return response()->json([
            'message' => "Mahsulot o'zgartirildi",
        ]);
    }
    public function destroy(int $id): JsonResponse
    {
        $product = Product::query()->findOrFail($id);
        $product->delete();
        return response()->json([
            'message' => "Mahsulot Muvaffaqiyatli o'chirildi!",
        ]);
    }
    public function ShowByCategory(int $id): ProductResource
    {
        $products = Product::query()->where('category_id', $id)
            ->with('category')
            ->get();
        return  new ProductResource($products->first());
    }
}
