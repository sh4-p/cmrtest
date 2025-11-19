<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'description' => $this->description,
            'type' => $this->type,
            'activity_date' => $this->activity_date?->toIso8601String(),
            'user' => new UserResource($this->whenLoaded('user')),
            'subject' => $this->when($this->relationLoaded('subject'), function () {
                return [
                    'id' => $this->subject?->id,
                    'type' => $this->subject_type,
                    'name' => $this->getSubjectName(),
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Get the name of the subject entity
     */
    protected function getSubjectName(): ?string
    {
        if (!$this->subject) {
            return null;
        }

        return match (true) {
            $this->subject instanceof \App\Models\Contact => $this->subject->first_name . ' ' . $this->subject->last_name,
            $this->subject instanceof \App\Models\Lead => $this->subject->first_name . ' ' . $this->subject->last_name,
            $this->subject instanceof \App\Models\Company => $this->subject->name,
            $this->subject instanceof \App\Models\Deal => $this->subject->name,
            default => null,
        };
    }
}
