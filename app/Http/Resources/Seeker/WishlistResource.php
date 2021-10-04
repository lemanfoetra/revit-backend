<?php

namespace App\Http\Resources\Seeker;

use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'bengkel_id'    => $this->bengkel_id,
            'role'          => $this->role,
            'name'          => $this->name,
            'email'         => $this->email,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
            'hashmap_code'  => $this->hashmap_code,
            'full_address'  => $this->full_address,
            'provinsi'      => $this->provinsi,
            'kabkot'        => $this->kabkot,
            'kecamatan'     => $this->kecamatan,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
