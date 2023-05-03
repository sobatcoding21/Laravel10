@extends('customer.layout')

@section('content')
    <style>
        .img-wrapper {
            width: 140px;
            height:140px;
            background: url('{{ isset($data) && !is_null($data->avatar) ? url($data->avatar) : 'https://placehold.co/140x140?text=AVATAR' }}');
            background-repeat:no-repeat;
            background-size: cover;
            background-position: center;
        }
    </style>
    <div class="card">
        <div class="card-header">{{ isset($data) ? 'Edit' : 'Tambah' }} Pelanggan</div>
        <div class="card-body">
            <form id="formCustomer" method="POST" action="{{ isset($data) ? route('customers.update', ['customer' => $data->id]) : route('customers.store') }}">
                @csrf
                <div class="row">
                    <div class="col-auto">
                        <div id="image-avatar" class="img-wrapper" ></div>
                        <input type="file" id="pickPhoto" name="photo" accept="image/jpeg,image/png,image/gif"/>
                    </div>
                    <div class="col">
                        <div class="mb-2">
                            <label for="inputNama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="inputName" name="name" autocomplete="off" value="{{ $data->name ?? '' }}" required >
                        </div>
                        <div class="mb-2">
                            <label for="inputAddress" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="inputAddress" name="address" autocomplete="off" value="{{ $data->address ?? '' }}" required >
                        </div>
                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary">SIMPAN</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(e) {

            $(document).on('change', '#pickPhoto', function(e) {
                var file = $(this)[0].files[0];
                var exts = ["image/png", "image/jpg", "image/jpeg"];
                var max  = 1024 * 1024;
                
                //validasi image
                if( !exts.includes(file.type) )
                {
                    alert("File must be an image. JPG OR PNG");
                    return false;
                }

                //validasi image size
                if( file.size >  max)
                {
                    alert("File not large than 1 MB");
                    return false;
                }
                

                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        
                        var image = new Image();
                        image.src = e.target.result;

                        image.onload = function() {
                            
                            $("#image-avatar").css("background-image", "url(" + this.src + ")");
                            $("#image-avatar").css("background-position", "center");
                            $("#image-avatar").css("background-size", "cover"); 
                            $("#image-avatar").css("background-repeat", "no-repeat");
                            
                        };

                    }
                    reader.readAsDataURL(file);
                } else {
                    alert('select a file to see preview');
                    $('#pickPhoto').val('');
                    $("#image-avatar").css("background", "url('https://placehold.co/140x140?text=AVATAR')");
                }
            });
            
            $("#formCustomer").validate({
                rules : {
                    name : "required",
                    address: "required",
                },
                messages : {
                    name : {
                        required : 'Nama harus diisi'
                    },
                    address : {
                        required : 'Alamat harus diisi'
                    }
                },
                errorPlacement: function(label, element) {
                    label.addClass('mt-2 invalid-feedback');
                    label.insertAfter(element);
                    $(element).removeClass('is-invalid')
                },
                highlight: function(element, errorClass) {
                    $(element).addClass('is-invalid')
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                    $(element).addClass('is-valid');
                },
                submitHandler: function(form) {
                    
                    let formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('name', $('#inputName').val());
                        formData.append('address', $('#inputAddress').val());
                         
                        // Attach file
                        formData.append('photo', $('input[type=file]')[0].files[0]); 

                    $.ajax({
                        url : $(form).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            //function here ...
                            $('button').prop('disabled', true);
                        },
                        success: function(response) {
                            $('button').prop('disabled', false);
                            console.log(response);
                            if( response.success == true )
                            {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '{{ route('customers.index') }}';
                                    }
                                })
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            
                            $('button').prop('disabled', false);
                            console.log('Message: ' + textStatus + ' , HTTP: ' + errorThrown );
                        },
                    })

                    return false;
                }
            });
        })
    </script>
@endsection