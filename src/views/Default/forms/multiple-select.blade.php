<div class="form-group"><!-- Validate error laravel -->
	<label class="control-label" for="MultiSelectName"> Nombre del Field
		<!-- Validador laravel ->
	    	<span class="text-danger">
	    		<i class="fa fa-times-circle-o"></i> Nombre con Field
	    	</span>
	    <!- Fin Validador -->
	</label>
    <select id='multiple-relation' multiple='multiple'>
    	@foreach ($realtions['options'] as $row)
    		<option value='{{ $row['key'] }}'>{{ $row['display'] }}</option>
    	@endforeach
    </select>
</div>