<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title">회원 : {{phone}} </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>    
  </div>
  <div class="modal-body">
    <form id="addform">
      <input type="hidden" name="id" value="{{id}}">
      
      <div class="row">
        <div class="col-4">
          <div class="form-group">
            <div class="input-group mb-2">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fas fa-phone"></i>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="전화번호" value="{{phone}}" disabled>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="form-group">
            <div class="input-group mb-2">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fas fa-wallet"></i>
                </div>
              </div>
              <input type="text" class="form-control" placeholder="전화번호" value="{{point}} {{point_name}}" disabled>
            </div>
          </div>          
        </div>
        <div class="col-4">
          <div class="form-group">
              <div class="input-group mb-3">
                <input id="passwordinp" type="text" class="form-control" placeholder="변경할 비밀번호를 입력해주세요" aria-label="">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button" onClick="changePwd('passwordinp', {{id}})">변경</button>
                </div>
              </div>
            </div>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group col-md-4">
          <label for="inputEmail4">은행</label>
          <input type="text" class="form-control" name="bank_name" value="{{bank_name}}">
        </div>
        <div class="form-group col-md-3">
          <label for="inputEmail4">예금주</label>
          <input type="text" class="form-control" name="name" value="{{name}}">
        </div>
        <div class="form-group col-md-5">
          <label for="inputPassword4">계좌번호</label>
          <input type="text" class="form-control" name="bank_account" value="{{bank_account}}">
        </div>
      </div>
      
 
      <div class="form-row">
        <div class="form-group col-md-4">
          <label for="inputEmail4">총패널티(회)</label>
          <input type="text" class="form-control" name="penalty_total" value="{{penalty_total}}" disabled>
        </div>
        <div class="form-group col-md-3">
          <label for="inputEmail4">판매자패널티(회)</label>
          <input type="text" class="form-control" name="penalty_sale" value="{{penalty_sale}}" disabled>
        </div>
        <div class="form-group col-md-5">
          <label for="inputPassword4">구매자패널티(회)</label>
          <input type="text" class="form-control" name="penalty_purchase" value="{{penalty_purchase}}" disabled>
        </div>
      </div>
    </form>
    <div class="text-right">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary" onClick="{{#if id}}
          default_form_prc({url:'/adm/api/member/save',form:'addform',reload:usertable,'msg' : '수정되었습니다.'})
        {{else}}
          default_form_prc({url:'/adm/api/member/save',form:'addform',reload:usertable})
        {{/if}}">{{#if id}}저장{{else}}생성{{/if}}</button>
    </div>
  </div>
  <div class="bg-whitesmoke br" style="padding:0 20px 20px 20px;">
    
    <div class="" style="display:flex;margin-top:20px; margin-bottom:20px;justify-content: space-between;">
      
      <div class="">
        <button type="button" class="btn btn-danger" onClick="addPenaltySale({{id}})">판매자 패널티 추가</button>
        <button type="button" class="btn btn-danger" onClick="addPenaltyPurchase({{id}})">구매자 패널티 추가</button>
        <button type="button" class="btn btn-primary ml-40" onClick="resetPanalty({{id}})">패널티 해재</button>
      </div>
      
      <div class="">
        <button type="button" class="btn btn-warning" onClick="incPoint({{id}})">{{point_name}} 추가</button>
        <button type="button" class="btn btn-warning ml-40" onClick="decPoint({{id}})">{{point_name}} 차감</button>
      </div>
      
    </div>
    
  </div>
  
</div>