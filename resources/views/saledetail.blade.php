@extends('layouts.default')
@section('css')
@endsection
@section('head-script','')
@section('body-class', '')

@section('body')
  <!-- Site Overlay -->
<div id="container">
@include('layouts.header')
  <section class="wrap">
  
<div class="box bg-pure shadow">
          <h2>판매</h2>

          <div class="text-center">
            <img src="/storage/{{$data->image_url}}" alt="">
            <h3 class="mt-3 text-primary">{{$data->product_name}}</h3>
          </div>

          <div class="row mt-4">
            <div class="col-md-6">
              <div class="form-group">
                <label for="">구매자 ID</label>
                <div class="form-control">{{$data->buyerPhone}}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">금액</label>
                <div class="form-control">{{ number_format($data->price) }}원</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">입금증</label>
              @if( $data->trading_status != 'MATCHED' )                
                <div class="input-group">
                  <div class="form-control">입금증파일</div>
                  <div class="input-group-append">
                    <!-- [D] 팝업창 연결됨 -->
                    <button class="btn btn-gray" type="button" data-toggle="modal" data-target="#file">첨부파일 확인</button>
                  </div>
                </div>
              @else
                <div class="form-control">입금전입니다.</div>                  
              @endif                
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">거래 시작일</label>
                <div class="form-control">{{ $data->created_at->format('Y.m.d H:i')}}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">입금 완료일</label>
                <div class="form-control">
                  @if ( $data->deposit_at )
                    {{ $data->deposit_at->format('Y.m.d H:i')}}
                  @else
                  
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">거래 완료일</label>
                <div class="form-control">
                  @if ( $data->completed_at )
                    {{ $data->completed_at->format('Y.m.d H:i')}}
                  @else
                  
                  @endif
                </div>
              </div>
            </div>
          </div><!-- row end -->
          <div class="btn-area d-table">
            @if( $data->trading_status == 'AWAITING' )
            <div class="td text-right">
              <button type="button" class="btn btn-lg btn-secondary" onclick="location.href='/my/salelist'">취소</button>
            </div>
            <div class="td" style="width: 8px"></div>
            <div class="td text-left"><button type="button" class="btn btn-lg btn-primary" onClick="completeTrading()">승인</button></div>
            @else
              <div class="td text-right"><button type="button" class="btn btn-lg btn-secondary" onclick="location.href='/my/salelist'">확인</button></div>
            @endif
          </div>

        </div>
  </section>      
</div>

    <!-- 첨부파일 확인 -->
    <div class="modal fade" id="file" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">입금증 확인</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p class="text-left px-3 text-center">
              <!-- [D] 예시 이미지 -->
              <img src="/storage/{{$data->deposit_file}}" alt="">
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
          </div>
        </div>
      </div>
    </div>

    <form id="tradeform"><input type="code" value="{{$secret}}"></form>
@endsection

@section('script')
<script>
function completeTrading(){
  if( confirm ("거래완료를 하시겠습니까?\n거래완료 후에는 취소가 불가능 합니다.") ){
    refreshToken( trade )
  }
}
  function trade() {
    var form = $("#tradeform")
     $.ajax({
       url: '/my/trade/cmpt',
       method:"POST",
       data: {code : "{{$secret}}"},
       dataType:'JSON',
       success:function(res) {
         alert("거래를 완료하였습니다.");
         location.reload()
       }
       , error: function ( err ){
          ajaxError(err);
       }
     });    
  }
</script>
@endsection