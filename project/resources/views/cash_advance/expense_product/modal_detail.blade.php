<div class="row">
    <div class="col-12">
		<div class="card-body">
			<div class="row">						
				<div class="col-md-6">
					<input type="hidden" name="id_grade_expense" id="id_grade_expense">
					<div class="row">
						<label class="col-sm-4 col-form-label">Job Grade</label>
						<div class="col-sm-8">
							<select name="job_grade" id="job_grade" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="job_gradeError">
								<strong></strong>
							</span>                                    
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Product</label>
						<div class="col-sm-8">
							<select name="id_product" id="id_product" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="id_productError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Region</label>
						<div class="col-sm-8">
							<select name="id_region" id="id_region" class="form-control form-control-sm select2" style="width: 100%;">
								<option></option>
							</select>
							<span class="invalid-feedback" role="alert" id="id_regionError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Branch</label>
						<div class="col-sm-8">
							<select name="id_branch" id="id_branch" class="form-control form-control-sm select2" disabled style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="id_branchError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Position</label>
						<div class="col-sm-8">
							<select name="id_position_routing" id="id_position_routing" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="id_position_routingError">
								<strong></strong>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Description</label>
						<div class="col-sm-8">
							<input type="text" name="description" id="description" class="form-control form-control-sm">
							<span class="invalid-feedback" role="alert" id="descriptionError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Min Price</label>
						<div class="col-sm-8">
							<input type="number" name="min_price" id="min_price" class="form-control form-control-sm">
							<span class="invalid-feedback" role="alert" id="min_priceError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Max Price</label>
						<div class="col-sm-8">
							<input type="number" name="max_price" id="max_price" class="form-control form-control-sm">
							<span class="invalid-feedback" role="alert" id="max_priceError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Notes</label>
						<div class="col-sm-8">
							<input type="text" name="notes" id="notes" class="form-control form-control-sm">
							<span class="invalid-feedback" role="alert" id="notesError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Status</label>
						<div class="col-sm-8">
							<select name="status" id="status" class="form-control form-control-sm select2" style="width: 100%;">
								
							</select>
							<span class="invalid-feedback" role="alert" id="id_branchError">
								<strong></strong>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	id_talent = {!! $cashExpense !!};
	
	$('#rating').select2().attr('readonly',true);
	
//	get_rating();	
	if(id_talent != 0){
		get_edit(id_talent);
	}
	else{
		get_grade();
		get_group_matrix();
		get_conclusion();
	}

	$.ajax({
		url: "{{ route('expense_product.initial') }}",
		method: "GET",
		success: function (response) {
			console.log(response)
		}
	})
	
});	
</script>