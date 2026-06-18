<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'industry' => $this->industry,
            'tech_stack' => $this->tech_stack,
            'description' => $this->description,
            'client_name' => $this->client_name,
            'outcome' => $this->outcome,
            'image_path' => $this->image_path,
            'live_url' => $this->live_url,
            'featured' => $this->featured,
            'completed_at' => $this->completed_at?->toDateString(),
            'sort_order' => $this->sort_order,
        ];
    }
}
