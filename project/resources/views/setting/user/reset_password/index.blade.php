@extends('adminlte::page')
@section('title', 'Profil')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Profil</h5>
            </div>
       
			<div class="card-body">
					@if(session('message'))
						<div class="row mb-2">
							<div class="col-lg-12">
								<div class="alert alert-success" role="alert">{{ session('message') }}</div>
							</div>
						</div>
					@endif
					@if($errors->count() > 0)
						<div class="alert alert-danger">
							<ul class="list-unstyled">
								@foreach($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-2"> 
							 <div class="circle" style="font-size: 80px;">
							   <!-- User Profile Image -->
							   <img class="profile-pic" id="attach_photo"/>
							 </div>
						</div>
						<div class="col-md-5">
							<div class="row">
								<label class="col-sm-4 col-form-label">Name</label>
								<div class="col-sm-8">
									<input type="text" id="emp_name" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">ID Number/KTP</label>
								<div class="col-sm-8">
									<input type="text" id="ktp" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Mobile  Phone</label>
								<div class="col-sm-8">
									<input type="text" id="mobile_phone" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Private Mail</label>
								<div class="col-sm-8">
									<input type="text" id="private_mail" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Gender</label>
								<div class="col-sm-8">
									<input type="text" id="gender" class="form-control form-control-sm" readonly>
								</div>
							</div>
						</div>
						<div class="col-md-5" style="margin-bottom:20px;">							
							<div class="row">
								<label class="col-sm-4 col-form-label">Marital Status</label>
								<div class="col-sm-8">
									<input type="text" id="marital_status" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">PTKP Status</label>
								<div class="col-sm-8">
									<input type="text" id="ptkp_status" class="form-control form-control-sm" readonly>
								</div>
							</div>
							
							<div class="row">
								<label class="col-sm-4 col-form-label">Date Birth</label>
								<div class="col-sm-8">
									<input type="text" id="date_birth" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Place of Birth</label>
								<div class="col-sm-8">
									<input type="text" id="place_birth" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Religion</label>
								<div class="col-sm-8">
									<input type="text" id="religion" class="form-control form-control-sm" readonly>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="row">
								<label class="col-sm-3 col-form-label">KTP Address</label>
								<div class="col-sm-9">
									<input type="text" id="ktp_address" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-3 col-form-label">Bank Name</label>
								<div class="col-sm-9">
									<input type="text" id="bank_name" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-3 col-form-label">Bank Account</label>
								<div class="col-sm-9">
									<input type="text" id="bank_account" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-3 col-form-label">BPJS Kesehatan</label>
								<div class="col-sm-9">
									<input type="text" id="bpjs_kesehatan" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-3 col-form-label">BPJS Ketenagakerjaan</label>
								<div class="col-sm-9">
									<input type="text" id="bpjs_kerja" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-3 col-form-label">Non BPJS (Asuransi Swasta)</label>
								<div class="col-sm-9">
									<input type="text" id="non_bpjs" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-3 col-form-label">NPWP Number</label>
								<div class="col-sm-9">
									<input type="text" id="npwp_number" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-3 col-form-label">Work Time Zone</label>
								<div class="col-sm-9">
									<input type="text" id="work_time" class="form-control form-control-sm" readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr>
				<form id="userForm" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="row">						
						<div class="col-md-5">
							<div class="row">
                                <label class="col-sm-4 col-form-label">User Name</label>
								<div class="col-sm-8">
                                    <input type="text" name="user_name" id="user_name" class="form-control form-control-sm" disabled>
									<span class="invalid-feedback" role="alert" id="user_nameError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Email</label>
								<div class="col-sm-8">
                                    <input type="text" name="email" id="email" class="form-control form-control-sm" disabled>
									<span class="invalid-feedback" role="alert" id="emailError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Description Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description_name" id="description_name" class="form-control form-control-sm" disabled>
                                    <span class="invalid-feedback" role="alert" id="description_nameError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
						</div>
						
						<div class="col-md-7">
						<div class="row">
								<label class="col-sm-5 col-form-label" for="current_password">Current Password</label>
								<div class="col-sm-7">
								<input type="password" id="current_password" name="current_password" class="form-control form-control-sm">
								<span class="invalid-feedback" role="alert" id="current_passwordError">
                                        <strong></strong>
                                    </span>
								</div>
							
						</div>
						<div class="row">
							<label class="col-sm-5 col-form-label" for="new_password">New Password</label>
								<div class="col-sm-7">
								<input type="password" id="new_password" name="new_password" class="form-control form-control-sm">
								<span class="invalid-feedback" role="alert" id="new_passwordError">
                                        <strong></strong>
                                    </span>
								</div>
							
						</div>
						<div class="row">
							<label class="col-sm-5 col-form-label" for="new_password_confirmation">New Password Confirmation</label>
								<div class="col-sm-7">
								<input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control form-control-sm">
								<span class="invalid-feedback" role="alert" id="new_password_confirmationError">
                                        <strong></strong>
                                    </span>
								</div>
							
						</div>
						</div>
					</div>
                </div>
                <div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
               
                </div>
            </form>


        </div>
    </div>
</div>

 
@endsection
@section('css')
<style type="text/css">
.profile-pic {
   margin-bottom:30px;
}
.circle {
    border-radius: 10px !important;
    overflow: hidden;
    width: 142px;
    height: 160px;
    border: 4px solid #aaaaaa;
}
img {
    max-width: 100%;
    height: auto;
}
</style>
@stop
@section('scripts')
<script>

$(document).ready(function(){	 	
  $.ajax({
   url :"reset_password/edit",
   dataType:"json",
   success:function(data)
   {
    $('#user_name').val(data.result.user_name);
    $('#email').val(data.result.email);
    $('#description_name').val(data.result.description_name);
	document.getElementById("attach_photo").src = data.result.image_attachment;	
	$('#emp_name').val(data.result.name);
	$('#ktp').val(data.result.identification_number);
	$('#mobile_phone').val(data.result.mobile_phone);
	$('#private_mail').val(data.result.private_mail);
	if(data.result.gender == 'M'){
		$('#gender').val('Male');
	}
	else{
		$('#gender').val('Female');
	}
	$('#marital_status').val(data.result.marital);
	$('#ptkp_status').val(data.result.ptkp_status);
	$('#date_birth').val(data.result.birthdate);
	$('#place_birth').val(data.result.place_of_birth);
	$('#religion').val(data.result.religion);
	$('#ktp_address').val(data.result.idcard_address);
	$('#bank_name').val(data.result.bank_name);
	$('#bank_account').val(data.result.bank_account);
	$('#bpjs_kesehatan').val(data.result.bpjs);
	$('#bpjs_kerja').val(data.result.ketenagakerjaan);
	$('#non_bpjs').val(data.result.non_bpjs);
	$('#npwp_number').val(data.result.npwp_number);
	$('#work_time').val(data.result.time_zone);
	
   }
  })
});

 $(function () {
        $('#userForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#userForm input").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					url: "{{ route('user.change_password') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(function() {
								window.location = "{{ route('us.logout') }}";
							});
                        } 
                    },
					error: function (response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").children("strong").text(errors[key][0]);
                            });
                        }
						
                    }
                });
            
        });

    });

	
</script>	
@endsection