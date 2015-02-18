<div class="form-group {{ ( $errors->first($field['name']) == '' ) ? '' : 'has-error' }}">
	<label class="control-label" for="SimpleInput{{ $field['name'] }}"> {{ $field['name_display'] }}
		@if($errors->first($field['name']))
	    	<span class="text-danger">
	    		<i class="fa fa-times-circle-o"></i> {{ $errors->first($field['name']) }}
	    	</span>
	    @endif
	</label>
	
    <select name="{{ $field['name'] }}" class="form-control">
    	@foreach($field['hasForeignKeys'] as $key => $value)
    		<option value="{{ $value }}" {{ ($value == Input::old($field['name'])) ? 'selected' : '' }}>{{ (is_numeric($key)) ? $value : $key }}</option>
    	@endforeach
    </select>
</div>