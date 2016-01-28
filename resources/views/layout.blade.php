<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">
        <meta name="_token" content="{{csrf_token()}}">

        <link type="image/x-icon" href="/img/favicon.png" rel="icon">
        <link type="image/x-icon" href="/img/favicon.ico" rel="shortcut icon">

        <title>Cinehost -  Software As A Service</title>




        <!-- Bootstrap core CSS -->
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/bootstrap-reset.css" rel="stylesheet">

        <!--Animation css-->
        <link href="/css/animate.css" rel="stylesheet">

        <!--Icon-fonts css-->
        <link href="/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link href="/assets/ionicon/css/ionicons.min.css" rel="stylesheet" />

        <!-- DataTables -->
        <link href="/assets/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="/assets/toggles/toggles.css" rel="stylesheet" />

        <!-- Custom styles for this template -->
        <link href="/css/helper.css" rel="stylesheet">
        <link href="/css/style.css" rel="stylesheet">
		<link href="/css/cinehost.css" rel="stylesheet">
		<link href="/css/token-input-facebook.css" rel="stylesheet">
		<link href="/css/uploadifiveCss/uploadifive.css" rel="stylesheet">
		<link href="/assets/select2/select2.css" rel="stylesheet" type="text/css" />
        <link href="/assets/timepicker/bootstrap-datepicker.min.css" rel="stylesheet" />
        <link href="/library/bitdash/bitdashplayer.min.css" rel="stylesheet" />
		<link href="/library/CHTransfer/css/bootstrap-custom-transfer.css" rel="stylesheet" />

        <script src="/js/jquery.js"></script>
		<script src="/assets/jquery-multi-select/jquery.multi-select.js" type="text/javascript" ></script>
		<script src="/assets/spinner/spinner.min.js" type="text/javascript" ></script>
		<script src="/assets/select2/select2.min.js" type="text/javascript"></script>

        <script src="/js/ArraySetMath.js"></script>
        <script src="/js/bootbox.min.js"></script>
        <script src="/js/functions.js"></script>
        <script src="/js/exec.js"></script>

        <script src="/library/bitdash/bitdash.min.js"></script>

		<script>
            var player_setup_movie= [];
            var player_setup_trailer = [];
            var player_movie= [];
            var player_trailer= [];
			$(document).ready(function(){

                console.log(player_setup_trailer+'1');
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
						'filmId': 341
					}
				});
			});
		</script>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
        <!--[if lt IE 9]>
          <script src="/js/html5shiv.js"></script>
          <script src="/js/respond.min.js"></script>

        <![endif]-->
    </head>


    <body>

		<!-- Result message -->
		<div class="loading">
			<i class="fa fa-spinner fa-2x fa-spin text-primary"></i>
		</div>

		<!-- Loading -->
		<div class="alert" id="autoCloseMsg">
		   <button onclick="autoCloseMsgHide()" id="autoCloseMsgHide" class="close text-muted" type="button">&times;</button>
		   <strong></strong>
		</div>

        <!-- Aside Start-->
        <aside class="left-panel">

            <!-- brand -->
            <div class="logo">
                <a href="/" class="logo-expanded">
                    <span class="nav-label"><img src="/img/cinehost_logo.svg"></span>
                </a>
            </div>
            <!-- / brand -->

            @include('partials.nav')

        </aside>
        <!-- Aside Ends-->


        <!--Main Content Start -->
        <section class="content">

            @include('partials.header')

            <!-- Page Content Start -->
            <!-- ================== -->

            <div class="wraper container-fluid">

                @yield('content')

            </div>
            <!-- Page Content Ends -->
            <!-- ================== -->

            <!-- Footer Start -->
            <footer class="footer">
                2015 © CINEHOST.
            </footer>
            <!-- Footer Ends -->



        </section>
        <!-- Main Content Ends -->



        <!-- js placed at the end of the document so the pages load faster -->

        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/pace.min.js"></script>
        <script src="/js/wow.min.js"></script>
        <script src="/js/jquery.nicescroll.js" type="text/javascript"></script>
        <script src="/js/jquery.tokeninput.js" type="text/javascript"></script>
        <script src="/js/uploadifiveJs/jquery.uploadifive.min.js" type="text/javascript"></script>


        <script src="/js/jquery.app.js"></script>
        <script src="/assets/datatables/jquery.dataTables.min.js"></script>
        <script src="/assets/datatables/dataTables.bootstrap.js"></script>
        <script src="/assets/timepicker/bootstrap-datepicker.js"></script>

        <script src="/library/CHTransfer/js/bootstrap-custom-transfer.js"></script>


        <script type="text/javascript">
            $(document).ready(function() {
                jQuery('#startDate-datepicker').datepicker({
                    setDate: new Date(),
                    numberOfMonths: 3,
                    showButtonPanel: true,
                    format:'dd-mm-yyyy',
                    autoclose:true,
                });
                jQuery('#endDate-datepicker').datepicker({
                    numberOfMonths: 3,
                    showButtonPanel: true
                });
            });

        </script>

        @yield('footer')
    </body>
</html>
