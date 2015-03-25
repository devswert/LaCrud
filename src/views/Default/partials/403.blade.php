@extends($template)

@section('header')
    {!! $header !!}
@stop

@section('footer')
    {!! $footer !!}
@stop

@section('content')
    <section class="content">
        <div class="error-page">
            <h2 class="headline text-info"> 403</h2>
            <div class="error-content">
                <h3><i class="fa fa-warning text-yellow"></i> Oops!</h3>
                <p>
                    {{ $message }}
                </p>
            </div>
        </div>
    </section>
@stop