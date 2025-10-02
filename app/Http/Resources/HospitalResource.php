<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalResource extends JsonResource
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
            'name'  => $this->name,
            'photo'  => $this->photo,
            'about' => $this->about,
            'address' => $this->address,
            'city' => $this->city,
            'post_code' => $this->post_code,
            'phone' => $this->phone,

            'doctors_count' => $this->doctors->count(),
            'specialists_count' => $this->specialists->count(),

            'doctors' => DoctorResource::collection($this->whenLoaded('doctors')),
            'specialists' => SpecialistResource::collection($this->whenLoaded('specialists')),

        ];
    }
}
