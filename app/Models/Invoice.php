<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        // Invoice Info
        'invoice_number',
        'invoice_date',
        'due_date',

        // Billed By
        'biller_name',
        'biller_address',
        'biller_gstin',
        'biller_pan',
        'place_of_supply',

        // Billed To
        'client_name',
        'client_address',
        'client_gstin',
        'client_pan',
        'country_of_supply',

        // Bank Details
        'account_holder_name',
        'account_number',
        'ifsc',
        'account_type',
        'bank_name',
        'upi_id',

        // Totals
        'discount_percent',
        'cgst_percent',
        'sgst_percent',

        // Early Pay
        'early_pay_discount',
        'early_pay_deadline',

        // Terms & Notes
        'terms_and_conditions',
        'additional_notes',

        // Contact
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'invoice_date'       => 'date',
        'due_date'           => 'date',
        'early_pay_deadline' => 'datetime',
        'discount_percent'   => 'float',
        'cgst_percent'       => 'float',
        'sgst_percent'       => 'float',
        'early_pay_discount' => 'float',
    ];

    // An invoice has many items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}