@extends($template.'.layout')

@section('header')
    {!! $header !!}
@stop

@section('footer')
    {!! $footer !!}
@stop

@section('content')
     <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">Edición de Registro</h3>
                    </div>
                    <form action="{{ URL::route('lacrud.'.$entity.'.update', array('id' => $pk) ) }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="box-body">
                            {!! $form !!}
                        </div>
                        <div class="box-footer clearfix">
                            <a href="{{ URL::route('lacrud.'.$entity.'.index') }}" class="btn btn-default pull-left">
                                <span class="fa fa-arrow-circle-o-left"></span> Volver
                            </a>
                            <button type="submit" class="btn btn-primary pull-right">
                                <span class="fa fa-refresh"></span> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@stop