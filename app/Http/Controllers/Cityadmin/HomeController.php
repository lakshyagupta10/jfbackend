<?php

namespace App\Http\Controllers\Cityadmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Hash;
use Carbon\Carbon;

class HomeController extends Controller
{
	public function cityadminIndex(Request $request)
    {
        $created_at = Carbon::Now();
    if(Session::has('cityadmin'))
     {
        $cityadmin_email=Session::get('cityadmin');

        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $cityadmin_id = $cityadmin->cityadmin_id;


        	$currentDate = date('Y-m-d');
				$day = 1;
       $current2 = date('d-m-Y', strtotime($currentDate.' + '.$day.' days'));



         $total_earnings = DB::table('orders')
                             ->join('vendor','orders.vendor_id','=','vendor.vendor_id')
    	                    ->join('cityadmin','vendor.cityadmin_id','=','cityadmin.cityadmin_id')
    	                    ->where('orders.order_status','=','Confirmed')
    	                    ->where('vendor.cityadmin_id',$cityadmin->cityadmin_id)
    	                    ->count();
    	   $orders = DB::table('orders')
    	                     ->join('vendor','orders.vendor_id','=','vendor.vendor_id')
    	                    ->join('cityadmin','vendor.cityadmin_id','=','cityadmin.cityadmin_id')
    	                    ->where('orders.order_status','=','Completed')
    	                    ->where('vendor.cityadmin_id',$cityadmin->cityadmin_id)
    	                    ->count();
    	   $total_cash = DB::table('orders')
    	                    ->join('vendor','orders.vendor_id','=','vendor.vendor_id')
    	                    ->join('cityadmin','vendor.cityadmin_id','=','cityadmin.cityadmin_id')
    	                    ->where('order_status','=','Completed')
    	                    ->where('vendor.cityadmin_id',$cityadmin->cityadmin_id)
    	                    ->sum('rem_price');

    	 $total_users = DB::table('tbl_user')
    	                    ->count();
    	  $ongoing =   DB::table('cityadmin')
    	                    ->count();
    	   $complete =   DB::table('vendor')
    	                    ->where('cityadmin_id',$cityadmin->cityadmin_id)
    	                    ->count();
    	   $cityadmin1 =   DB::table('delivery_boy')
    	                    ->where('cityadmin_id',$cityadmin->cityadmin_id)
    	                    ->count();
    	   $user =   DB::table('tbl_user')
    	                    ->count();
    	   $comment =   DB::table('support_queries')
    	                    ->count();
    	   $cancel =   DB::table('cancel_for')
    	                    ->count();
    	   $currency =   DB::table('currency')
    	                    ->first();
    	   $app_share =   DB::table('tbl_referral')
    	                    ->count();
    	   $daily_count =   DB::table('tbl_referral')
    	                    ->where('created_at','==',$created_at)
    	                    ->count();
    	   $today =       ($daily_count/1)*100;

    	   $recent_order = DB::table('orders')
    	                    ->join('vendor','orders.vendor_id','=','vendor.vendor_id')
    	                     ->join('cityadmin','vendor.cityadmin_id','=','cityadmin.cityadmin_id')
    	                    ->where('vendor.cityadmin_id',$cityadmin_id)

    	                    ->where('orders.payment_status','!=', 'NULL')
    	                    ->orderBy('delivery_date','DESC')
    	                    ->whereDate('orders.delivery_date', $currentDate)
    	                    ->paginate(5);
    	   $reffer_arning = DB::table('tbl_user_scratch_card')
    	                    ->sum('earning');

        return view('cityadmin.index', compact("cityadmin_email", "cityadmin", "total_earnings", "total_users", "ongoing","complete","cityadmin1","orders","user","comment","cancel","total_cash","currency","app_share","daily_count","today","recent_order","reffer_arning"));
	 }
	else
	 {
	    return redirect()->route('cityadminlogin')->withErrors('please login first');
	 }
    }
    	public function cityadminsettings(Request $request)
    {
            if(Session::has('cityadmin'))
     {
        $cityadmin_email=Session::get('cityadmin');

        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
				$city=DB::table('city')
				->where('city_id',$cityadmin->city_id)
				->first();
        $bigbanner = DB::table('big_banner')
    	 		 ->first();
    	 		 $top_message_banner = DB::table('top_message_banner')
    	 		 ->first();
    	 		 $closed_banner = DB::table('closed_banner')
    	 		 ->first();

        $cityadmin_id = $cityadmin->cityadmin_id;
        return view('cityadmin.settings', compact("cityadmin_email", "cityadmin","bigbanner","top_message_banner","closed_banner",'city'));
    }
    }
    public function cityadminupdatesettings(Request $request)
    {
        $cityadmin_id=$request->id;
				$extrapervendor=$request->extrapervendor;
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_id',$cityadmin_id)
        ->first();
				$city=DB::table('city')
				->where('city_id',$cityadmin->city_id)
				->first();
        $closing_time=$request->closing_time;
        $opening_time=$request->opening_time;
        $date = date('d-m-Y');
        if($request->status)
        $status=1;
        else
        $status=0;
        if($surge=$request->surge)
        $surge=1;
        else
        $surge=0;
        if($night=$request->night)
        $night=1;
        else
        $night=0;
        if($conv=$request->conv)
        $conv=1;
        else
        $conv=0;
        $surge_percent=$request->surge_percent;
        $maxincash=$request->maxincash;
        $night_charge=$request->night_charge;
        $conv_charge=$request->conv_charge;
        $top_message=$request->top_message;
        $bottom_message=$request->bottom_message;
				$surgechargemessage=$request->surgechargemessage;
				$timeslotmessage=$request->timeslotmessage;
        $value=array('closing_time'=>$closing_time,'opening_time'=>$opening_time,'status'=>$status,'surge'=>$surge,'night'=>$night,'conv'=>$conv,'surge_percent'=>$surge_percent,'night_charge'=>$night_charge,'conv_charge'=>$conv_charge,'top_message'=>$top_message,'bottom_message'=>$bottom_message,'maxincash'=>$maxincash);
$getImage = DB::table('big_banner')
                    ->first();

        $image = $getImage->banner_image;

        if($request->hasFile('bottom_banner')){
             if(file_exists($image)){
                unlink($image);
            }
            $city_image = $request->bottom_banner;
            $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
            $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;
        }
        else{
            $city_image = $image;
        }

        $update = DB::table('big_banner')
                                ->where('banner_id', $getImage->banner_id)
                                ->update(['banner_image'=>$city_image]);
$get2Image = DB::table('top_message_banner')
                    ->first();

        $image2 = $getImage->banner_image;

        if($request->hasFile('top_banner')){
             if(file_exists($image2)){
                unlink($image2);
            }
            $city_image2 = $request->top_banner;
            $fileName = date('dmyhisa').'-'.$city_image2->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image2->move('images/admin/admin_banner/'.$date.'/', $fileName);
            $city_image2 = 'images/admin/admin_banner/'.$date.'/'.$fileName;
        }
        else{
            $city_image2 = $image2;
        }

        $update = DB::table('top_message_banner')
                                ->where('banner_id', $get2Image->banner_id)
                                ->update(['banner_image'=>$city_image2]);
        $updatecityadmin=DB::table('cityadmin')
                 ->where('cityadmin_id', $cityadmin_id)
                 ->update($value);
				$updatecity=DB::table('city')
										->where('city_id',$cityadmin->city_id)
										->update(['extrapervendor'=>$extrapervendor,'surgechargemessage'=>$surgechargemessage,'timeslotmessage'=>$timeslotmessage]);
         if($updatecityadmin || $updatecity){
            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
        }
    }

}
