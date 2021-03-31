$(document).ready(function(){

  // feathericons load
  feather.replace();

  // main nav script
  var nav = $('#main-nav'),
  siteOverlay = $('.site-overlay');
  $('.navbar-toggler').click(function(){
    if( nav.hasClass('show') ){
      siteOverlay.removeClass('show');
    } else {
      siteOverlay.addClass('show');
    }
  });

  //배경 클릭시 메뉴닫기
  siteOverlay.on('click', function(){
    $(this).removeClass('show');
    $('.navbar-collapse').removeClass('show');
  });

  // 당첨자확인 공지
  var roll_count = $('.roll-list li').length;
  var roll_height = $('.roll-list li').height();
  function roll_step(index) {
      $('.roll-list ul').delay(2200).animate({
          top: -roll_height * index,
      }, 500, function() {
          roll_step((index + 1) % roll_count);
      });
  }
  roll_step(1);

  // 헤더 알람 슬림스크롤
  // $('.alarm .list-group').slimScroll({
  //   height : '400px'
  // });

  // 메뉴 드롭다운
  var $dropdown = $(".navbar-nav .dropdown");
  var $dropdownToggle = $(".dropdown-toggle");
  var $dropdownMenu = $(".dropdown-menu");
  var showClass = "show";

  $(window).on("load resize", function() {
    if (this.matchMedia("(min-width: 768px)").matches) {
      $dropdown.hover(
        function() {
          var $this = $(this);
          $this.addClass(showClass);
          $this.find($dropdownToggle).attr("aria-expanded", "true");
          $this.find($dropdownMenu).addClass(showClass);
        },
        function() {
          var $this = $(this);
          $this.removeClass(showClass);
          $this.find($dropdownToggle).attr("aria-expanded", "false");
          $this.find($dropdownMenu).removeClass(showClass);
        }
      );
    } else {
      $dropdown.off("mouseenter mouseleave");
    }
  });

  // multiple modal
  $(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
      $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
  });

  // multiple modal Scrollbar fix
  $(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
  });


  // 페이지 인식 소스
  function page_location(){
    // 서버용
    // var pageLocation = document.location.pathname || "";
    // console.log(pageLocation);

    var pageLocation = String(document.location).split('/').pop();
    // Remove unnecessary pageLocation parts
    pageLocation = pageLocation.replace(/(\.html).*/i, '$1');

    // if (pageLocation == '/info/password' || pageLocation == '/info/profile') {
    //   // info subpage경우 (메뉴없음)
    //   pageLocation = '/info/';
    // }
    // console.log(pageLocation);

    // Activate current nav item
    $('.nav-item').find("li a[href='" + pageLocation +"']").closest('.nav-item').addClass('on');
  }page_location();

  // datepikcer language
  if ($('.datepicker').length > 0) {
    $('.datepicker').datepicker({
      language : 'ko',
      todayHighlight: true
    });
  }

});
