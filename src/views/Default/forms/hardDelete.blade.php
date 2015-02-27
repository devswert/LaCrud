<form style="display:inline-block;" action="{{  URL::route('lacrud.'.$entity.'.delete',array('id' => $id)) }}" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_forcedelete" value="{{ Crypt::encrypt($id) }}">
    <button class="btn btn-danger">
        <span class="fa fa-ban"></span> {{ trans('lacrud::templates.force_delete') }}
    </button>
</form>