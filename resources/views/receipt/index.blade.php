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
            <h1 class="d-flex text-white fw-bold my-1 fs-3">Receipt Mail</h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center py-3 py-md-1">
            <!--begin::Button-->
            <button type="button" id="btnStamp" class="btn bg-body btn-active-color-success">
                <span class="indicator-label">
                    <i class="fa-solid fa-stamp me-2"></i>
                    <span>Stamping Files</span>
                </span>
            </button>
            &nbsp;
            <button type="button" id="btnSave" class="btn bg-body btn-active-color-primary">
                <span class="indicator-label">
                    <i class="fa-regular fa-paper-plane me-2"></i>
                    <span>Send Email</span>
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
                        id="receipt_table">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800">
                                <th class="min-w-30px"></th>
                                <th>Debtor Acct</th>
                                <th>Name</th>
                                <th>Email Addr</th>
                                <th>Doc No</th>
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
    var tblreceipt;
    tblreceipt = $('#receipt_table').DataTable({
        processing: true,
        serverSide: false,
        paging: false,
        ordering: false,
        ajax: "{{ route('table.receipt') }}",
        columns: [
            { data: null, className: 'select-checkbox', defaultContent: '', orderable: false },
            { data: 'debtor_acct', name: 'debtor_acct' },
            { data: 'debtor_name', name: 'debtor_name' },
            { data: 'email_addr', name: 'email_addr' },
            { data: 'doc_no', name: 'doc_no',
                render: function (data, type, row) {
                    if (row.doc_amt >= 5000000) {
                        color = "badge-light-success";
                    } else {
                        text = "Complete";
                        color = "badge-light-danger";
                    }
                    return '<span class="badge py-3 px-4 fs-7 '+color+'">'+data+'</span>'
                }
            },
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

    tblreceipt.on("click", "th.select-checkbox", function()
    {
        if ($("th.select-checkbox").hasClass("selected")) {
            tblreceipt.rows().deselect();
            $("th.select-checkbox").removeClass("selected");
        } else {
            var numRowsToSelect = 10;

            var rowsToSelect = [];
            for (var i = 0; i < numRowsToSelect; i++) {
                rowsToSelect.push(i);
            }

            tblreceipt.rows(rowsToSelect).select();
            $("th.select-checkbox").addClass("selected");
        }
    }).on("select deselect", function() {
        ("Some selection or deselection going on")
        if (tblreceipt.rows({
                selected: true
            }).count() !== tblreceipt.rows().count()) {
            $("th.select-checkbox").removeClass("selected");
        } else {
            $("th.select-checkbox").addClass("selected");
        }
    });

    tblreceipt.on("click", "td.select-checkbox", function()
    {
        var row = $(this).closest("tr");
        var dataTableRows = tblreceipt.rows({selected: true}).count();

        if (dataTableRows >= 10)
        {
            if (row.hasClass("selected")) {
                tblreceipt.rows(this).deselect();
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

    $('#receipt_table tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tblreceipt.row( tr );

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
        $.getJSON("{{ url('show-table-receipt-detail') }}" + "/" + d.doc_no, function (data) {
            if (data != null && data.length > 0) {
                $.each(data, function( key, val ) {
                    var year = val.doc_date.substr(0,4);
                    var month= val.doc_date.substr(5,2);
                    var day = val.doc_date.substr(8,2);
                    var doc_date = day+"/"+month+"/"+year;

                    var descs = val.descs.split("\n");

                    if (val.file_status == 'S') {
                        file_names_sign = '<a href="#" onclick=previewFile("'+val.file_names_sign+','+val.file_status+'") class="btn btn-icon-danger btn-text-danger" title="'+val.file_names_sign+'"><i class="ki-duotone ki-document fs-1"><span class="path1"></span><span class="path2"></span></i></a>';
                        file_status = '<span class="badge py-3 px-4 fs-7 badge-light-success">Success Stamp</span>';
                    } else if (val.file_status == 'A') {
                        file_names_sign = '<a href="#" onclick=previewFile("'+val.filenames+','+val.file_status+'") class="btn btn-icon-danger btn-text-danger" title="'+val.filenames+'"><i class="ki-duotone ki-document fs-1"><span class="path1"></span><span class="path2"></span></i></a>';
                        file_status = '<span class="badge py-3 px-4 fs-7 badge-light-info">Success Get Serial Number</span>&nbsp;'+
                        '<button type="button" id="btnRestamp'+val.filenames+'" class="btn btn-sm btn-round btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-bs-toggle="tooltip" data-bs-placement="top" title="Re-stamp File" onclick=restampFile("'+val.filenames+','+val.file_status+'")>'+
                            '<span class="indicator-label">'+
                                '<i class="fa-solid fa-rotate-left"></i>'+
                            '</span>'+
                            '<span class="indicator-progress">'+
                                '<span class="spinner-border spinner-border-sm align-middle"></span>'+
                            '</span>'
                        '</button>';
                    } else if (val.file_status == 'F') {
                        file_names_sign = '<a href="#" onclick=previewFile("'+val.filenames+','+val.file_status+'") class="btn btn-icon-danger btn-text-danger" title="'+val.filenames+'"><i class="ki-duotone ki-document fs-1"><span class="path1"></span><span class="path2"></span></i></a>';
                        file_status = '<span class="badge py-3 px-4 fs-7 badge-light-danger">Failed Stamp</span>&nbsp;'+
                        '<button type="button" id="btnRestamp'+val.filenames+'" class="btn btn-sm btn-round btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-bs-toggle="tooltip" data-bs-placement="top" title="Re-stamp File" onclick=restampFile("'+val.filenames+','+val.file_status+'")>'+
                            '<span class="indicator-label">'+
                                '<i class="fa-solid fa-rotate-left"></i>'+
                            '</span>'+
                            '<span class="indicator-progress">'+
                                '<span class="spinner-border spinner-border-sm align-middle"></span>'+
                            '</span>'
                        '</button>';
                    } else {
                        file_names_sign = '<a href="#" onclick=previewFile("'+val.filenames+','+val.file_status+'") class="btn btn-icon-danger btn-text-danger" title="'+val.filenames+'"><i class="ki-duotone ki-document fs-1"><span class="path1"></span><span class="path2"></span></i></a>';
                        file_status = '<span class="badge py-3 px-4 fs-7 badge-light-primary">This document has not stamped</span>';
                    }

                    $('#bodydetail'+d.doc_no).append(
                        '<tr>'+
                            '<td class="ps-9">'+descs[0]+'<br />'+descs[1]+'</td>'+
                            '<td>'+doc_date+'</td>'+
                            '<td class="text-end">'+number_format(val.doc_amt)+'</td>'+
                            '<td>'+file_names_sign+'</td>'+
                            '<td>'+file_status+'</td>'+
                        '</tr>'
                    )
                });
            } else {
                $('#bodydetail'+d.doc_no).append(
                    '<tr>'+
                        '<td colspan="5" class="fs-6 fw-bold text-center">No data available in table</td>'+
                    '</tr>'
                )
            }
        });

        var html =
            '<div class="card card-xxl-stretch mb-5 mb-xl-10">'+
                '<div class="table-responsive">'+
                    '<table id="tblreceiptdetail" class="table table-row-bordered align-middle gy-5">'+
                        '<thead>'+
                            '<tr class="fw-semibold fs-6 text-gray-800">'+
                                '<th class="min-w-90px ps-9">Descs</th>'+
                                '<th class="min-w-90px">Doc Date</th>'+
                                '<th class="min-w-100px text-end">Doc Amount</th>'+
                                '<th class="min-w-90px">File Names</th>'+
                                '<th class="min-w-90px">File Status</th>'+
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
        var dataTableRows = tblreceipt.rows({selected: true}).data().toArray();

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
                    url  : '{{ route("submit.receipt") }}',
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

    $(document).on('click', '#btnStamp', function(event)
    {
        var dataTableRows = tblreceipt.rows({selected: true}).data().toArray();
        var filteredData = dataTableRows.filter(function (row) {
            return row.file_status === null;
        });
        var filteredDataAmount = dataTableRows.filter(function (row) {
            return row.doc_amt > 5000000;
        });
        var filteredDataStamp = dataTableRows.filter(function (row) {
            return row.file_status === null && row.doc_amt > 5000000;
        });

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

        var html = 'This action will stamp the selected file. <br /><br /><b>Total Data Stamp : ' + filteredDataStamp.length + '</b>';

        Swal.fire({
            title: 'Confirm to stamp?',
            html: html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6576ff',
            cancelButtonColor: '#e85347',
            confirmButtonText: 'Yes, stamp it!'
        }).then(function(a) {
            if (a.isConfirmed == true)
            {
                if (filteredData.length == 0)
                {
                    Swal.fire({
                        title: "Information",
                        icon: "info",
                        text: "This document has been stamping.",
                        confirmButtonText: "OK"
                    });
                    return false;
                } else {
                    if (filteredDataAmount.length == 0)
                    {
                        Swal.fire({
                            title: "Information",
                            icon: "info",
                            text: "This document cannot be stamped, because the nominal is not in accordance with the provisions to be stamped.",
                            confirmButtonText: "OK"
                        });
                        return false;
                    } else {
                        // setup button loading
                        loading(true);

                        $.ajax({
                            url  : '{{ route("stamp.receipt") }}',
                            data : {
                                _token: "{{ csrf_token() }}",
                                models: filteredDataStamp
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
                    }
                }
            } else { }
        })
    });

    function previewFile(datas) {
        var split = datas.split(",");
        var filename = split[0];
        var status = split[1];
        var company_cd = 'CKFJWG';
        var file_path = '';

        if (status == 'A' || status == 'F' || status == "null") {
            file_path = "{{ env('ROOT_RECEIPT_FILE_PATH') }}";
        } else {
            file_path = "{{ env('ROOT_SIGNED_FILE_PATH') }}";
        }
        window.open(file_path + company_cd + '/' + filename, '__blank');
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
                    url  : '{{ route("delete.receipt") }}',
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