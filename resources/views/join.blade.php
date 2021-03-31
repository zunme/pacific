@extends('layouts.default')
@section('head-script','')
@section('body-class', 'login sub')

@section('header','')
@section('body')
    <div class="card shadow">
      <div class="card-body">
        <div class="d-flex justify-content-center">
          <a class="logo" href="/" title="홈으로"></a>
        </div>
        <h1>회원가입</h1>

        <form name="signup" id="signup">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="phone">휴대폰 <!--인증--> <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input id="phone" type="number" name="phone" class="form-control" required placeholder="휴대폰 번호(숫자만)" pattern="[0-9]*">
                  
          <!--
                  TODO : 인증버튼 
                  <div class="input-group-append">
                    <button class="btn btn-gray" type="button" data-toggle="modal" data-target="#code-send">인증</button>
                  </div>
          -->
                  
                </div>
              </div>
            </div>
            <!-- TODO 인증버튼
            <div class="col-md-6">
              <div class="form-group">
                <label for="phone">인증번호 입력 <span class="text-danger">*</span></label>
                <div class="position-relative">
                  <input id="sms_code" type="number" class="form-control" required placeholder="인증코드 6자리">
                  <span class="sms-time text-danger">3:00</span>
                </div>
              </div>
            </div>
            -->
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="password">비밀번호 <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input id="password" type="password" name="password" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="password_check">비밀번호 확인 <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input id="password_check" type="password" name="password_confirmation" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="bank_user">예금주</label>
                <div class="input-group">
                  <input id="bank_user" type="text" name="name" class="form-control">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="bank">은행</label>
                <div class="input-group">
                  <select id="bank" name="bank_name" class="form-control">
                    <option value="" selected disabled>은행명을 선택하세요</option>
                    @foreach ( $banklist as $bank)
                    <option value="{{$bank}}">{{$bank}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="bank_number">계좌번호</label>
                <input id="bank_number" type="number" name="bank_account" class="form-control" pattern="\d*" placeholder="숫자만 입력하세요">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">추천인 코드</label>
                <input type="text" name="recommender" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="custom-control custom-checkbox mb-2 was-validated">
                <input type="checkbox" class="custom-control-input" id="agree_privacy_2" name="agree_privacy" value="Y" required>
                <label class="custom-control-label" onclick="check_privacy()"><a href="#" class="link" data-toggle="modal" data-target="#privacy">개인정보처리방침</a>을 확인하고 동의합니다.</label>
                <div class="text-gray text-sm">약관을 끝까지 읽어주세요.</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="custom-control custom-checkbox mb-3 was-validated">
                <input type="checkbox" class="custom-control-input" id="agree_terms_2" name="agree_terms" value="Y" required>
                <label class="custom-control-label" onclick="check_terms()"><a href="#"  class="link" data-toggle="modal" data-target="#terms">이용약관</a>을 확인하고 동의합니다.</label>
                <div class="text-gray text-sm">약관을 끝까지 읽어주세요.</div>
              </div>
            </div>
            <div class="col-md-6"></div>
          </div>

          <div class="btn-area text-center">
            <div class="row">
              <div class="col-md-6">
                <a type="button" class="btn btn-lg btn-secondary btn-block" href="{{ URL::route('login') }}">로그인 하러 가기</a>
              </div>
              <div class="col-md-6">
                <button type="submit" class="btn btn-lg btn-primary btn-block">회원가입</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- sms전송완료 팝업 -->
    <div class="modal alert-modal fade" id="code-send" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
          <div class="modal-body">
            <h5 class="modal-title">인증코드 전송</h5>
            <p class="text">인증코드를 전송했습니다.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 회원가입 완료 팝업 -->
    <div class="modal alert-modal fade" id="join" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
          <div class="modal-body">
            <h5 class="modal-title">회원가입</h5>
            <p class="text">회원가입이 완료되었습니다.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal" onClick="location.replace('{{ URL::route('login') }}')">확인</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 개인정보처리방침 팝업 -->
    <div class="modal fade" id="privacy" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">개인정보처리방침</h5>
          </div>
          <div class="modal-body">
            <p class="text">처리방침</p>

            <hr>
            <div class="custom-control custom-checkbox mb-2 was-validated">
              <input type="checkbox" class="custom-control-input" id="agree_privacy" required>
              <label class="custom-control-label" for="agree_privacy">개인정보처리방침을 확인하고 동의합니다.</label>
              <div class="invalid-feedback">꼭 내용을 확인하시고 동의하세요.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">확인</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 이용약관 팝업 -->
    <div class="modal fade" id="terms" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">이용약관</h5>
          </div>
          <div class="modal-body">
            <p class="text">이용약관</p>

            <hr>
            <div class="custom-control custom-checkbox mb-2 was-validated">
              <input type="checkbox" class="custom-control-input" id="agree_terms" required>
              <label class="custom-control-label" for="agree_terms">이용약관을 확인하고 동의합니다.</label>
              <div class="invalid-feedback">꼭 내용을 확인하시고 동의하세요.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">확인</button>
          </div>
        </div>
      </div>
    </div><!-- E:// 이용약관 -->
@endsection

@section('footer','')

@section('script')
    <script>
    $(document).ready(function(){
      // feather icons load
      feather.replace();

      $("form[name='signup']").on('submit', function(e){
        e.preventDefault();
        join();
        //$("#join").modal('show'); //회원가입 완료팝업
      });

      // 개인정보처리방침 동의
      $('#agree_privacy').on('change', function(){
        if ($(this).is(':checked')) {
          $('#agree_privacy_2').prop("checked", true);
          console.log('check');
        } else {
          $('#agree_privacy_2').prop("checked", false);
        }
      });

      // 이용약관 동의
      $('#agree_terms').on('change', function(){
        if ($(this).is(':checked')) {
          $('#agree_terms_2').prop("checked", true);
          console.log('check');
        } else {
          $('#agree_terms_2').prop("checked", false);
        }
      });
    });
    function join() {
      var data = $("#signup").serialize();
      $.ajax({
        url : "{{URL::route('join.index')}}",
        method: 'POST',
        data : data,
        dataType:'JSON',
        success : function(res){
          $("#join").modal('show');
        },
        error: function ( err ){
          ajaxError(err);
        }
             
      })
    }
    function check_privacy(){
      $('#privacy').modal();
    };

    function check_terms(){
      $('#terms').modal();
    };
    </script>
@endsection