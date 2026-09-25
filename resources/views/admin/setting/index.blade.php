@extends('admin.layout')
@section('content')

<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="row layout-top-spacing">
            <div id="tableHover" class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>Data {{ __($title) }}</h4>
                            </div>                 
                        </div>
                    </div>
	
                    <div class="widget-content widget-content-area">
                        @include('admin.setting.create')
                    </div>
                        
            </div>
        </div>

    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var table;

    $(document).ready(function () {

        $('#myForm').submit(function (e) {
            e.preventDefault(); // Hindari pengiriman form secara default

            var id_setting = $('#id_setting').val();
            var address = $('#address').val();
            var phone = $('#phone').val();
            var email = $('#email').val();
            var application_name = $('#application_name').val();
            var short_application_name = $('#short_application_name').val();

            // Buat objek FormData untuk mengirim data form, termasuk file
            var formData = new FormData();
            formData.append('id', id_setting);
            formData.append('address', address);
            formData.append('phone', phone);
            formData.append('email', email);
            formData.append('application_name', application_name);
            formData.append('short_application_name', short_application_name);
            formData.append('_token', "{{ csrf_token() }}");

            var small_icon = document.getElementById('small_icon');
            if (small_icon.files.length > 0) {
                formData.append('small_icon', small_icon.files[0]);
            }

            var large_icon = document.getElementById('large_icon');
            if (large_icon.files.length > 0) {
                formData.append('large_icon', large_icon.files[0]);
            }

            var background_login = document.getElementById('background_login');
            if (background_login.files.length > 0) {
                formData.append('background_login', background_login.files[0]);
            }

            // Kirim permintaan validasi ke controller via Ajax
            var url = "{{ url('/setting/validate') }}";
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false, // Tidak mengatur contentType secara otomatis
                processData: false, // Tidak memproses data secara otomatis
                success: function (response) {
                    $('.fv-plugins-message-container').html(''); // Hapus pesan kesalahan
                    $('.is-invalid').removeClass('is-invalid'); // Hapus kelas is-invalid dari bidang-bidang yang divalidasi
                     update(id_setting);
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON.errors;

                    // Bersihkan semua pesan kesalahan sebelum menampilkan yang baru
                    $('.fv-plugins-message-container').html('');

                    // Tampilkan pesan kesalahan untuk setiap bidang jika ada
                    if (errors) {
                        $.each(errors, function (key, value) {
                            $('#' + key + '-error').html(value[0]);
                        });
                    }
                }
            });
        });


    });

    // Fungsi untuk menampilkan notifikasi toast dengan ikon centang
    function showSuccessToast(message) {
        Snackbar.show({
            text: message,
            showAction: false,
            actionTextColor: '#fff',
            backgroundColor: '#8dbf42',
            pos: 'top-right'
        });
    }
    
    // Update Data
    function update(id) {
        var formData = new FormData($('#myForm')[0]); // Buat objek FormData dari formulir
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('_method', "PUT");
        
        // Kirim data formulir ke server menggunakan AJAX
        var url = "{{ url('/setting/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "POST",
            data: formData,
            contentType: false, // Biarkan jQuery menentukan contentType secara otomatis
            processData: false, // Biarkan jQuery menangani proses data secara otomatis
            success: function (response) {
                showSuccessToast(response.message); // Tampilkan notifikasi toast untuk keberhasilan
                window.location.reload(true);
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }
    
</script>

       
@endsection