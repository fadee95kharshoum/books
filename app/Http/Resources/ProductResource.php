<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'age' => $this->age,
            'about' => $this->about,
            'image_path' => asset('storage/' . $this->image_path),
            'arabic_file_path' => asset('storage/' . $this->arabic_file_path),
            'english_file_path' => asset('storage/' . $this->english_file_path),
            'exercises_file_path' => asset('storage/' . $this->exercises_file_path),
            'short_Story_file_path' => asset('storage/' . $this->short_Story_file_path),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'cards' => CardResource::collection($this->whenLoaded('cards')),
            'ebooks' => EbookResource::collection($this->whenLoaded('ebooks')),
        ];
    }
}
