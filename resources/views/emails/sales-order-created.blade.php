<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Baru</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:40px 30px;text-align:center;background:linear-gradient(135deg,#003366,#0055a5);">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;">PAS Market</h1>
                            <p style="color:#e0e0e0;margin:8px 0 0;font-size:14px;">Konfirmasi Order Baru</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px 30px;">
                            <h2 style="color:#333;font-size:18px;margin:0 0 16px;">Order Anda Berhasil Dibuat!</h2>
                            <p style="color:#666;font-size:14px;line-height:1.6;margin:0 0 20px;">
                                Terima kasih telah berbelanja di PAS Market. Order Anda telah berhasil dibuat dan sedang diproses.
                            </p>
                            
                            <div style="background:#f8fafc;border-radius:8px;padding:20px;margin:20px 0;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="color:#666;font-size:13px;padding:8px 0;">Nomor Order:</td>
                                        <td style="color:#003366;font-size:13px;font-weight:bold;text-align:right;padding:8px 0;">{{ $order->order_no }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#666;font-size:13px;padding:8px 0;">Tanggal Order:</td>
                                        <td style="color:#333;font-size:13px;text-align:right;padding:8px 0;">{{ $order->order_date->format('d M Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#666;font-size:13px;padding:8px 0;">Status:</td>
                                        <td style="text-align:right;padding:8px 0;">
                                            <span style="display:inline-block;background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:bold;">{{ strtoupper($order->status) }}</span>
                                        </td>
                                    </tr>
                                    @if($order->customer)
                                    <tr>
                                        <td style="color:#666;font-size:13px;padding:8px 0;">Customer:</td>
                                        <td style="color:#333;font-size:13px;text-align:right;padding:8px 0;">{{ $order->customer->full_name }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <h3 style="color:#333;font-size:16px;margin:30px 0 16px;">Detail Order</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                <thead>
                                    <tr style="background:#f1f5f9;">
                                        <th style="color:#475569;font-size:12px;text-align:left;padding:12px;border-bottom:1px solid #e2e8f0;">Produk</th>
                                        <th style="color:#475569;font-size:12px;text-align:center;padding:12px;border-bottom:1px solid #e2e8f0;">Qty</th>
                                        <th style="color:#475569;font-size:12px;text-align:right;padding:12px;border-bottom:1px solid #e2e8f0;">Harga</th>
                                        <th style="color:#475569;font-size:12px;text-align:right;padding:12px;border-bottom:1px solid #e2e8f0;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td style="color:#333;font-size:13px;padding:12px;border-bottom:1px solid #f1f5f9;">{{ $item->product_name }}@if(!empty($item->notes))<div style="color:#92400e;font-style:italic;margin-top:4px;">Catatan: {{ $item->notes }}</div>@endif</td>
                                        <td style="color:#333;font-size:13px;text-align:center;padding:12px;border-bottom:1px solid #f1f5f9;">{{ $item->quantity }}</td>
                                        <td style="color:#333;font-size:13px;text-align:right;padding:12px;border-bottom:1px solid #f1f5f9;">Rp {{ number_format($item->net_price, 0, ',', '.') }}</td>
                                        <td style="color:#333;font-size:13px;text-align:right;padding:12px;border-bottom:1px solid #f1f5f9;">Rp {{ number_format($item->final_total, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" style="color:#666;font-size:13px;text-align:right;padding:12px;font-weight:bold;">Total:</td>
                                        <td style="color:#003366;font-size:16px;text-align:right;padding:12px;font-weight:bold;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>

                            @if($order->delivery_address)
                            <h3 style="color:#333;font-size:16px;margin:30px 0 16px;">Alamat Pengiriman</h3>
                            <div style="background:#f8fafc;border-radius:8px;padding:16px;">
                                <p style="color:#333;font-size:13px;margin:0 0 4px;font-weight:bold;">{{ $order->delivery_to }}</p>
                                <p style="color:#666;font-size:13px;margin:0 0 4px;line-height:1.5;">{{ $order->delivery_address }}</p>
                                <p style="color:#666;font-size:13px;margin:0;">{{ $order->delivery_phone }}</p>
                            </div>
                            @endif

                            @if($order->notes)
                            <h3 style="color:#333;font-size:16px;margin:30px 0 16px;">Catatan</h3>
                            <div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:12px 16px;border-radius:4px;">
                                <p style="color:#92400e;font-size:13px;margin:0;line-height:1.5;">{{ $order->notes }}</p>
                            </div>
                            @endif

                            <p style="color:#666;font-size:13px;line-height:1.6;margin:30px 0 0;">
                                Jika Anda memiliki pertanyaan, silakan hubungi tim kami. Terima kasih telah mempercayai PAS Market!
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 30px;text-align:center;border-top:1px solid #eee;">
                            <p style="color:#999;font-size:12px;margin:0;">&copy; {{ date('Y') }} PAS Market. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
