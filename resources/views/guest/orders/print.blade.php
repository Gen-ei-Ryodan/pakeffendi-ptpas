<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sales Order - {{ $order->order_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; background: #fff; color: #000; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 10mm; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4mm; }
        .header-left .company-name { font-size: 14px; font-weight: bold; }
        .header-left .company-address { font-size: 10px; margin-top: 1mm; }
        .header-right .title { font-size: 18px; font-weight: bold; text-align: right; }

        /* Info Section */
        .info-section { display: flex; justify-content: space-between; margin-bottom: 5mm; }
        .customer-info { width: 55%; }
        .customer-label { font-weight: bold; margin-bottom: 1mm; }
        .customer-name { font-weight: bold; font-size: 12px; margin-bottom: 1mm; }
        .customer-address { font-size: 10px; line-height: 1.4; }
        .order-meta { width: 40%; text-align: right; }
        .order-meta-table { margin-left: auto; }
        .order-meta-table td { padding: 0.5mm 0; font-size: 11px; }
        .order-meta-table .label { text-align: left; padding-right: 3mm; }
        .order-meta-table .value { text-align: left; font-weight: bold; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 3mm; }
        .items-table th,
        .items-table td { padding: 1.5mm 2mm; font-size: 10px; }
        .items-table th { border-bottom: 2px solid #000; font-weight: bold; text-align: center; }
        .items-table td { border-bottom: 1px solid #ccc; }
        .items-table thead tr { border-top: 2px solid #000; border-bottom: 2px solid #000; }
        .col-no { width: 4%; text-align: center; }
        .col-kode { width: 12%; text-align: left; }
        .col-nama { width: 38%; text-align: left; }
        .col-jml { width: 6%; text-align: center; }
        .col-unit { width: 6%; text-align: center; }
        .col-harga { width: 12%; text-align: right; }
        .col-disc { width: 8%; text-align: right; }
        .col-ext { width: 14%; text-align: right; }

        /* Bottom Section */
        .bottom-section { display: flex; justify-content: space-between; margin-top: 8mm; }
        .notes-section { width: 40%; }
        .notes-label { font-weight: bold; margin-bottom: 2mm; }
        .notes-content { font-size: 10px; line-height: 1.4; }

        .signature-section { width: 20%; text-align: center; }
        .signature-label { font-weight: bold; margin-bottom: 15mm; }
        .signature-line { border-top: 1px solid #000; padding-top: 2mm; font-size: 9px; }

        .totals-section { width: 35%; text-align: right; }
        .totals-table { margin-left: auto; border-collapse: collapse; }
        .totals-table td { padding: 1mm 3mm; font-size: 11px; }
        .totals-table .label { text-align: left; }
        .totals-table .colon { text-align: center; padding: 0 2mm; }
        .totals-table .value { text-align: right; font-weight: bold; }
        .totals-table .grand { font-size: 12px; font-weight: bold; border-top: 1px solid #000; padding-top: 2mm; }

        .footer-note { font-size: 9px; font-style: italic; margin-top: 3mm; text-align: right; }

        @media print {
            .page { width: 100%; padding: 8mm; }
            @page { size: A4 portrait; margin: 10mm 8mm; }
            .items-table thead { display: table-header-group; }
            .items-table tbody tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">PT.PARAMA ASIA SEJAHTERA</div>
                <div class="company-address">Jl. Cempaka Biru Selatan 1 No. 9X, Denpasar Utara, Bali</div>
                <div class="company-address">081210015727 /</div>
            </div>
            <div class="header-right">
                <div class="title">Sales Order</div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="customer-info">
                <div class="customer-label">Customer:</div>
                <div class="customer-name">{{ $order->customer?->name ?? $order->delivery_to }}</div>
                <div class="customer-address">
                    {{ $order->delivery_address ?: $order->customer?->address }}
                </div>
            </div>
            <div class="order-meta">
                <table class="order-meta-table">
                    <tr>
                        <td class="label">Sales Order No</td>
                        <td class="value">: {{ $order->order_no }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tgl</td>
                        <td class="value">: {{ optional($order->order_date)->format('d-M-Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jatuh Tempo Hr</td>
                        <td class="value">: {{ $order->payment_terms ?? '-' }} hari</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-kode">Kode</th>
                    <th class="col-nama">Nama Barang</th>
                    <th class="col-jml">Jml</th>
                    <th class="col-unit">Unit</th>
                    <th class="col-harga">Harga</th>
                    <th class="col-disc">Disc %</th>
                    <th class="col-ext">Ext Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product?->sku ?? $item->product?->code }}</td>
                    <td>
                        {{ $item->product_name }}
                        @if(!empty($item->notes))
                        <br><span style="font-size:9px;">* {{ $item->notes }}</span>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->product?->unit ?: 'PCS' }}</td>
                    <td>{{ number_format((float) $item->net_price, 0, ',', '.') }}</td>
                    <td>{{ number_format((float) $item->discount_percent, 2) }}</td>
                    <td>{{ number_format((float) $item->final_total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <div class="notes-section">
                <div class="notes-label">Notes:</div>
                <div class="notes-content">
                    @if(!empty($order->notes))
                        {{ $order->notes }}
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="signature-section">
                <div class="signature-label">Dibuat Oleh</div>
                <div class="signature-line">&nbsp;</div>
            </div>
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="label">Sub Total</td>
                        <td class="colon">:</td>
                        <td class="value">{{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Extra Discount</td>
                        <td class="colon">:</td>
                        <td class="value">{{ number_format((float) ($order->extra_discount ?? 0), 0) }} %</td>
                    </tr>
                    <tr>
                        <td class="label grand">Grand Total</td>
                        <td class="colon grand">:</td>
                        <td class="value grand">{{ number_format((float) ($order->grand_total + $order->shipping_fee), 0, ',', '.') }}</td>
                    </tr>
                </table>
                <div class="footer-note">* harga sudah termasuk ppn</div>
            </div>
        </div>
    </div>
</body>
</html>
