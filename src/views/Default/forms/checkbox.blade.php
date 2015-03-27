<div class="form-group {{ ( $errors->first($field['name']) == '' ) ? '' : 'has-error' }}">
	<label class="control-label" for="SimpleCheckbox{{ $field['name'] }}"> {{ $field['name_display'] }}
		@if($errors->first($field['name']))
	    	<span class="text-danger">
	    		<i class="fa fa-times-circle-o"></i> {{ $errors->first($field['name']) }}
	    	</span>
	    @endif
	    </label>
    <input type="checkbox" name="{{ $field['name'] }}" id="SimpleCheckbox{{ $field['name'] }}" {{ ((Input::old($field['name'])) ? Input::old($field['name']) : $field['value'] ) ? 'checked' : '' }}>
</div>