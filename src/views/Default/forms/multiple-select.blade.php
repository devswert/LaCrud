<div class="form-group {{ ( $errors->first('manyRelations#'.$relations['name']) == '' ) ? '' : 'has-error' }}">
	<label class="control-label" for="{{ md5('MultipleSelect'.$relations['name']) }}"> {{ $relations['name_display'] }}
		@if($errors->first('manyRelations#'.$relations["name"]))
	    	<span class="text-danger">
	    		<i class="fa fa-times-circle-o"></i> {{ $errors->first('manyRelations#'.$relations["name"]) }}
	    	</span>
	    @endif
	</label>
    
    <select id='{{ md5("MultipleSelect".$relations["name"]) }}' multiple='multiple' name="manyRelations#{{ $relations['name'] }}[]">
    	@foreach ($relations['options'] as $row)
    		<option value='{{ $row['key'] }}'
                @if (is_array(Input::old('manyRelations#'.$relations['name'])))
                    {{ ( in_array( $row['key'] , Input::old('manyRelations#'.$relations['name'])) ) ? 'selected' : '' }}
                @else
                    {{ $row['select'] }}
                @endif
                >
                {{ $row['display'] }}
            </option>
    	@endforeach
    </select>
    <script>
    	LaCrud.manyrelations.push('{{ md5("MultipleSelect".$relations["name"]) }}');
    </script>
</div>