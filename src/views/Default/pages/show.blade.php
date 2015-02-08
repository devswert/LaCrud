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
                        <h3 class="box-title">Vista de Registro</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            @foreach ($columns as $key => $value)
                                <tr>
                                    <td>
                                        <strong>
                                            @if( array_key_exists($key,$alias) )
                                                {{ $alias[$key] }}
                                            @else
                                                {{ ucfirst(str_replace("_", " ", $key)) }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $value }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="box-footer clearfix">
                        <a href="{{ URL::route('lacrud.'.$entity.'.index') }}" class="btn btn-default pull-left">
                            <span class="fa fa-arrow-circle-o-left"></span> Volver
                        </a>
                        <a href="{{ URL::route('lacrud.'.$entity.'.edit',array('id' => $columns[$pk])) }}" class="btn btn-warning pull-right">
                            <span class="fa fa-edit"></span> Editar
                        </a>
                        <form action="{{  URL::route('lacrud.'.$entity.'.delete',array('id' => $columns[$pk])) }}" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger pull-right">
                                <span class="fa fa-trash-o"></span> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop