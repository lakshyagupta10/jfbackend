<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function apply_coupon(Request $request)
    {
        $cart_id = $request->cart_id;
        $coupon_code = $request->coupon_code;

        $coupon = DB::table('coupon')
                ->where('coupon_code', $coupon_code)
                ->first();
        $check = DB::table('orders')
               ->where('cart_id',$cart_id)
               ->where('vendor_id',$coupon->vendor_id)
               ->first();
         $p=$check->total_price;
         $orderchecked =DB::table('orders')
              ->where('cart_id',$cart_id)
              ->where('coupon_id',$coupon->coupon_id)
              ->first();

        if(!$orderchecked){
         $check2 = DB::table('orders')
               ->where('coupon_id',$coupon->coupon_id)
               ->where('user_id',$check->user_id)
               ->count();

        if($coupon->uses_restriction > $check2){

        $mincart = $coupon->cart_value;
        $am = $coupon->amount;
        $type = $coupon->type;
        if($type=='%'||$type=='Percentage'||$type=='percentage'){
          $per = ($p*$am)/100;
          $rem_price = $p-$per;
        }
        else{
            $per = $am;
            $rem_price = $p-$am;
        }
        $update=DB::table('orders')
              ->where('cart_id',$cart_id)
              ->where('vendor_id',$coupon->vendor_id)
              ->update(['rem_price'=>$rem_price,
              'coupon_discount'=>$per,
              'coupon_id'=>$coupon->coupon_id]);
        $update2=DB::table('orders')
                ->where('cart_id',$cart_id)
                ->where('vendor_id','54')
                ->first();
        if($update2){
          $update3=DB::table('orders')
                  ->where('cart_id',$cart_id)
                  ->where('vendor_id','54')
                  ->update(['rem_price'=>$update2->total_price-$per,
                'coupon_discount'=>$update2->coupon_discount+$per,
              'coupon_id'=>$coupon->coupon_id]);
        }
        $order =DB::table('orders')
              ->where('cart_id',$cart_id)
              ->first();
     if($order){
        if($update){
            $message = array('status'=>'1', 'message'=>'Coupon Applied Successfully', 'data'=>$order);
            return $message;
            }
        else{
            $message = array('status'=>'0', 'message'=>'Cannot not Applied', 'data'=>$order);
            return $message;
        }
     }else{
         $message = array('status'=>'0', 'message'=>'order not found');
         return $message;
     }
    }
    else{
         $message = array('status'=>'0', 'message'=>'Invalid Coupon! Maximum use limit reached');
         return $message;
    }
        }
        else{
            $update=DB::table('orders')
              ->where('cart_id',$cart_id)
              ->update(['rem_price'=>$p,
              'coupon_discount'=>0,
              'coupon_id'=>0]);
             $order =DB::table('orders')
              ->where('cart_id',$cart_id)
              ->first();

         if($update){
            $message = array('status'=>'2', 'message'=>'Coupon Unapplied', 'data'=>$order);
            return $message;
            }
        else{
            $message = array('status'=>'0', 'message'=>'Something went wrong', 'data'=>$order);
            return $message;
        }
        }
    }

    public function coupon_list(Request $request)
    {
        $currentdate = Carbon::now();
        $vendors = json_decode($request->vendors);
        array_unique($vendors);
        array_push($vendors,"54");
        $data=array();
        foreach ($vendors as $vendor)
        {
            $coupon = DB::table('coupon')
                ->select('coupon.*','vendor.vendor_id','vendor.vendor_name')
                ->join('vendor','coupon.vendor_id','vendor.vendor_id')
                ->where('start_date','<=',$currentdate)
                ->where('end_date','>=',$currentdate)
                ->where('coupon.vendor_id',$vendor)
                ->get();
                $coupon=json_decode($coupon);
                $data=array_merge($data,$coupon);
        }
         if(count($data)>0){
            $message = array('status'=>'1', 'message'=>'Coupon List', 'data'=>$data);
            return $message;
            }
        else{
            $message = array('status'=>'0', 'message'=>'Coupon not Found');
            return $message;
        }

    }

}
