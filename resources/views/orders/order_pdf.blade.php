<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order PDF</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="text-center">
        <img src="assets/img/logo.png" alt="Logo" style="width: 100px;">
        <h2>Laporan Penjualan Toko Kembang Telon</h2>
        <h5>Periode ({{ $date[0] }} - {{ $date[1] }})</h5>
    </div>
    <hr>
    <table width="100%" class="table-hover table-bordered">
        <thead>
            <tr>
                <th>InvoiceID</th>
                <th>Pelanggan</th>
                <th>Produk</th>
                <th>Jumlah Ongkir</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse ($orders as $row)
                <tr>
                    <td><strong>{{ $row->invoice }}</strong></td>
                    <td>
                        <strong>{{ $row->customer_name }}</strong><br>
                        <label><strong>Telp:</strong> {{ $row->customer_phone }}</label><br>
                        <label><strong>Alamat:</strong> 
                            {{ $row->customer_address }}
                            {{ optional($row->customer->district)->name }}
                            - {{ optional($row->citie)->name }},
                            {{ optional($row->citie)->postal_code }}
                        </label>
                    </td>
                    <td>
                    @foreach ($row->details as $item)
                        <li>{{ $item->product->name }} - {{ $item->qty }} item</li>
                    @endforeach
                    </td>
                    <td>Rp {{ number_format($row->ongkos_kirim) }}</td>

                    <td>Rp {{ number_format($row->cost) }}</td>
                    <td>{{ $row->created_at->format('d-m-Y') }}</td>
                </tr>

                @php $total += $row->subtotal @endphp
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td>Rp {{ number_format($total) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
