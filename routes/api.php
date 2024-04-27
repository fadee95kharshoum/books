<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\HomeController;


Route::get('homeBestSelling', [HomeController::class, 'homeBestSelling'])->name('homeBestSelling');
Route::get('homeReviews', [HomeController::class, 'homeReviews'])->name('homeReviews');
Route::get('products', [HomeController::class, 'products'])->name('products');
Route::get('productsRelated/{id}', [HomeController::class, 'showRelatedProducts'])->name('product.related.products');
Route::get('product/{id}/comments', [HomeController::class, 'showCommentProduct'])->name('product.comments');
Route::get('product/serach/{search_txt}', [HomeController::class, 'search'])->name('serach');
Route::post('contact', [HomeController::class, 'contact'])->name('contact');
Route::get('contact/productsName', [HomeController::class, 'productsName'])->name('products.name');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Show comments for a specific product
Route::get('/productComment/{product_id}', [CommentController::class, 'index']);

// Store a new comment
Route::post('/productComment', [CommentController::class, 'storeComment'])->middleware('auth');

// Store a new review/rating
Route::post('/productComment/review', [CommentController::class, 'reviewstore'])->middleware('auth');

// Show comments for a specific product
Route::post('/productComment/{product_id}', [CommentController::class, 'index']);



Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function(){
    Route::post('auth/logout', [AuthController::class, 'logout']);
});


Route::middleware(['auth'])->group(function(){

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::post('/change-password', [PasswordController::class, 'changeUserPassword']);
});

// Admin Dashboard Routes :
Route::middleware(['isAdmin'])->group(function(){

    Route::get('/dashboard/categories', [DashboardController::class, 'categories'])->withoutMiddleware(['isAdmin']);

    // Products for specific category :
    Route::get('/dashboard/categoryProducts/{id}', [DashboardController::class, 'categoryProducts'])->name('dashboard.category.products');
    // Show specific product :
    Route::get('/dashboard/showProduct/{id}', [DashboardController::class, 'showProduct'])->name('dashboard.show.product');

    // Add Category :
    Route::post('/dashboard/categories/store', [CategoryController::class, 'store'])->name('dashboard.categories.store');
    // Update Category :
    Route::put('/dashboard/categories/update/{id}', [CategoryController::class, 'update'])->name('dashboard.categories.update');
    // Delete Category :
    Route::delete('/dashboard/categories/destroy/{id}', [CategoryController::class, 'destroy'])->name('dashboard.categories.destroy');


    // Products index :
    Route::get('/dashboard/products', [DashboardController::class, 'products'])->name('dashboard.products')->withoutMiddleware(['isAdmin']);;
    // Search products :
    Route::get('/dashboard/searchProducts', [DashboardController::class, 'searchProducts'])->name('dashboard.search.products');
    // Add Product :
    Route::post('/dashboard/products/store', [ProductController::class, 'store'])->name('dashboard.products.store');
    // Update Product :
    Route::put('/dashboard/products/update/{id}', [ProductController::class, 'update'])->name('dashboard.products.update');
    // Delete Product :
    Route::delete('/dashboard/products/destroy/{id}', [ProductController::class, 'destroy'])->name('dashboard.products.destroy');
    // Update Is Published Product :
    Route::put('/dashboard/products/update_is_published/{id}', [ProductController::class, 'updateIsPublished'])->name('dashboard.products.updateIsPublished');

    // Update Best Selling Product :
    Route::put('/dashboard/products/update_is_best_selling/{id}', [ProductController::class, 'updateIsBsetSelling'])->name('dashboard.products.updateIsBestSellings');

    // Comments index :
    Route::get('/dashboard/comments', [DashboardController::class, 'comments'])->name('dashboard.comments');
    // Post Comment :
    Route::put('/dashboard/comments/post/{id}', [CommentController::class, 'update'])->name('dashboard.comments.post');
    // Delete Comment :
    Route::delete('/dashboard/comments/destroy/{id}', [CommentController::class, 'destroy'])->name('dashboard.comments.destroy');
    // Show Comment :
    Route::get('/dashboard/comments/show/{id}', [CommentController::class, 'show'])->name('dashboard.comments.show');

    // Contacts index :
    Route::get('/dashboard/contacts', [DashboardController::class, 'contacts'])->name('dashboard.contacts');
    // Show Contact :
    Route::get('/dashboard/contacts/show/{id}', [DashboardController::class, 'show'])->name('dashboard.contacts.show');



});
