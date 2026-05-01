<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'hsn',
        'quantity',
        'rate',
        'gst_percent',
    ];

    protected $casts = [
        'quantity'    => 'integer',
        'rate'        => 'float',
        'gst_percent' => 'float',
    ];

    // An item belongs to an invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Calculate taxable amount for this item
    public function getTaxableAmountAttribute()
    {
        return $this->quantity * $this->rate;
    }

    // Calculate SGST for this item
    public function getSgstAttribute()
    {
        return $this->taxable_amount * ($this->gst_percent / 2) / 100;
    }

    // Calculate CGST for this item
    public function getCgstAttribute()
    {
        return $this->taxable_amount * ($this->gst_percent / 2) / 100;
    }

    // Calculate total amount for this item
    public function getTotalAmountAttribute()
    {
        return $this->taxable_amount + $this->sgst + $this->cgst;
    }
}