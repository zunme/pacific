@extends('layouts.default')
@section('head-script','')
@section('body-class', 'login sub')

@section('body')

    <div id="container">

@include('layouts.header')

      <section class="main-card">
        <div class="box bg-pure shadow">
          <div class="row align-items-center">
            <div class="col-md-12 link text-center">
              <a href="mypage.html">내정보</a>
              <a href="return.html">수익률</a>
              <a href="point_charge.html">{{ $siteconfig->point_name}} 관리</a>
            </div>
            <div class="col-md-12 link text-center">
              <a href="{{ URL::route('product') }}" class="big">구매예약</a>
            </div>
            <div class="col-md-12 link text-center">
              <a href="{{ URL::route('buylist')}}">구매내역</a>
              <a href="{{ URL::route('salelist')}}">판매내역</a>
              <a href="product_state.html">보유현황</a>
            </div>
            <div class="col-md-12 link text-center">
              <a href="notice.html">게시판</a>
              <a href="recommender.html">추천인</a>
            </div>
            <div class="col-12">
              <div class="form-group mb-0">
                <label for="code">추천인 코드</label>
                <div class="input-group">
                  <input id="code" type="text" class="form-control" value="{{$user->phone}}" readonly>
                  <div class="input-group-append">
                    <button class="btn btn-gray clipboard-btn" type="button" data-clipboard-target="#code">복사</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- 오늘 공지사항 -->
    <div class="modal alert-modal fade" id="today_notice" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
          <div class="modal-body">
            <button type="button" class="close" onclick="closeWin()">
              <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="modal-title mb-3">공지사항</h5>
            <p class="text-left px-3">
              그랜드오픈. 회원여러분께 안내말씀 드립니다.<br />
              Room 서비스가 새롭게 오픈했습니다.<br />
              가입시 많은 혜택을 드리고 있으니 자세한 사항은 공지사항을 참고해주세요.<br />
              감사합니다.
            </p>
          </div>
          <div class="modal-footer text-left">
            <label>
              <input id="pop_today" type="checkbox" name="accounts">
              <span class="ml-2">오늘 하루 보지않기</span>
            </label>
          </div>
        </div>
      </div>
    </div>

@endsection

@section('script')
    <script src="/js/common.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>

    <script>
    $(document).ready(function(){
      new ClipboardJS('.clipboard-btn');
    });

    //공지 팝업
    // function setCookie( name, value, expirehours ) {
    //   var todayDate = new Date();
    //   todayDate.setDate( todayDate.getDate() + expirehours ); // expiredays는 쿠키유효기간
    //   document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
    // }
    //
    // function closeWin() {
    //   if (document.getElementById("pop_today").checked){
    //     setCookie( "ncookie", "done" , 1 ); //하루동안 열지않기
    //   }
    //   $("#today_notice").modal('hide');
    // }
    //
    // cookiedata = document.cookie;
    // if (cookiedata.indexOf("ncookie=done") < 0){
    //   $("#today_notice").modal('show');
    // }
    // else {
    //   $("#today_notice").modal('hide');
    // }

    </script>
@endsection