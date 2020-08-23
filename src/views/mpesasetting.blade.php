@extends('admin.layouts.master')
@section('title', 'MPesa Setting - Admin')
@section('body')

	<div class="box">
		@include('admin.message')
		
		<div class="box-header with-border">
			<div class="box-title">
				MPesa Setting
			</div>
		</div>

		<div class="box-body">
			<div class="row">
				<form action="{{ route('mpesa.update') }}" method="POST">
					@csrf

					<div class="col-md-12">
						<div class="form-group eyeCy">
							<label>MPESA ENABLE: <span class="text-danger">*</span></label>
							 <li class="tg-list-item">              
						            <input class="tgl tgl-skewed" id="captcha_sec1" type="checkbox" name="MPESA_ENABLE" {{ env('MPESA_ENABLE') == 1 ? 'checked' : '' }} >
						            <label class="tgl-btn" data-tg-off="Disable" data-tg-on="Enable" for="captcha_sec1"></label>
						     </li>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group eyeCy">
							<label>MPESA KEY: <span class="text-danger">*</span></label>
							<input value="{{ env('MPESA_KEY') }}" id="mpesakey" required="" name="MPESA_KEY" type="password" class="form-control" placeholder="enter your mpesa key">
							<span toggle="#mpesakey" class="fa fa-fw fa-eye field-icon toggle-password"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group eyeCy">
							<label>MPESA SECRET: <span class="text-danger">*</span></label>
							<input value="{{ env('MPESA_SECRET') }}" id="mpesasecret" required="" type="password" name="MPESA_SECRET" class="form-control" placeholder="enter your mpesa secret key">
							<span toggle="#mpesasecret" class="fa fa-fw fa-eye field-icon toggle-password"></span>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group eyeCy">
							<label>MPESA INITIATOR NAME: <span class="text-danger">*</span></label>
							<input value="{{ env('MPESA_INITIATOR') }}" required="" type="text" name="MPESA_INITIATOR" class="form-control" placeholder="enter your mpesa initiator name">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>MPESA PAYBILL NO: <span class="text-danger">*</span></label>
							<input value="{{ env('MPESA_PAYBILL') }}" required="" type="text" class="form-control" name="MPESA_PAYBILL" placeholder="enter your mpesa initiator name">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label>MPESA SHORTCODE: <span class="text-danger">*</span></label>
							<input value="{{ env('MPESA_SHORTCODE') }}" required="" type="text" name="MPESA_SHORTCODE" class="form-control" placeholder="enter your mpesa short code">
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group eyeCy">
							<label>MPESA PASSKEY: <span class="text-danger">*</span></label>
							<input value="{{ env('MPESA_PASSKEY') }}" id="passkey" required="" type="password" name="MPESA_PASSKEY" class="form-control" placeholder="enter your mpesa pass key">
							<span toggle="#passkey" class="fa fa-fw fa-eye field-icon toggle-password"></span>
						</div>
					</div>

					

					<div class="col-md-6">
						<button type="submit" class="btn btn-md btn-primary">
							<i class="fa fa-save"></i> SAVE SETTING
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@section('script')
<script>
	 $(".toggle-password").on('click', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if(input.attr("type") == "password") {
          input.attr("type", "text");
        } else {
          input.attr("type", "password");
        }
      });
</script>
@endsection