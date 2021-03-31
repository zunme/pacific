<?php
namespace App\Traits;
use Carbon\Carbon;

use App\Models\SiteConfig;

trait ApiResponser
{

	protected function success($data = null, string $message = 'Success', int $code = 200)
	{
		return response()->json([
			'status' => 'Success',
			'message' => $message,
			'data' => $data
		], $code);
	}
	protected function error(string $message = null, int $code = 500, $data = null)
	{
		return response()->json([
			'status' => 'Error',
			'message' => $message,
			'data' => $data
		], $code);
	}
  protected function isBlockUser( User $user){
     if( $user->lslock =='Y' || $user->login_available =='N' ||  $user->penalty_sale >= $this->siteconfig->penalty_sale  || $user->penalty_purchase >= $this->siteconfig->penalty_purchase ) return true;
     else return false;
  }
  protected function getSiteConfig(){
    //return $this->siteconfig;
    return SiteConfig::first();
  }
}