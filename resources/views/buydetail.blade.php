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
          <h2>구매</h2>

          <div class="text-center">
            <img src="/storage/{{$data->image_url}}" alt="">
            <h3 class="mt-3 text-primary">{{$data->product_name}}</h3>
          </div>

          <div class="row mt-4">
            <div class="col-md-6">
              <div class="form-group">
                <label for="">판매자 ID</label>
                <div class="form-control">{{$data->sellerPhone}}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="">금액</label>
                <div class="form-control">{{ number_format($data->price) }}원</div>
              </div>
            </div>
            <div class="col-md-6">
              <form id="deposit_form">      
                <input type="hidden" name="trading_id" value="{{$data->id}}">
                <div class="form-group">
                  <label for="">입금증 <small class="text-danger">이미지 형식 : jpg, png, gif</small></label>
                  <div class="input-group">
                    @if (!$data->deposit_file)
                    <input type="file" name="select_img" class="form-control" accept=".gif, .jpg, .png">
                    <div class="input-group-append">
                      <button class="btn btn-gray" type="button" onClick="clickfile(this)">첨부</button>
                    </div>
                    @else
                    <div class="form-control">
                      이미 입금증을 올리셨습니다.
                    </div>
                    @endif
                  </div>
                </div>
              </form>
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
            <div class="td text-right"><button type="button" class="btn btn-lg btn-secondary" onclick="location.href='/my/buylist'">취소</button></div>
            <div class="td" style="width: 8px"></div>
            <div class="td text-left">
              @if ( $data->trading_status =='MATCHED')
              <button type="submit" class="btn btn-lg btn-primary" onClick='deposit()'>승인</button>
              @else
              
              @endif
            </div>
          </div>

        </div>    
    
    
  </section>      
</div>


@endsection

@section('script')
<script>
  function clickfile(btn){
    $(btn).closest('.input-group').children('input').trigger('click')
  }
  function deposit() {
    refreshToken( sendDeposit ) 
  }
  function sendDeposit() {
    var form = $("#deposit_form")
     $.ajax({
       url: '/my/buylist/depoist',
       method:"POST",
       data:new FormData( form[0] ),
       dataType:'JSON',
       contentType: false,
       cache: false,
       processData: false,
       success:function(res) {
         alert("입금내역을 업로드하였습니다.");
         location.reload()
       }
       , error: function ( err ){
          ajaxError(err);
       }
     });
   
     
  }
</script>
@endsection