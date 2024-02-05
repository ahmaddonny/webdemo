<!--begin::Heading-->
<div class="mb-13 text-center">
    <h1 class="mb-3">Top Up</h1>
</div>
<!--end::Heading-->
<!--begin::Content-->
<!--begin::Form-->
<form class="form" id="formpayment" method="POST" novalidate="novalidate">
    {{ csrf_field() }}
    <div class="current" data-kt-stepper-element="content">
        <div class="w-100">
            <!--begin::Input group-->
            <div class="fv-row mb-5">
                <!--begin::Label-->
                <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                    <span class="required">Quota Amount</span>
                </label>
                <!--end::Label-->
                <!--begin::Select2-->
                <select class="form-select mb-2" id="quota" name="quota" data-control="select2"
                    data-placeholder="Choose a Quota Amount" data-allow-clear="true">
                    <option value=""></option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                    <option value="250">250</option>
                    <option value="300">300</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>
                    <option value="1500">1500</option>
                    <option value="2000">2000</option>
                    <option value="2500">2500</option>
                    <option value="3000">3000</option>
                </select>
                <!--end::Select2-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="fv-row">
                <!--begin::Body-->
                <div class="card-body pt-0">
                    <!--begin::Summary-->
                    <div class="d-flex flex-stack bg-success rounded-3 p-6 mb-11">
                        <!--begin::Content-->
                        <div class="fs-6 fw-bold text-white">
                            <span class="d-block lh-1 mb-5">Price per Item</span>
                            <span class="d-block fs-2qx lh-1">Total</span>
                        </div>
                        <!--end::Content-->
                        <!--begin::Content-->
                        <div class="fs-6 fw-bold text-white text-end">
                            <span class="d-block lh-1 mb-5" data-kt-pos-element="total">Rp. 11.000</span>
                            <span class="d-block fs-2qx lh-1" data-kt-pos-element="grant-total" id="grant-total">Rp.
                                -</span>
                            <input type="hidden" class="form-control form-control-sm form-control-solid" id="calculated"
                                name="calculated" readonly />
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Summary-->
                </div>
                <!--end: Card Body-->
            </div>
            <!--end::Input group-->
        </div>
    </div>
</form>
<!--end::Form-->
<!--end::Content-->
<!--begin::Actions-->
<div class="d-flex flex-center flex-row-fluid pt-12">
    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="savefrm">
        <!--begin::Indicator label-->
        <span class="indicator-label">Continue Payment</span>
        <!--end::Indicator label-->
        <!--begin::Indicator progress-->
        <span class="indicator-progress">Please wait...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
        <!--end::Indicator progress-->
    </button>
</div>
<!--end::Actions-->

<script type="text/javascript">
    $(document).ready(function() {
        //dropdown select2
        $('#quota').select2({
            dropdownParent: $('#modal')
        });

        $('#quota').change(function() {
            if ($(this).val() == "") {
                $('#grant-total').text('Rp. -');
                $('#calculated').val('');
            } else {
                var selectedValue = parseInt($(this).val());
                var grantTotal = selectedValue * 11000;

                $('#grant-total').text('Rp. ' + grantTotal.toLocaleString());
                $('#calculated').val(grantTotal);
            }
        });

        const form = document.getElementById('formpayment');

        var validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    quota: {
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

        // Revalidate Select2 input. For more info, plase visit the official plugin site: https://select2.org/
        $(form.querySelector('[name="quota"]')).on('change', function () {
            // Revalidate the field when an option is chosen
            validator.revalidateField('quota');
        });

        $(document).on('click', '#savefrm', function(event) {
            event.preventDefault();
            if(event.handled != true) {
                event.handled = true;

                if (validator) {
                    validator.validate().then(function (status) {
                        if (status == 'Valid') {
                            var datafrm = $('#formpayment').serializeArray();

                            // setup button loading
                            loading(true);

                            $.ajax({
                                url : "{{ route('dash.save.topup') }}",
                                type: "POST",
                                data: datafrm,
                                dataType: "json",
                                success: function(event) {
                                    if (event.Error == false){
                                        Swal.fire({
                                            title: "Information",
                                            icon: "success",
                                            text: event.Pesan,
                                            confirmButtonText: "OK"
                                        }).then(function(){
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: "Error",
                                            icon: "error",
                                            text: event.Pesan,
                                            confirmButtonText: "OK"
                                        }).then(function(){
                                            loading(false);
                                        });
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown){
                                    if ('responseJSON' in jqXHR) {
                                        Swal.fire({
                                            title: "Error",
                                            icon: "error",
                                            text: textStatus+' : '+jqXHR.responseJSON.message,
                                            confirmButtonText: "OK"
                                        }).then(function(){
                                            loading(false);
                                        });
                                    } else {
                                        Swal.fire({
                                            title: "Error",
                                            icon: "error",
                                            text: textStatus+' : '+jqXHR.statusText,
                                            confirmButtonText: "OK"
                                        }).then(function(){
                                            loading(false);
                                        });
                                    }
                                }
                            })
                        }
                    })
                }
            }
        });
    });

    function loading(event) {
        const submitButton = document.getElementById('savefrm');

        if (event == true) {
            // Show loading indication
            submitButton.setAttribute('data-kt-indicator', 'on');

            // Disable button to avoid multiple click
            submitButton.disabled = true;
        } else {
            // Remove loading indication
            submitButton.removeAttribute('data-kt-indicator');

            // Enable button
            submitButton.disabled = false;
        }
    }
</script>