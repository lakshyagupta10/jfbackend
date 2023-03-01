<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;


class NearbystoreController extends Controller
{
    public function checkstorelocation(Request $request)
    {
        $lat=$request->lat;
        $lng=$request->lng;
        $vendor=$request->vendor_id;
        $c=$this->checklocation($lat,$lng,$vendor);
        return array("c"=>$c);
    }
     public function checklocation($lat,$lng,$vendor)
 {
    $vendor2=DB::table('vendor')
    ->where('vendor_id',$vendor)
    ->first();
     $latlng=json_decode($vendor2->latlngarray);
     if($vendor2->latlngarray=='N/A')
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

    public function nearbystore(Request $request)
    {

    	$lat = $request->lat;
    	$lng = $request->lng;
    	$vendor_category_id = $request->vendor_category_id;
      $currenttime = Carbon::Now();
      $current_time = strtotime($currenttime);
        $groupApp = DB::table("vendor")
    	 ->select("vendor.*",DB::raw("6371 * acos(cos(radians(".$lat . "))
        * cos(radians(lat))
        * cos(radians(lng) - radians(" . $lng . "))
        + sin(radians(" .$lat. "))
        * sin(radians(lat))) AS distance"))
        ->orderBy('distance')
        ->where('vendor_category_id',$vendor_category_id)
        ->get();
         $storelist = NULL;

        foreach($groupApp as $store)
        {
            if($this->checklocation($lat,$lng,$store->vendor_id)){
		        $store->inrange=1;
            }
	    else{
		        $store->inrange=0;
		        }
    $store->duration = (string)round((float)$store->distance/0.5,2)." mins";
    $starttime  = $store->opening_time;
    $endtime = $store->closing_time;
    $start_time    = strtotime ($starttime);
    $end_time      = strtotime ($endtime);
    $store->opening_time = date('h:i a',$start_time);
    $store->closing_time = date('h:i a',$end_time);
  $storelist[] = $store;
}
    if ($storelist != NULL){
            $message = array('status'=>'1', 'message'=>'NearBy Store', 'data'=>$storelist);
            return $message;

        }
        else{
             $message = array('status'=>'0', 'message'=>'We are Coming Soon in your area', 'data'=>[]);
            return $message;

        }
    }

    public function nearbyrest(Request $request)
    {

    	$lat = $request->lat;
    	$lng = $request->lng;
      $currenttime = Carbon::Now();
      $current_time = strtotime($currenttime);

        $groupApp = DB::table("vendor")
     	 ->select("vendor.*"
        ,DB::raw("6371 * acos(cos(radians(".$lat . "))
        * cos(radians(lat))
        * cos(radians(lng) - radians(" . $lng . "))
        + sin(radians(" .$lat. "))
        * sin(radians(lat))) AS distance"))
        ->where('ui_type',2)
        ->orderBy('distance')
        ->get();
        $storelist = NULL;
        foreach($groupApp as $store)
        {
            if($this->checklocation($lat,$lng,$store->vendor_id)){
		            $store->inrange=1;
            }
		else{
                $store->inrange=0;
		}
    $store->duration =(string)round((float)$store->distance/0.5,2)." mins";
    $starttime  = $store->opening_time;
    $endtime = $store->closing_time;
    $start_time    = strtotime ($starttime);
    $end_time      = strtotime ($endtime);
    $store->opening_time = date('h:i a',$start_time);
    $store->closing_time = date('h:i a',$end_time);
  $storelist[] = $store;

        }
    if ($storelist != NULL){
            $message = array('status'=>'1', 'message'=>'NearBy Restaurant', 'data'=>$storelist);
            return $message;

        }
        else{
             $message = array('status'=>'0', 'message'=>'We are Coming Soon in your area', 'data'=>[]);
            return $message;

        }
    }



}
