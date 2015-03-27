<div class="form-group {{ ( $errors->first($field['name']) == '' ) ? '' : 'has-error' }}">
	<label class="control-label" for="SimpleUpload{{ $field['name'] }}"> {{ $field['name_display'] }}
		@if($errors->first($field['name']))
	    	<span class="text-danger">
	    		<i class="fa fa-times-circle-o"></i> {{ $errors->first($field['name']) }}
	    	</span>
	    @endif
	</label>
	@if( $field['value'] != '' )
		@if ( is_array($field['paths']) )
			@if ( array_key_exists('public', $field['paths']) )
				<img src="{{ url($field['paths']['public'].'/'.$field['value']) }}">
				@if(array_key_exists('private', $field['paths']))
					 - <span class="fa fa-unlock-alt"> & </span> <span class="fa fa-lock"></span>
				@endif
			@elseif(array_key_exists('private', $field['paths'])) 
				{{ $field['value'] }} - <span class="fa fa-lock"></span>
			@endif
		@else 
			<img src="{{ url($field['paths'].'/'.$field['value']) }}">
		@endif
	@else
    	<input type="file" class="form-control" name="{{ $field['name'] }}" id="SimpleUpload{{ $field['name'] }}">
    @endif
</div>