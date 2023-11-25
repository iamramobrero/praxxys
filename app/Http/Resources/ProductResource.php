<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category->name,
            'date' => Carbon::parse($this->datetime_at)->format('Y-m-d h:i A'),
            'routes' => [
                'edit' => route('products.edit',[$this->id]),
                'destroy' => route('products.destroy',[$this->id]),
            ],
        ];
    }
}
