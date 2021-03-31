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
  .dataTables_length{display:none;}
</style>
@endsection

@section('body')

<div class="card">
  <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="datatable">
            <thead>
              <tr>
                <th>#</th>
                <th class="text-center">상품명</th>
                <th class="text-center">사용여부</th>
                <th class="text-center">최소보유기간</th>
                <th class="text-center">수익률(%)</th>
                <th class="text-center">수수료(%)</th>
                <th class="text-center">시작가</th>
                <th class="text-center"></th>
              </tr>
            </thead>
          </table>
        </div>
    
@endsection

@section('script')
<script id="formmodal" type="text/template">
      @verbatim
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title">수정</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>    
  </div>
  <div class="modal-body">
    <form id="addform">
      <input type="hidden" name="id" value="{{id}}">
      
      <div class="row">
        <div class="col-2 input-group-text border-none">
          상품명
        </div>
        <div class="col-4">
          <div class="form-group">
            <input type="text" class="form-control" name="product_name" value="{{product_name}}">
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-2 input-group-text border-none">
          사용여부
        </div>
        <div class="col-4">
          <div class="form-group">
            <select class="form-control" name="is_use" {{#js_if " this.is_use === 'Y' "}} Disabled {{/js_if}} >
              <option value="N" {{#js_if " this.is_use === 'N' "}} selected {{/js_if}} >사용안함</option>
              <option value="R" {{#js_if " this.is_use === 'R' "}} selected {{/js_if}} >오픈예정</option>
              <option value="Y" {{#js_if " this.is_use === 'Y' "}} selected {{/js_if}} >오픈</option>
            </select>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-2 input-group-text border-none">
          보유기간
        </div>
        <div class="col-2 col-md-2 col-sm-3">
          <div class="form-group">
            <input type="text" class="form-control" name="period" value="{{period}}">
          </div>
        </div>
        <div class="col-1 input-group-text border-bg-none padding-left-none">
          일
        </div>
      </div>
      
      <div class="row">
        <div class="col-2 input-group-text border-none">
          수익률
        </div>
        <div class="col-2 col-md-2 col-sm-3">
          <div class="form-group">
            <input type="text" class="form-control" name="profit_rate" value="{{profit_rate}}">
          </div>
        </div>
        <div class="col-1 input-group-text border-bg-none padding-left-none">
          %
        </div>
      </div>
      
      <div class="row">
        <div class="col-2 input-group-text border-none">
          수수료
        </div>
        <div class="col-2 col-md-2 col-sm-3">
          <div class="form-group">
            <input type="text" class="form-control" name="fee" value="{{fee}}">
          </div>
        </div>
        <div class="col-1 input-group-text border-bg-none padding-left-none">
          %
        </div>
      </div>
      
      <div class="row">
        <div class="col-2 input-group-text border-none">
          시작가
        </div>
        <div class="col-2 col-md-2 col-sm-3">
          <div class="form-group">
            <input type="text" class="form-control" name="price" value="{{price}}">
          </div>
        </div>
        <div class="col-1 input-group-text border-bg-none padding-left-none">
          원
        </div>
      </div>

      <div class="row">
        <div class="col-2 input-group-text border-none">
          상품이미지
        </div>
        <div class="col-6 col-md-6 col-sm-6">
        
          <div class="form-group">
              <input type="file" name="select_img" class="form-control" onChange="readURL(this, 'previewimage')">
          </div>        
        </div>
        <div class="col-4">
            <img src = '{{#if image_url }}/storage/{{image_url}}{{/if}}' style="max-width:100px;max-height:100px; max-width:100px;"  id="previewimage">
        </div>
      </div>
      
    </form>
  </div>
      <div class="modal-footer bg-whitesmoke br">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary" onClick="{{#if id}}
        default_form_prc({url:'/adm/api/product/save',form:'addform',reload:dt,'msg' : '수정되었습니다.'})
      {{else}}
        default_form_prc({url:'/adm/api/product/save',form:'addform',reload:dt})
      {{/if}}">{{#if id}}저장{{else}}생성{{/if}}</button>
    </div>
</div>
      @endverbatim
</script>
    
  <script>
    var dt;
    function edit ( btn ){
      var data =  dt.row($(btn).closest('tr')).data();

      pop_tpl('lg','formmodal' , data)
    }
    
    $(document).ready(function() {
      dt = $('#datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthMenu": [10],
        "order": [[ 0, "desc" ]],
        "ajax": {
          'url' : "{{ URL::route('admin.product.list') }}",
          'data' : function (data){
          }
        },
        "columnDefs": [
            {"targets": [ 0 ],"visible": false,"searchable": false},
            {"targets": [ 2 ],"searchable": false,"sortable":false},
            {"targets": [ 3 ],"searchable": false,"sortable":false},
            {"targets": [ 4 ],"searchable": false,"sortable":false},
            {"targets": [ 5 ],"searchable": false,"sortable":false},
            {"targets": [ 6 ],"searchable": false,"sortable":false},
            {"targets": [ 7 ],"searchable": false,"sortable":false},
        ],
        "columns" : [
          {"data" : "id"},
          {"data" : "product_name"},
          {"data" : "is_use", 
           'render' : function( data, type, row, meta) {
             var str = '';
             if( data == 'N') str ='사용안함';
             else if( data == 'R') str ='오픈예정';
             else str ='오픈';
             return str
            }},
          {"data" : "period"},
          {"data" : "profit_rate"},
          {"data" : "fee"},
          {"data" : "price"},
          {"data" : "id", 
           'render' : function( data, type, row, meta) {
             return `<button class="btn btn-sm btn-primary" onClick='edit(this)'>수정</button>`
            }
          },
        ],
        "initComplete": function(settings, json) {
            var textBox = $('#datatable_filter label input');
            textBox.unbind();
            textBox.bind('keyup input', function(e) {
                if(e.keyCode == 8 && !textBox.val() || e.keyCode == 46 && !textBox.val()) {
                    // do nothing ¯\_(ツ)_/¯
                } else if(e.keyCode == 13 || !textBox.val()) {
                    dt.search(this.value).draw();
                }
            });
        },
              
      });
    });
  </script>
@endsection