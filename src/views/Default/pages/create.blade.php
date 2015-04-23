@extends($template)

@section('header')
    {!! $header !!}
@stop

@section('footer')
    {!! $footer !!}
@stop

@section('content')
    <!-- Content Header-->
    <section class="content-header clearfix">
        <h1 class="pull-left">
            {{ $title }}
            <small>{{ $subtitle }}</small>
        </h1>
         @if( $isIndex && $permission['add'] )
            <a href="{{ URL::route('lacrud.'.$entity.'.create') }}" class="btn btn-success pull-right">
                <span class="fa fa-plus-circle"></span> {{ trans('lacrud::templates.new_register') }}
            </a>
        @endif
    </section>

     <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h2 class="box-title">{{ trans('lacrud::templates.title_create') }}</h2>
                    </div>
                    <div class="box-body">
                        <form action="{{ URL::route('lacrud.'.$entity.'.store') }}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="box-body">
                                {!! $form !!}
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn btn-success float-left" type="submit">{{ trans('lacrud::templates.save_register') }}</button>
                                        <a href="{{ \URL::route('lacrud.'.$entity.'.index') }}" class="btn btn-danger float-left">{{ trans('lacrud::templates.cancel') }}</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop