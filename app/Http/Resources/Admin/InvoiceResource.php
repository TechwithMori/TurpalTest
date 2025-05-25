<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'experience' => [
                'id' => $this->experience->id,
                'title' => $this->experience->title,
                'thumbnail' => $this->experience->thumbnail,
            ],
            'status' => $this->status,
            'channel' => $this->channel,
            'date' => $this->date,
            'buyer_name' => $this->buyer_name,
            'buyer_email' => $this->buyer_email,
            'buyer_phone' => $this->buyer_phone,
            'buyer_address' => $this->buyer_address,
            'buyer_city' => $this->buyer_city,
            'buyer_country' => $this->buyer_country,
            'items' => InvoiceItemResource::collection($this->items),
        ];
    }
}