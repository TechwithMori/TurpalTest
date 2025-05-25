<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;

Route::get('invoices', [InvoiceController::class, 'index']);