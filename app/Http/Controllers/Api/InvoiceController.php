<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    // POST /api/invoices — create invoice
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Invoice Info
            'invoice_number'       => 'required|string|max:50',
            'invoice_date'         => 'required|date',
            'due_date'             => 'required|date|after:invoice_date',

            // Billed By
            'biller_name'          => 'required|string|max:255',
            'biller_address'       => 'required|string',
            'biller_gstin'         => 'required|string|max:20',
            'biller_pan'           => 'required|string|max:15',
            'place_of_supply'      => 'required|string|max:100',

            // Billed To
            'client_name'          => 'required|string|max:255',
            'client_address'       => 'required|string',
            'client_gstin'         => 'required|string|max:20',
            'client_pan'           => 'required|string|max:15',
            'country_of_supply'    => 'required|string|max:100',

            // Bank Details
            'account_holder_name'  => 'required|string|max:255',
            'account_number'       => 'required|string|max:50',
            'ifsc'                 => 'required|string|max:20',
            'account_type'         => 'required|string|max:50',
            'bank_name'            => 'required|string|max:255',
            'upi_id'               => 'required|string|max:100',

            // Totals
            'discount_percent'     => 'sometimes|numeric|min:0|max:100',
            'cgst_percent'         => 'sometimes|numeric|min:0|max:100',
            'sgst_percent'         => 'sometimes|numeric|min:0|max:100',

            // Early Pay
            'early_pay_discount'   => 'sometimes|numeric|min:0',
            'early_pay_deadline'   => 'sometimes|date',

            // Terms & Notes
            'terms_and_conditions' => 'nullable|string',
            'additional_notes'     => 'nullable|string',

            // Contact
            'contact_email'        => 'nullable|email|max:255',
            'contact_phone'        => 'nullable|string|max:20',

            // Items
            'items'                => 'required|array|min:1',
            'items.*.description'  => 'required|string|max:255',
            'items.*.hsn'          => 'required|string|max:20',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.rate'         => 'required|numeric|min:0',
            'items.*.gst_percent'  => 'sometimes|numeric|min:0|max:100',
        ]);

        try {
            // Create the invoice
            $invoice = Invoice::create(collect($validated)->except('items')->toArray());

            // Create all items
            foreach ($validated['items'] as $item) {
                $invoice->items()->create($item);
            }

            return response()->json([
                'message' => 'Invoice created successfully',
                'invoice' => $invoice->load('items'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/invoices/{id} — view invoice as JSON
    public function show($id)
    {
        try {
            $invoice = Invoice::with('items')->find($id);

            if (! $invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }

            return response()->json(['invoice' => $invoice]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/invoices/{id}/download — download as PDF
    public function download($id)
    {
        try {
            $invoice = Invoice::with('items')->find($id);

            if (! $invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }

            // Calculate totals
            $subTotal      = $invoice->items->sum(fn($item) => $item->taxable_amount);
            $discountAmount = $subTotal * ($invoice->discount_percent / 100);
            $taxableAmount  = $subTotal - $discountAmount;
            $cgst           = $taxableAmount * ($invoice->cgst_percent / 100);
            $sgst           = $taxableAmount * ($invoice->sgst_percent / 100);
            $total          = $taxableAmount + $cgst + $sgst;
            $earlyPayAmount = $total - $invoice->early_pay_discount;

            $pdf = Pdf::loadView('invoices.template', [
                'invoice'        => $invoice,
                'subTotal'       => $subTotal,
                'discountAmount' => $discountAmount,
                'taxableAmount'  => $taxableAmount,
                'cgst'           => $cgst,
                'sgst'           => $sgst,
                'total'          => $total,
                'earlyPayAmount' => $earlyPayAmount,
            ])->setPaper('a4', 'portrait');

            return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/invoices — list all invoices
    public function index()
    {
        try {
            $invoices = Invoice::with('items')->latest()->get();

            return response()->json([
                'total'    => $invoices->count(),
                'invoices' => $invoices,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}