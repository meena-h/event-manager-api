<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
        font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 30px;
        }
    
        h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .invoice-meta p {
            margin-bottom: 4px;
        }

        .invoice-meta span {
            font-weight: bold;
        }

        .company-logo {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            border: 3px solid #333;
            padding: 10px 16px;
            display: inline-block;
            float: right;
        }

        .company-logo span {
            display: block;
            font-size: 13px;
            letter-spacing: 2px;
        }

        /* Billed By / Billed To */
        .billing-section {
            width: 100%;
            margin-bottom: 20px;
        }

        .billing-section table {
            width: 100%;
        }

        .billing-box {
            width: 48%;
            background: #f5f5f5;
            padding: 12px;
            vertical-align: top;
        }

        .billing-box h3 {
            font-size: 13px;
            margin-bottom: 8px;
            color: #555;
        }

        .billing-box p {
            margin-bottom: 3px;
            font-size: 11px;
        }

        .billing-box .company-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .supply-row {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .supply-row table {
            width: 100%;
        }

        .supply-row td {
            padding: 6px;
            border: 1px solid #ddd;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #333;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }

        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Totals */
        .totals-table {
            width: 40%;
            margin-left: auto;
            margin-bottom: 20px;
        }

        .totals-table td {
            padding: 5px 8px;
            font-size: 12px;
        }

        .totals-table .total-row td {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 8px;
        }

        .totals-table .discount-row td {
            color: red;
        }

        /* Words */
        .total-words {
            font-size: 11px;
            margin-bottom: 4px;
            color: #555;
        }

        .total-words span {
            font-style: italic;
        }

        /* Early Pay */
        .early-pay-table {
            width: 40%;
            margin-left: auto;
            margin-bottom: 20px;
        }

        .early-pay-table td {
            padding: 4px 8px;
            font-size: 11px;
            color: #555;
        }

        .early-pay-table .early-pay-amount td {
            font-weight: bold;
            font-size: 13px;
            color: #333;
        }

        /* Bank & Payment */
        .bottom-section table {
            width: 100%;
        }

        .bank-box {
            width: 48%;
            vertical-align: top;
        }

        .bank-box h3 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }

        .bank-box table {
            width: 100%;
        }

        .bank-box td {
            padding: 3px 0;
            font-size: 11px;
        }

        .bank-box .label {
            color: #777;
            width: 45%;
        }

        /* Terms */
        .terms-box {
            margin-top: 20px;
        }

        .terms-box h3 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .terms-box ol {
            padding-left: 16px;
            list-style: decimal;
        }

        .terms-box ol li {
            font-size: 11px;
            margin-bottom: 4px;
            color: #555;
        }

        .notes-box {
            margin-top: 16px;
        }

        .notes-box h3 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .notes-box p {
            font-size: 11px;
            color: #555;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
            color: #555;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <h1>Invoice</h1>

    <table style="width:100%; margin-bottom:20px;">
        <tr>
            <td style="vertical-align:top;">
                <p><strong>Invoice#</strong> &nbsp; {{ $invoice->invoice_number }}</p>
                <p><strong>Invoice Date</strong> &nbsp; {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</p>
                <p><strong>Due Date</strong> &nbsp; {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
            </td>
            <td style="text-align:right; vertical-align:top;">
                <div class="company-logo">
                    {{ strtoupper($invoice->biller_name) }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Billed By / Billed To --}}
    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td class="billing-box">
                <h3>Billed by</h3>
                <p class="company-name">{{ $invoice->biller_name }}</p>
                <p>{{ $invoice->biller_address }}</p>
                <p><strong>GSTIN</strong> &nbsp; {{ $invoice->biller_gstin }}</p>
                <p><strong>PAN</strong> &nbsp; {{ $invoice->biller_pan }}</p>
            </td>
            <td style="width:4%;"></td>
            <td class="billing-box">
                <h3>Billed to</h3>
                <p class="company-name">{{ $invoice->client_name }}</p>
                <p>{{ $invoice->client_address }}</p>
                <p><strong>GSTIN</strong> &nbsp; {{ $invoice->client_gstin }}</p>
                <p><strong>PAN</strong> &nbsp; {{ $invoice->client_pan }}</p>
            </td>
        </tr>
    </table>

    {{-- Place / Country of Supply --}}
    <table style="width:100%; margin-bottom:20px; border:1px solid #ddd;">
        <tr>
            <td style="padding:6px; text-align:center;">
                <strong>Place of Supply</strong> &nbsp; {{ $invoice->place_of_supply }}
            </td>
            <td style="padding:6px; text-align:center; border-left:1px solid #ddd;">
                <strong>Country of Supply</strong> &nbsp; {{ $invoice->country_of_supply }}
            </td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>Item #/Item description</th>
                <th>HSN</th>
                <th>Qty.</th>
                <th>GST</th>
                <th>Taxable Amount</th>
                <th>SGST</th>
                <th>CGST</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}. {{ $item->description }}</td>
                <td>{{ $item->hsn }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->gst_percent }}%</td>
                <td>&#8377; {{ number_format($item->taxable_amount, 2) }}</td>
                <td>&#8377;{{ number_format($item->sgst, 2) }}</td>
                <td>&#8377;{{ number_format($item->cgst, 2) }}</td>
                <td>&#8377; {{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

 

    {{-- Bank & Payment --}}
   <table style="width:100%; margin-top:20px;">
    <tr>
        <!-- LEFT: Bank Details -->
        <td style="width:55%; vertical-align:top;">
            <div class="bank-box">
                <h3>Bank &amp; Payment Details</h3>
                <table>
                    <tr>
                        <td class="label">Account Holder Name</td>
                        <td>{{ $invoice->account_holder_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Account Number</td>
                        <td>{{ $invoice->account_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">IFSC</td>
                        <td>{{ $invoice->ifsc }}</td>
                    </tr>
                    <tr>
                        <td class="label">Account Type</td>
                        <td>{{ $invoice->account_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">Bank</td>
                        <td>{{ $invoice->bank_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">UPI</td>
                        <td>{{ $invoice->upi_id }}</td>
                    </tr>
                </table>
            </div>
            {{-- Terms & Conditions --}}
    @if($invoice->terms_and_conditions)
    <div class="terms-box">
        <h3>Terms and Conditions</h3>
        <ol>
            @foreach(explode("\n", $invoice->terms_and_conditions) as $term)
                @if(trim($term) != '')
                <li style="margin-bottom:6px; color:#555;">{{ trim($term) }}</li>
                @endif
            @endforeach
        </ol>
    </div>
    @endif

    {{-- Additional Notes --}}
    @if($invoice->additional_notes)
    <div class="notes-box">
        <h3>Additional Notes</h3>
        <p>{{ $invoice->additional_notes }}</p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        @if($invoice->contact_email)
            For any enquiries, email us on <strong>{{ $invoice->contact_email }}</strong>
        @endif
        @if($invoice->contact_phone)
            or call us on <strong>{{ $invoice->contact_phone }}</strong>
        @endif
    </div>
        </td>

        <!-- RIGHT: Totals -->
        <!-- RIGHT: Totals -->
        <td style="width:45%; vertical-align:top;">
            <table style="width:100%;">
                <tr>
                    <td>Sub Total</td>
                    <td style="text-align:right;">&#8377;{{ number_format($subTotal, 2) }}</td>
                </tr>
                <tr style="color:red;">
                    <td>Discount({{ $invoice->discount_percent }}%)</td>
                    <td style="text-align:right;">- &#8377;{{ number_format($discountAmount, 2) }}</td>
                </tr>
                <tr>
                    <td>Taxable Amount</td>
                    <td style="text-align:right;">&#8377;{{ number_format($taxableAmount, 2) }}</td>
                </tr>
                <tr>
                    <td>CGST</td>
                    <td style="text-align:right;">&#8377;{{ number_format($cgst, 2) }}</td>
                </tr>
                <tr>
                    <td>SGST</td>
                    <td style="text-align:right;">&#8377;{{ number_format($sgst, 2) }}</td>
                </tr>

                <!-- TOTAL -->
                <tr>
                    <td colspan="2" style="border-top:2px solid #333;"></td>
                </tr>
                <tr>
                    <td style="font-size:16px; font-weight:bold;">Total</td>
                    <td style="text-align:right; font-size:16px; font-weight:bold;">
                        &#8377;{{ number_format($total, 2) }}
                    </td>
                </tr>

                <!-- WORDS -->
                <tr>
                    <td colspan="2" style="padding-top:10px;">
                        <p style="font-size:11px; color:#555;">Invoice Total (in words)</p>
                        <p style="font-size:12px; font-style:italic;">
                            @php
                                function numberToWords($num) {
                                    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                                            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                                            'Seventeen', 'Eighteen', 'Nineteen'];
                                    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

                                    if ($num == 0) return 'Zero';

                                    $result = '';

                                    if ($num >= 10000000) {
                                        $result .= numberToWords((int)($num / 10000000)) . ' Crore ';
                                        $num %= 10000000;
                                    }
                                    if ($num >= 100000) {
                                        $result .= numberToWords((int)($num / 100000)) . ' Lakh ';
                                        $num %= 100000;
                                    }
                                    if ($num >= 1000) {
                                        $result .= numberToWords((int)($num / 1000)) . ' Thousand ';
                                        $num %= 1000;
                                    }
                                    if ($num >= 100) {
                                        $result .= $ones[(int)($num / 100)] . ' Hundred ';
                                        $num %= 100;
                                    }
                                    if ($num >= 20) {
                                        $result .= $tens[(int)($num / 10)] . ' ';
                                        $num %= 10;
                                    }
                                    if ($num > 0) {
                                        $result .= $ones[$num] . ' ';
                                    }

                                    return trim($result);
                                }

                                $words = numberToWords((int)$total) . ' Rupees Only';
                            @endphp
                            {{ $words }}
                        </p>
                    </td>
                </tr>

                <!-- EARLY PAY -->
                @if($invoice->early_pay_discount > 0)
                <tr>
                    <td style="padding-top:10px;">EarlyPay Discount</td>
                    <td style="text-align:right; padding-top:10px;">
                        &#8377;{{ number_format($invoice->early_pay_discount, 2) }}
                    </td>
                </tr>

                @if($invoice->early_pay_deadline)
                <tr>
                    <td colspan="2" style="font-size:10px; color:#888;">
                        if paid before {{ \Carbon\Carbon::parse($invoice->early_pay_deadline)->format('M d, Y') }}
                        {{ \Carbon\Carbon::parse($invoice->early_pay_deadline)->format('h:i A') }}
                    </td>
                </tr>
                @endif

                <tr>
                    <td style="font-weight:bold;">EarlyPay Amount</td>
                    <td style="text-align:right; font-weight:bold;">
                        &#8377;{{ number_format($earlyPayAmount, 2) }}
                    </td>
                </tr>
                @endif

            </table>
        </td>

    </tr>
</table>


    

</body>
</html>