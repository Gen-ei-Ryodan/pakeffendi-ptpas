<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Pesanan - {{ $order->order_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; background: #fff; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10mm; }
        .header-left { text-align: left; }
        .header-left .company-name { font-size: 14px; font-weight: bold; }
        .header-right { text-align: right; }
        .header-right .title { font-size: 24px; font-weight: bold; letter-spacing: 2px; }
        .header-right .meta { margin-top: 5mm; font-size: 12px; }
        .header-right .meta div { margin-bottom: 1mm; }
        .meta-label { display: inline-block; width: 80px; text-align: left; }

        /* Customer Info */
        .customer-info { margin-bottom: 8mm; }
        .customer-row { display: flex; margin-bottom: 1mm; }
        .customer-label { width: 100px; font-weight: bold; }
        .customer-sublabel { width: 100px; text-align: right; margin-right: 5px; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 5mm; }
        .items-table th,
        .items-table td { border: 1px solid #000; padding: 3mm 2mm; font-size: 11px; }
        .items-table th { background: #f0f0f0; font-weight: bold; text-align: center; }
        .items-table td { vertical-align: top; }
        .col-no { width: 5%; text-align: center; }
        .col-qty { width: 10%; text-align: center; }
        .col-unit { width: 10%; text-align: center; }
        .col-desc { width: 50%; }
        .col-note { width: 25%; }

        /* Notes */
        .notes-section { margin-bottom: 8mm; }
        .notes-label { font-weight: bold; margin-bottom: 2mm; }

        /* Signature */
        .signature-section { display: flex; justify-content: space-between; margin-top: 15mm; }
        .signature-box { width: 45%; border: 1px solid #000; padding: 5mm; text-align: center; }
        .signature-box .box-title { font-weight: bold; margin-bottom: 15mm; text-decoration: underline; }
        .signature-box .box-footer { margin-top: 15mm; font-size: 10px; }

        /* Footer */
        .footer { text-align: center; margin-top: 10mm; padding-top: 5mm; border-top: 2px solid #000; font-size: 11px; }
        .footer .thank-you { font-style: italic; margin-bottom: 2mm; }
        .footer .address { font-weight: bold; }

        @media print {
            .no-print { display: none !important; }
            .page { width: 100%; padding: 10mm; }
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
                <div class="meta">
                    <div><span class="meta-label">Tanggal</span> : {{ optional($order->order_date)->format('d/m/Y') }}</div>
                    <div><span class="meta-label">No Nota</span> : {{ $order->order_no }}</div>
                    <div><span class="meta-label">Customer ID</span> : {{ $order->customer?->customer_code }}</div>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
            <div class="customer-row">
                <div class="customer-label">PEMESAN</div>
                <div class="customer-sublabel">NAMA</div>
                <div>: {{ $order->customer?->full_name }}</div>
            </div>
            @if($order->customer?->city || $order->customer?->province)
            <div class="customer-row">
                <div class="customer-label"></div>
                <div class="customer-sublabel">KOTA / KAB</div>
                <div>: {{ $order->customer->city }}{{ $order->customer->city && $order->customer->province ? ', ' : '' }}{{ $order->customer->province }}</div>
            </div>
            @endif
            @if($order->delivery_address || $order->customer?->address)
            <div class="customer-row">
                <div class="customer-label"></div>
                <div class="customer-sublabel">ALAMAT</div>
                <div>: {{ $order->delivery_address ?: $order->customer->address }}</div>
            </div>
            @endif
            @if($order->delivery_phone || $order->customer?->phone)
            <div class="customer-row">
                <div class="customer-label"></div>
                <div class="customer-sublabel">TELP</div>
                <div>: {{ $order->delivery_phone ?: $order->customer->phone }}</div>
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
                    <th class="col-note">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>pcs</td>
                    <td>{{ $item->product_name }}</td>
                    <td>
                        @if(!empty($item->notes))
                            {{ $item->notes }}
                        @endif
                        @if((float) $item->discount_percent > 0)
                            Discount {{ number_format((float) $item->discount_percent, 0) }}%
                        @endif
                    </td>
                </tr>
                @endforeach
                {{-- Empty rows --}}
                @for($i = count($order->items); $i < 10; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
            </tbody>
        </table>

        <!-- Notes -->
        <div class="notes-section">
            <div class="notes-label">CAT : {{ $order->notes ?? '-' }}</div>
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

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Thank you for your business!</div>
            <div class="address">Jl. Effendi, Kota Gorontalo</div>
            <div>Telp. 0812-3456-7890</div>
        </div>
    </div>
</body>
</html>
