<?php 
use App\Models\Employee\Employee\Employee;

$emp = Employee::where('id_user',session('id_user'))->get()->toArray();
 ?>
@php( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )
@php( $profile_url = View::getSection('profile_url') ?? config('adminlte.profile_url', 'logout') )

@if (config('adminlte.usermenu_profile_url', false))
    @php( $profile_url = Session::get('profile_picture') )
@endif

@if (config('adminlte.use_route_url', false))
    @php( $profile_url = $profile_url ? route($profile_url) : '' )
    @php( $logout_url = $logout_url ? route($logout_url) : '' )
@else
    @php( $profile_url = $profile_url ? url($profile_url) : '' )
    @php( $logout_url = $logout_url ? url($logout_url) : '' )
@endif
<style>
.badge-request{
    right: 30px;
}
.text-black{
    color: black;
}
i.fas.fa-bullhorn::before{
		font-size:17px;
	}
i.far.fa-bell::before, i.fas.fa-plane::before {
		font-size:20px;
	}
i.fa.fa-book::before{
	font-size:19px;
	margin-top:15px;
}
i.fa.fa-book{
	margin-top:2px;
}

span.badge.navbar-badge-left, .navbar-badge-left {
    position: absolute;
    font-size: .6rem;
    font-weight: 600;
    left: 10px;
    top: 8px;
}
.bg-approval{
    background-color: #ffc107!important;
}
.bg-request{
    background-color: #ffa3a3!important;
}
@media screen and (max-width: 560px) {
	.navbar{
		padding: 1rem 0.5rem 0.7rem 0.5rem;
	}
/*	span#select2-company_session-container{
		font-size:12px;
	}
*/
	img.user-image.img-circle.elevation-2{
		height:35px;
		width:35px;
		margin-top:-10px;
	}
	i.fas.fa-bars{
		margin-top:-8px;
	}
	i.fas.fa-bars::before{
		font-size:22px;
	}
	i.fas.fa-bullhorn{
		margin-top:-8px;
	}
	i.fas.fa-bullhorn::before{
		font-size:24px;
		margin-left:5px;
	}
	i.far.fa-bell, i.fas.fa-plane {
		margin-top:-8px;
	}
	i.far.fa-bell::before, i.fas.fa-plane::before {
		font-size:26px;
		margin-left:10px;
	}
	i.fa.fa-book{
		margin-top:-5px;
	}
	i.fa.fa-book::before{
		font-size:26px;
	}
	i.fa.fa-book{
		margin-left:10px;
	}
	span.badge.badge-warning.navbar-badge.text-bold, .badge-request, .badge-approval{
		font-size:12px;
		margin-top:-8px;
	}
    .badge-request{
        right: 35px;
    }
    span.badge.navbar-badge-left, .navbar-badge-left {
        position: absolute;
        font-size: .6rem;
        font-weight: 600;
        left: 15px;
        top: 2px;
    }
}
@media screen and (min-width: 0px) and (max-width: 560px) {
  .div_company { display: inherit;margin-top:50px;margin-bottom:-50px;}  
  .div_company_full { display: none;}  
}
@media screen and (min-width: 561px){
  .div_company_full { display: inline; float:left;}  
  .div_company { display: none; float:left;}   
}
@media screen and (max-width: 575px){
	.navbar-nav > .user-menu .user-image {
	margin-top: -8px;
	}
}
</style>

<li class="nav-item dropdown user-menu">
    {{-- User menu toggler --}}
		<div class="div_company_full">
			<b>
				<select name="id_company" id="company_session_full" class="form-control form-control-sm select2" style="width:100%;">
				</select>
			</b>
		</div>
		<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" style="float:right;">
					<!-- b style="margin-right:10px;font-size:16px;">{{ Session::get('company_name') }}</b -->
			@if(config('adminlte.usermenu_image'))
				<img src="{{ Session::get('profile_picture') }}"
					 class="user-image img-circle elevation-2"
					 alt="{{ Session::get('username') }}">
			@endif
			<span @if(config('adminlte.usermenu_image')) class="d-none d-md-inline" @endif>
			<b>
			   {{ Session::get('username') }}
			</b>
			</span>
		</a>
	
    {{-- User menu dropdown --}}
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        {{-- User menu header --}}
        @if(!View::hasSection('usermenu_header') && config('adminlte.usermenu_header'))
            <li class="user-header bg-gray @if(!config('adminlte.usermenu_image')) h-auto @endif">
                @if(config('adminlte.usermenu_image'))
                    <img src="{{ Session::get('profile_picture') }}"
                         class="img-circle elevation-2"
                         alt="{{ Session::get('username') }}">
                @endif
                <p class="@if(!config('adminlte.usermenu_image')) mt-0 @endif" style="font-size:16px;">
				<?php 
				if(count($emp) > 0){
					echo $emp[0]['name'].'<br>';
					echo '<div style="font-size:14px;">'.$emp[0]['nik_employee'].'</div>';
				}
				else{
					echo Session::get('username');
				}
				?>
                    @if(config('adminlte.usermenu_desc'))
                    @endif
                </p>
            </li>
        @else
            @yield('usermenu_header')
        @endif

        {{-- Configured user menu links --}}
        @each('adminlte::partials.navbar.dropdown-item', $adminlte->menu("navbar-user"), 'item')

        {{-- User menu body --}}
        @hasSection('usermenu_body')
            <li class="user-body">
                @yield('usermenu_body')
            </li>
        @endif

        {{-- User menu footer --}}
        <li class="user-footer">
            @if($profile_url)
                <a href="{{ route('user.index') }}" class="btn btn-default btn-flat">
                    <i class="fa fa-fw fa-user"></i>
                    {{ __('adminlte::menu.profile') }}
                </a>
            @endif
            <a class="btn btn-default btn-flat float-right @if(!$profile_url) btn-block @endif"
               href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-fw fa-power-off"></i>
                {{ __('adminlte::adminlte.log_out') }}
            </a>
            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                @if(config('adminlte.logout_method'))
                    {{ method_field(config('adminlte.logout_method')) }}
                @endif
                {{ csrf_field() }}
            </form>
        </li>

    </ul>

</li>
@section('js')
<script type="text/javascript">
    $(function () {
		getCompany_user();
        // getCompany_session()
        getNotification();
		getNotificationAnnoun();
        getSettlementNotification();
    });

    function getCompany_session(){
        $.ajax({
            url: "<?= url('getCompany_session') ?>",
            method: "GET",
            data: {id_company:'<?= session::get('id_company') ?>'},
            success: function (response) {                  
                setTimeout(function () {
                    $('#company_session_full').val(response.id).trigger('change');
                    $('#company_session').val(response.id).trigger('change');
                    if($('#company_session_full').val() == null){
                        setTimeout(function () {
                            $('#company_session_full').val(response.id).trigger('change');
                        }, 500);
                    }  
					if($('#company_session').val() == null){
                        setTimeout(function () {
                            $('#company_session').val(response.id).trigger('change');
                        }, 500);
                    }                       
                }, 50);
            },
            error: function(xhr, textStatus, errorThrown) {
               getCompany_session()
            }
        }); 
    }

    function getNotification(){
        $.ajax({
            url: "{{ route('getNotification') }}",
            method: "GET",
            dataType: 'json',
            async: true,
            success: function (response) {     
                let approval = response.employeeApproval;
                let revised = response.revisedEmployeeRequest;
                let rejected = response.rejectedEmployeeRequest;
                notification(approval, revised, rejected)
            },
            error: function(xhr, textStatus, errorThrown) {
        //       getNotification()
            }
        });
    }

    function getSettlementNotification() {
        $.ajax({
            url: "{{ route('settlementtravel.notification') }}",
            method: "GET",
            dataType: 'json',
            async: true,
            success: function (response) {     
                let chief = response.chief;
                let finance = response.finance;
                let financePayment = response.finance_payment_cashadvance;
                let financeLink = response.finance_link;
                settlementNotification(chief, finance, financePayment, financeLink)
            },
            error: function(xhr, textStatus, errorThrown) {
               getSettlementNotification()
            }
        });
    }

	function getNotificationAnnoun(){
        $.ajax({
            url: "{{ route('getNotificationAnnoun') }}",
            method: "GET",
            dataType: 'json',
            async: true,
            success: function (response) {                  
                notificationAnnoun(response.employeeAnnoun)
            },
            error: function(xhr, textStatus, errorThrown) {
               getNotificationAnnoun()
            }
        });
    }

    const getCompany_user = async () => {
        try {
            let result;
            let id_company_session = "<?= session('id_company') ?>";
            result = await $.getJSON('<?= url('getCompany_user') ?>', function (res) { 
			var data = {results: []};
			$.each(res, function (key, val) {
				 data.results.push({id: this.id, text: this.text+'  <span class="badge badge-warning" style="padding:3px;">'+this.total+'</span>' });
			});
			results = data.results;
                $('#company_session_full').select2({
                    placeholder: "Select Company",
                    data: results,
					escapeMarkup: function(markup) {
						return markup;
					},
                }); 
                $('#company_session_full').val(id_company_session).trigger('change', [true]);
				
				$('#company_session').select2({
                    placeholder: "Select Company",
                    data: results,
					escapeMarkup: function(markup) {
						return markup;
					},
                }); 
                $('#company_session').val(id_company_session).trigger('change', [true]);
				$('#select2-company_session_full-container').attr('title', '');
            });
            return result;
        } catch (error) {
       //     getCompany_user();
        }
    }

    $("#company_session_full").change(function(e, isTriggered) {
        let id_company = $(this).val();
        if (!isTriggered){ // change by human
            $.ajax({
                type: 'POST',
                headers: {Accept: "application/json",},
                url: "<?= url('change_session') ?>",                        
                data:{ id_company :id_company, _token:"{{ csrf_token() }}" },
                beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                complete: function () { 
                    window.location.reload();
                    // $('#loader').addClass('hidden')
                }           
            });        
        } 
    });

	$("#company_session").change(function(e, isTriggered) {
        let id_company = $(this).val();
        if (!isTriggered){ // change by human
            $.ajax({
                type: 'POST',
                headers: {Accept: "application/json",},
                url: "<?= url('change_session') ?>",                        
                data:{ id_company :id_company, _token:"{{ csrf_token() }}" },
                beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                complete: function () { 
                    window.location.reload();
                    // $('#loader').addClass('hidden')
                }           
            });        
        } 
    });

    function settlementNotification(chiefCount, financeCount, financePaymentCount, financeLink) {
        let badgeTotalApproval = `<span class="badge bg-approval navbar-badge text-bold text-black badge-approval">${chiefCount}</span>`;
        let badgeTotalFinanceApproval = `<span class="badge bg-info navbar-badge-left text-bold text-black">${financeCount + financePaymentCount}</span>`;
        let notificationTitle = `${chiefCount + financeCount + financePaymentCount} Settlement Approval`;
        let listChiefApproval = `
            <div class="dropdown-divider"></div>
            <span style="margin: 0px 5px 0px 5px;">
                <i class="fas fa-user mr-2"></i> ${chiefCount} Settlement Approval User
            </span>
        `;
        let seeAllChiefApproval = `<div class="dropdown-divider"></div>
                <a href="{{ route('settlementtravel.approval.chief')}}" class="dropdown-item dropdown-footer bg-approval ">See All Settlement Approval User</a>`;
        let listFinanceApproval = `
            <div class="dropdown-divider"></div>
            <span style="margin: 0px 5px 0px 5px;">
                <i class="fas fa-money mr-2"></i> ${financeCount} Settlement Approval Finance
            </span>
        `;
        let listFinancePayment = `
            <div class="dropdown-divider"></div>
            <span style="margin: 0px 5px 0px 5px;">
                <i class="fas fa-money mr-2"></i> ${financePaymentCount} Cash Advance Payment Finance
            </span>
        `;
        let seeAllFinanceApproval = `<div class="dropdown-divider"></div>
                <a href="${financeLink}" class="dropdown-item dropdown-footer bg-info">See All Settlement Approval Finance</a>`;
        let seeAllFinancePayment = `<div class="dropdown-divider"></div>
                <a href="{{ route('settlementtravel.payment.finance')}}" class="dropdown-item dropdown-footer bg-info">See All Cash Advance Payment Finance</a>`;
        let notification = `
            <a class="nav-link" data-toggle="dropdown">
                ${financeCount > 0 ? badgeTotalFinanceApproval: ""}
                <i class="fas fa-plane"></i>
                ${chiefCount > 0 ? badgeTotalApproval : ""}
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left">
                <span class="dropdown-item dropdown-header text-bold">${notificationTitle}</span>
                ${chiefCount > 0 ? listChiefApproval : ""}
                ${chiefCount > 0 ? seeAllChiefApproval : ""}
                ${financePaymentCount > 0 ? listFinancePayment : ""}
                ${financePaymentCount > 0 ? seeAllFinancePayment : ""}
                ${financeCount > 0 ? listFinanceApproval : ""}
                ${financeCount > 0 ? seeAllFinanceApproval : ""}
            </div>
            `;
        $(".notification_settlement").html(notification);
    }

    function notification(dataApproval, dataRevised, dataRejected) {
        let totalApproval = 0;
        let listApproval = ``;
        let totalRequest = 0;
        let listRequest = ``;
        let badgeTotalApproval = ``;
        let seeAllApproval = ``;
        let badgeTotalRequest = ``;
        let seeAllRequest = ``;
        let nameTotalApproval = ``;
        let nameTotalRequest = ``;
        let pemisah = ``;
        let notifText = ``;

        for (let item of dataApproval) {
            totalApproval += parseInt(item.total);
            type = item.source_transaction_type.replace(/_/g, " ");
            listApproval += `
                <div class="dropdown-divider"></div>
                <span style="margin: 0px 5px 0px 5px;">
                    <i class="fas fa-envelope mr-2"></i> ${item.total}   ${type}
                </span>
            `;
        }
        for (let item of dataRevised) {
            totalRequest += parseInt(item.total);
            type = item.description.replace(/_/g, " ");
            listRequest += `
                <div class="dropdown-divider"></div>
                <span style="margin: 0px 5px 0px 5px;">
                    <i class="fas fa-envelope mr-2"></i> ${item.total}   ${type}
                </span>
            `;
        }
        for (let item of dataRejected) {
            totalRequest += parseInt(item.total);
            type = item.description.replace(/_/g, " ");
            listRequest += `
                <div class="dropdown-divider"></div>
                <span style="margin: 0px 5px 0px 5px;">
                    <i class="fas fa-envelope mr-2"></i> ${item.total}   ${type}
                </span>
            `;
        }

        if(totalApproval > 0){
            badgeTotalApproval += `<span class="badge bg-approval navbar-badge text-bold text-black badge-approval">${totalApproval}</span>`;
            seeAllApproval = `<div class="dropdown-divider"></div>
                <a href="{{ route('emp_approval.index')}}" class="dropdown-item dropdown-footer bg-approval ">See All Employee Approval Notifications</a>`;
            nameTotalApproval = `${totalApproval} Employee Approval`;
        }
        if(totalRequest > 0){
            badgeTotalRequest += `<span class="badge bg-request navbar-badge text-bold text-black badge-request">${totalRequest}</span>`;
            seeAllRequest = `<div class="dropdown-divider"></div>
                <a href="{{ route('employee_request.index')}}" class="dropdown-item dropdown-footer bg-request">See All Employee Request Notifications</a>`;
            nameTotalRequest = `${totalRequest} Employee Request`;
        }

        if(totalApproval > 0 && totalRequest > 0){
            pemisah = ` &nbsp; | &nbsp; `;
        }
        if(nameTotalApproval == '' && nameTotalRequest == ''){
            notifText = `0 Notifications`
        } else {
            notifText = `${nameTotalRequest} ${pemisah} ${nameTotalApproval}`;
        }

        let notification = `
            <a class="nav-link" data-toggle="dropdown">
                ${badgeTotalRequest}
                <i class="far fa-bell"></i>
                ${badgeTotalApproval}
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left">
                <span class="dropdown-item dropdown-header text-bold">${notifText}</span>
                ${listApproval}
                ${seeAllApproval}
                ${listRequest}
                ${seeAllRequest}
            </div>
            `;
        $(".notification").html(notification);
    }
    function notificationAnnoun(data) {
        let total = 0;
        let list = '';
        for (let item of data) {
            total += parseInt(item.total_announ);
            list += `
 				<a href="{{ route('announ.index')}}" class="dropdown-item bg-warning">
               <span style="margin: 0px 5px 0px 0px;">
                    <i class="fas fa-envelope mr-2"></i> ${item.total_announ}  New Announcement				
                </span>
				</a>
            `;
        }
        let badge_total = '';

        if(total > 0){
			localStorage.setItem("announ_count", total);	
            badge_total = `<span class="badge badge-warning navbar-badge text-bold">${total}</span>`;
        }
		else{
			localStorage.setItem("announ_count", total);
		}

        let notification_announ = `
            <a class="nav-link" data-toggle="dropdown">
                <i class="fas fa-bullhorn"></i>
                ${badge_total}
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left">
				<a href="#" onClick="get_announ_new(0)">
					<span class="dropdown-item dropdown-header bg-warning">${total} New Announcement</span>
				</a>	
            </div>
            `;
        $(".notification_announ").html(notification_announ);
    }
</script>
@stop

