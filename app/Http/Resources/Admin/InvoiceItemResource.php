<?php


namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'invoice' => [
                'id' => $this->invoice->id,
                'status' => $this->invoice->status,
                'channel' => $this->invoice->channel,
                'date' => $this->invoice->date,
            ],
            'type' => $this->type,
            'experience' => [
                'id' => $this->experience->id,
                'title' => $this->experience->title,
                'thumbnail' => $this->experience->thumbnail,
            ],
            'availability' => [
                'id' => $this->availability->id,
                'start_time' => $this->availability->start_time,
                'end_time' => $this->availability->end_time,
                'buy_price' => $this->availability->buy_price,
                'sell_price' => $this->availability->sell_price,
            ],
            'pax' => $this->pax,
            'execute_date' => $this->execute_date,
            'buy_price' => $this->buy_price,
            'sell_price' => $this->sell_price,
            'vat_amount' => $this->vat_amount,
            'start' => $this->start,
            'end' => $this->end,
            'status' => $this->status,
        ];
    }
}