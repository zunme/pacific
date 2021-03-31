<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ env('APP_NAME') }} ADMIN</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- CSS Libraries -->
 <link rel="stylesheet" href="/stisla/node_modules/bootstrap-daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="/stisla/node_modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
  <link rel="stylesheet" href="/stisla/node_modules/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="/stisla/node_modules/selectric/public/selectric.css">
  <link rel="stylesheet" href="/stisla/node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
  <link rel="stylesheet" href="/stisla/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
  <link rel="stylesheet" href="/stisla/node_modules/izitoast/dist/css/iziToast.min.css">
  
  <link rel="stylesheet" href="/stisla/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="/stisla/node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css">
  
  <!-- Template CSS -->
  <link rel="stylesheet" href="/stisla/assets/css/style.css">
  <link rel="stylesheet" href="/stisla/assets/css/components.css">
  
  <link rel="stylesheet" href="/plugins/ztreev3/css/zTreeStyle/zTreeStyle.css" />
  
  <style>
    .navbar-bg{
      height: 64px !important;
      margin-bottom:10px;
    }
    .overlayloading{
        position: fixed;
        height: 100vw;
        width: 100vw;
        z-index: 100000;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: rgb(153 153 153 / 30%);
        dis
    }
    .border-none{
      border:none !important;
    }
    .border-bg-none{
      border:none !important;
      background:none !important;
    }
    .bg-none{
      background:none !important;
    }
    .padding-left-none{
      padding-left: 0 !important;
    }
    .color-red{color:red !important;}
    
     .overlayloading > i{
      font-size: 30px;
      margin-top: 40%;
      margin-left: 50%;
      transform: translate(-50%, -50%);
    }
    .flex-right{
      display: flex;
      justify-content: flex-end;
    }
    .swal-title{
      font-size:22px !important;
    }
    select[name="datatable_length"]{
      width:70px !important;
    }
    
    .ml-10{ margin-left:10px !important;}
    .mr-10{ margin-right:10px !important;}
    .ml-40{ margin-left:40px !important;}
    .mr-40{ margin-right:40px !important;}
  </style>
    @yield('css')
    @yield('head-script')  
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>

        </form>
        <ul class="navbar-nav navbar-right">

          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="/stisla/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">Hi, {{ env('APP_NAME') }} ADMIN</div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <a href="/admin/logout" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="index.html">{{ env('APP_NAME') }}</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">St</a>
          </div>
          <ul class="sidebar-menu">
              <li class="menu-header">설정</li>
              <li><a class="nav-link" href="{{ URL::route('admin.config') }}"><i class="far fa-square"></i> <span>기본설정</span></a></li>
              <li><a class="nav-link" href="{{ URL::route('admin.product.config') }}"><i class="far fa-square"></i> <span>상품설정</span></a></li>
            
              <li class="menu-header">회원관리</li>
              <li><a class="nav-link" href="{{ URL::route('admin.member') }}"><i class="far fa-square"></i> <span>회원리스트</span></a></li>
            
            
              <li class="menu-header">상품관리</li>
              <li><a class="nav-link" href="{{ URL::route('admin.reservation') }}"><i class="far fa-square"></i> <span>매칭대기리스트</span></a></li>
            
            <!--
              <li class="menu-header">Starter</li>
              <li class="nav-item dropdown active">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i> <span>Layout</span></a>
                <ul class="dropdown-menu">
                  <li class="active"><a class="nav-link" href="layout-default.html">Default Layout</a></li>
                  <li><a class="nav-link" href="layout-transparent.html">Transparent Sidebar</a></li>
                  <li><a class="nav-link" href="layout-top-navigation.html">Top Navigation</a></li>
                </ul>
              </li>
              <li><a class="nav-link" href="blank.html"><i class="far fa-square"></i> <span>Blank Page</span></a></li>
              -->
            </ul>

            <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
              <a href="https://getstisla.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-rocket"></i> Documentation
              </a>
            </div>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
@yield('body')
        </section>
      </div>
      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; 2021 <div class="bullet"></div>{{ env('APP_NAME') }}
        </div>
        <div class="footer-right">
          1.0.0
        </div>
      </footer>
    </div>
  </div>
  
  <div class="overlayloading" id="ajaxloading" style="display:none;"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>
    <div class="modal fade" id="modal-sm" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" id="modal-sm-area">
    </div>
  </div>
  <div class="modal fade" id="modal-lg" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" id="modal-lg-area">
    </div>
  </div>

  <div class="modal fade" id="modal-xl" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" id="modal-xl-area">

    </div>
  </div>

  <div class="modal fade" id="modal-default" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" id="modal-default-area">
    </div>
  </div>
  
  <div class="modal fade" id="modal-tree" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" id="modal-tree-area">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">추천인 리스트 </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>    
        </div>
        <div class="modal-body">
           <ul id="treeDemo" class="ztree"></ul>
        </div>
      </div>      
    </div>
  </div>
  
  <!-- General JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="/stisla/assets/js/stisla.js"></script>
  
  <script src="/stisla/node_modules/bootstrap-daterangepicker/daterangepicker.js"></script>
  <script src="/stisla/node_modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
  <script src="/stisla/node_modules/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
  <script src="/stisla/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
  <script src="/stisla/node_modules/select2/dist/js/select2.full.min.js"></script>
  <script src="/stisla/node_modules/selectric/public/jquery.selectric.min.js"></script>
  <script src="/stisla/node_modules/izitoast/dist/js/iziToast.min.js"></script>
  <script src="/stisla/node_modules/sweetalert/dist/sweetalert.min.js"></script>
  
  <script src="/stisla/node_modules/datatables/media/js/jquery.dataTables.min.js"></script>
  <script src="/stisla/node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="/stisla/node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js"></script>
  
  <!-- JS Libraies -->

  <!-- Template JS File -->
  <script src="/stisla/assets/js/scripts.js"></script>
  <script src="/stisla/assets/js/custom.js"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/template7/1.4.1/template7.min.js"></script>
  <script src="/plugins/ztreev3/js/jquery.ztree.all.min.js"></script>
  
  <script>
    moment.locale("ko");
    var zTreeObj;
    $(function() {
      $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      $(document).ajaxStart( function() {
        $("#ajaxloading").show();
      })
      $(document).ajaxStop( function() {
        $("#ajaxloading").hide();
        console.log ( "end")
      })
    });
  function pop_tpl(size, id, data) {
      if (typeof id == 'undefined') return false;
      var availsize = ['sm', 'lg', 'xl']
      if (!availsize.includes(size)) size = 'default';
    
      //var template = Handlebars.compile($("#" + id).html());
      var template = Template7.compile($("#" + id).html());
    
      $("#modal-" + size + "-area").html(template(data));

      $("#modal-" + size).modal('handleUpdate')
      $("#modal-" + size).modal('show')
  }
    
function toastmessage(msg){
          iziToast.info({
            message: msg,
            position: 'topRight'
          }); 
}
function ajaxError(jqXHR) {

  $('.loading_wrap').hide();

  if(jqXHR.status != 422 && jqXHR.status != 500 ) {
    
          iziToast.error({
            message: '잠시후에 이용해주세요',
            position: 'topRight'
          });    
      console.log ( jqXHR  )
      return;
  }

  var msg ;
  var exception ;
  if (jqXHR.responseJSON ) {
    msg = jqXHR.responseJSON.errors;
    exception = jqXHR.responseJSON.exception;
  }

    if(msg) {
      for(key in msg) {
      if(msg.hasOwnProperty(key)) {
        if(key.indexOf('.') < 0 ) {
          $('input[name='+key+']').focus();
        }

        if ( $.isNumeric( key )) {
          iziToast.error({
            message: msg,
            position: 'topRight'
          });
        } else {
          iziToast.error({
            message: msg[key][0],
            position: 'topRight'
          });
        }
        break;
      }
    }
    } else if(jqXHR.status == 422 && jqXHR.responseJSON.message){
       iziToast.error({
        message: jqXHR.responseJSON.message,
        position: 'topRight'
      });         
    }else {
      iziToast.error({
        message: '시스템 오류입니다',
        position: 'topRight'
      });
    }
}
function default_form_prc(info) {
  var msg = ( typeof info.msg =='undefined') ? '정상적으로 처리되었습니다.' : info.msg;
  $.ajax({
     url:info.url,
     method:"POST",
     data:new FormData( document.getElementById(info.form) ),
     dataType:'JSON',
     contentType: false,
     cache: false,
     processData: false,
     success:function(res)
     {
       if( res.result =='Error'){
         iziToast.error({
           message: res.msg,
           position: 'topRight'
         });
         return;
       } else {
         iziToast.success({
           message: msg,
           position: 'topRight'
         });
       }
       if( typeof info.reload !='undefined')   {
		   if ( info.reload=="self"){
			   location.reload();
		   }else info.reload.ajax.reload(null, false);
	   }
      $('.modal.show').modal('hide');
    },
     error: function ( err ){
       ajaxError(err)
     }
   });
}
function default_form_delete( info ){
  let title='';
  if (typeof info.title != 'undefined') title = `[${info.title}] 을(를) 삭제합니다.`;
  swal({
      title: '삭제하시겠습니까?',
      text : title,
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
        $.ajax({
           url: info.url,
           method:"POST",
           data:{delete_id: info.id },
           dataType:'JSON',
           success:function(res)
           {
             if( res.result =='Error'){
               iziToast.error({
                 message: res.msg,
                 position: 'topRight'
               });
               return;
             } else {
               iziToast.success({
                 message: '삭제되었습니다.',
                 position: 'topRight'
               });
             }
             if( typeof info.reload !='undefined')   info.reload.ajax.reload(null, false);
          },
           error: function ( err ){
             ajaxError(err)
           }
         });
      } else {
      swal('취소되었습니다.');
      }
    });

}
function readURL(input, imgid) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#'+imgid).attr('src', e.target.result);
            $('#'+imgid).show();
            $("#card-noneimg").hide();

        }
        reader.readAsDataURL(input.files[0]);
    }else {
      $('#'+imgid).hide();
      $("#card-noneimg").show();
    }
}    
  </script>
  <!-- Page Specific JS File -->
  @yield('script')
</body>
</html>
