<style>
    .select2-container--open {
        z-index: 1065;
    }
</style>
<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title-delete">Generate Psychogram</h4>
            <button type="button" class="close close-psychogram-modal" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="row">
                <input type="hidden" id="id_user_assessment">
                <input type="hidden" id="id_batch">
                <input type="hidden" id="user_type">
                <div class="col-md-4">
                    <div class="form-group" >
                        <label class="col-md-12 col-form-label">Job Grade :</label>
                        <div class="col-md-12">
                            <select id="psychomodal_grade" class="form-control form-control-sm select2 " style="width: 100%;">
                            </select>
                            <span class="invalid-feedback" role="alert" id="gradeError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group" >
                        <label class="col-md-12 col-form-label">Department :</label>
                        <div class="col-md-12">
                            <select id="psychomodal_department" class="form-control form-control-sm select2 " style="width: 100%;">
                            </select>
                            <span class="invalid-feedback" role="alert" id="departmentError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="col-md-12 col-form-label">&nbsp;</label>
                            <button id="button_generate_psychogram" onclick="getPsychogramResult()" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Generate</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 result_psychogram"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-danger close-psychogram-modal" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
get_psychomodal_grade();
get_psychomodal_department();

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function getPsychogramResult () {
    let data_id = {{ $data_id }};
    let urlWebCareer = '{{ $urlWebCareer }}';
    let user_type = "{{ $source != 'Candidate' ? 'employee' : 'candidate' }}";
//    let id_user_assessment = $(`#id_user_assessment`).val();
//    let id_batch = $(`#id_batch`).val();
    let id_grade = $(`#grade option:selected`).val();
    let id_department = $(`#department option:selected`).val();

    let modify = false;
    let q_param = {
        'user_type' : user_type,
        'id_user_assessment' : {{ $source != 'Candidate' ? $employee_id : $candidate_id }},
        'id_batch' : {{ $batch_id }},
        'id_department' : $('#psychomodal_department').val(),
        'id_grade' : $('#psychomodal_grade').val(),
        'request_type' : 'table',
        'modify': modify
    };
    let paramRaw = q_param;
    let param = objectToQueryString(q_param);
    paramRaw.request_type = 'raw';
    let idPsychogramMatrix = null;
    $.ajax({
        url: '<?= url('recruitment/psychotest/psychogram/get_psychogram') ?>',
        data: {'url':urlWebCareer+'psikogram/summary?'+objectToQueryString(paramRaw)},
        method: "POST",
        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
        async: false,
        success: (res) => {
            let data = JSON.parse(res.data)
            idPsychogramMatrix = data.result.psychogram.weight_scale.result_matrix.id_psychogram_matrix
        }
    })
    $.ajax({
        url: '<?= url('recruitment/psychotest/psychogram/get_psychogram') ?>',
        data: {'url':urlWebCareer+'psikogram/summary?'+param},
        method: "POST",
        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
        async: true,
        success: function (res) {
            if(res.status == true){
                $(`.result_psychogram`).html(res.data);	
                let res_param = {
                    'id_user_assessment' : {{ $employee_id ?? 'null' }},
                    'id_candidate' : {{ $candidate_id ?? 'null' }},
                    'id_batch' : {{ $batch_id ?? 'null' }},
                    'source' : "{{ $source }}",
                //	'data_id' : {{ $data_id }},	
                    'id_psychogram_matrix' : idPsychogramMatrix
                };
                $.ajax({
                    url: "{{ route('talent_reco.get_val_psychogram') }}",
                    data : res_param,
                    success: function(resp) {
                        if(resp.length > 0){							
                            $('#emp_'+data_id+'_id_potencies').val(resp[0].id_potencies).trigger('change');
                            if(resp[0].potencies == 'Not Recommended'){
                                $('#emp_'+data_id+'_potencies').html(resp[0].potencies).removeClass("badge-success badge-warning").addClass("badge-danger");
                            }
                            else if(resp[0].potencies == 'Considered'){
                                $('#emp_'+data_id+'_potencies').html(resp[0].potencies).removeClass("badge-danger badge-success").addClass("badge-warning").css('color','white');
                            }
                            else if(resp[0].potencies == 'Recommended'){
                                $('#emp_'+data_id+'_potencies').html(resp[0].potencies).removeClass("badge-danger badge-warning").addClass("badge-success");
                            }
                            else{
                                $('#emp_'+data_id+'_potencies').html(resp[0].potencies);
                            }
                            $(`#emp_${data_id}_psych_dept`).text(`${resp[0].department} (${resp[0].job_grade})`);
                            $(`#emp_${data_id}_psych_id_dept`).val(resp[0].id_department);
                            $(`#emp_${data_id}_psych_id_job_grade`).val(resp[0].id_job_grade);
                        }
                    },
                })
            } else {
                $(`.result_psychogram`).html(res.message);
            }
        
        },
        beforeSend: function () {
            $('#loader').removeClass('hidden');
        },
        complete: function(){
            $('#loader').addClass('hidden');
        },
    });
    
}
// $(document).on('click', '#button_generate_psychogram', getPsychogramResult());
</script>