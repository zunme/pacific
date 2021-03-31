<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{ env('APP_NAME') }}</title>
    <meta name="description" content="Pacific">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    

    <!-- Pushy CSS -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/normalize.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/fonts/styles.css">
    <link rel="stylesheet" href="/font-awesome/css/all.min.css">

    <!-- feathericons -->
    <link rel="stylesheet" href="/feather-icon/feather.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @yield('css')
    @yield('head-script')
  </head>
  
  <body class="@yield('body-class','')">
    @section('header')
    <div class="site-overlay"></div>

    <header class="site-header">
      <div class="wrap justify-content-center">
        <a class="navbar-brand" href="/"></a>
      </div>
    </header>
    @show
    
    @yield('body')
    
    @section('footer')
    <footer class="site-footer">
      <div class="wrap">
        <p class="address mt-4 mb-2">
          <b class="pr-3">Pacific</b>
        </p>
        <p class="my-0 no-letter">
          Copyright &copy; Pacific. All rights reserved.
        </p>
      </div>
    </footer>
    @show
      <!-- jQuery -->
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/feather-icon/feather.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/template7/1.4.1/template7.min.js"></script>
    <script>
      $(function() {
        $.ajaxSetup({
           headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
        });
        $(document).ajaxStart( function() {
          preloader_show()
        })
        $(document).ajaxStop( function() {
          preloader_hide()
        })
      })

    function ajaxError(jqXHR ){
      if( jqXHR.status == 401){
        return loginNeedAlert();
      }else if(jqXHR.status == 403) {
        toastmessage('권한이 없습니다.');return;
      }else if(jqXHR.status != 422 && jqXHR.status != 500 && jqXHR.status !=400) {
        toastmessage('잠시후에 이용해주세요');
        return;
      }else {
        var msg ;
        var exception ;
		  
        if (jqXHR.responseJSON ) {
          msg = jqXHR.responseJSON.errors;
          exception = jqXHR.responseJSON.exception;
        }else if ( typeof jqXHR.data == "object"){
		      msg = jqXHR.data.errors;
          exception = jqXHR.data.exception;
		    }
        if(msg) {
          for(key in msg) {
            if(msg.hasOwnProperty(key)) {
              if(key.indexOf('.') < 0 ) {
                $('input[name='+key+']').focus();
              }
              if ( $.isNumeric( key )) {
                toastmessage(msg);
              } else {
                toastmessage(msg[key][0]);
              }
              break;
            }
          }
        } else if(jqXHR.responseJSON.message && jqXHR.responseJSON.message !='') {
          toastmessage(jqXHR.responseJSON.message);
        }else {
          toastmessage('시스템 오류입니다');
        }
      }
    }
      toastr.options = { "closeButton": true, "debug": false, "newestOnTop": false, "progressBar": true, "positionClass": "toast-top-right", "preventDuplicates": false, "onclick": null, "showDuration": "300", "hideDuration": "1000", "timeOut": "5000", "extendedTimeOut": "1000", "showEasing": "swing", "hideEasing": "linear", "showMethod": "fadeIn", "hideMethod": "fadeOut" }
    function toastmessage( msg ) {
      toastr.error(msg);
    }
    function preloader_show(){
      
    }
    function preloader_hide(){
      
    }

    function refreshToken( callback){
      $.ajax({
          url: "{{url('refresh-token')}}",
          type: 'get',
          dataType: 'json',
          success: function (result) {
              $('meta[name="csrf-token"]').attr('content', result.token);
            
              $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': result.token
                  }
              });
              if ( typeof callback =='function') callback()
              
          },
          error: function (xhr, status, error) {
              console.log(xhr);
          }
      }); 
    }
    </script>
    @yield('script')
    
  </body>
</html>