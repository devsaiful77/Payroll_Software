<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="" name="description" />
        <meta content="Uzzal" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link rel="shortcut icon" href="{{asset('contents/admin')}}/assets/images/favicon_1.ico">
        <link href="{{asset('contents/admin')}}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{{asset('contents/admin')}}/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="{{asset('contents/admin')}}/assets/css/moltran.css" rel="stylesheet" type="text/css" />
        <link href="{{asset('contents/admin')}}/assets/css/style.css" rel="stylesheet" type="text/css" />
        <script src="{{asset('contents/admin')}}/assets/js/modernizr.min.js"></script>
       

    </head>
    <body>
        <div class="wrapper-page">
            <div class="card card-pages">
                @yield('content')
            </div>
        </div>
    	
        <script src="{{asset('contents/admin')}}/assets/js/jquery.min.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/bootstrap.bundle.min.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/detect.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/fastclick.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.slimscroll.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.blockUI.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/waves.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/wow.min.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.nicescroll.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.scrollTo.min.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.app.js"></script>
        <script src="{{asset('contents/admin')}}/assets/js/jquery.app.js"></script>
        <script>
            var resizefunc = [];
            // Login Page Email Input lowercase conversion
            $('#username').keyup(function() {
                this.value = this.value.toLocaleLowerCase();
            });
        </script>
	</body>
</html>


 