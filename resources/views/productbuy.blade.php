@extends('layouts.default')
@section('css')
<style>
  .over_block_div{
        position: absolute;
    z-index: 1;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgb(0 0 0 / 26%);
    border-radius: 5px;
  }
</style>
@endsection
@section('head-script','')
@section('body-class', '')

@section('body')
  <!-- Site Overlay -->

    <div id="container">

    @include('layouts.header')

      <section class="wrap">
        <div class="box bg-pure shadow">
          <h2>구매 예약</h2>

          <div class="row mt-4">
            <div class="col-md-12">
              <div class="form-group">
                <label for="">보유 {{$siteconfig->point_name}}</label>
                <div class="form-control">{{$user->point}}</div>
              </div>
            </div>
          </div>
          
          <form>
          <input type="hidden" name="prd_id" value="{{$product->id}}">
          <div class="row mt-4 product justify-content-center">
            <div class="col-md-5 px-2">
              <div class="card">
                <div class="card-body">
                  <img src="{{$product->getImageUrl() }}" alt="" class="d-block m-auto">
                  <h3 class="mt-3 text-center text-primary">{{$product->product_name}}</h3>
                  <div class="d-table">
                    <div class="tr">
                      <div class="td">가격</div>
                      <div class="td value">{{ number_format($product->price) }} ~ ∞</div>
                    </div>
                    <div class="tr">
                      <div class="td">수익률</div>
                      <div class="td value">{{$product->profit_rate}}%</div>
                    </div>
                    <div class="tr">
                      <div class="td">보유기간</div>
                      <div class="td value">{{$product->period}}일</div>
                    </div>
                    <div class="tr">
                      <div class="td">구매예약 수량</div>
                      <div class="td value">
                        <input id="product_amt" type="number" class="form-control" name="product_amount" min="1" max="{{ $siteconfig->max_trading}}" pattern="\d*" placeholder="숫자만 입력하세요" onkeyup="checkMaxPrdAmt(this)">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </form>
          <div class="btn-area d-table">
            <div class="td text-right"><button type="button" class="btn btn-lg btn-secondary" onclick="location.href='/product'">취소</button></div>
            <div class="td" style="width: 8px"></div>
            <div class="td text-left"><button type="button" class="btn btn-lg btn-primary" onClick="reservation()">구매예약</button></div>
          </div>

        </div>
      </section>

    </div>


@endsection

@section('script')
<script>
  var productname="{{$product->product_name}}"
  function checkMaxPrdAmt(inp){
    var num = $(inp).val()
    res = parseInt( num.replace(/[^0-9]/g,"") )
    if ( res != num ) console.log ( "dfff")
    console.log (res)
    if( res > {{ $siteconfig->max_trading}} ) {
      console.log( res,  {{ $siteconfig->max_trading}} )
      $(inp).val( "{{ $siteconfig->max_trading}}" )
    }
  }
  function reservation() {
    var num = $("#product_amt").val()
    if ( num =='') {
      alert( "구매 수량을 입력해주세요")
      return;
    }        
    res = parseInt( num.replace(/[^0-9]/g,"") )
    if ( res != num ) {
      alert( "구매수량에는 숫자만 입력해주세요")
      return;
    }
    else if( res > {{ $siteconfig->max_trading}} ) {
      alert( "최대 구매수량운 {{$siteconfig->max_trading}}개 입니다. ")
      return;
    }
    else if ( res < 1) {
      alert( "구매 수량을 입력해주세요")
      return;
    }
    if( confirm( productname+ " "+ res + " 개를 구매 예약하시겠습니까?") ){
      $.ajax({
        url : '/frontapi/reservation',
        method: 'POST',
        data : {product_id : '{{$product->id}}', amount: $("#product_amt").val() },
        dataType:'JSON',
        success : function(res){
          alert("구매 예약을 하셨습니다.")
          location.replace('/my/buylist')
          console.log (res)
        },
        error: function ( err ){
          ajaxError(err);
        }
      })  
    }

    
  }
</script>
@endsection