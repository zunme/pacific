@extends('layouts.admin')

@section('head-script')
  <style>
    .dataTables_length{display:none;}
  </style>
@endsection

@section('body')

<div class="card">
  <div class="card-body">
    <div style="padding:0 20px;">
          <div class="row" style="justify-content: space-between;">
            <div class="col-md-5">
 
            </div>
            <div class="col-md-6">
              <div class="form-row" style="justify-content:flex-end;">
                
                    <div class="form-group">
                      <select class="form-control" name="product_id" id="product_id" onChange="reloadtable()">
                        <option value='0'>상품 전체</option>
                        @foreach( $products as $product)
                        <option value='{{$product->id}}'>{{$product->product_name}}</option>
                        @endforeach
                      </select>
                    </div> 
                
                  <div class="form-group col-md-6">
                    <div class="input-group mb-2">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-search"></i></div>
                      </div>
                      <input type="text" class="form-control" id="search_keyword" placeholder="구매자 검색" onKeyUp="searchdt(event)">
                    </div>
                  </div>
              </div>
            </div>            
          </div>
    </div>
        <div class="table-responsive-">
          
          <!--div class="form-row">
            <div class="form-group col-md-2">
              <label>시작일</label>
              <input type="text" class="form-control datepicker" value="{{\Carbon\Carbon::today()->format('Y-m-d')}}" id="search_startdate">
            </div>
            
            <div class="form-group col-md-2">
              <label>종료일</label>
              <input type="text" class="form-control datepicker" value="{{\Carbon\Carbon::today()->format('Y-m-d')}}" id="search_enddate">
            </div>
            
          </div-->
          <table class="table table-striped" id="datatable">
            <thead>
              <tr>
                <th>#</th>
                <th class="text-center">구매자</th>
                <th class="text-center">상품</th>
                <th class="text-center">신청갯수</th>
                <th class="text-center">구매자 {{$siteconfig->point_name}}</th>
                
                <th class="text-center">상태</th>
                
                <th class="text-center">등록일</th>
                <th class="text-center">매칭</th>
                <th class="text-center">취소</th>
              </tr>
            </thead>
          </table>
        </div>
    
    
  </div>
</div>
@endsection

@section('script')
   
<script>
  var reserv ;
  function searchdt(e){
    if (e.keyCode == 13){
      reserv.ajax.reload(null, false);
    }
    return ;
  }
  function reloadtable() {
    reserv.ajax.reload(null, false);
  }
  
  $(document).ready(function() {
      ajaxUrl = '/adm/reservation/list'
      reserv = $('#datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthMenu": [10],
        "order": [[ 0, "desc" ]],
        "ajax": {
          'url' : ajaxUrl ,
          'data' : function (data){
            data.startDate = $("#search_startdate").val();
            data.endDate = $("#search_enddate").val();
            data.search_keyword = $("#search_keyword").val();
            data.product_id = $("#product_id").val();
          }
        },
        "columnDefs": [
          {"targets": [ 0 ],"visible": false,"searchable": false},
          {"targets": [ 1 ],"searchable": true,"sortable":true, className:'text-left'},
          {"targets": [ 2 ],"searchable": true,"sortable":true, className:'text-center'},
          {"targets": [ 3 ],"searchable": false,"sortable":true, className:'text-center'},
          {"targets": [ 4 ],"searchable": false,"sortable":true, className:'text-center'},
          {"targets": [ 5 ],"searchable": false,"sortable":false, className:'text-center'},
          {"targets": [ 6 ],"searchable": false,"sortable":false, className:'text-center'},
          {"targets": [ 7 ],"searchable": false,"sortable":false, className:'text-center'},
          {"targets": [ 8 ],"searchable": false,"sortable":false, className:'text-center'},
        ],
        "columns" : [
          {"data" : "id"},
          {"data" : "phone"},
          {"data" : "product_name"},
          {"data" : "amount"},
          {"data" : "point"},
          {"data" : "reservation_status",
             "render": function( data, type, row, meta) {
               return '예약중'
             }
          },
          {"data" : "created_at",
               "render": function( data, type, row, meta) {
                   var date = moment( data );
                   return date.local().format('Y-MM-DD')
               }
          },
          {"data": "id",
             "render": function( data, type, row, meta) {
               return `
<!--<button class="btn btn-sm btn-primary">랜덤</button>
<button class="btn btn-sm btn-success">유저</button>-->
<button class="btn btn-sm btn-dark" onClick="matching_admin( ${data} )">관리자</button>
                `
             }
          },
          {"data": "id",
             "render": function( data, type, row, meta) {
               return `
<button class="btn btn-sm btn-danger" onClick="cancel( ${data} )">취소</button>
                `
             }
          }          
        ],
        "initComplete": function(settings, json) {
            $("#datatable_filter").hide();
            var textBox = $('#datatable_filter label input');
            textBox.unbind();
            textBox.bind('keyup input', function(e) {
                if(e.keyCode == 8 && !textBox.val() || e.keyCode == 46 && !textBox.val()) {
                    // do nothing ¯\_(ツ)_/¯
                } else if(e.keyCode == 13 || !textBox.val()) {
                    reserv.search(this.value).draw();
                }
            });
        },
              
      });
    });
  
  // 관리자 매칭
  function matching_admin(reserve_id){
    var url ="/adm/api/matching/adm"
      $.ajax({
        url : url,
        method: 'POST',
        data : {reserve_id : reserve_id},
        dataType:'JSON',
        success : function(res){
          toastmessage('매칭하였습니다')
          reserv.ajax.reload(null, false);
        },
        error: function ( err ){
          ajaxError(err);
        }
      }) 
  }
  function cancel(reserve_id){
    var url ="/adm/api/matching/cancel_reservation"
      $.ajax({
        url : url,
        method: 'POST',
        data : {reserve_id : reserve_id},
        dataType:'JSON',
        success : function(res){
          toastmessage('구매예약을 취소하였습니다')
          reserv.ajax.reload(null, false);
        },
        error: function ( err ){
          ajaxError(err);
        }
      })     
  }
</script>
@endsection