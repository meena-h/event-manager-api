<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Invoice Info
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->date('due_date');

            // Billed By
            $table->string('biller_name');
            $table->text('biller_address');
            $table->string('biller_gstin');
            $table->string('biller_pan');
            $table->string('place_of_supply');

            // Billed To
            $table->string('client_name');
            $table->text('client_address');
            $table->string('client_gstin');
            $table->string('client_pan');
            $table->string('country_of_supply');

            // Bank Details
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('ifsc');
            $table->string('account_type');
            $table->string('bank_name');
            $table->string('upi_id');

            // Totals
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('cgst_percent', 5, 2)->default(9);
            $table->decimal('sgst_percent', 5, 2)->default(9);

            // Early Pay
            $table->decimal('early_pay_discount', 10, 2)->default(0);
            $table->timestamp('early_pay_deadline')->nullable();

            // Terms & Notes
            $table->text('terms_and_conditions')->nullable();
            $table->text('additional_notes')->nullable();

            // Contact
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};