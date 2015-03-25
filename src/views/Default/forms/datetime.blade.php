<div class="form-group {{ ( $errors->first($field['name']) == '' ) ? '' : 'has-error' }}">
	<label class="control-label" for="SimpleInput{{ $field['name'] }}"> {{ $field['name_display'] }}
		@if($errors->first($field['name']))
	    	<span class="text-danger">
	    		<i class="fa fa-times-circle-o"></i> {{ $errors->first($field['name']) }}
	    	</span>
	    @endif
	</label>
	<div class="row">
		<div class="col-md-6">
	    	<input name="{{ $field['name'] }}" class="datepicker form-control" id="DateTime{{ $field['name'] }}" placeholder="{{ $field['name_display'] }}" value="{{ (Input::old($field['name'])) ? Input::old($field['name']) : $field['value']['date'] }}">
	    </div>
	    <div class="col-md-6">
			<div class="bootstrap-timepicker">
                <input type="text" name="timepicker" class="form-control timepicker"/>
            </div>
	    </div>
	</div>
</div>