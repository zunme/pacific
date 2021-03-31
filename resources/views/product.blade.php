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

      <section class="container-md">
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

          <div class="row mt-4 product">
            @foreach ( $products as $product)
            <div class="col-md-3 px-2">
              <div class="card">
                <div class="card-body">
                  <img src="{{$product->getImageUrl() }}" alt="">
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
                      <div class="td">수수료</div>
                      <div class="td value">판매금 기준 {{$product->fee}}%</div>
                    </div>
                  </div>
                  @if($product->is_use =='Y')
                  <a href="/product/{{$product->id}}" class="btn btn-primary btn-block mt-3">{{$product->product_name}} 구매예약</a>
                  @else
                  <span class="btn btn-primary btn-block mt-3">Comming Soon</span>
                  @endif
                </div>
                @if($product->is_use !='Y')
                  <div class="over_block_div"> </div>
                @endif
              </div>

            </div>
            @endforeach
           
          </div>

          <div class="card info">
            <div class="card-body text-center">
              ※ 예약 안내 ※<br />
              1. 거래중개 수수료는 “구매자”가 아닌 “판매자＂가 부담합니다.<br />
              2. 상품은 일정 금액이 달성될 경우 랜덤으로 “분할＂되거나 “업그레이드＂됩니다.<br />
              3. 구매예약은 일일 단위로 초기화됩니다.<br />
              4. 구매예약 후 본인이 구매예약 취소는 불가능하며, 매칭 후 자동으로 취소 됩니다.<br />
            </div>
          </div>

        </div>
      </section>

    </div>


@endsection

@section('script')
@endsection