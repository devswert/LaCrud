<div class="form-group {{ ( $errors->first($field['name']) == '' ) ? '' : 'has-error' }}">
	<label class="control-label" for="SimpleUpload{{ $field['name'] }}"> {{ $field['name_display'] }}
		@if($errors->first($field['name']))
	    	<span class="text-danger">
	    		<i class="fa fa-times-circle-o"></i> {{ $errors->first($field['name']) }}
	    	</span>
	    @endif
	</label>
	@if( $field['value'] != '' )
		<div class="row">
				@if ( is_array($field['paths']) )
					@if ( array_key_exists('public', $field['paths']) )
						<div class="col-md-6">
							<a  class="img-thumbnail" target="_blank" href="{{ url( ( is_array($field['paths']['public']) ? $field['paths']['public']['path'] : $field['paths']['public'] ) .'/'.$field['value']) }}"> {{ $field['value'] }} </a>
						</div>
						<div class="col-md-6">
							@if(array_key_exists('private', $field['paths']))
								 - <span class="fa fa-unlock-alt"> & </span> <span class="fa fa-lock"></span>
							@endif
							<input type="file" class="form-control" name="{{ $field['name'] }}" id="SimpleUpload{{ $field['name'] }}">
						</div>
					@elseif(array_key_exists('private', $field['paths'])) 
						<div class="col-md-6">
							{{ $field['value'] }} - <span class="fa fa-lock"></span>
						</div>
						<div class="col-md--6">
							<input type="file" class="form-control" name="{{ $field['name'] }}" id="SimpleUpload{{ $field['name'] }}">
						</div>
					@endif
				@else 
					<div class="col-md-6">
						<a  class="img-thumbnail" target="_blank" href="{{ url( $field['paths'].'/'.$field['value']) }}"> {{ $field['value'] }} </a>
					</div>
					<div class="col-md-6">
						<input type="file" class="form-control" name="{{ $field['name'] }}" id="SimpleUpload{{ $field['name'] }}">
					</div>
				@endif
		</div>
	@else
    	<input type="file" class="form-control" name="{{ $field['name'] }}" id="SimpleUpload{{ $field['name'] }}">
    @endif
</div>