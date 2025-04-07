@extends('adminlte::page')
@section('title', 'Organization Hierarchy')

@section('content')

 <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
           <div class="card card-danger card-outline">
              <div class="card-header">
                <h5 class="m-0">Organization Hierarchy</h5>
              </div>
			   <div class="card-body" style="margin-bottom:-20px;">
			    <form method="post" id="orgForm">
					{{ csrf_field() }}
					<div class="col-md-12">
						   <div class="row">	
								<div class="col-md-10">
									<select name="fill" id="key-word" class="form-control form-control-md select2" style="width:100%;">
									</select>							
								</div>
							  <div class="col-md-2">
								<button type="submit" class="btn btn-md btn-info" id="btn-filter-node"><i class="fa fa-filter"></i> Filter</button>
								<button type="button" class="btn btn-md btn-secondary" id="btn-cancel">Reset</button>
							  </div>
						  </div>
					  </div>
				</form>
			  </div>
              <div class="defchart" id="defchart"></div>
              <div class="quechart"><i id="load_sp" class="fas fa-cog fa-spin sp" style="font-size:80px; color: rgba(217, 83, 79, 0.8);"></i><div id="quechart"></div></div>
            </div>
           
          </div>
         
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

@endsection
@section('css')
 <link rel="stylesheet" href="{{ asset('vendor/orgchart/css/jquery.orgchart.css') }}">
<style type="text/css">	
.defchart {
  text-align:center;
  overflow:auto;
  margin:20px;
  border:1px solid #dc3545;
}
.quechart {
  text-align:center;
  overflow:auto;
  margin:20px;
  border:1px solid #dc3545;
}
.select2-selection__rendered {
    line-height: 32px !important;
}
.select2-container .select2-selection--single {
    height: 35px !important;
}
.select2-selection__arrow {
    height: 35px !important;
}
</style>
@stop
@section('scripts')
	<script type="text/javascript" src="https://dabeng.github.io/OrgChart/js/jquery.mockjax.min.js"></script>
    <script src="{{ asset('vendor/orgchart/js/jquery.orgchart.js') }}"></script>
    <script src="{{ asset('vendor/orgchart/js/html2canvas.min.js') }}"></script>
	
<script type="text/javascript">
function get_position_filter(){
		$.getJSON('<?= url('organization/organization_structure/organization_hierarchy/get_position_filter') ?>', function (data) {
			var res = Object.values(data);
            $('#key-word').prepend('<option selected></option>').select2({
                placeholder: "Select Position Detail",
                allowClear: true,
                data: res
            });
			
        }).fail(function (data) { // Call failed
            get_position_filter();
		});  
	}
$(document).ready(function(){
	   get_position_filter();	   
	   let avatar = "{{ asset('public/global/img/avatar.jpg') }}";
	   	var datasource = {!! json_encode($so) !!}
//console.log(datasource);
	$.mockjax({
        url: '/orgchart/initdata1',
        responseText: datasource
      });
	$('.quechart').css('display', 'none');
    var oc = $('#defchart').orgchart({
	  'visibleLevel': 2,
      'data' : '/orgchart/initdata1',
      'nodeContent': 'title',
	  'nodeID': 'id',
	  'zoom': true,
	  'zoominLimit': 2,
      'zoomoutLimit': 0,
	  'exportButton': true,
      'exportFileextension': 'png',
      'exportFilename': 'HRIS STRUKTUR ORGANIZATION',
	 'createNode': function($node, data) {
			 let img_profile = data.image_attachment;
			 img_profile = img_profile.replaceAll(" ", "%20");
			var secondMenuIcon = $('<i></i>', {
			  'class': 'oci oci-info-circle second-menu-icon',
			  click: function() {
				$(this).siblings('.second-menu').toggle();
			  }
			});
			var secondMenu = '<div class="second-menu"><img class="avatar" src='+img_profile+'></div>';
			$node.append(secondMenuIcon).append(secondMenu);
		  }	
    });
	   
	 $('#orgForm').submit(function (e) {
            e.preventDefault();
			let formData = $(this).serializeArray();
		$.ajax({
			 type: 'POST',
			headers: {
				Accept: "application/json",
			},
			url:"{{ route('hierarchy.queryChart') }}",
			data:formData,
			beforeSend: function () {
				$('#load_sp').show();
				$('#quechart').html('');
				$('#defchart').html('');			
			},
			success:function(data){
				$('.defchart').css('display', 'none');
				$('.quechart').css('display', 'inline');
				$('#load_sp').hide();
				
				if(data != 'null'){
				var nodeTemplate = function(data) {
					var con = "";
					if(data.secondary_position != false){
						con += '<i style="color:red;">Concurent</i>';
						con += '<div class="title" style="background-color:red;">'+data.name+'</div>';
						if(data.title != null){
							con += '<div class="content">'+data.title+'</div>';
						}
						else{
							con += '<div class="content"></div>';
						}
						
					}
					else{
						con += '<div class="title">'+data.name+'</div>';
						if(data.title != null){
							con += '<div class="content">'+data.title+'</div>';
						}
						else{
							con += '<div class="content"></div>';
						}
					}
					
				  return con;
				};	
				$('#quechart').orgchart({
					  'data' : data,
					  'nodeContent': 'title',
					  'nodeTemplate': nodeTemplate,
					  'nodeID': 'id',
					  'zoom': true,
					  'zoominLimit': 2,
					  'zoomoutLimit': 0,
					  'exportButton': true,
					  'exportFileextension': 'png',
					  'exportFilename': 'HRIS STRUKTUR HIERARCHY',
					 'createNode': function($node, data) {
						 let img_profile = data.image_attachment;
						 img_profile = img_profile.replaceAll(" ", "%20");

						var secondMenuIcon = $('<i></i>', {
						  'class': 'oci oci-info-circle second-menu-icon',
						  click: function() {
							$(this).siblings('.second-menu').toggle();
						  }
						});
						if(data.secondary_position == false){
							var secondMenu = '<div class="second-menu"><img class="avatar" src='+img_profile+'></div>';
						}
						else{
							var secondMenu = '<div class="second-menu"><img class="avatar" style="border:2px solid red;" src='+img_profile+'></div>';	
						}
						
						$node.append(secondMenuIcon).append(secondMenu);
					  }				 
					});
					var nodeTemplate = function(data) {
					  return `<div class="title" style="background:green;">${data.name}</div>`;
					};
				}
				else{
					$('.defchart').css('display', 'inline');
					$('.quechart').css('display', 'none');
					oc.init({ 'data': '/orgchart/initdata1' });
				}
			 },
		});
	 
    });
	
	$('#btn-cancel').on('click', function() {
		$('#key-word').empty();	
		$('.quechart').css('display', 'none');
		$('.defchart').css('display', 'inline');
		get_position_filter();
		oc.init({ 'data': '/orgchart/initdata1' });
    });
  });
   
   
  </script>
 
@endsection