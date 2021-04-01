<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;
use Validator;

use Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

use App\Models\User;
use App\Models\Recommender;

use App\Models\SiteConfig;

use App\Models\Product;
use App\Models\PenaltyHistory;
use App\Models\PointHistory;

use Yajra\DataTables\Facades\DataTables;
/**
 * Class UserController
 * @package App\Http\Controllers\Front
 */
class AdminController extends Controller
{
  use ApiResponser;
  /* 
  * 설정관련
  */
  function configpage(Request $request){
    $siteconfig= SiteConfig::first();
    return view('admin.config', compact(['siteconfig']));
  }
  function configsave(Request $request){
    $messages = [
		'point_name.*' => '포인트 이름을 확인해주세요.',
		'price_per_point.*' => '포인트 가격을 확인해주세요.',
      
		'start_trade.*'    => '거래가능 시간을 확인해주세요.',
    'start_trade.*'    => '거래가능 시간을 확인해주세요.',
      
    'penalty_sale.*'    => '판매자 패널티를 확인해주세요.',
    'penalty_purchase.*'    => '구매자 패널티를 확인해주세요.',

    ];    
    $data = $request->validate([
      'point_name'=>'bail|required|string|min:1|max:20',
      'price_per_point'=>'bail|required|numeric|gt:0',
      
      //'start_trade' => 'required|date_format:h:i A',
      //'end_trade' => 'required|date_format:h:i A'
      
      'start_trade' => 'bail|required',
      'end_trade' => 'bail|required',
      'max_trading'=> 'bail|required|numeric|gt:0',
      'trading_limit'=>'bail|required|numeric',

      'penalty_sale' => 'bail|required|numeric|gt:0',
      'penalty_purchase' => 'bail|required|numeric|gt:0'
    ],$messages);
    if ( !strtotime( $data['start_trade'] ) || !strtotime( $data['end_trade'] ) ) return $this->error();
    $data['start_trade'] = date("H:i:s", strtotime( $data['start_trade'] ));
    $data['end_trade'] = date("H:i:s", strtotime( $data['end_trade'] ));
    $siteconfig= SiteConfig::first();
    try{
      $siteconfig->update( $data );
      return $this->success();
    }catch (\Exception $e) {
      return $this->error();
    }
  }
  
  
  /* 
  * 상품설정관련
  */
  function productConfigPage( Request $request ){
    return view('admin.products_config');
  }
  function productList(Request $request ){
    $data = Product::orderBy('id','asc');
    return Datatables::of($data)
                ->addIndexColumn()
                //->with(['today'=>Carbon::now()->format('Y-m-d')])
                ->rawColumns(['action'])
                ->make(true);
  }
  function productSave( Request $request ){
    $messages = [
      'product_name.*' => '상품명을 확인해주세요.',
      'is_use.*' => '사용여부를 확인해주세요.',
      'period.*' => '보유기간을 확인해주세요.',
      'profit_rate.*'    => '수익률을 확인해주세요.',
      'fee.*'    => '판매수수료를 확인해주세요.',
      'price.*'    => '상품시작가를 확인해주세요.',
      
      'select_img.required'=>"이미지 파일을 올려주세요",
      'select_img.*'=>"올바른 이미지 파일을 올려주세요",
    ];    
    $data = $request->validate([
      'product_name'=>'bail|required|string|min:1|max:20',
      'is_use'=>'bail|nullable|in:Y,N,R',
      'period'=>'bail|required|numeric|gt:0',
      'profit_rate'=>'bail|required|numeric|gt:0',
      'fee'=>'bail|required|numeric|gt:0',
      'price'=>'bail|required|numeric|gt:0',
      'select_img' => 'bail|nullable|image|mimes:jpeg,jpg,gif,png|max:5000',
      
    ],$messages);
    if( $request->id){
      $product = Product::findOrFail( $request->id );     
      try{
        $product->update( $data );
        if( $request->file('select_img') ){
           $this->uploadimage( $product, $request->file('select_img') );
        } 
        return $this->success(); 
      } catch (\Exception $e) {
        return $this->error(); 
      }
    }else{
      try{
        $product= Product::create( $data );
        return $this->success(); 
      } catch (\Exception $e) {
        return $this->error(); 
      }
    }
  }
  
  /* 
  * 회원관련
  */
  function member () {
    $siteconfig = SiteConfig::first();
    
    return view( 'admin.member', compact(['siteconfig']) );
  }
  function memberList(Request $request ){
    $data = User::orderBy('id','asc');
    return Datatables::of($data)
                ->addIndexColumn()
                //->with(['today'=>Carbon::now()->format('Y-m-d')])
                ->addColumn( 'recommender_id' , function (User $user){
                  
                  $recommender = Recommender::select('setp1user.id as step1_id','setp1user.phone  as step1_phone','setp2user.id as step2_id','setp2user.phone as step2_phone')->where('user_id', $user->id)
                                  ->join('users as setp1user', 'recommenders.parent_step1_id','=', 'setp1user.id')
                                  ->leftJoin('users as setp2user', 'recommenders.parent_step2_id','=', 'setp2user.id')
                                  ->first()
                    ;
                  return $recommender;
                })
                ->rawColumns(['action'])
                ->make(true);
  }
  
  function memberSave(Request $request) {
    $user = User::findOrFail($request->id);
    
    $messages = [
    ];    
    $data = $request->validate([
      'name'=>'bail|nullable|string',
      'bank_name'=>'bail|nullable|string',
      'bank_account'=>'bail|nullable|string',
    ],$messages);
    $data['name'] = $data['name'] ?? '';   
    try {
      $user->update($data);
      return $this->success();
    }catch (\Exception $e) {
      return $this->error();
    }
  }
  function memberPasswordChange(Request $request){
    Validator::extend('without_spaces', function($attr, $value){
      return preg_match('/^\S*$/u', $value);
    });	        
    $messages = [
      'password.regex'    => '비밀번호는 4~20자로 입력해주세요.',
      'password.without_spaces'    => '공백은 사용할 수 없습니다.',
      'password.*'    => '비밀번호(4~20자)를 입력해주세요.',
    ];    
    $data = $request->validate([
      'id'=>'bail|required|numeric|gt:0',
      'password'=>'bail|required|string|min:4|max:20|without_spaces',
    ],$messages);
    $user = User::findOrFail($request->id);
    $data['password'] = \Hash::make( $data['password'] );
    try{
      $user->update(['password'=>$data['password']]);
      return $this->success();
    }catch ( \Exception $e) {
      return $this->error();
    }
  }
  
  /* 회원관련 패널티 */
  function addPenalty(Request $request , $penaltytype){
    $user = User::findOrFail($request->id);
    $data = ['user_id'=>$user->id, 'penalty_type'=> $penaltytype ];
    $penalty = new PenaltyHistory();
    try{
      $ret = $penalty->penalty( $request->id, $penaltytype);
    } catch (\Exception $e) {
       return $this->error($e->getMessage(), 422);  
    }
    return $this->success([],'패널티 적용 완료');
  }
  
  /* 회원관련 포인트*/
  function pointprc(Request $request , $pointprc){
    $siteconfig = SiteConfig::first();
    
    $messages = [
      'point.*' => $siteconfig->point_name.' 갯수를 확인해주세요.',
    ];    
    $data = $request->validate([
      'point'=>'bail|required|numeric|gt:0',
    ],$messages);
    
    $user = User::findOrFail($request->id);
    
    $type = 'P'; 
    $addPoint = (int)$data['point'];

    if( $request->pointprc == 'minus'){
      if( $user->point < $request->point ){
        $msg = '차감 가능한 '.$siteconfig->point_name.' 은(는) 총 '.$user->point.'개 입니다.';
        return $this->error($msg, 422);
      }
      $type="M"; 
      $addPoint = (int)$data['point'] * -1;
    }
    $log = ['user_id'=>$user->id,'point_type'=>$type, 'before_amount'=>$user->point, 'amount'=>$addPoint , 'after_amount'=> (int)$user->point + $addPoint ];
   
     \DB::beginTransaction();
		try {
      $user->update([
       'point' => \DB::raw('point '.( $type=='P'? ' + ':' - ' ). (int)$data['point'] )
        ]);
      PointHistory::create($log);
      \DB::commit();
      
      return $this->success($user);
    } catch (\Exception $e) {
		  \DB::rollback();
      return $this->error('error', 500, $e);
		}  
    
  }
  
  private function delfile($path){
		$storage = Storage::disk('public');
	
		if($storage->exists($path) && $path !== null){
			$storage->delete($path);
		}
	}
  private function uploadimage($table, $file){
    $this->delfile($table->image_url);
    $image = Image::make($file);
    $image_name = Carbon::now()->timestamp.'_'.( Str::random(8) ).'.'.$file->getClientOriginalExtension();
    $path = 'images/'. $image_name;
    Storage::disk('public')->put($path, $image->stream()->__toString());

    $table->update([
        'image_url' => $path,
    ]);
  }
  
}