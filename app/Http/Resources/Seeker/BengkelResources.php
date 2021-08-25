<?php

namespace App\Http\Resources\Seeker;

use Illuminate\Http\Resources\Json\JsonResource;

class BengkelResources extends JsonResource
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
            'name'          => $this->name,
            'email'         => $this->email,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
            'latitude'      => $this->latitude,
            'full_address'  => $this->full_address,
            'provinsi'      => $this->provinsi,
            'kabkot'        => $this->kabkot,
            'kecamatan'     => $this->kecamatan,
        ];
    }
}
