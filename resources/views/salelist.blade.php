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
          <h2>판매현황</h2>

          <div class="row mt-4">
            <div class="col-md-4">
              <div class="form-group">
                <label for="">보유 {{$siteconfig->point_name}}</label>
                <div class="form-control">{{$user->point}}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="">판매 필요 {{$siteconfig->point_name}}</label>
                <div class="form-control">{{$saleNeedPoint->need_point}}</div>
              </div>  
            </div>
          </div>
       

          <ul class="row history-product">
            @foreach( $products as $product )
            <li class="col-md-6">
              <div class="card">
                <div class="card-header text-center bg-primary text-white">
                  <span class="title">{{$product->product_name}}</span>
                </div>
                <div class="d-table">
                  <div class="td img-area">
                    <img src="/storage/{{$product->image_url}}" alt="" class="d-block img">
                  </div>
                  <div class="td info-area align-middle">
                    <ul class="d-table">
                      <li class="tr">
                        <span class="td label">판매신청</span>
                        <span class="td value">{{$product->reserv}}</span>
                      </li>
                      <li class="tr">
                        <span class="td label">입금대기</span>
                        <span class="td value">{{$product->matched}}</span>
                      </li>
                      <li class="tr">
                        <span class="td label">승인대기</span>
                        <span class="td value">{{$product->waiting}}</span>
                      </li>
                      <li class="tr">
                        <span class="td label">구매완료</span>
                        <span class="td value">{{$product->completed}}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </li>
            @endforeach



          </ul>

          <h2 class="mt-4">판매내역</h2>
          
          <div id="history_wrap">          

          </div>
          
        </div>
      </section>      

    </div>


@endsection

@section('script')
<script>
  function getlist(pageurl){
      $.ajax({
        url : pageurl,
        method: 'get',
        //data : {page:page },
        dataType:'html',
        success : function(res){
          $("#history_wrap").html( res)
        },
        error: function ( err ){
          
        }
      })       

  }
  $("document").ready( function() {
    getlist('/my/salelist/history');
  })
</script>
@endsection