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
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('lacrud::templates.title_edit') }}</h3>
                    </div>
                    <form action="{{ URL::route('lacrud.'.$entity.'.update', array('id' => $pk) ) }}" method="post" enctype="multipart/form-data">
                        <div class="box-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="PUT">
                            <div class="box-body">
                                {!! $form !!}
                            </div>
                        </div>
                        <div class="box-footer clearfix">
                            <a href="{{ URL::route('lacrud.'.$entity.'.index') }}" class="btn btn-default pull-left">
                                <span class="fa fa-arrow-circle-o-left"></span> {{ trans('lacrud::templates.back') }}
                            </a>
                            <button type="submit" class="btn btn-primary pull-right">
                                <span class="fa fa-refresh"></span> {{ trans('lacrud::templates.update_register') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@stop