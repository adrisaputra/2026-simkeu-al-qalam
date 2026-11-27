@extends('admin.layout')
@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-12">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4>{{ $title }}</h4>
                            </div>
                        </div>
                    </div>
                    @include('admin.transaction_bank.edit')
                    @include('admin.transaction_bank.create')
                    <div class="widget-content widget-content-area">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if($errors->has('bill'))
                            <div class="alert alert-danger">{{ $errors->first('bill') }}</div>
                        @endif

                        <p style="font-weight:bold">{{ $transaction->desc }}</p>
                        <p style="font-size:24px">Rp. {{ \App\Helpers\Helpers::format_number($transaction->amount) }}</p>
            
                        <hr>
                        <div class="row" style="font-size: 16px;">
                            <div class="col-md-4 mb-2">
                                <small class="text-muted d-block">Transaksi</small>
                                <b>{{ \App\Helpers\Helpers::format_number($transaction->transaction_banks->count()) }}</b>
                            </div>
                            <div class="col-md-4 mb-2">
                                <small class="text-muted d-block">Cocok</small>
                                <b class="text-success">
                                    <b>{{ \App\Helpers\Helpers::format_number($transaction->transaction_banks->whereNotNull('student_id')->count()) }}</b>
                                </b>
                            </div>
                            <div class="col-md-4 mb-2">
                                <small class="text-muted d-block">Belum Cocok</small>
                                <b class="text-danger">
                                    <b>{{ \App\Helpers\Helpers::format_number($transaction->transaction_banks->whereNull('student_id')->count()) }}</b>
                                </b>
                            </div>
                        </div>
                        <hr>

                        <form method="GET" action="{{ route('transaction_bank.show', $transaction->id) }}">
                            <input type="hidden" name="academic_year_id" value="{{ request('academic_year_id') }}">
                            <div class="row justify-content-end">
                                    
                                <div class="col-md-9">
                                    <a href="#" class="btn mb-2 mr-1 btn-success" data-placement="top" data-toggle="modal" data-target="#exampleModal" title="Tambah Data" onClick="clearForm()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg></a>
									<a href="{{ url(Request::segment(1).'/'.Request::segment(2)) }}" class="btn mb-2 mr-1 btn-warning" data-toggle="tooltip" data-placement="top" title="Refresh"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg></a>
                                    <a href="{{ url(Request::segment(1)) }}" class="btn mb-2 mr-1 btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg></a>
                                </div>
                                
                                <div class="col-md-3 col-lg-3 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <select id="status" name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                        <option value="">Semua Status</option>
                                        <option value="unmatched" @selected(($filters['status'] ?? '') === 'unmatched')>Belum cocok</option>
                                        <option value="matched" @selected(($filters['status'] ?? '') === 'matched')>Cocok</option>
                                    </select>
                                </div>
                            </div>
                        </form>

                        @forelse($transaction_bank as $v)
                            <div class="card mb-3">
                                <div class="card-body"  style="color:#515365">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            No. Transaksi : <b>{{ $v->transaction_number }}</b>
                                            <div class="text-muted small">
                                                {{ \App\Helpers\Helpers::date_indo_full($v->date) }}
                                            </div>
                                            <br>
                                            @if($v->status == 'unmatched')
                                                Nama : {{ $v->student_name }}<br>
                                                Kelas : {{ $v->classes }}
                                            @elseif($v->status == 'matched')
                                                Nama : {{ $v->student->name }}<br>
                                                Jenjang : {{ $v->student->classes->work_unit->name ?? '-' }}<br>
                                                Kelas : {{ $v->student->classes->name ?? '-' }}
                                            @endif
                                        </div>
                                        
                                        @if($v->status == 'unmatched')
                                            <span class="badge badge-danger">Belum cocok</span>
                                        @elseif($v->status == 'matched')
                                            <span class="badge badge-success">Cocok</span>
                                        @endif
                                    </div>
                                    <hr>
                                    <div class="row" style="font-size: 16px;">
                                        <div class="col-md-9">
                                            <small class="text-muted d-block">{{ $transaction->transaction_code->name}}</small>
                                            <b>{{ \App\Helpers\Helpers::format_number($v->amount) }}</b>
                                        </div>
                                        <div class="col-md-3 d-flex justify-content-end align-items-center">
                                            @if($v->status == 'unmatched')
                                            <a href="#" onclick="getData({{ $v->id }})" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#editModal">Cocokkan Siswa</a>
						                    
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info mb-0">Tidak ada transaksi bank yang sesuai dengan filter.</div>
                        @endforelse

                        <div class="mt-3">{{ $transaction_bank->links() }}</div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('backend/assets/js/jquery-3.4.1.min.js')}}"></script>
<script>
      
    $(document).ready(function () {
        
        $('#student_id').select2({
            dropdownParent: $('#editModal'),
            width: '100%',
            placeholder: 'Ketik nama siswa...',
            minimumInputLength: 2,

            ajax: {
                url: "{{ url('/student/get') }}",
                dataType: 'json',
                delay: 300,

                data: function (params) {
                    return {
                        search: params.term
                    };
                },

                processResults: function (data) {
                    return {
                        results: data
                    };
                    
                }
            }
        });

        $('#myForm').submit(function (e) {
            e.preventDefault(); // Hindari pengiriman form secara default

            var action = document.getElementById('action').innerText;
            var id_transaction_bank = $('#id_transaction_bank').val();
            var student_id = $('#student_id').val();

            // Buat objek FormData untuk mengirim data form, termasuk file
            var formData = new FormData();
            formData.append('id', id_transaction_bank);
            formData.append('student_id', student_id);
            formData.append('_token', "{{ csrf_token() }}");

            // Kirim permintaan validasi ke controller via Ajax
            var url = "{{ url('/transaction_bank/validate') }}";
            $.ajax({
                url: url + "/" + action,
                type: "POST",
                data: formData,
                contentType: false, // Tidak mengatur contentType secara otomatis
                processData: false, // Tidak memproses data secara otomatis
                success: function (response) {
                   
                    $('.invalid-feedback').html(''); // Hapus pesan kesalahan
                    $('.is-invalid').removeClass('is-invalid'); // Hapus kelas is-invalid dari bidang-bidang yang divalidasi

                    if (action === "Simpan") {
                        send();
                    } else {
                        update(id_transaction_bank);
                    }

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

        $('#myForm2').submit(function (e) {
            e.preventDefault(); // Hindari pengiriman form secara default

            var action = document.getElementById('action').innerText;
            var id_transaction= $('#id_transaction').val();
            var student_id2 = $('#student_id2').val();

            // Buat objek FormData untuk mengirim data form, termasuk file
            var formData = new FormData();
            formData.append('id', id_transaction);
            formData.append('student_id2', student_id2);
            formData.append('_token', "{{ csrf_token() }}");

            // Kirim permintaan validasi ke controller via Ajax
            var url = "{{ url('/transaction_bank/validate') }}";
            $.ajax({
                url: url + "/" + action,
                type: "POST",
                data: formData,
                contentType: false, // Tidak mengatur contentType secara otomatis
                processData: false, // Tidak memproses data secara otomatis
                success: function (response) {
                   
                    $('.invalid-feedback').html(''); // Hapus pesan kesalahan
                    $('.is-invalid').removeClass('is-invalid'); // Hapus kelas is-invalid dari bidang-bidang yang divalidasi

                    if (action === "Simpan") {
                        send();
                    } else {
                        update(id_transaction);
                    }

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
    
    function clearForm(){
        document.getElementById("head_title").textContent = "Tambah {{ __($title) }}";
        $('#myForm2')[0].reset();
        document.getElementById("action").textContent = "Simpan";
    }

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

    function showFailedToast(message) {
        Snackbar.show({
            text: message,
            showAction: false,
            actionTextColor: '#fff',
            backgroundColor: '#e7515a',
            pos: 'top-right'
        });
    }
    
    // Create Data
    function send() {
        var formData = new FormData($('#myForm2')[0]); // Buat objek FormData dari formulir

        // Kirim data formulir ke server menggunakan AJAX
        $.ajax({
            url: "{{ url('transaction_bank/store') }}",
            type: "POST",
            data: formData,
            contentType: false, // Biarkan jQuery menentukan contentType secara otomatis
            processData: false, // Biarkan jQuery menangani proses data secara otomatis
            success: function (response) {
                showSuccessToast(response.message); // Tampilkan notifikasi toast
                $('#myForm')[0].reset(); // Reset form setelah berhasil menambahkan data
                $('#exampleModal').modal('hide');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }
        
    // Get Data
    function getData(id){
        document.getElementById("head_title").textContent = "Ubah {{ __($title) }}";
        document.getElementById("action").textContent = "Update";
        // Kirim data formulir ke server menggunakan AJAX

        var url = "{{ url('/transaction_bank') }}";
        $.ajax({
            url: url + "/" + id + "/edit",
            type: "GET",
            success: function (response) {
                document.getElementById("id_transaction_bank").value = response.data.id;
                            
                $("#editModal").modal('show');

                $('#student_id').select2('open');

                setTimeout(function () {
                    $('.select2-container--open .select2-search__field')
                        .val(response.data.student_name)
                        .trigger('input');
                }, 300);
                console.log(response.data.student_name);
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }

    // Update Data
    function update(id) {
        var formData = new FormData($('#myForm')[0]); // Buat objek FormData dari formulir
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('_method', "PUT");
        
        // Kirim data formulir ke server menggunakan AJAX

        var url = "{{ url('/transaction_bank') }}";
        $.ajax({
            url: url + "/" + id + "/edit",
            type: "POST",
            data: formData,
            contentType: false, // Biarkan jQuery menentukan contentType secara otomatis
            processData: false, // Biarkan jQuery menangani proses data secara otomatis
            success: function (response) {
                showSuccessToast(response.message); // Tampilkan notifikasi toast untuk keberhasilan
                $('#myForm')[0].reset(); // Reset form setelah berhasil memperbarui data
                $('#editModal').modal('hide'); // Tutup modal setelah berhasil memperbarui data
                location.reload();
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }
    
    function getClasses(selectedClassId = null) {
        var work_unit_id = $("#work_unit_id").val();

        $("#classes_id").html(
            '<option value="">- Pilih Kelas-</option>'
        );

        if (!work_unit_id) {
            return false;
        }

        var url = "{{ url('/classes/get') }}";

        $.ajax({
            url: url + "/" + work_unit_id,
            type: "GET",

            success: function (html) {
                $("#classes_id").html(html);

                // Pilih kelas setelah opsi selesai dimuat
                if (selectedClassId !== null) {
                    $("#classes_id").val(String(selectedClassId));
                }
            },

            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }

    function getStudent(selectedStudentId = null) {
        var classes_id = $("#classes_id").val();
        console.log("Selected Class ID:", classes_id); // Debug: Tampilkan nilai classes_id di konsol

        $("#student_id2").html(
            '<option value="">- Pilih Siswa-</option>'
        );

        if (!classes_id) {
            return false;
        }

        var url = "{{ url('/student/get2') }}";

        $.ajax({
            url: url + "/" + classes_id,
            type: "GET",

            success: function (html) {
                $("#student_id2").html(html);

                // Pilih siswa setelah opsi selesai dimuat
                if (selectedStudentId !== null) {
                    $("#student_id2").val(String(selectedStudentId));
                }
            },

            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }

</script>
@endsection
