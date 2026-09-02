<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Pesanan - {{ $order->order_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; background: #fff; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 10mm; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8mm; }
        .header-left { text-align: left; }
        .header-left .company-name { font-size: 14px; font-weight: bold; }
        .header-right { text-align: right; }
        .header-right .title { font-size: 22px; font-weight: bold; letter-spacing: 2px; }
        .header-right .meta { margin-top: 4mm; font-size: 11px; }
        .header-right .meta div { margin-bottom: 1mm; }
        .meta-label { display: inline-block; width: 70px; text-align: left; }

        /* Order Status */
        .status-badge { display: inline-block; padding: 1mm 3mm; background: #f0f0f0; border: 1px solid #999; border-radius: 3px; font-size: 10px; font-weight: bold; margin-bottom: 3mm; }

        /* Customer Info */
        .customer-info { margin-bottom: 5mm; }
        .customer-row { display: flex; margin-bottom: 1mm; }
        .customer-label { width: 80px; font-weight: bold; }
        .customer-sublabel { width: 80px; text-align: right; margin-right: 5px; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 3mm; }
        .items-table th,
        .items-table td { border: 1px solid #000; padding: 2mm; font-size: 10px; }
        .items-table th { background: #f0f0f0; font-weight: bold; text-align: center; }
        .items-table td { vertical-align: top; }
        .col-no { width: 4%; text-align: center; }
        .col-qty { width: 8%; text-align: center; }
        .col-unit { width: 8%; text-align: center; }
        .col-price { width: 15%; text-align: right; }
        .col-subtotal { width: 15%; text-align: right; }
        .col-desc { width: 50%; }

        /* Bottom Section - Notes left, Totals right */
        .bottom-section { display: flex; justify-content: space-between; margin-top: 3mm; }
        .notes-section { width: 55%; }
        .notes-label { font-weight: bold; margin-bottom: 2mm; }
        .notes-content { font-size: 10px; line-height: 1.5; }

        .totals-section { width: 40%; text-align: right; }
        .totals-table { display: inline-table; border-collapse: collapse; width: auto; }
        .totals-table td { border: 1px solid #000; padding: 2mm 5mm; font-size: 10px; }
        .totals-table .label { font-weight: bold; text-align: left; white-space: nowrap; }
        .totals-table .value { text-align: right; white-space: nowrap; }
        .totals-table .grand { font-size: 12px; font-weight: bold; }

        /* Signature */
        .signature-section { display: flex; justify-content: space-between; margin-top: 10mm; }
        .signature-box { width: 45%; border: 1px solid #000; padding: 4mm; text-align: center; }
        .signature-box .box-title { font-weight: bold; margin-bottom: 12mm; text-decoration: underline; font-size: 10px; }
        .signature-box .box-footer { margin-top: 12mm; font-size: 9px; }

        @media print {
            .page { width: 100%; padding: 8mm; }
            @page { size: A4 portrait; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">PAS</div>
            </div>
            <div class="header-right">
                <div class="title">NOTA PESANAN</div>
                <div class="status-badge">{{ $order->status }}</div>
                <div class="meta">
                    <div><span class="meta-label">No. Pesanan</span> : {{ $order->order_no }}</div>
                    <div><span class="meta-label">Tanggal</span> : {{ optional($order->order_date)->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
            <div class="customer-row">
                <div class="customer-label">Penerima</div>
                <div>: {{ $order->delivery_to ?: $order->customer?->full_name }}</div>
            </div>
            @if($order->delivery_phone || $order->customer?->phone)
            <div class="customer-row">
                <div class="customer-label">Telepon</div>
                <div>: {{ $order->delivery_phone ?: $order->customer->phone }}</div>
            </div>
            @endif
            @if($order->delivery_address || $order->customer?->address)
            <div class="customer-row">
                <div class="customer-label">Alamat</div>
                <div>: {{ $order->delivery_address ?: $order->customer->address }}</div>
            </div>
            @endif
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th class="col-qty">JUMLAH</th>
                    <th class="col-unit">SATUAN</th>
                    <th class="col-desc">URAIAN</th>
                    <th class="col-price">HARGA</th>
                    <th class="col-subtotal">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->product?->unit ?: 'pcs' }}</td>
                    <td>
                        {{ $item->product_name }}
                        @if(!empty($item->notes))
                        <div style="font-size:9px; color:#666;">* {{ $item->notes }}</div>
                        @endif
                        @if((float) $item->discount_percent > 0)
                        <div style="font-size:9px; color:#666;">Disc {{ number_format((float) $item->discount_percent, 0) }}%</div>
                        @endif
                    </td>
                    <td>Rp {{ number_format((float) $item->net_price, 0, ',', '.') }}/{{ $item->product?->unit ?: 'pcs' }}</td>
                    <td>Rp {{ number_format((float) $item->final_total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @for($i = count($order->items); $i < 8; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
            </tbody>
        </table>

        <!-- Bottom Section: Notes left, Totals right -->
        <div class="bottom-section">
            <div class="notes-section">
                <div class="notes-label">Catatan</div>
                <div class="notes-content">
                    @if(!empty($order->notes))
                        {{ $order->notes }}
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="label">Total</td>
                        <td class="value">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Ongkir</td>
                        <td class="value">Rp {{ number_format((float) $order->shipping_fee, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label grand">Grand Total</td>
                        <td class="value grand">Rp {{ number_format((float) ($order->grand_total + $order->shipping_fee), 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="box-title">PEMESAN</div>
                <div class="box-footer">NAMA / TTD / CAP</div>
            </div>
            <div class="signature-box">
                <div class="box-title">PAS</div>
                <div class="box-footer">NAMA / TTD / CAP</div>
            </div>
        </div>
    </div>
</body>
</html>