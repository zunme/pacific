@extends('layouts.admin')

@section('head-script')
<style>
  .form-middle-label{
    display: flex;
    justify-content: center;
    align-content: center;
    height: 42px;
    text-align: center;
    font-size: 26px;
  }

</style>
@endsection

@section('body')
<form id="siteconfig_form">
  

<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label>포인트 이름</label>
          <input type="text" class="form-control" name="point_name"  value="{{$siteconfig->point_name}}">
        </div>    
      </div>
      
      <div class="col-6">
        <div class="form-group">
          <label>포인트 가격</label>
          <input type="text" class="form-control" name="price_per_point"  value="{{$siteconfig->price_per_point}}">
        </div>    
      </div>
      
    </div>
  </div>
</div>
  
<div class="card">
  <div class="card-body">
    <label>거래가능시간</label>
    <div class="row">
      
      <div class="col-3">
        <div class="form-group">
          <div class="input-group">
            <div class="input-group-prepend">
              <div class="input-group-text">
                <i class="fas fa-clock"></i>
              </div>
            </div>
            <input type="text" class="form-control timepicker" name="start_trade" value="{{$siteconfig->start_trade}}">
          </div>
        </div>        
      </div>
      <div class="col-1">
        <div class="form-middle-label">
          ~
        </div>
      </div>
      <div class="col-3">
        <div class="form-group">
          <div class="input-group">
            <div class="input-group-prepend">
              <div class="input-group-text">
                <i class="fas fa-clock"></i>
              </div>
            </div>
            <input type="text" class="form-control timepicker" name="end_trade" value="{{$siteconfig->end_trade}}">
          </div>
        </div>        
      </div>

    </div>
    
    
    <div class="row">
      <div class="col-3">
        <label>최대 구매수량</label>
        <div class="form-group">
          <input type="text" class="form-control" name="max_trading"  value="{{$siteconfig->max_trading}}">
        </div>         
      </div>
      
      <div class="col-3">
        <label>1일 구매횟수제한 - 0 : 제한없음</label>
        <div class="form-group">
          <input type="text" class="form-control" name="trading_limit"  value="{{$siteconfig->trading_limit}}">
        </div>         
      </div>
      
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row">
      
      <div class="col-3">
        <div class="form-group">
          <label>판매자 패널티 </label>
          <input type="text" class="form-control" name="penalty_sale"  value="{{$siteconfig->penalty_sale}}">
        </div>    
      </div>
      
      <div class="col-3">
        <div class="form-group">
          <label>구매자 패널티 </label>
          <input type="text" class="form-control" name="penalty_purchase"  value="{{$siteconfig->penalty_purchase}}">
        </div>    
      </div>
      
    </div>
  </div>
</div>
  
</form>
<div class="flex-right">
  <span class="btn btn-danger" onclick="save()">저장</span>
</div>
@endsection


@section('script')
<script>
  $("document").ready( function() {})
function save_(){
  swal({
      title: '저장하시겠습니까?',
      //text: 'Once deleted, you will not be able to recover this imaginary file!',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
      
      } else {
      
      }
    });
}
  function save(){
      $.ajax({
        url : "/adm/api/config/save",
        method: 'POST',
        data : $("#siteconfig_form").serialize(),
        dataType:'JSON',
        success : function(res){
          toastmessage('저장하였습니다')
        },
        error: function ( err ){
          ajaxError(err);
        }
             
      })
  }
</script>
@endsection