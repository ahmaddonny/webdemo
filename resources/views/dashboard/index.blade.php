@extends('template.base')
@section('body')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<!--begin::Toolbar-->
<div class="toolbar py-5 pb-lg-15" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column me-3">
            <!--begin::Title-->
            <h1 class="d-flex text-white fw-bold my-1 fs-3">Dashboard</h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
    </div>
    <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">
        <h2 class="fw-bold text-gray-800">Welcome,</h2>
        <div class="text-white fw-semibold fs-6 mb-5">
            {{-- {{ Auth()->user()->name }} --}}
            Developer
        </div>
        <!--begin::Row-->
        <div class="row gy-5 g-xl-8 mb-xl-5">
            <!--begin::Col-->
            <div class="col-xl-3 col-sm-6 col-12">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xxl-stretch shadow">
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="fw-bold fs-1" data-kt-countup="true"
                                    data-kt-countup-value="{{ $trans_pending }}">
                                    {{ $trans_pending }}</div>
                                <div class="fw-semibold fs-6">Pending Transaction</div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-center rounded-circle w-50px h-50px bg-light-warning border-clarity-warning"
                                    style="border: 1px solid var(--bs-warning-clarity)">
                                    <i class="fa-regular fa-clock text-warning fs-2qx lh-0"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 1-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-3 col-sm-6 col-12">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xxl-stretch shadow">
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="fw-bold fs-1" data-kt-countup="true"
                                    data-kt-countup-value="{{ $trans_done }}">
                                    {{ $trans_done }}</div>
                                <div class="fw-semibold fs-6">Complete Transaction</div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-center rounded-circle w-50px h-50px bg-light-primary border-clarity-primary"
                                    style="border: 1px solid var(--bs-primary-clarity)">
                                    <i class="fa-regular fa-clock text-primary fs-2qx lh-0"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 1-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-6 col-sm-12 col-12">
                <!--begin::Mixed Widget 2-->
                <div class="card card-xxl-stretch shadow">
                    <!--begin::Header-->
                    <div class="card-header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">E-meterai Quota Balance</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Check your current available
                                quota balance</span>
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--begin::Item-->
                        <div class="d-flex align-items-center bg-light-success rounded p-5 mb-7">
                            @if($saldo < '10' ) <span class="symbol-label"
                                style="background: rgba(255, 255, 255, 0.15);" data-bs-toggle="tooltip"
                                data-bs-placement="left" title="Recharge Balance">
                                <i class="fa-solid fa-exclamation text-success fs-1 me-5"></i>
                                </span>
                                @endif
                                <!--begin::Title-->
                                <div class="flex-grow-1 me-2">
                                    <div class="fw-bold text-gray-800 fs-6">Quota</div>
                                    <div class="text-muted fw-semibold d-block fs-1" data-kt-countup="true"
                                        data-kt-countup-value="{{ $saldo }}">
                                        {{ $saldo }}
                                    </div>
                                </div>
                                <!--end::Title-->
                                <!--begin::Lable-->
                                <span class="fw-bold text-success py-1 fs-2">
                                    <button type="button" class="btn btn-primary fw-semibold" id="topUp">Top Up</button>
                                    &nbsp;
                                    <span class="indicator-label cursor-pointer" data-bs-toggle="tooltip"
                                        data-bs-placement="right" title="How to Top Up" id="btnInfo">
                                        <i class="fa-solid fa-circle-info text-success fs-1"></i>
                                    </span>
                                </span>
                                <!--end::Lable-->
                        </div>
                        <!--end::Item-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 2-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
        <!--begin::Row-->
        <div class="row gy-5 g-xl-8 mb-xl-5">
            <!--begin::Col-->
            <div class="col-xl-12 col-sm-12 col-12">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xxl-stretch shadow">
                    <!--begin::Header-->
                    <div class="card-header border-0">
                        <!--begin::Title-->
                        <div class="card-title">
                            <h3 class="m-0 text-gray-900">Transaction Summary</h3>
                        </div>
                        <!--end::Title-->
                        <!--begin::Toolbar-->
                        <div class="card-toolbar" data-select2-id="select2-data-126-u5kq">
                            <!--begin::Filters-->
                            <div class="d-flex flex-stack flex-wrap gap-4" data-select2-id="select2-data-125-2hd3">
                                <!--begin::Status-->
                                <div class="d-flex align-items-center fw-bold">
                                    <!--begin::Label-->
                                    <div class="text-gray-400 fs-7 me-2">
                                        Status
                                    </div>
                                    <!--end::Label-->
                                    <!--begin::Select-->
                                    <select
                                        class="form-select form-select-transparent text-dark fs-7 lh-1 fw-bold py-0 ps-3 w-auto"
                                        data-control="select2" data-hide-search="true" data-dropdown-css-class="w-150px"
                                        data-placeholder="Choose a Status" data-kt-table-widget-4="filter_status"
                                        id="status" name="status">
                                        <option value="pending">Pending</option>
                                        <option value="done">Complete</option>
                                    </select>
                                    <!--end::Select-->
                                </div>
                                <!--end::Status-->
                            </div>
                            <!--begin::Filters-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--begin::Table container-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table id="tbltransaction"
                                class="table table-rounded table-striped border gy-4 align-middle fw-bold">
                                <thead class="border-bottom border-gray-200 fs-6 fw-bold bg-lighten">
                                    <tr>
                                        <th class="min-w-20px ps-9">No</th>
                                        <th class="min-w-100px ps-0">Order ID</th>
                                        <th class="min-w-75px">Product</th>
                                        <th class="min-w-100px">Purchase Date</th>
                                        <th class="min-w-120px">Amount</th>
                                        <th class="min-w-120px">Item Price</th>
                                        <th class="min-w-120px">Total</th>
                                        <th class="min-w-75px">Status</th>
                                        <th class="min-w-150px ps-0">Payment Link</th>
                                    </tr>

                                </thead>
                                <tbody class="fs-6 fw-semibold text-gray-600">

                                </tbody>
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 1-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
        <!--begin::Row-->
        <div class="row gy-5 g-xl-8 mb-xl-5">
            <!--begin::Col-->
            <div class="col-xl-3 col-sm-6 col-12">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xxl-stretch shadow">
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="fw-bold fs-1" data-kt-countup="true" data-kt-countup-value="{{ $pending }}">
                                    {{ $pending }}</div>
                                <div class="fw-semibold fs-6">Pending</div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-center rounded-circle w-50px h-50px bg-light-warning border-clarity-warning"
                                    style="border: 1px solid var(--bs-warning-clarity)">
                                    <i class="fa-regular fa-clock text-warning fs-2qx lh-0"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 1-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-3 col-sm-6 col-12">
                <!--begin::Mixed Widget 1-->
                <div class="card card-xxl-stretch shadow">
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="fw-bold fs-1" data-kt-countup="true" data-kt-countup-value="{{ $process }}">
                                    {{ $process }}</div>
                                <div class="fw-semibold fs-6">Processed</div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-center rounded-circle w-50px h-50px bg-light-primary border-clarity-primary"
                                    style="border: 1px solid var(--bs-primary-clarity)">
                                    <i class="fa-regular fa-clock text-primary fs-2qx lh-0"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 1-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-3 col-sm-6 col-12">
                <!--begin::Mixed Widget 2-->
                <div class="card card-xxl-stretch shadow">
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="fw-bold fs-1" data-kt-countup="true" data-kt-countup-value="{{ $deliver }}">
                                    {{ $deliver }}</div>
                                <div class="fw-semibold fs-6">Delivered</div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-center rounded-circle w-50px h-50px bg-light-success border-clarity-success"
                                    style="border: 1px solid var(--bs-success-clarity)">
                                    <i class="fa-solid fa-envelope-circle-check text-success fs-2qx lh-0"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 2-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-3 col-sm-6 col-12">
                <!--begin::Mixed Widget 3-->
                <div class="card card-xxl-stretch shadow">
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="fw-bold fs-1" data-kt-countup="true" data-kt-countup-value="{{ $reject }}">
                                    {{ $reject }}</div>
                                <div class="fw-semibold fs-6">Rejected</div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex flex-center rounded-circle w-50px h-50px bg-light-danger border-clarity-danger"
                                    style="border: 1px solid var(--bs-danger-clarity)">
                                    <i class="fa-regular fa-circle-xmark text-danger fs-2qx lh-0"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 3-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->

        <div class="row gy-5 g-xl-8">
            <!--begin::Col-->
            <div class="col-xl-12 col-sm-12 col-12">
                <!--begin::Tables Widget 4-->
                <div class="card card-xl-stretch mt-3 mb-xl-8">
                    <!--begin::Header-->
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Email Statistics</span>
                        </h3>
                        <div class="card-toolbar">
                            <div class="input-group mb-3">
                                <span class="input-group-text cursor-pointer">
                                    <span class="menu-icon">
                                        <i class="ki-duotone ki-calendar">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </span>
                                <input type="text" class="form-control" placeholder="Pick date range" id="date_range"
                                    name="date_range" />
                            </div>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body py-3">
                        <!--begin::Chart container-->
                        <div id="divChart">
                            <canvas id="myChart"></canvas>
                        </div>
                        <!--end::Chart container-->
                    </div>
                    <!--begin::Body-->
                </div>
                <!--end::Tables Widget 4-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Post-->
</div>
<!--end::Container-->

<!--begin::Javascript-->
<script type="text/javascript">
    $(document).ready(function() {
        $('#status').change(function() {
            tbltransaction.ajax.reload(null, true);
        });
        
        $("#date_range").daterangepicker({
            locale: {
                format: 'DD/MM/YYYY',
            }
        });

        var date_range = $('#date_range').val();
        var split = date_range.split(" - ");
        var start_date = split[0];
        var end_date = split[1];

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ route('dash.get.graph') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                start_date: start_date,
                end_date: end_date,
            },
            success: function(data){
                $("#myChart").remove();
                $("#divChart").append('<canvas id="myChart"></canvas>');
                const ctx = document.getElementById('myChart').getContext("2d");

                new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    });

    $('#topUp').click(function() {
        $('#modaldialog').removeClass('modal-sm');
        $('#modaldialog').addClass('modal-md');
        $('#modaltitle').html('Top Up');
        $('#modalbody').load("{{ route('dash.topup') }}");
        $('#modal').modal({
            'backdrop': 'static',
            'keyboard': false
        });
        $('#modal').modal('show');
    });

    //popup cara pembelian
    $('#btnInfo').click(function() {
        $('#modaldialog').removeClass('modal-sm');
        $('#modaldialog').addClass('modal-md');
        $('#modalbody').load("{{ route('dash.how.to.topup') }}");
        $('#modal').modal({
            'backdrop': 'static',
            'keyboard': false
        });
        $('#modal').modal('show');
    });

    // TABLE TRANSACTION
    var tbltransaction;
    tbltransaction = $('#tbltransaction').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            "url" : "{{ route('dash.getTable') }}",
            "type": "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "status": function (d) {
                    var search = $('#status').val();
                    var b = "";
                    if (search == null || search == "") {
                        return b;
                    } {
                        return search;
                    }
                }
            }
        },
        columns: [
            { data: null, searchable: false, className: 'ps-9',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1 +'.';
                }
            },
            { data: 'transaction_number', name: 'transaction_number', className: 'ps-0', },
            { data: 'items_name', name: 'items_name' },
            { data:'invoice_date', name:'invoice_date', 
                render: function (data, type, row) {
                    var year = data.substr(0,4);
                    var month= data.substr(5,2);
                    var day = data.substr(8,2);
                    return day+"/"+month+"/"+year;
                }
            },
            { data: 'items_qty', name: 'items_qty' },
            { data: 'items_price', name: 'items_price', 
                render: function(data, type, row) {
                    return 'Rp. '+number_format(data);
                }
            },
            { data: 'total', name: 'total', 
                render: function(data, type, row) {
                    return 'Rp. '+number_format(data);
                }
            },
            { data: 'status', name: 'status', 
                render: function (data, type, row) {
                    if (data == "Pending") {
                        text = "Pending";
                        color = "badge-light-warning";
                    } else if (data == "Process") {
                        text = "Process";
                        color = "badge-light-primary";
                    } else {
                        text = "Complete";
                        color = "badge-light-success";
                    }
                    return '<span class="badge py-3 px-4 fs-7 '+color+'">'+text+'</span>'
                }
            },
            { data: 'response_payper_url', name: 'response_payper_url', className: 'ps-0',
                render: function (data, type, row) {
                    if (row.status !== "Complete") {
                        return '<a href="#" onclick=getLink("'+data+'")>'+data+'</a>';
                    } else {
                        return '-';
                    }
                }
            },
        ],
        dom: 'frtip'
    });

    function getLink(link)
    {
        window.open('//' + link, '__blank');
    }

    function number_format (number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    $("#date_range").change(function() {
        var date_range = this.value;
        var split = date_range.split(" - ");
        var start_date = split[0];
        var end_date = split[1];

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ route('dash.get.graph') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                start_date: start_date,
                end_date: end_date,
            },
            success: function(data){
                $("#myChart").remove();
                $("#divChart").append('<canvas id="myChart"></canvas>');
                const ctx = document.getElementById('myChart').getContext("2d");

                new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    });
</script>
<!--end::Javascript-->
@endsection