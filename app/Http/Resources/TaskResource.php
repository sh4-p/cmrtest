<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->toDateString(),
            'is_overdue' => $this->due_date && $this->due_date->isPast() && $this->status !== 'Completed',
            'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
            'related_to' => $this->when($this->relationLoaded('relatedTo'), function () {
                return [
                    'id' => $this->relatedTo?->id,
                    'type' => $this->related_to_type,
                    'name' => $this->getRelatedToName(),
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Get the name of the related entity
     */
    protected function getRelatedToName(): ?string
    {
        if (!$this->relatedTo) {
            return null;
        }

        return match (true) {
            $this->relatedTo instanceof \App\Models\Contact => $this->relatedTo->first_name . ' ' . $this->relatedTo->last_name,
            $this->relatedTo instanceof \App\Models\Lead => $this->relatedTo->first_name . ' ' . $this->relatedTo->last_name,
            $this->relatedTo instanceof \App\Models\Company => $this->relatedTo->name,
            $this->relatedTo instanceof \App\Models\Deal => $this->relatedTo->name,
            default => null,
        };
    }
}
