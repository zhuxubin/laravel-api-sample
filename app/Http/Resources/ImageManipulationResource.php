<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ImageManipulationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'original' => URL::to($this->path),
            'output' => URL::to($this->output_path),
            'album_id' => $this->album_id,
            'created_at' => $this->created_at,
        ];
    }
}
