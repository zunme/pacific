<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Carbon\Carbon;
use App\Models\SiteConfig;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    var $siteconfig;
    var $now;
    public function __construct() {
      $this->siteconfig = SiteConfig::first();
      $this->now = Carbon::now();
    }
  
    function checkAvailTime(  ){
      
      $now = Carbon::now()->format('H:i:s');
      if( $this->siteconfig['end_trade']=='00:00:00'){
          if( $now < $this->siteconfig['start_trade'] ){
            return false;
          }
      }else if($this->siteconfig['end_trade'] < $this->siteconfig['start_trade']) {
        if( $now >= $this->siteconfig['end_trade'] && $now <= $this->siteconfig['start_trade'] ){
          return false;
        }
      }else {
          if( $now < $this->siteconfig['start_trade'] || $now > $this->siteconfig['end_trade'] ){
            return false;
          }  
      }
      return true;
    }
}
