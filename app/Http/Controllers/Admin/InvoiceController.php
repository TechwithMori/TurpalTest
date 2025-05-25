<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Http\Resources\Admin\InvoiceResource;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();
        return InvoiceResource::collection($invoices);
    }
}