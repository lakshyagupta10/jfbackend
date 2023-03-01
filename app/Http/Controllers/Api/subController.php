<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class subController extends Controller
{

public function checklocation($lat,$lng,$vendor)
 {
     $latlng=json_decode($vendor->latlngarray);
     if($vendor->latlngarray=='N/A')
     {
         return 0;
     }
        foreach($latlng as $latlngs)
        {
            $vertices_x[] = $latlngs->lat;
            $vertices_y[] = $latlngs->lng;
        }
        $points_polygon = count($vertices_x) - 1;
        $longitude_x = $lat;
        $latitude_y = $lng;

          $i = $j = $c = 0;
          for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
            if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
             ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
              $c = !$c;
          }
          return $c;
 }

  public function reasonofcancellist(Request $request)
    {
   $pauseorder = DB::table('cancel_reason')
                  ->get();

       if($pauseorder){
        	$message = array('status'=>'1', 'message'=>'data found', 'data'=>$pauseorder);
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'no data available', 'data'=>[]);
        	return $message;
        }
  }
    public function subscription(Request $request)
    {
        $user_id = $request->user_id;
        $subs = $request->subs_id;
        $subscribe = DB::table('tbl_user')
                ->where('user_id',$user_id)
                ->update(['subscription'=>$subs]);

       if($subscribe){
        	$message = array('status'=>'1', 'message'=>'subscription started', 'data'=>$subscribe);
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'no data available', 'data'=>$subscribe);
        	return $message;
        }
  }
      public function checksubs(Request $request)
    {
        $user_phone = $request->user_phone;
        $today=Carbon::now();
        $subscribe = DB::table('tbl_user')
                ->join('subscription_plans','tbl_user.subscription','=','subscription_plans.plan_id')
                ->where('user_phone',$user_phone)
                ->first();
        $user=DB::table('tbl_user')
        ->where('user_phone',$user_phone)
        ->first();
        $todayorder=DB::table('orders')
        ->where('user_id',$user->user_id)
        ->where('order_date',$today)
        ->get();
       if($subscribe->subscription>0){
           if(count($todayorder)>0){
            $allowmultishop=0;
        }
        else{
            $allowmultishop=0;
        }
            $time = strtotime(Carbon::now());
        	$message = array('status'=>'1', 'message'=>'subscriber', 'data'=>$subscribe);
        	$subsettings = DB::table("subscription_plans")
        	->first();
        	$start_time = strtotime($subsettings->start_time);
        	$end_time = strtotime($subsettings->end_time);
        	$days = (int)$subsettings->days;
        	$enddate=Carbon::now();
        	$enddate->addDays($days);
        	$enddate=$enddate->toDateString();
        	if($subsettings)
        	{
        	   $message = array('status'=>'1', 'message'=>'subscriber', 'data'=>$subscribe,'enddate'=>$enddate,'allowmultishop'=>$allowmultishop);
        	}
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'no data available', 'data'=>$subscribe,'enddate'=>$enddate,'allowmultishop'=>$allowmultishop);
        	return $message;
        }
  }
        public function subsstore(Request $request)
    {
        $lat= $request->lat;
        $lng= $request->lng;
        $currenttime = Carbon::Now();
        $current_time = strtotime($currenttime);        
        $subscribestores = DB::table('vendor')
	 ->select("vendor.*"
        ,DB::raw("6371 * acos(cos(radians(".$lat . "))
        * cos(radians(lat))
        * cos(radians(lng) - radians(" . $lng . "))
        + sin(radians(" .$lat. "))
        * sin(radians(lat))) AS distance"))
        ->orderBy('distance')
                ->where('subscription',1)
                ->get();
        foreach($subscribestores as $store)
        {
            if($this->checklocation($lat,$lng,$store)){
	               $store->inrange=1;
            }
            else
            {
                $store->inrange=0;
            }
            $store->duration =(string)round((float)$store->distance/0.5,2)." mins";
            $starttime  = $store->opening_time;
            $endtime = $store->closing_time;
            $start_time    = strtotime ($starttime);
            $end_time      = strtotime ($endtime);
            $store->opening_time = date('h:i a',$start_time);
            $store->closing_time = date('h:i a',$end_time);
            if($store->online_status=="ON"||$store->online_status=="on"){
            if($current_time<$start_time || $current_time>$end_time){
              $store->online_status="OFF";
            }
            else{
              $store->online_status="ON";
            }
          }
        }


       if($subscribestores){
        	$message = array('status'=>'1', 'message'=>'subscriber', 'data'=>$subscribestores);
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'no data available', 'data'=>$subscribestores);
        	return $message;
        }
  }

}
