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
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Data Table With Full Features</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table id="dataTableInfo" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                    
                                    @foreach($headers as $header)
                                        <td>{{ $header }}</td>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                    @foreach($keys as $key)
                                        @if(strtolower($key) != 'actions')
                                            <td>{{ substr(strip_tags($row->$key),0,200) }}{{ (strlen(strip_tags($row->$key)) > 200 ) ? '...' : '' }}</td>
                                        @else
                                            <td width="125">
                                                <a href="{{ URL::route('lacrud.'.$entity.'.edit',array('id' => $row->id)) }}" class="btn btn-warning">
                                                    <span class="fa fa-edit"></span>
                                                </a>
                                                <a href="{{ URL::route('lacrud.'.$entity.'.show',array('id' => $row->id)) }}" class="btn btn-info">
                                                    <span class="fa fa-search"></span>
                                                </a>
                                                <form action="{{  URL::route('lacrud.'.$entity.'.delete',array('id' => $row->id)) }}" method="post">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button class="btn btn-danger">
                                                        <span class="fa fa-trash-o"></span>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop