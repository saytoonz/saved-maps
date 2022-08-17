<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocalMapsPlacesIdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'place_id'=>$this->place_id,
            'formatted_address'=>isset($this->formatted_address) ? $this->formatted_address : $this->address,
            'lat'=>isset($this->lat) ? $this->lat : NULL,
            'lng'=>isset($this->lng) ? $this->lng : NULL,
            'created_at'=>$this->created_at->format('Y-m-d H:i:s'),
            'local_map'=>$this->local_map,
            
        ];
    }
}
