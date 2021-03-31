<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;
use Validator;

use App\Models\User;
use App\Models\Recommender;
/**
 * Class UserController
 * @package App\Http\Controllers\Front
 */
class UserController extends Controller
{
  use ApiResponser;
	public function showRegisterForm()
    {
        if( Auth::id() ){
          return redirect()->to('/');
        }
        $banklist = $this->banklist();
        return view('join', compact(['banklist']));
    }
  public function register(Request $request){
    Validator::extend('without_spaces', function($attr, $value){
      return preg_match('/^\S*$/u', $value);
    });	    
    $messages = [
		'agree_privacy.*' => '개인정보 처리방침에 동의를 하지 않으셨습니다.',
		'agree_terms.*' => '서비스 이용에 동의를 하지 않으셨습니다.',
      
    'phone.unique'    => '이미 사용중인 휴대폰번호입니다.',
    'phone.regex'    => '휴대폰번호를 확인해주세요.',
		'phone.*'    => '휴대폰번호를 입력해주세요.',
      
		'password.confirmed'    => '비밀번호 확인이 일치하지 않습니다.',
		'password.regex'    => '비밀번호는 4~20자로 입력해주세요.',
		'password.without_spaces'    => '공백은 사용할 수 없습니다.',
		'password.*'    => '비밀번호를 입력해주세요.',
    ];    
    $data = $request->validate([
      'phone'=>'bail|required||regex:/(01)[0-9]{8}/|unique:users,phone,'.$request->phone,
		  'password'=>'bail|required|min:4|max:20|without_spaces|confirmed',
      'agree_privacy'=>'bail|required|in:Y',
      'name'=>'bail|nullable|string',
      'bank_name'=>'bail|nullable|string',
      'bank_account'=>'bail|nullable|string',
      'recommender'=>'bail|nullable|string',
    ],$messages);
    $data['name'] = $data['name'] ?? '';
    $data['password'] = \Hash::make( $data['password'] );
    $recommenders = $this->getRecommenderStep($data['recommender']);
     return $this->success();
    \DB::beginTransaction();
		try {
      $user = User::create($data);
      if( $recommenders ){
        $recommendersetp = ['user_id'=>$user->id,
                            'parent_step1_id'=> $recommenders['step1']->id,
                            'parent_step2_id'=> $recommenders['step2']->id,
                            
                            'parent_step1_id_string'=> $recommenders['step1']->phone,
                            'parent_step2_id_string'=> $recommenders['step2']->phone,
                           ];
        Recommender::create($recommendersetp);
      }
      \DB::commit();
    } catch (\Exception $e) {
		  \DB::rollback();
      return $this->error();
		}
    return $this->success([],'가입완료');
  }
  private function getRecommenderStep( $recommender ){
    $ret = ['step1'=>null, 'step2'=>null];
    if( $recommender ){
      $step1 = User::select('id','phone','recommender')->where('phone', $recommender)->first();
      if( $step1 ){
        $ret['step1']=$step1;
        if( $step1->recommender ){
          $step2 = User::select('id','phone')->where('phone', $step1->recommender )->first();
        }else $step2 = new Recommender();
        $ret['step2']=$step2;
        return $ret;
      }
    }
    return null;
  }
  private function banklist() {
    return [
      "경남은행",
      "광주은행",
      "국민은행",
      "기업은행",
      "농협중앙회",
      "농협회원조합",
      "대구은행",
      "도이치은행",
      "부산은행",
      "산업은행",
      "상호저축은행",
      "새마을금고",
      "수협중앙회",
      "신한금융투자",
      "신한은행",
      "신협중앙회",
      "외환은행",
      "우리은행",
      "우체국",
      "전북은행",
      "제주은행",
      "카카오뱅크",
      "케이뱅크",
      "하나은행",
      "한국씨티은행",
      "HSBC은행",
      "SC제일은행"
      ];
  }
}