@extends('layouts.admin')

@section('head-script')
  <style>
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
                <th class="text-center">전화번호</th>
                <th class="text-center">은행</th>
                <th class="text-center">예금주</th>
                <th class="text-center">계좌번호</th>
                <th class="text-center">{{$siteconfig->point_name}}</th>
                
                <th class="text-center">계정잠김</th>
                
                <th class="text-center">판매자페널티</th>
                <th class="text-center">구매자패널티</th>
                
                <th class="text-center">추천인</th>
                <th class="text-center"></th>
              </tr>
            </thead>
          </table>
        </div>
  </div>
</div>
@endsection

@section('script')
<script id="formmodal" type="text/template">
  @include('admin.poptpl.member_edit')
</script>
    
  <script>
    var usertable;
    var ajaxUrl = "{{ URL::route('admin.member.list') }}";
    
    function edit ( btn ){
      var data =  usertable.row($(btn).closest('tr')).data();
      data['point_name'] = '진주'
      
      pop_tpl('lg','formmodal' , data)
    }
    
    $(document).ready(function() {
      usertable = $('#datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthMenu": [10],
        "order": [[ 0, "desc" ]],
        "ajax": {
          'url' : ajaxUrl ,
          'data' : function (data){
          }
        },
        "columnDefs": [
            {"targets": [ 0 ],"visible": false,"searchable": false},
            {"targets": [ 2 ],"searchable": true,"sortable":false, className:'text-center'},
            {"targets": [ 3 ],"searchable": false,"sortable":false, className:'text-center'},
            {"targets": [ 4 ],"searchable": true,"sortable":false},
            {"targets": [ 5 ],"searchable": false,"sortable":false, className:'text-right'},
          
            {"targets": [ 6 ],"searchable": false,"sortable":false, className:'text-right'},
            {"targets": [ 7 ],"searchable": false,"sortable":false, className:'text-right'},
            {"targets": [ 8 ],"searchable": false,"sortable":false, className:'text-right'},
          
            {"targets": [ 9 ],"searchable": false,"sortable":false},
            {"targets": [ 10 ],"searchable": false,"sortable":false, className:'text-right'},
        ],
        "columns" : [
          {"data" : "id"},
          {"data" : "phone"},
          {"data" : "bank_name"},
          {"data" : "name"},
          {"data" : "bank_account"},
          {"data" : "point"},
          
          {"data" : "islock", 
           'render' : function( data, type, row, meta) {
             var lock = false;
             if ( row.islock =='Y' || row.penalty_sale >= {{$siteconfig->penalty_sale}} || row.penalty_purchase >=  {{$siteconfig->penalty_purchase}}  ) lock = true;
             
             if( lock) return '<i class="fas fa-lock color-red"></i>'
             else return ''

            }
          },
          
          {"data" : "penalty_sale"},
          {"data" : "penalty_purchase"},
          
          {"data" : "recommender",
            'render' : function( data, type, row, meta) {
            if ( row.recommender_id ){
              let data = JSON.parse(row.recommender_id)
              let setp2 = (data.step2_phone)??'없음'
              return `<div>추천 : ${data.step1_phone}</div>
                      <div>상위 : ${setp2}</div>
                     `
            }
             return '';
            }
          },
          {"data" : "id", 
           'render' : function( data, type, row, meta) {
             return `<button class="btn btn-sm btn-primary" onClick='edit(this)'>수정</button>
<button class="btn btn-sm btn-primary" onClick='viewtree(${data})'>추천</button>
                    `
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
                    usertable.search(this.value).draw();
                }
            });
        },
              
      });
    });
    
    function addPenaltySale(user_id){
      var url = "/adm/api/penalty/sale"
      swal({
          title: '판매자 패널티를 추가하시겠습니까?',
          text: '총{{$siteconfig->penalty_sale}}개의 패널티를 받게되면 로그인이 불가능합니다.',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
              $.ajax({
                url : url,
                method: 'POST',
                data : {id : user_id},
                dataType:'JSON',
                success : function(res){
                  toastmessage('저장하였습니다')
                  usertable.ajax.reload(null, false);
                  $('.modal.show').modal('hide');
                },
                error: function ( err ){
                  ajaxError(err);
                }
              })                         
          } else {

          }
        });      
    }
    
    function addPenaltyPurchase(user_id){
      var url = "/adm/api/penalty/purchase"
      swal({
          title: '구매자 패널티를 추가하시겠습니까?',
          text: '총 {{$siteconfig->penalty_purchase}}개의 패널티를 받게되면 로그인이 불가능합니다.',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
              $.ajax({
                url : url,
                method: 'POST',
                data : {id : user_id},
                dataType:'JSON',
                success : function(res){
                  toastmessage('저장하였습니다')
                  usertable.ajax.reload(null, false);
                  $('.modal.show').modal('hide');
                },
                error: function ( err ){
                  ajaxError(err);
                }
              })                          
          } else {

          }
        });      
    }
    
    function resetPanalty (user_id){
      var url = "/adm/api/penalty/reset"
      
      swal({
          title: '패널티를 초기화 하시겠습니까?',
          text: '구매자, 판매자 패널티가 초기화됩니다.',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
              $.ajax({
                url : url,
                method: 'POST',
                data : {id : user_id},
                dataType:'JSON',
                success : function(res){
                  toastmessage('저장하였습니다')
                  usertable.ajax.reload(null, false);
                  $('.modal.show').modal('hide');
                },
                error: function ( err ){
                  ajaxError(err);
                }
              })                                
          } else {

          }
        });      
    }
    
    function incPoint (user_id){
      swal({
        title: '추가할 {{$siteconfig->point_name}}갯수를 입력해주세요',
        content: {
        element: 'input',
        attributes: {
          placeholder: '{{$siteconfig->point_name}} 갯수',
          type: 'text',
        },
        },
      }).then((data) => {
        if ( parseInt(data) > 0 ){
            pointprc(user_id, data, 'plus')
        } else  swal('{{$siteconfig->point_name}} 을(를) 추가하지 않았습니다 ');
        
      });      
    }
    function decPoint (user_id){
      swal({
        title: '차감할 {{$siteconfig->point_name}}갯수를 입력해주세요',
        content: {
        element: 'input',
        attributes: {
          placeholder: '{{$siteconfig->point_name}} 갯수',
          type: 'text',
        },
        },
      }).then((data) => {
        if ( parseInt(data) > 0 ){
            pointprc(user_id, data, 'minus')
        } else  swal('{{$siteconfig->point_name}} 을(를) 차감하지 않았습니다 ');
        
      });      
    }    
    function pointprc(user_id, point, prctype){
        let msg = ( prctype == 'plus') ? '추가' : '차감'
        var url = "/adm/api/point/"+prctype
        
        swal({
          title: `{{$siteconfig->point_name}} ${point}개를  ${msg}하시겠습니까?`,
          text: '',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
              $.ajax({
                url : url,
                method: 'POST',
                data : {id : user_id, point: point },
                dataType:'JSON',
                success : function(res){
                  toastmessage('저장하였습니다')
                  usertable.ajax.reload(null, false);
                  $('.modal.show').modal('hide');
                },
                error: function ( err ){
                  ajaxError(err);
                }
              })                                
          } else {

          }
        });  
    }
    function changePwd ( target, user_id){
      var url = "/adm/api/member/pwd"
      
      var pwd = $("#"+target).val()
      if( pwd == '' ) {alert("변경할 비밀번호를 입력해주세요");return;}
        
        swal({
          title: `비밀번호를  변경하시겠습니까?`,
          text: '',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
              $.ajax({
                url : url,
                method: 'POST',
                data : {id : user_id, password: pwd },
                dataType:'JSON',
                success : function(res){
                  toastmessage('변경하였습니다')
                  $("#"+target).val('')
                },
                error: function ( err ){
                  ajaxError(err);
                }
              })                                
          } else {

          }
        }); 
      
    }
    function viewtree(user_id){
      let url ="/recommender"
      $.ajax({
        url : url,
        method: 'get',
        data : {id : user_id},
        dataType:'JSON',
        success : function(res){
         drawtree(res);
        },
        error: function ( err ){
          ajaxError(err);
        }
      }) 
    }
    function drawtree(zNodes){
      var setting = {
          data: {
            key: {
              title:"t"
            },
            simpleData: {
              enable: true
            }
          },
          view:{
            showIcon:true,
          }
          // more settings here
      };

      zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
      $("#modal-tree").modal('handleUpdate')
      $("#modal-tree" ).modal('show')
    }
  </script>
@endsection