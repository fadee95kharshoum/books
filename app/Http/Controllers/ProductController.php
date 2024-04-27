<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Card;
use App\Models\Ebook;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
    {

        // Validate request data (customize as needed)
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|max:70',
            'age' => 'required|max:70',
            'about' => 'required|max:600',
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'arabic_file' => 'mimes:pdf',
            'english_file' => 'mimes:pdf',
            'exercises_file' => 'mimes:pdf',
            'short_story_file' => 'mimes:pdf',
            'cards_file.*' => 'mimes:pdf',
            'ebooks_file.*' => 'mimes:pdf',
        ]);

        // Save image
        $imagePath = $request->file('image')->store('images', 'public');

        // Save PDF files
        $arabicFile = $request->file('arabic_file');
        $arabicPath = $arabicFile ? $arabicFile->store('arabic_files', 'public') : null;
        $englishFile = $request->file('english_file');
        $englishPath = $englishFile ? $englishFile->store('english_files', 'public') : null;
        $exercisesFile = $request->file('exercises_file');
        $exercisesPath = $exercisesFile ? $exercisesFile->store('exercises_files', 'public') : null;
        $shortStoryFile = $request->file('short_story_file');
        $shortStoryPath = $shortStoryFile ? $shortStoryFile->store('short_story_files', 'public') : null;

        // Create a new product record

        $product = new Product();
        $product->category_id = $request->input('category_id');
        $product->name = $request->input('name');
        $product->age = $request->input('age');
        $product->about = $request->input('about');
        $product->image_path = $imagePath;
        $product->arabic_file_path = $arabicPath;
        $product->english_file_path = $englishPath;
        $product->exercises_file_path = $exercisesPath;
        $product->short_Story_file_path = $shortStoryPath;
        $product->save();


        if ($request->hasFile('cards_file')) {
            foreach ($request->file('cards_file') as $card) {
                $cardPath = $card->store('cards_file', 'public');
                $card = new Card();
                $card->product_id = $product->id;
                $card->card_file_path = $cardPath;
                $card->save();

            }
        }


        if ($request->hasFile('ebooks_file')) {
            foreach ($request->file('ebooks_file') as $ebook) {
                $ebookPath = $ebook->store('ebooks_file', 'public');
                $ebook = new Ebook();
                $ebook->product_id = $product->id;
                $ebook->ebook_file_path = $ebookPath;
                $ebook->save();
            }
        }

        $product = Product::find($product->id)->with(['cards','ebooks'])->orderBy('id', 'Desc')->first();

        return response()->json([
            'product' => new ProductResource($product),
            'message' => 'Product created successfully'
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $product = Product::findOrFail($id);
    return new ProductResource($product);
}

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {


        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:70',
            'age' => 'required|string|max:70',
            'about' => 'required|string|max:600',
            'image' => 'image|mimes:jpeg,png|max:2048', // Validate image file
            'arabic_file' => 'file|mimes:pdf',
            'english_file' => 'file|mimes:pdf',
            'exercises_file' => 'file|mimes:pdf',
            'short_story_file' => 'file|mimes:pdf',
            'cards_file.*' => 'mimes:pdf',
            'ebooks_file.*' => 'mimes:pdf',
        ]);

        // Find the product by ID
        $product = Product::findOrFail($id);

        // Update the product attributes
        $product->name = $request->input('name');
        $product->age = $request->input('age');
        $product->about = $request->input('about');

        // Handle image upload (if provided)
        if ($request->hasFile('image')) {
                        Storage::disk('public')->delete('images/' . $product->image_path);
            $imagePath = $request->file('image')->store('images', 'public');
            $product->image_path = $imagePath;
        }

        // Handle PDF file uploads (if provided)
        if ($request->hasFile('arabic_file')) {
              Storage::disk('public')->delete('arabic_files/' . $product->arabic_file_path);
            $product->arabic_file_path = $request->file('arabic_file')->store('arabic_files', 'public');
        }
        if ($request->hasFile('english_file')) {
            Storage::disk('public')->delete('english_files/' . $product->english_file_path);
            $product->english_file_path = $request->file('english_file')->store('english_files', 'public');
        }
        if ($request->hasFile('exercises_file')) {
            Storage::disk('public')->delete('exercises_files/' . $product->exercises_file_path);
            $product->exercises_file_path = $request->file('exercises_file')->store('exercises_files', 'public');
        }
        if ($request->hasFile('short_story_file')) {
            Storage::disk('public')->delete('short_story_files/' . $product->short_Story_file_path);
            $product->short_Story_file_path = $request->file('short_story_file')->store('short_story_files', 'public');
        }


        //cards file
        if ($request->hasFile('cards_file')) {

            $cards_file_path = Card::where('product_id', $product->id)->get();
            foreach( $cards_file_path as $card_path){
                Storage::disk('public')->delete($card_path->card_file_path);
            }

            foreach ($request->file('cards_file') as $card) {
                $cardPath = $card->store('cards_file', 'public');
                $product->cards()->where('product_id', $product->id)->update([
                    'card_file_path' => $cardPath
                ]);
            }
        }



        //Ebook file
        if ($request->hasFile('ebooks_file')) {

            $ebooks_file_path = Ebook::where('product_id', $product->id)->get();
            foreach( $ebooks_file_path as $ebooks_file_path){
                Storage::disk('public')->delete($ebooks_file_path->ebook_file_path);
            }

            foreach ($request->file('ebooks_file') as $ebook) {
                $ebookPath = $ebook->store('ebooks_file', 'public');
                $product->ebooks()->update([
                    'ebook_file_path' => $ebookPath
                ]);
            }
        }

        // Save the changes
        $product->save();


        $product = Product::with(['ebooks', 'cards'])->find($product->id);
        return response()->json([
            'product' => new ProductResource($product),
            'message' => 'Product created successfully'
        ]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Retrieve the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the associated image
        if ($product->image_path) {
            Storage::disk('public')->delete('images/' . $product->image_path);
        }

        // Delete the associated Arabic file
        if ($product->arabic_file_path) {
            Storage::disk('public')->delete('arabic_files/' . $product->arabic_file_path);
        }

        // Delete the associated English file
        if ($product->english_file_path) {
            Storage::disk('public')->delete('english_files/' . $product->english_file_path);
        }
        // Delete the associated Exercises file
        if ($product->exercises_file_path) {
            Storage::disk('public')->delete('exercises_files/' . $product->exercises_file_path);
        }

        // Delete the associated Short Story file
        if ($product->short_Story_file_path) {
            Storage::disk('public')->delete('short_story_files/' . $product->short_Story_file_path);
        }

        // Delete the product from the database
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    public function updateIsPublished(Request $request, $id) {
        $request->validate([
            'is_published' => 'boolean',
        ]);

        $product = Product::findOrFail($id);
        $product->is_published = $request->is_published;
        $product->save();

        return response()->json($product->with(['ebooks', 'cards'])->get());
    }

    public function updateIsBsetSelling(Request $request, $id) {
        $request->validate([
            'is_best_selling' => 'boolean',
        ]);

        $product = Product::findOrFail($id);
        if($product->is_published) {
            $product->is_best_selling = $request->is_best_selling;
            $product->save();


            return response()->json($product->with(['ebooks', 'cards'])->get());
        } else {
            return response()->json('error product is not published');
        }
    }
}
