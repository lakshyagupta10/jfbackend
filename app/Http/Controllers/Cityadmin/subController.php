<?php

namespace App\Http\Controllers\cityadmin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use DB;
use Session;
use Hash;
use Excel;

class subController extends Controller
{
    public function sublist(Request $request)
    {
     if(Session::has('cityadmin'))
     {

        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $subscribers= DB::table('tbl_user')
        ->where('subscription','>','0')
        ->get();
        return view('cityadmin.subscription.subscribers',compact("cityadmin_email","subscribers","cityadmin"));
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
        public function substores(Request $request)
    {
     if(Session::has('cityadmin'))
     {

        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $substores= DB::table('vendor')
        ->where('subscription','1')
        ->get();
        return view('cityadmin.subscription.substores',compact("cityadmin_email","substores","cityadmin"));
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
    public function addsubplan(Request $request)
    {
     if(Session::has('cityadmin'))
     {
        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        return view('cityadmin.subscription.addsubplan',compact("cityadmin_email","cityadmin"));
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
    public function subplan(Request $request)
    {
     if(Session::has('cityadmin'))
     {
       $subplan_id = $request->id;

        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $subplans= DB::table('subscription_plans')
        ->where('plan_id',$subplan_id)
        ->first();
        return view('cityadmin.subscription.subplan',compact("cityadmin_email","subplans","cityadmin"));
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
    public function subplans(Request $request)
    {
     if(Session::has('cityadmin'))
     {

        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $subplans= DB::table('subscription_plans')
        ->get();
        return view('cityadmin.subscription.subplans',compact("cityadmin_email","subplans","cityadmin"));
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
        public function subsettings(Request $request)
    {
     if(Session::has('cityadmin'))
     {
       $subplan_id = $request->id;
        $amount = $request->amount;
        $plans = $request->plans;
        $days = $request->days;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $description = $request->description;
        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $subplan = DB::table('subscription_plans')
        ->where('plan_id',$subplan_id)
        ->first();
                $image = $subplan->banner;

        if($request->hasFile('banner')){
             if(file_exists($image)){
                unlink($image);
            }
            $city_image = $request->banner;
            $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
            $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;
        }
        else{
            $city_image = $image;
        }
        $subplans= DB::table('subscription_plans')
        ->where('plan_id',$subplan_id)
        ->update(['plans'=>$plans,'description'=>$description,'amount'=>$amount,'start_time'=>$start_time,'end_time'=>$end_time,'banner'=>$city_image,'days'=>$days]);
        if($subplans)
        {
            return redirect()->back()->withErrors('Updated Sucessfullly');
        }
        else
        {
            return redirect()->back()->withErrors('Something went wrong');
        }
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
     public function addsubstore(Request $request)
    {
     if(Session::has('cityadmin'))
     {

        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $vendors=DB::table('vendor')
        ->get();
         return view('cityadmin.subscription.addsubstore',compact("cityadmin_email","cityadmin","vendors"));
     }
     else
         {
            return redirect()->route('cityadminlogin')->withErrors('please login first');
         }
    }
    public function addnewsubstore(Request $request)
   {
    if(Session::has('cityadmin'))
    {
       $vendor_id=$request->vendor_id;
       $cityadmin_email=Session::get('cityadmin');
       $cityadmin=DB::table('cityadmin')
       ->where('cityadmin_email',$cityadmin_email)
       ->first();
       $vendors=DB::table('vendor')
       ->where('vendor_id',$vendor_id)
       ->update(["subscription"=>1]);

       if($vendors)
       {
          return redirect()->back()->withErrors('Added Sucessfully');
       }
       else
       {
          return redirect()->back()->withErrors('Something went wrong');
       }
    }
    else
        {
           return redirect()->route('cityadminlogin')->withErrors('please login first');
        }
   }
     public function addnewsubplan(Request $request)
     {
      if(Session::has('cityadmin'))
      {
         $amount = $request->amount;
         $plans = $request->plans;
         $days = $request->days;
         $start_time = $request->start_time;
         $end_time = $request->end_time;
         $description = $request->description;
         $cityadmin_email=Session::get('cityadmin');
         $cityadmin=DB::table('cityadmin')
         ->where('cityadmin_email',$cityadmin_email)
         ->first();

         if($request->hasFile('banner')){
             $city_image = $request->banner;
             $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
             $fileName = str_replace(" ", "-", $fileName);
             $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
             $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;
         }
         else{
             $city_image = 'N/A';
         }
         $subplans= DB::table('subscription_plans')
         ->insertGetId(['plans'=>$plans,'description'=>$description,'amount'=>$amount,'start_time'=>$start_time,'end_time'=>$end_time,'banner'=>$city_image,'days'=>$days]);
         if($subplans)
         {
             return redirect()->back()->withErrors('Added Sucessfullly');
         }
         else
         {
             return redirect()->back()->withErrors('Something went wrong');
         }
      }
      else
      {
         return redirect()->route('cityadminlogin')->withErrors('please login first');
      }
     }
     public function deletesubplan(Request $request)
     {
      if(Session::has('cityadmin'))
      {

         $cityadmin_email=Session::get('cityadmin');
         $cityadmin=DB::table('cityadmin')
         ->where('cityadmin_email',$cityadmin_email)
         ->first();
         $subplan_id=$request->id;
         $user=DB::table('subscription_plans')
         ->where('plan_id',$subplan_id)
         ->delete();
         return redirect()->back()->withErrors('removed');
      }
      else
          {
             return redirect()->route('cityadminlogin')->withErrors('please login first');
          }
     }

    public function deletesubscriber(Request $request)
    {
     if(Session::has('cityadmin'))
     {

        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $user_id=$request->user_id;
        $user=DB::table('tbl_user')
        ->where('user_id',$user_id)
        ->update(['subscription','0']);
        return redirect()->back()->withErrors('removed');
     }
     else
         {
            return redirect()->route('cityadminlogin')->withErrors('please login first');
         }
    }
        public function deletesubstore(Request $request)
    {
     if(Session::has('cityadmin'))
     {

        $cityadmin_email=Session::get('cityadmin');
        $vendor_id=$request->id;
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $vendor=DB::table('vendor')
        ->where('vendor_id',$vendor_id)
        ->update(['subscription'=>'0']);
        if($vendor)
        {

        return redirect()->back()->withErrors('Delete successfully');

        }
        else
        {
           return redirect()->back()->withErrors('Unsuccessfull delete');
        }
}
     else
         {
            return redirect()->route('cityadminlogin')->withErrors('please login first');
         }
    }
}
