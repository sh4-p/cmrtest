<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
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
            'amount' => (float) $this->amount,
            'probability' => $this->probability,
            'closing_date' => $this->closing_date?->toDateString(),
            'description' => $this->description,
            'contact' => new ContactResource($this->whenLoaded('contact')),
            'stage' => new DealStageResource($this->whenLoaded('stage')),
            'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
