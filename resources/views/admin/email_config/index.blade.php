@extends('template.base')
@section('body')
<!--begin::Toolbar-->
<div class="toolbar py-5 pb-lg-15" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column me-3">
            <!--begin::Title-->
            <h1 class="d-flex text-white fw-bold my-1 fs-3">Email Configuration</h1>
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
        <div class="card mb-5 mb-xl-10">
            <!--begin::Card body-->
            <div class="card-body p-9">
                <!--begin::Form-->
                <form method="POST" class="form" id="emailForm" novalidate="novalidate" autocomplete="off">
                    {{ csrf_field() }}
                    <!--begin::Input group-->
                    <div class="fv-row row mb-15">
                        <!--begin::Col-->
                        <div class="col-lg-5">
                            <div class="input-group">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6">Mail Driver</label>
                                <!--end::Label-->
                            </div>
                            <!--begin::Hint-->
                            <div class="form-text">Specify the email sending driver to
                                be used (e.g., smtp, mail, sendmail, etc.).</div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <input type="text" class="form-control form-control-lg form-control-solid" id="driver"
                                name="driver" maxlength="20">
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row row mb-15">
                        <!--begin::Col-->
                        <div class="col-lg-5 fv-row">
                            <div class="input-group">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6">Mail Host</label>
                                <!--end::Label-->
                            </div>
                            <!--begin::Hint-->
                            <div class="form-text">Specify the host server address for sending emails.</div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <input type="text" class="form-control form-control-lg form-control-solid" id="host"
                                name="host" maxlength="20">
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row row mb-15">
                        <!--begin::Col-->
                        <div class="col-lg-5 fv-row">
                            <div class="input-group">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6">Mail Port</label>
                                <!--end::Label-->
                            </div>
                            <!--begin::Hint-->
                            <div class="form-text">Specify the port number for email server communication (e.g., 25,
                                587, 465).
                            </div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <input type="text" class="form-control form-control-lg form-control-solid" id="port"
                                name="port" maxlength="10">
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row row mb-15">
                        <!--begin::Col-->
                        <div class="col-lg-5 fv-row">
                            <div class="input-group">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6">Mail Username</label>
                                <!--end::Label-->
                            </div>
                            <!--begin::Hint-->
                            <div class="form-text">Enter the username for authenticating with the email server.</div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <input type="text" class="form-control form-control-lg form-control-solid" id="username"
                                name="username">
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row row mb-15">
                        <!--begin::Col-->
                        <div class="col-lg-5 fv-row">
                            <div class="input-group">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6">Mail Password</label>
                                <!--end::Label-->
                            </div>
                            <!--begin::Hint-->
                            <div class="form-text">Enter the password for authenticating with the email server.</div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <input type="password" class="form-control form-control-lg form-control-solid" id="password"
                                name="password">
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row row mb-15">
                        <!--begin::Col-->
                        <div class="col-lg-5 fv-row">
                            <div class="input-group">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6">Mail Encryption</label>
                                <!--end::Label-->
                            </div>
                            <!--begin::Hint-->
                            <div class="form-text">Specify the encryption type for secure email communication (e.g.,
                                tls, ssl).
                            </div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <input type="text" class="form-control form-control-lg form-control-solid" id="encryption"
                                name="encryption">
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row row mb-15">
                        <!--begin::Col-->
                        <div class="col-lg-5 fv-row">
                            <div class="input-group">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6">Mail From Name</label>
                                <!--end::Label-->
                            </div>
                            <!--begin::Hint-->
                            <div class="form-text">Enter the name that will appear as the sender's name when sending
                                emails.
                            </div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <input type="text" class="form-control form-control-lg form-control-solid" id="sender_name"
                                name="sender_name">
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row row mb-15">
                        <!--begin::Col-->
                        <div class="col-lg-5 fv-row">
                            <div class="input-group">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6">Mail From Address</label>
                                <!--end::Label-->
                            </div>
                            <!--begin::Hint-->
                            <div class="form-text">Enter the name that will appear as the sender's name when sending
                                emails.
                            </div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <input type="text" class="form-control form-control-lg form-control-solid" id="sender_email"
                                name="sender_email">
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="fv-row row mb-15">
                        <!--begin::Col-->
                        <div class="col-lg-5 fv-row">
                            &nbsp;
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <button type="button" class="btn btn-primary" id="savefrm">
                                <span class="indicator-label">
                                    Save Changes
                                </span>
                                <span class="indicator-progress">
                                    Please wait... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Card body-->
        </div>
    </div>
    <!--end::Post-->
</div>
<!--end::Container-->

<!--begin::Javascript-->
<script type="text/javascript">
    $(document).ready(function() {
        //menampilkan data
        loaddata();

        const form = document.getElementById('emailForm');

        var validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    driver: {
                        validators: {
                            notEmpty: {
                                message: 'This field is required.'
                            }
                        }
                    },
                    host: {
                        validators: {
                            notEmpty: {
                                message: 'This field is required.'
                            }
                        }
                    },
                    port: {
                        validators: {
                            notEmpty: {
                                message: 'This field is required.'
                            }
                        }
                    },
                    username: {
                        validators: {
                            notEmpty: {
                                message: 'This field is required.'
                            }
                        }
                    },
                    password: {
                        validators: {
                            notEmpty: {
                                message: 'This field is required.'
                            }
                        }
                    },
                    encryption: {
                        validators: {
                            notEmpty: {
                                message: 'This field is required.'
                            }
                        }
                    },
                    sender_name: {
                        validators: {
                            notEmpty: {
                                message: 'This field is required.'
                            }
                        }
                    },
                    sender_email: {
                        validators: {
                            notEmpty: {
                                message: 'This field is required.'
                            }
                        }
                    },

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
                            // Show loading indication
                            submitButton.setAttribute('data-kt-indicator', 'on');

                            // Disable button to avoid multiple click
                            submitButton.disabled = true;

                            var datafrm = $('#emailForm').serializeArray();
                            
                            datafrm.push(
                                { name: "_token", value: "{{ csrf_token() }}" }
                            );

                            $.ajax({
                                url : "{{ url('submit-config') }}",
                                type: "POST",
                                data: datafrm,
                                dataType: "json",
                                success: function(event, data) {
                                    if (event.Error == false)
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
                        }
                    })
                }
            }
        });
    });

    function loaddata() {
        $.getJSON("{{ url('show-config') }}", function(data)
        {
            if (data.data_email !== null) {
                $('#driver').val(data.data_email.driver);
                $('#host').val(data.data_email.host);
                $('#port').val(data.data_email.port);
                $('#username').val(data.data_email.username);
                $('#password').val(data.decryptPass);
                $('#encryption').val(data.data_email.encryption);
                $('#sender_name').val(data.data_email.sender_name);
                $('#sender_email').val(data.data_email.sender_email);
                $('#bcc_email').val(data.data_email.bcc_email);
            }
        });
    }
</script>
<!--end::Javascript-->
@endsection