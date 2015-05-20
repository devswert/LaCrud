<div class="form-group {{ ( $errors->first($field['name']) == '' ) ? '' : 'has-error' }}">
	<label class="control-label" for="SimpleTime{{ $field['name'] }}"> {{ $field['name_display'] }}
		@if($errors->first($field['name']))
	    	<span class="text-danger">
	    		<i class="fa fa-times-circle-o"></i> {{ $errors->first($field['name']) }}
	    	</span>
	    @endif
	</label>
	<div class="row">
	    <div class="col-md-12">
			<div class="bootstrap-timepicker">
                <input type="text" name="{{ $field['name'] }}" id="SimpleTime{{ $field['name'] }}" class="form-control timepicker" value="{{ (Input::old($field['name'])) ? Input::old($field['name']) : $field['value'] }}" />
            </div>
	    </div>
	</div>
</div>