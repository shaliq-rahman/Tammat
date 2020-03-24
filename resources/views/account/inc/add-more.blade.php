
  <div class="modal fade" id="addmore" tabindex="-1" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          	<button type="button" class="close" data-dismiss="modal">
				<span aria-hidden="true">&times;</span>
				<span class="sr-only">{{ t('Close') }}</span>
			</button>
          <h4 class="modal-title"><i class="icon-mail-2"></i> {{ t('Add More in Offer') }} </h4>
        </div>
        <form role="form" method="POST" action="{{ lurl('account/makeanoffers/' . $makeanoffers->id . '/addmore') }}" enctype="multipart/form-data">
		{!! csrf_field() !!}
        <div class="modal-body">
          <div class="form-group required <?php echo (isset($errors) and $errors->has('offer_price')) ? 'has-error' : ''; ?>">
				<label for="offer_price" class="control-label">{{ t('Add Post') }}
					@if (!isEnabledField('email'))
						<sup>*</sup>
					@endif
				</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
					<select name="post" class="form-control">
					@foreach ($all_post as $post)
					    <option value="{{ $post->id }}">{{ $post->title }} ({{ $post->price }})</option>
					@endforeach
					</select>
				</div>
			</div>
        </div>
        <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">{{ t('Cancel') }}</button>
			<button type="submit" class="btn btn-success pull-right">{{ t('Add') }}</button>
		</div>
		</form>
      </div>
      
    </div>
  </div>

@section('after_styles')
	@parent
	<link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
	@if (config('lang.direction') == 'rtl')
		<link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput-rtl.min.css') }}" rel="stylesheet">
	@endif
	<style>
		.krajee-default.file-preview-frame:hover:not(.file-preview-error) {
			box-shadow: 0 0 5px 0 #666666;
		}
	</style>
@endsection

@section('after_scripts')
    @parent
	
	<script src="{{ url('assets/plugins/bootstrap-fileinput/js/plugins/sortable.min.js') }}" type="text/javascript"></script>
	<script src="{{ url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js') }}" type="text/javascript"></script>
	@if (file_exists(public_path() . '/assets/plugins/bootstrap-fileinput/js/locales/'.config('app.locale').'.js'))
		<script src="{{ url('assets/plugins/bootstrap-fileinput/js/locales/'.config('app.locale').'.js') }}" type="text/javascript"></script>
	@endif

	<script>
		/* Initialize with defaults (Resume) */
		$('#filename').fileinput(
		{
            language: '{{ config('app.locale') }}',
			@if (config('lang.direction') == 'rtl')
				rtl: true,
			@endif
			showPreview: false,
			allowedFileExtensions: {!! getUploadFileTypes('file', true) !!},
			showUpload: false,
			showRemove: false,
			maxFileSize: {{ (int)config('settings.upload.max_file_size', 1000) }}
		});
	</script>
	<script>
		$(document).ready(function () {
			@if ($errors->any())
				@if ($errors->any() and old('messageForm')=='1')
					$('#makeAnOffer').modal();
				@endif
			@endif
		});
	</script>
@endsection
