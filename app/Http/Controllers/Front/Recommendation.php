<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Cache;

use App\Models\User;
use App\Models\Recommender;
/**
 * Class UserController
 * @package App\Http\Controllers\Front
 */
class Recommendation extends Controller
{
    function getData( Request $request) {
      $seconds = 60*5;
      $userid = $request->id;
     // $data = $this->makeData( $reuqest->id);
     //$data = $this->makeData( '1' );
      /*
      $data = Cache::remember('u_rcmd_'.$userid, $seconds, function () use($userid) {
          return $this->simpleData($userid );
      });
      */
      $data = $this->simpleData($userid );
     return response()->json($data);
  }
  private function makeData( $user_id){      
    $ret = null;
    $data = Recommender::select( 'users.id', 'users.phone','parent_step1_id')
      ->where('parent_step1_id', $user_id)
      ->join('users' , 'recommenders.user_id','=','users.id')->get();
    foreach( $data as $row){
      $tmp = $this->makeData( $row->id );
      if( !$tmp ) {
        $isParent = false;
        $children = [];
      }
      else {
        $isParent = true;
        $children = $tmp;
      }
      $ret[] = array( "id"=>$row->id, 'pid'=> $row->parent_step1_id,"name"=>$row->phone, "isParent"=>$isParent,"children"=>$children);
    }
    return $ret;
  }
  private function simpleData($user_id , $step=1){
    $ret = [];
    if( $step == 1){
      $user = User::findOrFail($user_id);
      $ret[] = array( "id"=>"u".$user->id, 'pId'=> 0 ,"name"=>$user->phone, 'isParent'=> true , 'open'=>true, 'iconSkin'=>'masterUserIcon'  );
    }
    if( $step > 10 ) return [];
    
    $data = Recommender::select( 'users.id', 'users.phone','parent_step1_id')
      ->where('parent_step1_id', $user_id)
      ->join('users' , 'recommenders.user_id','=','users.id')->get();
    foreach( $data as $row){
      //if( $step==1) $row->parent_step1_id = 0;
      
      $tmp = $this->simpleData( $row->id , ++$step);

      if( count($tmp)>0) $isparent = true;
      else $isparent = false;
      if( $step < 3 && $isparent) $isopen = true;
      else $isopen = false;
      
      $ret[] = array( "id"=>"u".$row->id, 'pId'=> $row->parent_step1_id ? "u".$row->parent_step1_id :  0 ,"name"=>$row->phone, 'isParent'=> $isparent , 'open'=>$isopen, 'iconSkin'=>'subrUserIcon'  );
      $ret = array_merge($ret, $tmp);
      /*
      foreach( $tmp as $tmprow){
        $ret[] = array( "id"=>$tmprow['id'], 'pId'=> $tmprow['pId'] ,"name"=>$tmprow['name'], 'isParent'=> $tmprow['isParent'] , 'open'=>$tmprow['open'], 'iconSkin'=>'subrUserIcon' );
      }
      */
    }
    return $ret;
  }
}