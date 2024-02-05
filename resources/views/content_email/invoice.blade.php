<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="" />
    <title>IFCA Property365 Indonesia</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="id_ID" />
    <meta property="og:title" content="IFCA Software - Web Blast Email" />
    <meta property="og:site_name" content="IFCA Software" />
    <link rel="shortcut icon" href="{{ url('assets/media/logos/favicon.ico') }}" />
</head>
<!--end::Head-->
<!--begin::Body-->

<body style="font-family:calibri; font-size:14px;">
    <p> Kepada Yth. <br>
        <b> {{ $name }} </b><br>
        <b>Unit : {{ $lot_descs }} </b><br>
        APARTMENT Ciputra World 2 Jakarta <br>
        Jl. Prof. DR. Satrio Kav.11 Jakarta Selatan 12940
        <br><br>

        Tagihan : <br>
        {{ $descs }}
        <br><br>

        <b>Total Kewajiban<br>
            Rp. {{ $amount }}</b>
        <br>

    <p><b>Tanggal Jatuh Tempo : {{ $jatuh_tempo }}</b></p>

    <i>* Nominal tersebut adalah tagihan pada bulan berjalan, sudah termasuk tunggakan (jika ada) pada periode tahun
        {{ $tahun }}. Pertanyaan perihal invoice dan tunggakan dapat di email ke
        ar.officer@cw2j.com, atau menghubungi billing department.</i>
    <br><br>

    <b>Cara dan Metode Pembayaran :</b><br>
    <table>
        <tr>
            <td>-</td>
            <td>Akun pembayaran Virtual Account (VA) IPL/SCSF memiliki nomor berbeda dengan VA Utilitas, tertera dalam
                masing-masing Invoice</td>
        </tr>
        <tr>
            <td>-</td>
            <td>Jumlah yang dibayarkan sesuai dengan nilai tagihan</td>
        </tr>
        <tr>
            <td>-</td>
            <td>Badan pengelola tidak menerima pembayaran tunai, cek, dan giro.</td>
        </tr>
    </table>
    <br>
    VA atas nama Bpk/Ibu {{ $name }}<br>
    <br>
    <b>Nomor Rekening VA Bank Mandiri & BCA - Hanya dapat dibayarkan melalui Bank yang Sama</b>
    <table>
        <tr>
            <td>VA Mandiri </td>
            <td>:</td>
            <td> {{ $va_mandiri }} [{{ $lot_descs }}] </td>
        </tr>
        <tr>
            <td>VA BCA </td>
            <td>:</td>
            <td> {{ $va_bca }} [{{ $lot_descs }}] </td>
        </tr>

    </table>
    <b>VA BCA baru bisa dibayarkan 1 x 24 jam setelah email ini diterima</b><br>
    <br>
    <b>Pembayaran dari Bank lain dapat menggunakan:</b>
    <table>
        <tr>
            <td>VA Permata </td>
            <td>:</td>
            <td> {{ $va_permata }} [{{ $lot_descs }}] </td>
        </tr>
    </table>
    <br>
    <b>Pembayaran dari Tokopedia dapat menggunakan :</b><br>
    VA Tokopedia : {{ $va_tokped }} [{{ $lot_descs }}]
    <br><br><br>
    AR Officer<br>
    Building Management CW2J Apartment<br>
    Lower Ground, The Orchard<br>
    No Telp : 081283598237
    </p>
</body>
<!--end::Body-->

</html>