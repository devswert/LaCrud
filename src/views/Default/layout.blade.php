<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LaCrud</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Raleway:700,300' rel='stylesheet' type='text/css'>

    <link href="{{ url('LaCrud/Default/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('LaCrud/Default/dist/css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('LaCrud/Default/dist/css/skins/_all-skins.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('LaCrud/Default/plugins/iCheck/square/blue.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('LaCrud/Default/plugins/datepicker/datepicker3.css') }} " rel="stylesheet" type="text/css"/>
    <link href="{{ url('LaCrud/Default/plugins/timepicker/bootstrap-timepicker.min.css') }} " rel="stylesheet" type="text/css"/>
    <link href="{{ url('LaCrud/Default/plugins/datatables/dataTables.bootstrap.css') }} " rel="stylesheet" type="text/css"/>
    <link href="{{ url('LaCrud/Default/plugins/daterangepicker/daterangepicker-bs3.css') }} " rel="stylesheet" type="text/css"/>
    <link href="{{ url('LaCrud/Default/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }} " rel="stylesheet" type="text/css"/>
    <link href="{{ url('LaCrud/Default/plugins/multiselect/multi-select.css') }} " rel="stylesheet" type="text/css"/>    
    <link href="{{ url('LaCrud/Default/plugins/FileInput/fileinput.min.css') }} " rel="stylesheet" type="text/css"/>
    
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>
        window.LaCrud = {
            texteditors : Array(),
            manyrelations : Array()
        };
    </script>
</head>
    <!--<body class="{{ (Auth::guest()) ? 'login-page' : 'skin-blue' }}">-->
    <body class="skin-yellow">

        <div class="wrapper">

            @yield('header')

            @yield('content')

            @yield('footer')

            <!--
            if (!Auth::guest())
                yield('header')
            endif

    		yield('content')

            if (!Auth::guest())
    		    yield('footer')
            endif
            -->
        </div>

        <script src="{{ url('LaCrud/Default/plugins/jQuery/jQuery-2.1.3.min.js') }}"></script>
        <script src="{{ url('LaCrud/Default/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ url('LaCrud/Default/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
        <script src="//code.jquery.com/ui/1.11.1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="//cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>
        <script src="{{ url('LaCrud/Default/plugins/daterangepicker/daterangepicker.js') }} "></script>
        <script src="{{ url('LaCrud/Default/plugins/datepicker/bootstrap-datepicker.js') }} "></script>
        <script src="{{ url('LaCrud/Default/plugins/timepicker/bootstrap-timepicker.min.js') }} "></script>
        <script src="{{ url('LaCrud/Default/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }} "></script>
        <script src="{{ url('LaCrud/Default/plugins/datatables/jquery.dataTables.js') }} "></script>
        <script src="{{ url('LaCrud/Default/plugins/datatables/dataTables.bootstrap.js') }} "></script>
        <script src="{{ url('LaCrud/Default/plugins/multiselect/jquery.multi-select.js') }} "></script>
        <script src="{{ url('LaCrud/Default/plugins/multiselect/jquery.quicksearch.js') }} "></script>
        <script src="{{ url('LaCrud/Default/plugins/FileInput/fileinput.min.js') }} "></script>
        <script src="{{ url('LaCrud/Default/dist/js/app.js') }} "></script>
		<script>
			$(document).ready(function(){
                //Init Datatables for indexs views
				if($('#dataTableInfo').length > 0){
			    	$('#dataTableInfo').DataTable();					
				}

                $("input[type='file']").fileinput({
                    showUpload : false
                });

                //Init texteditors avalaible
                if( LaCrud.texteditors.length > 0 ){
                    for (var i = 0; i < LaCrud.texteditors.length; i++) {
                        CKEDITOR.replace( LaCrud.texteditors[i] );
                    };
                }

                //Init inputs type date
                $('.datepicker').datepicker({
                    format : 'dd-mm-yyyy'
                });
                $(".timepicker").timepicker({
                    showInputs: false,
                    showSeconds: true,
                    showMeridian: false
                });

                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%'
                });

                //Init fields manyrelations
                if( LaCrud.manyrelations.length > 0 ){
                    for (var i = 0; i < LaCrud.manyrelations.length; i++) {
                        $('#' + LaCrud.manyrelations[i]).multiSelect({
                            keepOrder: true,
                            selectableHeader: "<div class='custom-header'> {{ trans('lacrud::templates.mr_selections_title') }} </div><input type='text' class='form-control' autocomplete='off' placeholder='Search?'>",
                            selectionHeader: "<div class='custom-header'> {{ trans('lacrud::templates.mr_options_title') }} </div><input type='text' class='form-control' autocomplete='off' placeholder='Search?'>",
                            afterInit: function(ms){
                                var that = this,
                                    $selectableSearch = that.$selectableUl.prev(),
                                    $selectionSearch = that.$selectionUl.prev(),
                                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                                .on('keydown', function(e){
                                    if (e.which === 40){
                                        that.$selectableUl.focus();
                                        return false;
                                    }
                                });

                                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                                .on('keydown', function(e){
                                    if (e.which == 40){
                                        that.$selectionUl.focus();
                                        return false;
                                    }
                                });
                            },
                            afterSelect: function(){
                                this.qs1.cache();
                                this.qs2.cache();
                            },
                            afterDeselect: function(){
                                this.qs1.cache();
                                this.qs2.cache();
                            }
                        });
                    };
                }
			});
		</script>
    </body>
</html>