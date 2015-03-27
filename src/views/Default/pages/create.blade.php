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
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('lacrud::templates.title_create') }}</h3>
                    </div>
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
    </section>
@stop