<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;


class VendorCategoryController extends Controller
{
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
    public function adminsettings(Request $request)
    {
        $admin_set = DB::table('cityadmin')
        		   ->first();
        $time = Carbon::now();
        $time = strtotime($time);
        $opening_time = strtotime($admin_set->opening_time);
        $closing_time = strtotime($admin_set->closing_time);
        if($time<$opening_time||$time>=$closing_time)
        $update=DB::table('cityadmin')
        ->where('cityadmin_id',$admin_set->cityadmin_id)
        ->update(['status'=>0]);
        else
        {
         $update=DB::table('cityadmin')
        ->where('cityadmin_id',$admin_set->cityadmin_id)
        ->update(['status'=>1]);           
        }
        if($admin_set->surge)
        {
            $msg="Due to heavy rains  a surge charge of ₹".$admin_set->surge_percent." will be applicable";
        }
        else
        {
            $msg="";
        }
        $admin_set2 = DB::table('cityadmin')
        		   ->first();   
        $admin_set2->surge_msg=$msg;
        
      if($admin_set)
      {
        	$message = array('status'=>'1', 'message'=>'data found', 'data'=>$admin_set2);
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
        	return $message;
        		  
    }
    }
        public function top_message_banner(Request $request)
    {
    	$banner = DB::table('top_message_banner')
        		   ->get();
      if($banner)
      {
        	$message = array('status'=>'1', 'message'=>'data found', 'data'=>$banner);
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
        	return $message;
        		  
    }
    }
            public function closed_banner(Request $request)
    {
    	$banner = DB::table('closed_banner')
        		   ->get();
      if($banner)
      {
        	$message = array('status'=>'1', 'message'=>'data found', 'data'=>$banner);
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
        	return $message;
        		  
    }
    }
    public function vendorcategory(Request $request)
    {   
        $lat = $request->lat;
    	$lng = $request->lng;
        $banner = DB::table('vendor_category')
                    ->where('hide','!=','1')
                    ->orderby('seq')
        		   ->get();
        foreach($banner as $vendorcategory)
    {
        $groupApp = DB::table("vendor")
      ->select("vendor.*"
        ,DB::raw("6371 * acos(cos(radians(".$lat . ")) 
        * cos(radians(lat)) 
        * cos(radians(lng) - radians(" . $lng . ")) 
        + sin(radians(" .$lat. ")) 
        * sin(radians(lat))) AS distance"))
        ->orderBy('distance')
        ->where('vendor_category_id',$vendorcategory->vendor_category_id)
        ->get();
        $storelist = [];
        foreach($groupApp as $store)
        {
            if($store->delivery_range > $store->distance){
                $store->inrange=1;
                $store->duration =(string)round((float)$store->distance/0.5,2)." mins";
                $storelist[] = $store;
            }
            else
            {
                $store->inrange=0;
                $store->duration =(string)round((float)$store->distance/0.5,2)." mins";
                $storelist[] = $store;
            }
        }
        if(count($storelist)>0)
        $vendorcategory->vendors=$storelist;
    }

        if(count($banner)>0){
        	$message = array('status'=>'1', 'message'=>'data found', 'data'=>$banner);
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
        	return $message;
        }
    }
        public function vendorcategorylist(Request $request)
    {   
        $lat = $request->lat;
    	$lng = $request->lng;
        $banner = DB::table('vendor_category')
                    ->where('hide','!=','1')
        		   ->get();
        foreach($banner as $i=>$vendorcategory)
    {
        $groupApp = DB::table("vendor")
      ->select("vendor.*"
        ,DB::raw("6371 * acos(cos(radians(".$lat . ")) 
        * cos(radians(lat)) 
        * cos(radians(lng) - radians(" . $lng . ")) 
        + sin(radians(" .$lat. ")) 
        * sin(radians(lat))) AS distance"))
        ->orderBy('distance')
        ->where('vendor_category_id',$vendorcategory->vendor_category_id)
        ->get();
         $storelist = [];
         foreach($groupApp as $store)
        {
            if($store->delivery_range > $store->distance){
                $store->inrange=1;
                $store->duration =(string)round((float)$store->distance/0.5,2)." mins";
                $storelist[] = $store;
            }
            else
            {
                $store->inrange=0;
                $store->duration =(string)round((float)$store->distance/0.5,2)." mins";
                $storelist[] = $store;
            }
        }
        if(count($storelist))
        $vendorcategory->vendors=$storelist;
        else
        $banner->forget($i);
    }

        if(count($banner)>0){
        	$message = array('status'=>'1', 'message'=>'data found', 'data'=>$banner);
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
        	return $message;
        }
    }

}    
