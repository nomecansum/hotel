<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Lock Screen | Nifty - Admin Template</title>

    <!--STYLESHEET-->
    <!--=================================================-->

    <!--Open Sans Font [ OPTIONAL ]-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>


    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    @if(session('template')!==null && isset(session('template')->esquema)) <link rel="stylesheet" href="{{ asset('/assets/css'.session('template')->esquema.'/bootstrap.min.css') }}"> @else <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}"> @endif


    <!--Nifty Stylesheet [ REQUIRED ]-->
    <link href="css/nifty.min.css" rel="stylesheet">


    <!--Nifty Premium Icon [ DEMONSTRATION ]-->
    <link href="css/demo/nifty-demo-icons.min.css" rel="stylesheet">


    <!--=================================================-->



    <!--Pace - Page Load Progress Par [OPTIONAL]-->
    <link href="plugins/pace/pace.min.css" rel="stylesheet">
    <script src="plugins/pace/pace.min.js"></script>


        
    <!--Demo [ DEMONSTRATION ]-->
    <link href="css/demo/nifty-demo.min.css" rel="stylesheet">

    
    <!--=================================================

    REQUIRED
    You must include this in your project.


    RECOMMENDED
    This category must be included but you may modify which plugins or components which should be included in your project.


    OPTIONAL
    Optional plugins. You may choose whether to include it in your project or not.


    DEMONSTRATION
    This is to be removed, used for demonstration purposes only. This category must not be included in your project.


    SAMPLE
    Some script samples which explain how to initialize plugins or components. This category should not be included in your project.


    Detailed information and more samples can be found in the document.

    =================================================-->
        
</head>

<!--TIPS-->
<!--You may remove all ID or Class names which contain "demo-", they are only used for demonstration. -->

<body>
    <div id="container" class="cls-container">
        
		<!-- BACKGROUND IMAGE -->
		<!--===================================================-->
		<div id="bg-overlay"></div>
		
		<!-- LOCK SCREEN -->
		<!--===================================================-->
		<div class="cls-content" style=" backgroud-color: none">
		    <div class="cls-content-sm panel">
		        <div class="card-body">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="mar-ver pad-btm">
                                <h1 class="h3">{{ Auth::user()->name }}</h1>
                                <span>{{ DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->cod_nivel)->first()->des_nivel_acceso }}</span>
                            </div>
                            <div class="pad-btm mar-btm">
                                
                                @if (isset(Auth::user()->img_usuario ) && Auth::user()->img_usuario!='')
                                    <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.Auth::user()->img_usuario) }}" id="main_user_image" class="img-md rounded-circle">
                                @else
                                    {!! icono_nombre(Auth::user()->name) !!}
                                @endif
                            </div>
                            <p>Introduzca su password para desbloquear!</p>
                            <form method="POST" id="loginform" action="{{ route('login') }}">
                                @csrf
                                <input type="hidden" class="form-control"  name="email" value="{{ Auth::user()->email }}" required autocomplete="email" autofocus>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Password">
                                </div>
                                <div class="form-group text-center mt-3">
                                    <button class="btn btn-block btn-lg btn-success" type="submit">Login In</button>
                                </div>
                            </form>
                            <div class="pad-ver">
                                <a href="{{ url('/login') }}" class="btn-link mar-rgt text-bold">Utilizar una cuenta distinta</a>
                            </div>
                        </div>
                    </div>
		            
		        </div>
		    </div>
		</div>
		<!--===================================================-->
		
		
		<!-- DEMO PURPOSE ONLY -->
		<!--===================================================-->
		<div class="demo-bg">
		    <div id="demo-bg-list">
		        <div class="demo-loading"><i class="demo-psi-repeat-2"></i></div>
		        <img data-id="1" class="demo-chg-bg bg-trans active" src="img/bg-img/thumbs/bg-trns.jpg" alt="Background Image">
		        <img data-id="2" class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-1.jpg" alt="Background Image">
		        <img data-id="3" class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-2.jpg" alt="Background Image">
		        <img data-id="4" class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-3.jpg" alt="Background Image">
		        <img data-id="5" class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-4.jpg" alt="Background Image">
		        <img data-id="6" class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-5.jpg" alt="Background Image">
		        <img data-id="7" class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-6.jpg" alt="Background Image">
		        <img data-id="8" class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-7.jpg" alt="Background Image">
		    </div>
		</div>
		<!--===================================================-->
		
		
		
    </div>
    <!--===================================================-->
    <!-- END OF CONTAINER -->


        
    <!--JAVASCRIPT-->
    <!--=================================================-->

    <!--jQuery [ REQUIRED ]-->
    <script src="js/jquery.min.js"></script>


    <!--BootstrapJS [ RECOMMENDED ]-->
    <script src="js/bootstrap.min.js"></script>


    <!--NiftyJS [ RECOMMENDED ]-->
    <script src="js/nifty.min.js"></script>




    <!--=================================================-->
    
    <!--Background Image [ DEMONSTRATION ]-->
    <script src="js/demo/bg-images.js"></script>


    <script>
        let index=2;
        let max=8;

        function cambio(){
            console.log(cambio);
            $("img[data-id='" + index +"']").removeClass('active');
            $("img[data-id='" + index +"']").click();
            index++;
            $("img[data-id='" + index +"']").addClass('active');
            if (index==max){
                index=1;
            }
        }

        t=setInterval(cambio,10000);

    </script>

</body>
</html>
