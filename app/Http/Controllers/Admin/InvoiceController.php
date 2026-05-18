<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function show($id)
    {
        $invoice = SalesInvoice::with(['order.items.product', 'order.table', 'order.branch.setting.currency', 'customer'])
            ->where('id', $id)
            ->orWhere('order_id', $id)
            ->firstOrFail();

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => $invoice,
            'pageTitle' => 'Invoice #' . $invoice->invoice_number
        ]);
    }
}
