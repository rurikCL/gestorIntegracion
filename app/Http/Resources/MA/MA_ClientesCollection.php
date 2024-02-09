<?php

namespace App\Http\Resources\MA;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MA_ClientesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' =>$this->collection
        ];
    }
}
