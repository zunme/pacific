<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Pacific</title>
    <meta name="description" content="Pacific">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">


    <!-- Pushy CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="fonts/styles.css">
    <link rel="stylesheet" href="font-awesome/css/all.min.css">

    <!-- feathericons -->
    <link rel="stylesheet" href="feather-icon/feather.css">
  </head>
  <body>

    <div class="login">
    	<form name="frmLogin" method="POST" class="login_form" action="{{ URL::route('login') }}">
			
			 @csrf
			
    		<input type="hidden" name="dataType" value="json">
    		<div class="card shadow">
    			<div class="card-body">
    				<div class="d-flex justify-content-center">
              <a class="logo mb-4" href="/" title="홈으로"></a>

    					<!-- <div class="dropdown ml-auto">
                <a class="dropdown-toggle" href="#" role="button" id="lang" data-toggle="dropdown" aria-haspopup="true">
                  <i class="feather-globe"></i> 한국어
                </a>
                <div class="dropdown-menu shadow" aria-labelledby="lang">
                  <ul>
                    <li>
                      <a class="dropdown-item" href="#" onclick="">한국어</a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#" onclick="">영어</a>
                    </li>
                  </ul>
                </div>
              </div> -->
    				</div>
    				<!-- <form> -->
    				<div class="form-group">
    					<div class="input-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i data-feather="user"></i></span>
    						</div>
    						<input name="phone" type="text" class="form-control" placeholder="Phone Number" autocapitalize="off" required="">
    					</div>
    				</div>
    				<div class="form-group">
    					<div class="input-group">
    						<div class="input-group-prepend">
    							<span class="input-group-text"><i data-feather="lock"></i></span>
    						</div>
    						<input id="loginPassword" name="password" class="form-control" type="password" placeholder="Password" required="">
    					</div>
    				</div>
    				<div class="btn-area">
    					<button type="submit" class="btn btn-primary btn-block btn-lg">로그인</button>
    				</div>
    			</div>
    			<div class="card-body d-flex align-items-end">
    				<div class="text-area w-100">
    					<div class="d-table">
    						<div class="td">
    							<a href="out_inquiry.html" class="">1:1 문의</a>
    						</div>
    						<div class="td text-right">
    							<a href="{{ URL::route('join.index')}}" class="">회원가입</a>
    						</div>
    					</div>
    				</div>

    			</div>
    		</div><!-- E://card -->
    	</form>

    </div>


    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="feather-icon/feather.js"></script>
    <script>
    $(document).ready(function(){
      // feather icons load
      feather.replace();
    });
    </script>
  </body>
</html>
