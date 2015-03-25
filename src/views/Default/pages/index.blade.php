@extends($template)

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
            @if (Session::has('success_message'))
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Yeah!</strong> {{ Session::get('success_message') }}
                    </div>
                </div>                
            @endif
            @if (Session::has('error_message'))
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>D'oh!</strong> {!! Session::get('error_message') !!}
                    </div>
                </div>                
            @endif

            <div class="col-xs-12">
                <div class="box">
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
                                            <td width="140">
                                                @if ($permission['edit'])
                                                    <a href="{{ URL::route('lacrud.'.$entity.'.edit',array('id' => $row->id)) }}" class="btn btn-warning">
                                                        <span class="fa fa-edit"></span>
                                                    </a>
                                                @endif
                                                @if ($permission['show'])
                                                    <a href="{{ URL::route('lacrud.'.$entity.'.show',array('id' => $row->id)) }}" class="btn btn-info">
                                                        <span class="fa fa-search"></span>
                                                    </a>
                                                @endif
                                                @if ($permission['delete'])
                                                    <form style="display:inline-block;" action="{{  URL::route('lacrud.'.$entity.'.delete',array('id' => $row->id)) }}" method="post">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button class="btn btn-danger">
                                                            <span class="fa fa-trash-o"></span>
                                                        </button>
                                                    </form>
                                                @endif
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