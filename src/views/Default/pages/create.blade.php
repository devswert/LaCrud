@extends($template.'.layout')

@section('header')
    {{ $header }}
@stop

@section('footer')
    {{ $footer }}
@stop

@section('content')
     <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">Agregando nuevo registro</h3>
                    </div>
                    {{ Form::open( array('url' => URL::route('lacrud.'.$entity.'.store') ) ) }}
                        <div class="box-body">
                            {{ $form }}
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-success float-left" type="submit">Guardar</button>
                                    <a href="{{ URL::route('lacrud.'.$entity.'.index') }}" class="btn btn-danger float-left">Cancelar</a>
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
@stop