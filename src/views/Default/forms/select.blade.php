<div class="form-group {{ ( $errors->first($field['name']) == '' ) ? '' : 'has-error' }}">
	<label class="control-label" for="SimpleInput{{ $field['name'] }}"> {{ $field['name_display'] }}
		@if($errors->first($field['name']))
	    	<span class="text-danger">
	    		<i class="fa fa-times-circle-o"></i> {{ $errors->first($field['name']) }}
	    	</span>
	    @endif
	</label>
    <select name="{{ $field['name'] }}" class="form-control">
    	@foreach($field['options'] as $option)
    		<option value="{{ $option }}" {{ ($option == $field['value'] || $field['value'] === $value) ? 'selected' : '' }}>{{ $option }}</option>
    	@endforeach
    </select>
</div>