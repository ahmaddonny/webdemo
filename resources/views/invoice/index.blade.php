@extends('template.base')
@section('body')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.1/css/select.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>

<style type="text/css">
    table.dataTable tr th.select-checkbox.selected::after {
        content: "✔";
        margin-top: -11px;
        margin-left: -6px;
        text-align: center;
        text-shadow: rgb(255, 255, 255) 1px 1px, rgb(255, 255, 255) -1px -1px, rgb(255, 255, 255) 1px -1px, rgb(255, 255, 255) -1px 1px;
    }
</style>

<!--begin::Toolbar-->
<div class="toolbar py-5 pb-lg-15" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column me-3">
            <!--begin::Title-->
            <h1 class="d-flex text-white fw-bold my-1 fs-3">Invoice List</h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center py-3 py-md-1">
            <!--begin::Button-->
            {{-- <button type="button" id="btnSendWA" class="btn bg-body btn-active-color-success">
                <span class="indicator-label">
                    <i class="fa-brands fa-whatsapp me-2"></i>
                    <span>Send WhatsApp</span>
                </span>
            </button>
            &nbsp;
            <button type="button" id="btnSave" class="btn bg-body btn-active-color-primary">
                <span class="indicator-label">
                    <i class="fa-regular fa-paper-plane me-2"></i>
                    <span>Send Email</span>
                </span>
            </button>
            &nbsp; --}}
            <button type="button" id="btnSendInvoice" class="btn bg-body btn-active-color-primary">
                <span class="indicator-label">
                    <i class="fa-regular fa-paper-plane me-2"></i>
                    <span>Send Invoice</span>
                </span>
            </button>
            <!--end::Button-->
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table table-rounded table-striped table-row-bordered gy-4 align-middle fw-bold"
                        id="invoice_table">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800">
                                <th class="min-w-30px"></th>
                                <th>Debtor Acct</th>
                                <th>Lot No</th>
                                <th>Name</th>
                                <th>Doc No</th>
                                <th>Telphone No</th>
                                <th>Email Addr</th>
                                <th>Delete</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">

                        </tbody>
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Post-->
</div>
<!--end::Container-->

<!--begin::Javascript-->
<script type="text/javascript">
    var tblinvoice;
    tblinvoice = $('#invoice_table').DataTable({
        processing: true,
        serverSide: false,
        paging: false,
        ordering: false,
        ajax: "{{ route('table.invoice') }}",
        columns: [
            { data: null, className: 'select-checkbox', defaultContent: '', orderable: false },
            { data: 'debtor_acct', name: 'debtor_acct' },
            { data: 'lot_no', name: 'lot_no' },
            { data: 'debtor_name', name: 'debtor_name' },
            { data: 'doc_no', name: 'doc_no' },
            { data: 'email_addr', name: 'email_addr',
                render: function(data, type, row) {
                    return '08112777873';
                }
            },
            { data: 'email_addr', name: 'email_addr' },
            { data: 'doc_no', name: 'doc_no', className: 'text-center',
                render: function(data, type, row) {
                    var html = 
                        '<button type="button" id="btnDelete'+data+'" class="btn btn-icon-dark btn-text-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" onclick=deleteFile("'+data+'")>'+
                            '<span class="indicator-label">'+
                                '<i class="ki-duotone ki-trash-square fs-1">'+
                                    '<span class="path1"></span>'+
                                    '<span class="path2"></span>'+
                                    '<span class="path3"></span>'+
                                    '<span class="path4"></span>'+
                                '</i>'+
                            '</span>'+
                            '<span class="indicator-progress">'+
                                '<span class="spinner-border spinner-border-sm align-middle"></span>'+
                            '</span>'+
                        '</button>';
                    return html;
                }
            },
            { data: null, className: 'details-control text-end', 
                defaultContent: '<button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row">'+
                    '<i class="fa-solid fa-caret-down"></i>'+
                '</button>', 
                orderable: false,
            }
        ],
        select: {
            style: 'multi',
            selector: 'td:first-child'
        },
        dom: 'frtip'
    });

    tblinvoice.on("click", "th.select-checkbox", function()
    {
        if ($("th.select-checkbox").hasClass("selected")) {
            tblinvoice.rows().deselect();
            $("th.select-checkbox").removeClass("selected");
        } else {
            var numRowsToSelect = 10;

            var rowsToSelect = [];
            for (var i = 0; i < numRowsToSelect; i++) {
                rowsToSelect.push(i);
            }

            tblinvoice.rows(rowsToSelect).select();
            $("th.select-checkbox").addClass("selected");
        }
    }).on("select deselect", function() {
        ("Some selection or deselection going on")
        if (tblinvoice.rows({
                selected: true
            }).count() !== tblinvoice.rows().count()) {
            $("th.select-checkbox").removeClass("selected");
        } else {
            $("th.select-checkbox").addClass("selected");
        }
    });

    tblinvoice.on("click", "td.select-checkbox", function()
    {
        var row = $(this).closest("tr");
        var dataTableRows = tblinvoice.rows({selected: true}).count();

        if (dataTableRows >= 10)
        {
            if (row.hasClass("selected")) {
                tblinvoice.rows(this).deselect();
            } else {
                Swal.fire({
                    title: "Error",
                    icon: "error",
                    text: "Sorry, only 10 rows are allowed for a single process.",
                    confirmButtonText: "OK"
                });
            }
            return false;
        }
    });

    $('#invoice_table tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tblinvoice.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            var data = row.data();
            row.child( detail(data) ).show();
            tr.addClass('shown');
        }
    });

    function detail ( d ) {
        //TABLE AR EMAIL DETAIL
        $.getJSON("{{ url('show-table-invoice-detail') }}" + "/" + d.doc_no, function (data) {
            if (data != null && data.length > 0) {
                $.each(data, function( key, val ) {
                    var year = val.doc_date.substr(0,4);
                    var month= val.doc_date.substr(5,2);
                    var day = val.doc_date.substr(8,2);
                    var doc_date = day+"/"+month+"/"+year;

                    $('#bodydetail'+d.doc_no).append(
                        '<tr>'+
                            '<td class="ps-9">'+val.descs+'</td>'+
                            '<td>'+doc_date+'</td>'+
                            '<td class="text-end">'+number_format(val.doc_amt)+'</td>'+
                            '<td class="text-end">'+'<a href="#" onclick=previewFile("'+val.filenames+'") class="btn btn-icon-danger btn-text-danger" title="'+val.filenames+'"><i class="ki-duotone ki-document fs-1"><span class="path1"></span><span class="path2"></span></i></a></td>'+
                        '</tr>'
                    )
                });
            } else {
                $('#bodydetail'+d.doc_no).append(
                    '<tr>'+
                        '<td colspan="4" class="fs-6 fw-bold text-center">No data available in table</td>'+
                    '</tr>'
                )
            }
        });

        var html =
            '<div class="card card-xxl-stretch mb-5 mb-xl-10">'+
                '<div class="table-responsive">'+
                    '<table id="tblinvoicedetail" class="table table-row-bordered align-middle gy-5">'+
                        '<thead>'+
                            '<tr class="fw-semibold fs-6 text-gray-800">'+
                                '<th class="min-w-90px ps-9">Descs</th>'+
                                '<th class="min-w-90px">Doc Date</th>'+
                                '<th class="min-w-100px text-end">Doc Amount</th>'+
                                '<th class="min-w-90px text-end">Invoice</th>'+
                            '</tr>'+
                        '</thead>'+
                        '<tbody class="fs-8" id="bodydetail'+d.doc_no+'">'+

                        '</tbody>'+
                    '</table>'+
                '</div>'+
            '</div>';
        return html;
    }

    function loading(event) {
        const loadingDiv = document.getElementById('kt_body');

        if (event == true) {
            // Show loading indication
            loadingDiv.setAttribute('data-kt-app-page-loading', 'on');
        
            loadingDiv.setAttribute('data-kt-app-page-loading-enabled', 'true');
        } else {
            // Remove loading indication
            loadingDiv.removeAttribute('data-kt-app-page-loading');

            loadingDiv.removeAttribute('data-kt-app-page-loading-enabled');
        }
    }

    $(document).on('click', '#btnSave', function(event)
    {
        var dataTableRows = tblinvoice.rows({selected: true}).data().toArray();

        if (dataTableRows.length == 0)
        {
            Swal.fire({
                title: "Error",
                icon: "error",
                text: "Please select at least one or select all of them.",
                confirmButtonText: "OK"
            });
            return false;
        }

        Swal.fire({
            title: 'Are you sure you want to send the selected data?',
            html: '<b>Total Data Send : ' + dataTableRows.length + '</b>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6576ff',
            cancelButtonColor: '#e85347',
            confirmButtonText: 'Yes, send it!'
        }).then(function(a) {
            if (a.isConfirmed == true)
            {
                loading(true);

                $.ajax({
                    url  : '{{ route("submit.invoice") }}',
                    data : {
                        _token: "{{ csrf_token() }}",
                        models: dataTableRows,
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(event, data)
                    {
                        if(event.Error == false)
                        {
                            loading(false);
                            Swal.fire({
                                title: "Information",
                                icon: "success",
                                text: event.Pesan,
                                confirmButtonText: "OK"
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            loading(false);
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: event.Pesan,
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        loading(false);
                        if ('responseJSON' in jqXHR) {
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: textStatus+' : '+jqXHR.responseJSON.message,
                                confirmButtonText: "OK"
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: textStatus+' : '+jqXHR.statusText,
                                confirmButtonText: "OK"
                            });
                        }
                    }
                });
            } else { }
        })
    });

    $(document).on('click', '#btnSendWA', function(event)
    {
        var dataTableRows = tblinvoice.rows({selected: true}).data().toArray();

        if (dataTableRows.length == 0)
        {
            Swal.fire({
                title: "Error",
                icon: "error",
                text: "Please select at least one or select all of them.",
                confirmButtonText: "OK"
            });
            return false;
        }

        Swal.fire({
            title: 'Are you sure you want to send the selected data?',
            html: '<b>Total Data Send : ' + dataTableRows.length + '</b>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6576ff',
            cancelButtonColor: '#e85347',
            confirmButtonText: 'Yes, send it!'
        }).then(function(a) {
            if (a.isConfirmed == true)
            {
                loading(true);

                $.ajax({
                    url  : '{{ route("submit.wa.invoice") }}',
                    data : {
                        _token: "{{ csrf_token() }}",
                        models: dataTableRows,
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(event, data)
                    {
                        if(event.Error == false)
                        {
                            loading(false);
                            Swal.fire({
                                title: "Information",
                                icon: "success",
                                text: event.Pesan,
                                confirmButtonText: "OK"
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            loading(false);
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: event.Pesan,
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        loading(false);
                        if ('responseJSON' in jqXHR) {
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: textStatus+' : '+jqXHR.responseJSON.message,
                                confirmButtonText: "OK"
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: textStatus+' : '+jqXHR.statusText,
                                confirmButtonText: "OK"
                            });
                        }
                    }
                });
            } else { }
        })
    });

    $(document).on('click', '#btnSendInvoice', function(event)
    {
        var dataTableRows = tblinvoice.rows({selected: true}).data().toArray();

        if (dataTableRows.length == 0)
        {
            Swal.fire({
                title: "Error",
                icon: "error",
                text: "Please select at least one or select all of them.",
                confirmButtonText: "OK"
            });
            return false;
        }

        $('#modaldialog').removeClass('modal-sm');
        $('#modaldialog').addClass('modal-md');
        $('#modaltitle').html('Send Invoice');
        $('#modalbody').load("{{ route('index.popup-send-invoice') }}");
        $('#modal').modal({
            'backdrop': 'static',
            'keyboard': false
        });
        $('#modal').data('models', dataTableRows);
        $('#modal').modal('show');
    });

    function previewFile(filename) {
        var file_path = "{{ env('ROOT_INVOICE_FILE_PATH') }}";
        window.open(file_path + 'invoice/' + filename, '__blank');
    }

    function deleteFile(doc_no) {
        Swal.fire({
            title: 'Are you sure you want to delete this selected data?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6576ff',
            cancelButtonColor: '#e85347',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(a) {
            if (a.isConfirmed == true)
            {
                loading(true);

                $.ajax({
                    url  : '{{ route("delete.invoice") }}',
                    data : {
                        _token: "{{ csrf_token() }}",
                        doc_no: doc_no
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(event, data)
                    {
                        if(event.Error == false)
                        {
                            loading(false);
                            Swal.fire({
                                title: "Information",
                                icon: "success",
                                text: event.Pesan,
                                confirmButtonText: "OK"
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            loading(false);
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: event.Pesan,
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        loading(false);
                        if ('responseJSON' in jqXHR) {
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: textStatus+' : '+jqXHR.responseJSON.message,
                                confirmButtonText: "OK"
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                icon: "error",
                                text: textStatus+' : '+jqXHR.statusText,
                                confirmButtonText: "OK"
                            });
                        }
                    }
                });
            } else { }
        })
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
</script>
<!--end::Javascript-->
@endsection