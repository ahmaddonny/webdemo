<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="" />
    <title>IFCA Software</title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="id_ID" />
    <meta property="og:title" content="IFCA Software" />
    <meta property="og:site_name" content="IFCA Software" />
    <link rel="shortcut icon" href="{{ url('assets/media/logos/favicon.ico') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ url('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="app-blank">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Body-->
            <div class="flex-column-fluid px-10 py-10" style="background-color:#D5D9E2;">
                <!--begin::Email template-->
                <style>
                    html,
                    body {
                        padding: 0;
                        margin: 0;
                        font-family: Inter, Helvetica, "sans-serif";
                    }
                </style>
                <div id="#kt_app_body_content"
                    style="background-color:#D5D9E2; font-family:Arial,Helvetica,sans-serif; line-height: 1.5; min-height: 100%; font-weight: normal; font-size: 15px; color: #2F3044; margin:0; padding:0; width:100%;">
                    <div
                        style="background-color:#ffffff; padding: 45px 0 34px 0; border-radius: 24px; margin:40px auto; max-width: 600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" height="auto"
                            style="border-collapse:collapse">
                            <tbody>
                                <tr>
                                    <td align="center" valign="center" style="text-align:center; padding-bottom: 10px">
                                        <!--begin:Email content-->
                                        <div style="text-align:center; margin:0 60px 34px 60px">
                                            <!--begin:Logo-->
                                            <div style="margin-bottom:10px" class="mb-10">
                                                <img alt="Logo" src="{{ url('assets/media/auth/logo-dark.png') }}"
                                                    width="150" />
                                            </div>
                                            <!--end:Logo-->
                                            <!--begin:Text-->
                                            <div
                                                style="text-align:start; font-size: 13px; font-weight: 500; margin-bottom: 27px; font-family:Arial,Helvetica,sans-serif;">
                                                <p
                                                    style="margin-bottom:2px; color:#181C32; font-size: 14px; font-weight:600; text-align: left">
                                                    Bapak / Ibu Penyewa Ayani Megamal Pontianak yang terhormat</p>
                                                <p
                                                    style="margin-bottom:40px; color:#181C32; font-size: 14px; font-weight:600; text-align: left">
                                                    {{ $debtor_name }} - Unit {{ $debtor_acct }}</p>
                                                <p style="margin-bottom:10px; color:#5E6278; text-align: left">Kami
                                                    ucapkan terima kasih atas pembayaran dari bapak / ibu. Bersama
                                                    ini kami sampaikan
                                                    bukti penerimaan atas transaksi tanggal {{ $gen_date }}</p>
                                                <p style="margin-bottom:40px; color:#5E6278; text-align: justify">
                                                    Demikian
                                                    kami sampaikan, atas perhatian dan kerjasama yang baik kami ucapkan
                                                    terima kasih.</p>
                                                <p style="margin-bottom:2px; color:#5E6278; text-align: left">Hormat
                                                    kami, </p>
                                                <p style="margin-bottom:2px; color:#5E6278; text-align: left">Manajemen
                                                    Ayani Megamal
                                                    Pontianak
                                                </p>
                                                <!--end:Text-->
                                            </div>
                                        </div>
                                        <!--end:Email content-->
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="center"
                                        style="font-size: 13px; padding:0 15px; text-align:center; font-weight: 500; color: #e91414; font-family:Arial,Helvetica,sans-serif">
                                        <p>
                                            <i>Email ini dikirim secara otomatis oleh sistem, mohon untuk tidak
                                                dibalas.</i>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--end::Email template-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Root-->
</body>
<!--end::Body-->

</html>