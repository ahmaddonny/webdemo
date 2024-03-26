<form method="POST" class="form" id="popupSendForm" novalidate="novalidate" autocomplete="off">
    <!--begin::Heading-->
    <div class="mb-13 text-center">
        <h1 class="mb-3">Choose Type for Send Offical Receipt</h1>
    </div>
    <!--end::Heading-->

    <div class="row g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']"
        data-kt-initialized="1">
        <!--begin::Col-->
        <div class="col fv-row">
            <!--begin::Option-->
            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6"
                data-kt-button="true">
                <!--begin::Radio-->
                <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                    <input class="form-check-input" type="checkbox" id="type_send" name="type_send[]" value="email"
                        checked="checked">
                </span>
                <!--end::Radio-->

                <!--begin::Info-->
                <span class="ms-5">
                    <span class="fs-4 fw-bold text-gray-800 d-block">Email (10/1000)</span>
                    <span class="fw-semibold fs-7 text-gray-600">
                        Pilihan cepat dan efisien untuk mengirimkan invoice langsung ke inbox email penerima.
                    </span>
                </span>
                <!--end::Info-->
            </label>
            <!--end::Option-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col fv-row">
            <!--begin::Option-->
            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6"
                data-kt-button="true">
                <!--begin::Radio-->
                <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                    <input class="form-check-input" type="checkbox" id="type_send" name="type_send[]" value="whatsapp">
                </span>
                <!--end::Radio-->

                <!--begin::Info-->
                <span class="ms-5">
                    <span class="fs-4 fw-bold text-gray-800 d-block">WhatsApp (20/1000)</span>
                    <span class="fw-semibold fs-7 text-gray-600">
                        Kirimkan invoice langsung ke WhatsApp penerima untuk pemberitahuan yang lebih personal dan
                        langsung.
                    </span>
                </span>
                <!--end::Info-->
            </label>
            <!--end::Option-->
        </div>
        <!--end::Col-->
        <!--begin::Input group-->
        <div class="fv-row row mt-10">
            <!--begin::Col-->
            <div class="col-lg-5 fv-row">
                &nbsp;
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-lg-7">
                <button type="button" class="btn btn-primary" id="savefrm">
                    <span class="indicator-label">
                        Send
                    </span>
                    <span class="indicator-progress">
                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
    </div>
</form>

<!--begin::Javascript-->
<script type="text/javascript">
    $(document).ready(function() {
        var dataTableRows = $('#modal').data('models');

        const form = document.getElementById('popupSendForm');

        var validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'type_send[]': {
                        validators: {
                            choice: {
                                min: 1,
                                max: 2,
                                message: 'Please choose 1 for send',
                            },
                        }
                    }
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        //simpan&edit data
        const submitButton = document.getElementById('savefrm');
        $('#savefrm').click(function(event) {
            event.preventDefault();
            if (event.handled !== true) {
                event.handled = true;

                if (validator) {
                    validator.validate().then(function (status) {
                        if (status == 'Valid') {
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
                                    // Show loading indication
                                    submitButton.setAttribute('data-kt-indicator', 'on');

                                    // Disable button to avoid multiple click
                                    submitButton.disabled = true;

                                    var datafrm = $('#popupSendForm').serializeArray();

                                    $.ajax({
                                        url  : '{{ route("submit.invoice.multi.platform") }}',
                                        data : {
                                            _token: "{{ csrf_token() }}",
                                            models: dataTableRows,
                                            datafrm: datafrm
                                        },
                                        type : 'POST',
                                        dataType: 'json',
                                        success: function(event, data)
                                        {
                                            if(event.Error == false)
                                            {
                                                Swal.fire({
                                                    title: "Information",
                                                    icon: "success",
                                                    text: event.Pesan,
                                                    confirmButtonText: "OK"
                                                }).then(function() {
                                                    // Remove loading indication
                                                    submitButton.removeAttribute('data-kt-indicator');

                                                    // Enable button
                                                    submitButton.disabled = false;

                                                    location.reload();
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: "Information",
                                                    icon: "error",
                                                    text: event.Pesan,
                                                    confirmButtonText: "OK"
                                                }).then(function() {
                                                    // Remove loading indication
                                                    submitButton.removeAttribute('data-kt-indicator');

                                                    // Enable button
                                                    submitButton.disabled = false;
                                                });
                                            }
                                        },
                                        error: function(jqXHR, textStatus, errorThrown)
                                        {
                                            if ('responseJSON' in jqXHR) {
                                                Swal.fire({
                                                    title: "Error",
                                                    icon: "error",
                                                    text: textStatus+' : '+jqXHR.responseJSON.message,
                                                    confirmButtonText: "OK"
                                                }).then(function() {
                                                    // Remove loading indication
                                                    submitButton.removeAttribute('data-kt-indicator');

                                                    // Enable button
                                                    submitButton.disabled = false;
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: "Error",
                                                    icon: "error",
                                                    text: textStatus+' : '+jqXHR.statusText,
                                                    confirmButtonText: "OK"
                                                }).then(function() {
                                                    // Remove loading indication
                                                    submitButton.removeAttribute('data-kt-indicator');

                                                    // Enable button
                                                    submitButton.disabled = false;
                                                });
                                            }
                                        }
                                    });
                                } else { }
                            })
                        }
                    })
                }
            }
        });
    });
</script>
<!--end::Javascript-->