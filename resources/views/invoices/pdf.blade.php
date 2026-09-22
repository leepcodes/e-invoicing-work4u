<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body { width: 100%; margin: 0; padding: 0; background: #fff; }
    body { font-family: Arial, Helvetica, sans-serif; color: #000; font-size: 7px; }

    @page {
        size: A4;
        margin: 4mm;
    }

    .container {
        width: 96%;
        margin: 0 auto;
        min-height: 285mm;
        padding: 2mm;
        background: #fff;
    }

    .header { width: 100%; display: table; table-layout: fixed; }
    .header-left { display: table-cell; width: 68%; vertical-align: top; }
    .header-right { display: table-cell; width: 32%; vertical-align: top; text-align: right; }

    .company-wrapper { display: table; width: 100%; }
    .logo-box {
        display: table-cell;
        width: 36px;
        height: 36px;
        vertical-align: middle;
        text-align: center;
        border: 1px solid #000;
        font-size: 9px;
        font-weight: bold;
        line-height: 36px;
        overflow: hidden;
    }

    .logo-box img {
        width: 36px;
        height: 36px;
        object-fit: contain;
        vertical-align: middle;
    }

    .company-details {
        display: table-cell;
        vertical-align: top;
        padding-left: 6px;
        font-size: 6.5px;
        line-height: 1.2;
    }

    .company-name {
        font-size: 11px;
        font-weight: bold;
        margin-bottom: 1px;
        line-height: 1.1;
    }

    .company-details .tin,
    .company-details .address { font-size: 6.5px; }

    .invoice-title {
        font-size: 11px;
        font-weight: bold;
        text-align: right;
        white-space: nowrap;
    }

    .invoice-number {
        margin-top: 13px;
        text-align: right;
        font-size: 6.8px;
        font-weight: 500;
        color: #dc2626;
    }

    .sales-info {
        width: 100%;
        margin-top: 6px;
        display: table;
        font-size: 6.5px;
    }

    .sales-info-left,
    .sales-info-right {
        display: table-cell;
        width: 50%;
    }

    .sales-info-left {
        text-align: left;
        font-weight: 500;
        text-transform: uppercase;
    }

    .sales-info-right { text-align: right; }

    .billed-to {
        width: 100%;
        margin-top: 6px;
        border: 1px solid #000;
        border-collapse: collapse;
    }

    .billed-to-title {
        height: 17px;
        padding: 3px 5px;
        border-bottom: 1px solid #000;
        font-size: 6.8px;
        font-weight: bold;
    }

    .billed-row {
        width: 100%;
        display: table;
        table-layout: fixed;
        min-height: 15px;
    }

    .billed-row.address-row { min-height: 22px; }

    .billed-label {
        display: table-cell;
        width: 18%;
        padding: 2px 4px;
        vertical-align: top;
        font-size: 6.5px;
        font-weight: bold;
    }

    .billed-value {
        display: table-cell;
        width: 42%;
        padding: 2px 4px;
        vertical-align: top;
        font-size: 6.5px;
        word-wrap: break-word;
    }

    .billed-spacer {
        display: table-cell;
        width: 20%;
        padding: 2px 4px;
        vertical-align: top;
        font-size: 6.5px;
    }

    .billed-right {
        display: table-cell;
        width: 20%;
        padding: 2px 4px;
        vertical-align: top;
        font-size: 6.5px;
        word-wrap: break-word;
    }

    .address-value { line-height: 1.2; }

    .items-table {
        width: 100%;
        margin-top: 6px;
        border-collapse: collapse;
        border: 1px solid #000;
        table-layout: fixed;
        font-size: 6.5px;
    }

    .items-table th {
        height: 19px;
        padding: 3px 4px;
        border: 1px solid #000;
        text-align: center;
        font-size: 6.5px;
        font-weight: bold;
    }

    .items-table td {
        padding: 3px 4px;
        border: 1px solid #000;
        font-size: 6.5px;
        vertical-align: top;
        word-wrap: break-word;
    }

    .description { width: 45%; }
    .quantity { width: 10%; text-align: center; }
    .unit-price { width: 22.5%; text-align: right; }
    .amount { width: 22.5%; text-align: right; }

    .item-code { color: #777; }

    .buyer-contact {
        width: 38%;
        min-height: 18px;
        margin-top: 5px;
        padding: 3px 5px;
        border: 1px solid #000;
        font-size: 6.5px;
        word-wrap: break-word;
    }

    .buyer-contact + .buyer-contact { margin-top: 0; }
    .buyer-contact-label { font-weight: bold; }

    .totals-section {
        width: 100%;
        margin-top: 9px;
        text-align: right;
    }

    .totals-table {
        width: 185px;
        max-width: 100%;
        margin-left: auto;
        border-collapse: collapse;
        border: 1px solid #000;
        font-size: 6.5px;
    }

    .totals-table td {
        padding: 3px 5px;
        border: 1px solid #000;
        text-align: right;
    }

    .totals-table td:first-child { width: 55%; }
    .totals-table td:last-child { width: 45%; }
    .totals-table .bold { font-weight: bold; }

    .tax-notice {
        margin-top: 11px;
        text-align: center;
        font-size: 6px;
        font-weight: bold;
    }
</style>

</head>

@php
    $buyer = $invoice->buyer;
    $buyerName = $buyer?->registered_name ?: $invoice->buyer_name ?: '-';
    $buyerTin = $buyer?->tin ?: $invoice->buyer_tin ?: '-';
    $buyerTradeName = $buyer?->trade_name ?: $invoice->buyer_trade_name;
    $buyerAddress1 = $buyer?->address_line_1 ?: $invoice->buyer_address_line_1;
    $buyerAddress2 = $buyer?->address_line_2 ?: $invoice->buyer_address_line_2;
    $buyerBarangay = $buyer?->barangay ?: $invoice->buyer_barangay;
    $buyerCity = $buyer?->city ?: $invoice->buyer_city;
    $buyerProvince = $buyer?->province ?: $invoice->buyer_province;
    $buyerPostalCode = $buyer?->postal_code ?: $invoice->buyer_postal_code;
    $buyerCountry = $buyer?->country_code ?: $invoice->buyer_country_code ?: 'PH';
    $buyerCustomerCode = $buyer?->customer_code ?: '-';
    $buyerEmail = $buyer?->email ?: $invoice->buyer_email;
    $buyerPhone = $buyer?->phone ?: $invoice->buyer_phone;
@endphp

<body>
<div class="container">

<div class="header">
    <div class="header-left">
        <div class="company-wrapper">
            <div class="logo-box">
              @if ($invoice->seller->logo)
                    @php
                        $logoPath = storage_path('app/public/' . $invoice->seller->logo);
                    @endphp

                    @if (file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="Company Logo">
                    @else
                        <span>{{ $initials ?? '' }}</span>
                    @endif
                @else
                    {{ $initials }}
                @endif
            </div>

            <div class="company-details">
                <div class="company-name">{{ $invoice->seller->trade_name ?? $invoice->seller->registered_name }}</div>
                <div class="tin">TIN: <strong>{{ $invoice->seller->tin ?? '-' }}</strong></div>
                @if ($invoice->seller->address_line_1)
                    <div class="address">{{ $invoice->seller->address_line_1 }}</div>
                @endif
                @if ($invoice->seller->address_line_2)
                    <div class="address">{{ $invoice->seller->address_line_2 }}</div>
                @endif
                <div class="address">
                    @if ($invoice->seller->barangay)Brgy. {{ $invoice->seller->barangay }}, @endif
                    @if ($invoice->seller->city){{ $invoice->seller->city }}@endif
                    @if ($invoice->seller->province), {{ $invoice->seller->province }}@endif
                    @if ($invoice->seller->postal_code) {{ $invoice->seller->postal_code }}@endif
                    @if ($invoice->seller->country_code), {{ $invoice->seller->country_code }}@endif
                </div>
            </div>
        </div>
    </div>

    <div class="header-right">
        <div class="invoice-title">{{ $invoice->document_type ?? 'Service Invoice' }}</div>
    </div>
</div>

<div class="invoice-number">Invoice No: {{ $invoice->invoice_number }}</div>

<div class="sales-info">
    <div class="sales-info-left">{{ $invoice->document_type ?? '-' }}</div>
    <div class="sales-info-right">Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</div>
</div>

<div class="billed-to">
    <div class="billed-to-title">BILLED TO:</div>

    <div class="billed-row">
        <div class="billed-label">Registered Name:</div>
        <div class="billed-value">{{ $buyerName }}</div>
        <div class="billed-spacer"></div>
        <div class="billed-right"></div>
    </div>

    <div class="billed-row">
        <div class="billed-label">PO Number:</div>
        <div class="billed-value">{{ $invoice->purchase_order_number ?? '-' }}</div>
        <div class="billed-spacer"></div>
        <div class="billed-right"></div>
    </div>

    <div class="billed-row">
        <div class="billed-label">Reference No:</div>
        <div class="billed-value">
            {{ $invoice->reference_number ?? '-' }}
            @if ($buyerTradeName) ({{ $buyerTradeName }}) @endif
        </div>
        <div class="billed-spacer"></div>
        <div class="billed-right"></div>
    </div>

    <div class="billed-row">
        <div class="billed-label">TIN:</div>
        <div class="billed-value">{{ $buyerTin }}</div>
        <div class="billed-spacer"></div>
        <div class="billed-right"></div>
    </div>

    <div class="billed-row address-row">
        <div class="billed-label">Address:</div>
        <div class="billed-value address-value">
            {{ $buyerAddress1 }}
            @if ($buyerAddress2), {{ $buyerAddress2 }}@endif
            @if ($buyerBarangay), Brgy. {{ $buyerBarangay }}@endif
            @if ($buyerCity), {{ $buyerCity }}@endif
            @if ($buyerProvince), {{ $buyerProvince }}@endif
            @if ($buyerPostalCode) {{ $buyerPostalCode }}@endif
        </div>
        <div class="billed-spacer"></div>
        <div class="billed-right"></div>
    </div>

    <div class="billed-row">
        <div class="billed-label">Customer Code:</div>
        <div class="billed-value">{{ $buyerCustomerCode }}</div>
        <div class="billed-spacer"></div>
        <div class="billed-right"></div>
    </div>

    <div class="billed-row">
        <div class="billed-label">Nationality:</div>
        <div class="billed-value">{{ $buyerCountry }}</div>
        <div class="billed-spacer"></div>
        <div class="billed-right"></div>
    </div>

    <div class="billed-row">
        <div class="billed-label">Currency:</div>
        <div class="billed-value">{{ $invoice->currency_code }}</div>
        <div class="billed-spacer">Due Date:</div>
        <div class="billed-right">{{ $invoice->due_date?->format('m/d/Y') ?? '-' }}</div>
    </div>
</div>

<table class="items-table">
    <thead>
        <tr>
            <th class="description">Item Description / Nature of Service</th>
            <th class="quantity">Qty</th>
            <th class="unit-price">Unit Cost/Price</th>
            <th class="amount">Amount</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($invoice->items as $item)
            <tr>
                <td>
                    {{ $item->description }}
                    @if ($item->item_code)
                        <span class="item-code">({{ $item->item_code }})</span>
                    @endif
                </td>
                <td class="quantity">
                    {{ (int) $item->quantity }}
                    @if ($item->unit_code) {{ $item->unit_code }}@endif
                </td>
                <td class="unit-price">
                    {{ $invoice->currency_code }} {{ number_format((float) $item->unit_price, 2) }}
                </td>
                <td class="amount">
                    {{ $invoice->currency_code }} {{ number_format((float) ($item->line_total ?? ((float) $item->quantity * (float) $item->unit_price)), 2) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center;color:#999;padding:6px;">No line items</td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($buyerEmail)
    <div class="buyer-contact">
        <span class="buyer-contact-label">Buyer's Email: </span>{{ $buyerEmail }}
    </div>
@endif

@if($buyerPhone)
    <div class="buyer-contact">
        <span class="buyer-contact-label">Buyer's Phone No: </span>{{ $buyerPhone }}
    </div>
@endif

<div class="totals-section">
    <table class="totals-table">
        <tbody>
            <tr>
                <td class="bold">Total Sales</td>
                <td>{{ $invoice->currency_code }} {{ number_format((float) $invoice->gross_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Less: Discount</td>
                <td>{{ $invoice->currency_code }} {{ number_format((float) $invoice->discount_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Less: Withholding Tax</td>
                <td>{{ $invoice->currency_code }} {{ number_format((float) $invoice->withholding_tax_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Add: VAT</td>
                <td>{{ $invoice->currency_code }} {{ number_format((float) $invoice->vat_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="bold">TOTAL AMOUNT DUE</td>
                <td class="bold">{{ $invoice->currency_code }} {{ number_format((float) $invoice->amount_due, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="tax-notice">"THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAX."</div>


</div>
</body>
</html>
