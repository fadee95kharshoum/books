<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProductResource;

class HomeController extends Controller
{
    public function homeBestSelling () {
        // Retrieve the latest 6 products where 'is_best_selling' column is 1
        $bestSellingProducts = Product::where('is_best_selling', 1)
        ->latest()
        ->take(6)
        ->with('category') // Load the associated category
        ->get();

        // Prepare the response data
        $responseData = $bestSellingProducts->map(function ($product) {
            return [
                'id' => $product->id,
                'image' => $product->image,
                'name' => $product->name,
                'age' => $product->age,
                'about' => $product->about,
                'category' => $product->category->name, // Get the category name
            ];
        });

        // Return the best-selling products with category names as a JSON response
        return  response()->json(['products' => $responseData]);
    }

    public function homeReviews () {
        // Retrieve the latest 10 comments with associated product and user details
        $latestComments = Comment::with(['product', 'user'])
            ->where('is_approved', 1) // Only approved comments
            ->latest()
            ->take(10)
            ->get();

        // Prepare the response data
        $responseData = $latestComments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'user_name' => $comment->user->name,
                'product_name' => $comment->product->name,
                'text' => $comment->text,
                'is_approved' => $comment->is_approved,
                'created_at' => $comment->created_at,
            ];
        });

        // Return the latest comments with additional details as a JSON response
        return  response()->json(['comments' => $responseData]);
    }

    public function products() {
        // Retrieve published products
        $publishedProducts = Product::where('is_published', 1)
            ->select('id', 'image_path')
            ->paginate(10); // Paginate with 10 products per page

        // Return as JSON API
        return response()->json([
            'data' => $publishedProducts,
        ]);
    }


    public function search(Request $request)
    {
        $searchTerm = $request->input('search'); // Get the search term from the user

        // Query products based on the search term (case-insensitive)
        $products = Product::where('name', 'like', '%' . $searchTerm . '%')
            ->where('is_published', true)
            ->with(['ebooks' , 'cards'])
            ->get();


        return response()->json(['products' => $products]);

    }


    public function showCommentProduct ($id) {
        $comments = Comment::with(['user'])
            ->where('product_id', $id)
            ->get();

        return response()->json([
            'comments' => $comments,
        ]);
    }
    public function showRelatedProducts ($id) {

        $product = Product::findOrFail($id);
        // Get the category ID of the given product
        $categoryId = $product->category_id;
        $relatedProducts = Product::where('category_id', $categoryId)
            ->where('id', '!=', $id)
            ->with(['category', 'cards', 'ebooks'])
            ->take(10)
            ->get();

        return response()->json($relatedProducts);
    }

    public function contact(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'product_name' => 'required|string',
        'message' => 'required|string',
    ]);

    // Check if the product name exists
    $productExists = Product::where('name', $request->input('product_name'))->exists();

    if ($productExists) {
        // Product exists
        $product = Product::where('name', $request->input('product_name'))->first();
        $contact = new Contact();
        $contact->name = $request->input('name');
        $contact->email = $request->input('email');
        $contact->product_id = $product->id;
        $contact->message = $request->input('message');
        $contact->save();
        return response()->json(['status' => 'exist', 'message' => 'Successfuly']);
    } else {
        // Product does not exist
        return response()->json(['status' => 'error', 'message' => 'Invalid product name']);
    }
}
    public function productsName() {
        $productsName = (object) Product::pluck('name');

        return response()->json($productsName);
    }

}
