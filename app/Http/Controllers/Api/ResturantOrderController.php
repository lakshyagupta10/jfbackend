<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use DateTime;
use Carbon\Carbon;
use App\Traits\SendMail;
use App\Traits\SendSms;

class ResturantOrderController extends Controller
{
 use SendMail;
 use SendSms;
 public function getDistance($lat1,$long1,$lat2,$long2)
{
 $key=DB::table('map_API')
 ->first();
 $key=$key->map_api_key;
 $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=en-EN&key=".$key;
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $response = curl_exec($ch);
 curl_close($ch);
 $response_a = json_decode($response, true);
 $dist = $response_a['rows']['0']['elements']['0']['distance']['text'];
 $time = $response_a['rows']['0']['elements']['0']['duration']['text'];
 $dist = floatval($dist);
  // $dist = $response_a;
  // $time = $response_a;

 return $dist;
}
  public function deliverycharges($ar,$vendor)
 {
        $dist = $this->getDistance($ar->lat,$ar->lng,$vendor->lat,$vendor->lng);
        $delivery_charge_id=DB::table('vendor')->select('delivery_charge_id')->where('vendor_id',$vendor->vendor_id)->first();
        $delivery_charges = DB::table('delivery_charges')
        ->where('delivery_charge_id',$delivery_charge_id->delivery_charge_id)
        ->orderBy('charges')
        ->get();
        $del_c = 0;
        foreach ($delivery_charges as $delivery_charge) {
          if($dist>=$delivery_charge->range_start && $dist<=$delivery_charge->range_end){
            $del_c = ($delivery_charge->charges);
          }
        }
        if(!$del_c)
        {
            $delivery_charge=$delivery_charges->last();
            $del_c = ($delivery_charge->charges);
        }
        return $del_c;
 }
  public function ordercharges(Request $request)
 {
        $vendor_id= $request->vendor_id;
        $user_id= $request->user_id;
                $ar= DB::table('user_address')
            ->select('*')
            ->where('user_id', $user_id)
            ->where('select_status', 1)
            ->first();
       if(!$ar){
           	$message = array('status'=>'0', 'message'=>'Select any Address');
        	return $message;
       }
       $vendor=DB::table('vendor')->where('vendor_id',$vendor_id)->first;
$del_c=$this->deliverycharges($ar,$vendor);
        	$message = array('status'=>'1', 'delivery_charges'=>$del_c);
        	return $message;
 }
 public function returant_order(Request $request)
    {
      $admin_set = DB::table('cityadmin')
     ->first();
      $surgecharge=0;
      $nightcharge=0;
      $convcharge=0;
      if($admin_set->surge==1){
          $surgecharge=$admin_set->surge_percent;}
      if($admin_set->night==1){
          $nightcharge=$admin_set->night_charge;}
      if($admin_set->conv==1){
          $convcharge=$admin_set->conv_charge;}
        $current = Carbon::now();
        $data= $request->order_array;
        $del_c=$request->del_c;
        $data_array = json_decode($data);

        $data1= $request->order_array1;
        $data_array1 = json_decode($data1);

        $user_id= $request->user_id;
        $delivery_date = $request-> delivery_date;
        $time_slot= $request->time_slot;
        $vendor_id = $request->vendor_id;
        $ui_type = $request->ui_type;
        $packaging_charges=DB::table('vendor')
        ->where('vendor_id',$vendor_id)
        ->first();
        $packaging_charges=$packaging_charges->packaging_charges;
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $val = "";
                for ($i = 0; $i < 4; $i++){
                    $val .= $chars[mt_rand(0, strlen($chars)-1)];
                }

        $chars2 = "0123456789";
                $val2 = "";
                for ($i = 0; $i < 2; $i++){
                    $val2 .= $chars2[mt_rand(0, strlen($chars2)-1)];
                }
        $cr  = substr(md5(microtime()),rand(0,26),2);
        $cart_id = $val.$val2.$cr;
        $ar= DB::table('user_address')
            ->select('*')
            ->where('user_id', $user_id)
            ->where('select_status', 1)
            ->first();
        $area_id = $ar->area_id;
          $c = DB::table('vendor_area')
        //   ->join('vendor_area','area.area_id','=','vendor_area.area_id')
        //   ->select('vendor_area.*','area.area_name')
        //   ->where('area.area_id',$area_id)
        ->where('area_id',$area_id)
        ->where('vendor_id',$vendor_id)
           ->first();
       $vendor= DB::table('vendor')
        ->where('vendor_id',$vendor_id)
        ->first();
        $created_at = Carbon::now();
        $user_id= $request->user_id;
        $price2=0;
        $price3=0;
        $price5=0;
        $gst=0;
        $total_mrp = 0;
        $ph = DB::table('tbl_user')
                  ->select('user_phone','wallet_credits')
                  ->where('user_id',$user_id)
                  ->first();
        $user_phone = $ph->user_phone;


    foreach ($data_array as $h){
        $variant_id = $h->variant_id;
         $p =  DB::table('resturant_variant')
             ->join('resturant_product','resturant_variant.product_id','=','resturant_product.product_id')
             ->join('resturant_category','resturant_product.subcat_id','resturant_category.resturant_cat_id')
           ->Leftjoin('resturant_deal_product','resturant_variant.variant_id','=','resturant_deal_product.variant_id')
           ->where('resturant_variant.variant_id',$variant_id)
           ->where('resturant_variant.vendor_id',$vendor_id)
           ->first();
         if($p->deal_price != NULL){
          $price= $p->deal_price;
        }else{
      $price = $p->price;
        }

        $mrpprice = $p->strick_price;
        $order_qty = $h->qty;
        $price2+= $price*$order_qty;
        $gst+=(($price*$order_qty)*($p->tax_slab)/100);
        $total_mrp =$mrpprice*$order_qty;
        $price5+=$mrpprice*$order_qty;
        $unit[] = $p->unit;
        $qty[]= $p->quantity;
        $p_name[] = $p->product_name."(".$p->quantity.$p->unit.")*".$order_qty;
        $prod_name = implode(',',$p_name);
        $n =$p->product_name;
        ////////addons/////////

        if($data1 != NUll){
     foreach ($data_array1 as $i){
        $addon_id = $i->addon_id;

        $add = DB::table('restaurant_addons')
            ->select('addon_price','addon_name')
            ->where('restaurant_addons.addon_id', $addon_id)
            ->first();
        $addon_price = $add->addon_price;
        $addprice = $addon_price;
        $a_name[] = $add->addon_name;
        $ad_name = implode(',',$a_name);
        $price3+= $addon_price;
        $total_price = $price3+$price;


        $insert = DB::table('order_details')
                ->insertGetId([
                        'varient_id'=>$variant_id,
                        'qty'=>$order_qty,
                        'product_name'=>$n,
                        'varient_image'=>$p->product_image,
                        'quantity'=>$p->quantity,
                        'unit'=>$p->unit,
                        'total_mrp'=>$total_mrp,
                        'order_cart_id'=>$cart_id,
                        'order_date'=>$created_at,
                        'price'=>$total_price,
                        'addon_name'=>$add->addon_name,
                        'addon_id'=>$addon_id,
                        'addon_price'=>$addon_price]);




       }
        }
        else{
            $a_name =0;
            $a_price =0;
            $addon_id = 0;
            $total_price =$price;

        $insert = DB::table('order_details')
                ->insertGetId([
                        'varient_id'=>$variant_id,
                        'qty'=>$order_qty,
                        'product_name'=>$n,
                        'varient_image'=>$p->product_image,
                        'quantity'=>$p->quantity,
                        'unit'=>$p->unit,
                        'total_mrp'=>$total_mrp,
                        'order_cart_id'=>$cart_id,
                        'order_date'=>$created_at,
                        'price'=>$total_price,
                        'addon_name'=>$a_name,
                        'addon_id'=>$addon_id,
                        'addon_price'=>$a_price]);

        }



 }


  if($insert){
        $oo = DB::table('orders')
            ->insertGetId(['cart_id'=>$cart_id,
            'total_price'=>$price2 + $del_c + $surgecharge+$nightcharge+$convcharge+$packaging_charges+$gst,
            'price_without_delivery'=>$price2,
            'total_products_mrp'=>$price5,
            'delivery_charge'=>$del_c,
            'user_id'=>$user_id,
            'dboy_incentive'=>0,
            'vendor_id'=>$vendor_id,
            'rem_price'=>$price2 + $del_c+ $surgecharge+$nightcharge+$convcharge+$packaging_charges+$gst,
            'order_date'=> $created_at,
            'delivery_date'=> $current,
            'time_slot'=>$time_slot,
            'ui_type'=>$ui_type,
            'address_id'=>$ar->address_id,
            'gst'=>$gst
          ]);



          $ordersuccessed = DB::table('orders')
                          ->where('order_id',$oo)
                          ->first();
        $instruction = $request->instruction;
        if($request->instruction){
            DB::table("order_instructions")
            ->insertGetId([
                'order_id'=>$oo,
                'instruction'=>$instruction
                ]);
        }

        }

          if($oo){
        	$message = array('status'=>'1', 'message'=>'Proceed to payment', 'data'=>$ordersuccessed );
        	return $message;
        }

        else{
        	$message = array('status'=>'0', 'message'=>'insertion failed', 'data'=>[]);
        	return $message;
        }


 }

 public function orderplaced(Request $request)
    {
        $cart_id=$request->cart_id;
        $payment_method= $request->payment_method;
        $payment_status = $request->payment_status;
        $wallet = $request->wallet;
        $orderr = DB::table('orders')
           ->where('cart_id', $cart_id)
           ->first();
        $vendor_id = $orderr->vendor_id;
        $user_id= $orderr->user_id;
        $delivery_date = $orderr->delivery_date;
        $time_slot= $orderr->time_slot;

        $var= DB::table('order_details')
           ->where('order_cart_id', $cart_id)
           ->get();
        $price2 = $orderr->rem_price;
        $ph = DB::table('tbl_user')
                  ->select('user_phone','wallet_credits')
                  ->where('user_id',$user_id)
                  ->first();
        $user_phone = $ph->user_phone;
        foreach ($var as $h){
        $varient_id = $h->varient_id;
        $p = DB::table('order_details')
           ->where('order_cart_id',$cart_id)
           ->where('varient_id',$varient_id)
           ->first();
        $price = $p->price;
        $order_qty = $h->qty;
        $unit[] = $p->unit;
        $qty[]= $p->quantity;
        $p_name[] = $p->product_name."(".$p->quantity.$p->unit.")*".$order_qty;
        $prod_name = implode(',',$p_name);
        // $addon_name[]= $p->addon_name;
        // $addon_name = implode(',',$addons_name);
        }
         $charge = 0;
         $prii = $price2;
        if ($payment_method == 'COD' || $payment_method =='cod'){
             $walletamt = 0;

             $payment_status="COD";
            if($wallet == 'yes' || $wallet == 'Yes' || $wallet == 'YES'){
             if($ph->wallet_credits >= $prii){
                $rem_amount = 0;
                $walletamt = $prii;
                $rem_wallet = $ph->wallet_credits-$prii;
                $walupdate = DB::table('tbl_user')
                           ->where('user_id',$user_id)
                           ->update(['wallet_credits'=>$rem_wallet]);
                $payment_status="success";
                $payment_method = "wallet";
             }
             else{

                $rem_amount= $prii - $ph->wallet_credits;
                $walletamt = $ph->wallet_credits;
                $rem_wallet = 0;
                $walupdate = DB::table('tbl_user')
                           ->where('user_id',$user_id)
                           ->update(['wallet_credits'=>$rem_wallet]);
             }
         }
         else{
             $rem_amount=  $prii;
             $walletamt= 0;
         }

          $oo = DB::table('orders')
           ->where('cart_id',$cart_id)
            ->update([
            'paid_by_wallet'=>$walletamt,
            'rem_price'=>$rem_amount,
            'payment_status'=>$payment_status,
            'payment_method'=>$payment_method
            ]);

            $sms = DB::table('notificationby')
                      ->select('sms')
                      ->where('user_id',$user_id)
                      ->first();
            $sms_status = $sms->sms;

                if($sms_status == 1){
                    $orderplacedmsg = $this->ordersuccessfull($cart_id,$prod_name,$price2,$delivery_date,$time_slot,$user_phone);
                }
                      /////send mail
            $email = DB::table('notificationby')
                  ->select('email','app')
                  ->where('user_id',$user_id)
                  ->first();
             $q = DB::table('tbl_user')
                              ->select('user_email','user_name')
                              ->where('user_id',$user_id)
                              ->first();
            $user_email = $q->user_email;

            $user_name = $q->user_name;
            $email_status = $email->email;
            if($email_status == 1){

                    $codorderplaced = $this->codorderplacedMail($cart_id,$prod_name,$price2,$delivery_date,$time_slot,$user_email,$user_name);
              }
             if($email->app ==1){
                  $notification_title = "WooHoo! Your Order is Placed";
                $notification_text = "Order Successfully Placed: Your order id #".$cart_id." contains of " .$prod_name." of price rs ".$price2. " is placed Successfully.You can expect your item(s) will be delivered on ".$delivery_date." between ".$time_slot.".";

                $date = date('d-m-Y');


                $getDevice = DB::table('tbl_user')
                         ->where('user_id', $user_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();

                if($getDevice){


                $getFcm = DB::table('fcm_key')
                            ->first();

                $getFcmKey = $getFcm->user_app_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;


                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];

                    $extraNotificationData = ["message" => $notification];

                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];

                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);


                $dd = DB::table('user_notification')
                    ->insert(['user_id'=>$user_id,
                     'noti_title'=>$notification_title,
                     'noti_message'=>$notification_text]);

                $results = json_decode($result);
                }
             }
                $orderr1 = DB::table('orders')
                       ->where('cart_id', $cart_id)
                       ->first();

                ///////send notification to vendor//////

                $notification_title = "WooHoo ! You Got a New Order";
                $notification_text = "you got an order cart id #".$cart_id." contains of " .$prod_name." of price rs ".$price2. ". It will have to delivered on ".$delivery_date." between ".$time_slot.".";

                $date = date('d-m-Y');
                $getUser = DB::table('vendor')
                                ->get();

                $getDevice = DB::table('vendor')
                         ->where('vendor_id', $vendor_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();

                if($getDevice){


                $getFcm = DB::table('fcm_key')
                            ->first();

                $getFcmKey = $getFcm->vendor_app_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;


                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];

                    $extraNotificationData = ["message" => $notification];

                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];

                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);

                     ///////send notification to vendor//////

                $dd = DB::table('vendor_notification')
                    ->insert(['vendor_id'=>$vendor_id,
                     'not_title'=>$notification_title,
                     'not_message'=>$notification_text]);

                $results = json_decode($result);
                }




            $message = array('status'=>'1', 'message'=>'Order Placed successfully', 'data'=>$orderr1 );
        	return $message;
        }

        else{
        $walletamt = 0;
        $prii = $price2 + $charge;
        if($request->wallet == 'yes' || $request->wallet == 'Yes' || $request->wallet == 'YES'){
             if($ph->wallet_credits >= $prii){
                $rem_amount = 0;
                $walletamt = $prii;
                $rem_wallet = $ph->wallet_credits - $prii;
                $walupdate = DB::table('tbl_user')
                           ->where('user_id',$user_id)
                           ->update(['wallet_credits'=>$rem_wallet]);
                $payment_status="success";
                $payment_method = "wallet";
             }
             else{

                $rem_amount=  $prii-$ph->wallet_credits;
                $walletamt = $ph->wallet_credits;
                $rem_wallet =0;
                $walupdate = DB::table('tbl_user')
                           ->where('user_id',$user_id)
                           ->update(['wallet_credits'=>$rem_wallet]);
             }
         }
          else{
              $rem_amount=  $prii;
              $walletamt = 0;
          }
        if($payment_status=='success'){
            $oo = DB::table('orders')
           ->where('cart_id',$cart_id)
            ->update([
            'paid_by_wallet'=>$walletamt,
            'rem_price'=>$rem_amount,
            'payment_method'=>$payment_method,
            'payment_status'=>'success'
            ]);
            $sms = DB::table('notificationby')
                      ->select('sms')
                      ->where('user_id',$user_id)
                      ->first();
            $sms_status = $sms->sms;
                if($sms_status == 1){
                $codorderplaced = $this->ordersuccessfull($cart_id,$prod_name,$price2,$delivery_date,$time_slot,$user_phone);
                }
                      /////send mail
            $email = DB::table('notificationby')
                   ->select('email','app')
                   ->where('user_id',$user_id)
                   ->first();
            $email_status = $email->email;
             $q = DB::table('tbl_user')
                  ->select('user_email','user_name')
                  ->where('user_id',$user_id)
                  ->first();
            $user_email = $q->user_email;
            $user_name = $q->user_name;
            if($email_status == 1){


                    $orderplaced = $this->orderplacedMail($cart_id,$prod_name,$price2,$delivery_date,$time_slot,$user_email,$user_name);
              }
            if($email->app == 1){
                  $notification_title = "WooHoo! Your Order is Placed";
                $notification_text = "Order Successfully Placed: Your order id #".$cart_id." contains of " .$prod_name." of price rs ".$price2. " is placed Successfully.You can expect your item(s) will be delivered on ".$delivery_date." between ".$time_slot.".";

                $date = date('d-m-Y');


                $getDevice = DB::table('tbl_user')
                         ->where('user_id', $user_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();

                if($getDevice){


                $getFcm = DB::table('fcm_key')
                            ->first();

                $getFcmKey = $getFcm->user_app_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;


                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];

                    $extraNotificationData = ["message" => $notification];

                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];

                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);


                $dd = DB::table('user_notification')
                    ->insert(['user_id'=>$user_id,
                     'noti_title'=>$notification_title,
                     'noti_message'=>$notification_text]);

                $results = json_decode($result);
                }
             }
            $orderr1 = DB::table('orders')
           ->where('cart_id', $cart_id)
           ->first();

              ///////send notification to vendor//////

                $notification_title = "WooHoo ! You Got a New Order";
                $notification_text = "you got an order cart id #".$cart_id." contains of " .$prod_name." of price rs ".$price2. ". It will have to delivered on ".$delivery_date." between ".$time_slot.".";

                $date = date('d-m-Y');
                $getUser = DB::table('vendor')
                                ->get();

                $getDevice = DB::table('vendor')
                         ->where('vendor_id', $vendor_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();

                if($getDevice){

                $getFcm = DB::table('fcm_key')
                            ->first();

                $getFcmKey = $getFcm->vendor_app_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;


                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];

                    $extraNotificationData = ["message" => $notification];

                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];

                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);

                     ///////send notification to vendor//////

                $dd = DB::table('vendor_notification')
                    ->insert(['vendor_id'=>$vendor_id,
                     'not_title'=>$notification_title,
                     'not_message'=>$notification_text]);

                $results = json_decode($result);
                }


            $message = array('status'=>'1', 'message'=>'Order Placed successfully', 'data'=>$orderr1 );
        	return $message;
         }
         else{
              $oo = DB::table('orders')
           ->where('cart_id',$cart_id)
            ->update([
            'paid_by_wallet'=>0,
            'rem_price'=>$rem_amount,
            'payment_method'=>NULL,
            'payment_status'=>'failed'
            ]);
        	$message = array('status'=>'0', 'message'=>'Payment Failed');
        	return $message;
         }
      }
    }

 public function order_cancel(Request $request)
    {
      $created_at = Carbon::now();
      $cart_id = $request->cart_id;
       $user = DB::table('orders')
              ->where('cart_id',$cart_id)
              ->first();
        $user_id1 = $user->user_id;
         $userwa1 = DB::table('tbl_user')
                     ->where('user_id',$user_id1)
                     ->first();
      $reason = $request->reason;
      $order_status = 'Cancelled';
      $updated_at = Carbon::now();
      $order = DB::table('orders')
                  ->where('cart_id', $cart_id)
                  ->update([
                        'cancelling_reason'=>$reason,
                        'order_status'=>$order_status,
                        'canceled_at'=>$updated_at]);

       if($order){
        if($user->payment_method == 'COD' || $user->payment_method == 'Cod' || $user->payment_method == 'cod'){
            $newbal1 = $userwa1->wallet_credits + $user->paid_by_wallet;
            $insert= DB::table('wallet_history')
                    ->insert([
                                'type'=>'Refund',
                                'amount'=>$user->paid_by_wallet,
                                'user_id'=>$user_id1,
                                'created_at'=>$created_at,
                             ]);

              }
        elseif($user->payment_status=='success'){
                  $newbal1 = $userwa1->wallet_credits + $user->rem_price + $user->paid_by_wallet;

                  $insert= DB::table('wallet_history')
                    ->insert([
                                'type'=>'Refund',
                                'amount'=>$user->paid_by_wallet +$user->rem_price,
                                'user_id'=>$user_id1,
                                'created_at'=>$created_at,
                             ]);
              }
        else{
                   $newbal1 = $userwa1->wallet_credits;
              }

           $userwalletupdate = DB::table('tbl_user')
             ->where('user_id',$user_id1)
             ->update(['wallet_credits'=>$newbal1]);
        	$message = array('status'=>'1', 'message'=>'order cancelled', 'data'=>$order);
        	return $message;
        }
        else{
        	$message = array('status'=>'0', 'message'=>'something went wrong', 'data'=>[]);
        	return $message;
        }


  }

 public function user_completed_orders(Request $request)
    {
      $user_id = $request->user_id;
      $completeds = DB::table('orders')
                ->join('vendor','orders.vendor_id','=','vendor.vendor_id')
                ->join('user_address','orders.address_id','=','user_address.address_id')
               ->leftJoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.delivery_boy_id')
                ->select('orders.*','delivery_boy.*','vendor.*','user_address.*','vendor.lat as vendorlat','vendor.lng as vendorlng','user_address.lat as userlat','user_address.lng as userlng')
              ->where('orders.user_id',$user_id)
              ->where('orders.order_status', 'Completed')
              ->where('orders.ui_type', '2')
              ->get();

      if(count($completeds)>0){
      foreach($completeds as $completed){
      $order = DB::table('order_details')
              ->leftJoin('resturant_variant', 'order_details.varient_id','=','resturant_variant.variant_id')
              ->leftJoin('restaurant_addons', 'order_details.addon_id','=','restaurant_addons.addon_id')
              ->select('order_details.*')
              ->where('order_details.order_cart_id',$completed->cart_id)
              ->orderBy('order_details.order_date', 'DESC')
              ->get();


        $data[]=array('order_status'=>$completed->order_status, 'delivery_date'=>$completed->delivery_date,'time_slot'=>$completed->time_slot,'payment_method'=>$completed->payment_method,'payment_status'=>$completed->payment_status,'paid_by_wallet'=>$completed->paid_by_wallet, 'cart_id'=>$completed->cart_id ,'price'=>$completed->total_price,'del_charge'=>$completed->delivery_charge,'remaining_amount'=>$completed->rem_price,'coupon_discount'=>$completed->coupon_discount,'surgecharge'=>$completed->surgecharge,'nightcharge'=>$completed->nightcharge,'convcharge'=>$completed->convcharge,'gst'=>$completed->gst,'delivery_boy_name'=>$completed->delivery_boy_name,'delivery_boy_phone'=>$completed->delivery_boy_phone,'vendor_name'=>$completed->vendor_name,
            'address'=>$completed->address,'delivery_lat'=>$completed->userlat,'delivery_lng'=>$completed->userlng,'vendor_lat'=>$completed->vendorlat,'vendor_lng'=>$completed->vendorlng,'data'=>$order);
        }
        }
        else{
            $data=array('data'=>[]);
        }
        return $data;


  }

  public function user_cancel_order_history(Request $request)
    {
      $user_id = $request->user_id;
      $completed = DB::table('orders')
            ->join('vendor','orders.vendor_id','=','vendor.vendor_id')
            ->leftjoin('user_address','orders.address_id','=','user_address.address_id')
              ->where('orders.user_id',$user_id)
              ->where('order_status', 'cancelled')
              ->where('orders.ui_type', '2')
               ->get();

      if(count($completed)>0){
      foreach($completed as $completeds){
      $order = DB::table('order_details')
            ->join ('resturant_variant', 'order_details.varient_id', '=', 'resturant_variant.variant_id')
            ->join ('resturant_product', 'resturant_variant.product_id', '=', 'resturant_product.product_id')
            ->join ('restaurant_addons', 'resturant_product.product_id', '=', 'restaurant_addons.product_id')
                  ->select('resturant_variant.variant_id','resturant_product.product_name', 'resturant_product.product_image','order_details.qty','resturant_product.description','resturant_variant.unit','resturant_variant.quantity','order_details.order_cart_id','restaurant_addons.addon_name','restaurant_addons.addon_price','restaurant_addons.addon_id')
                  ->where('order_details.order_cart_id',$completeds->cart_id)
                  ->groupBy('resturant_variant.variant_id','resturant_product.product_name', 'resturant_product.product_image','order_details.qty','resturant_product.description','resturant_variant.unit','resturant_variant.quantity','order_details.order_cart_id','restaurant_addons.addon_name','restaurant_addons.addon_price','restaurant_addons.addon_id')
                  ->orderBy('order_details.order_date', 'DESC')
                  ->get();


        $data[]=array('order_status'=>$completeds->order_status,'vendor_name'=>$completeds->vendor_name, 'delivery_date'=>$completeds->delivery_date, 'time_slot'=>$completeds->time_slot,'payment_method'=>$completeds->payment_method,'payment_status'=>$completeds->payment_status,'paid_by_wallet'=>$completeds->paid_by_wallet,'surgecharge'=>$completeds->surgecharge,'nightcharge'=>$completeds->nightcharge,'convcharge'=>$completeds->convcharge, 'cart_id'=>$completeds->cart_id ,'price'=>$completeds->total_price,'del_charge'=>$completeds->delivery_charge,'gst'=>$completeds->gst,'remaining_amount'=>$completeds->rem_price,'coupon_discount'=>$completeds->coupon_discount,
            'address'=>$completeds->address,'data'=>$order);
        }
        }
        else{
            $data[]=array('data'=>'No Cancelled Orders Yet');
        }
        return $data;


  }

   public function user_ongoing_order(Request $request)
    {
      $user_id = $request->user_id;
      $ongoing = DB::table('orders')

             ->leftJoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.delivery_boy_id')
             ->join('vendor','orders.vendor_id','=','vendor.vendor_id')
             ->join('user_address','orders.address_id','=','user_address.address_id')
              ->select('orders.*','delivery_boy.*','vendor.*','user_address.*','vendor.lat as vendorlat','vendor.lng as vendorlng','user_address.lat as userlat','user_address.lng as userlng')
              ->where('orders.user_id',$user_id)
              ->where('orders.order_status', '!=', 'Completed')
               ->where('orders.order_status', '!=', 'Cancelled')
              ->where('orders.payment_method', '!=', NULL)
              ->where('orders.ui_type', '2')
              ->orderBy('orders.order_id', 'DESC')
               ->get();

      if(count($ongoing)>0){
      foreach($ongoing as $ongoings){
      $order = DB::table('order_details')
            ->leftJoin('resturant_variant', 'order_details.varient_id','=','resturant_variant.variant_id')
            ->select('order_details.*')
            ->where('order_details.order_cart_id',$ongoings->cart_id)
            ->orderBy('order_details.order_date', 'DESC')
            ->get();


        $data[]=array('order_status'=>$ongoings->order_status,'vendor_name'=>$ongoings->vendor_name, 'delivery_date'=>$ongoings->delivery_date, 'time_slot'=>$ongoings->time_slot,'payment_method'=>$ongoings->payment_method,'payment_status'=>$ongoings->payment_status,'paid_by_wallet'=>$ongoings->paid_by_wallet,'surgecharge'=>$ongoings->surgecharge,'nightcharge'=>$ongoings->nightcharge,'convcharge'=>$ongoings->convcharge,'gst'=>$ongoings->gst, 'cart_id'=>$ongoings->cart_id ,'price'=>$ongoings->total_price,'price_without_delivery'=>$ongoings->price_without_delivery,'delivery_charge'=>$ongoings->delivery_charge,'remaining_amount'=>$ongoings->rem_price,'coupon_discount'=>$ongoings->coupon_discount,'delivery_boy_name'=>$ongoings->delivery_boy_name,'delivery_boy_phone'=>$ongoings->delivery_boy_phone,
             'address'=>$ongoings->address,'delivery_lat'=>$ongoings->userlat,'delivery_lng'=>$ongoings->userlng,'vendor_lat'=>$ongoings->vendorlat,'vendor_lng'=>$ongoings->vendorlng,'data'=>$order);
        }
        }
        else{
             $data=array('data'=>[]);
        }
        return $data;


  }

   public function orderdetails(Request $request)
    {
      $user_id = $request->user_id;
      $cart_id = $request->cart_id;
      $ongoing = DB::table('orders')

             ->leftJoin('delivery_boy', 'orders.dboy_id', '=', 'delivery_boy.delivery_boy_id')
             ->join('vendor','orders.vendor_id','=','vendor.vendor_id')
             ->join('user_address','orders.address_id','=','user_address.address_id')
              ->select('orders.*','delivery_boy.*','vendor.*','user_address.*','vendor.lat as vendorlat','vendor.lng as vendorlng','user_address.lat as userlat','user_address.lng as userlng')
              ->where('orders.user_id',$user_id)
              ->where('orders.order_status', '!=', 'Completed')
               ->where('orders.order_status', '!=', 'Cancelled')
              ->where('orders.payment_method', '!=', NULL)
              ->where('orders.cart_id', '=', $cart_id)
              ->where('orders.ui_type', '2')
              ->orderBy('orders.order_id', 'DESC')
               ->get();

      if(count($ongoing)>0){
      foreach($ongoing as $ongoings){
      $order = DB::table('order_details')
            ->leftJoin('resturant_variant', 'order_details.varient_id','=','resturant_variant.variant_id')
            ->select('order_details.*')
            ->where('order_details.order_cart_id',$ongoings->cart_id)
            ->orderBy('order_details.order_date', 'DESC')
            ->get();


        $data[]=array('order_status'=>$ongoings->order_status,'vendor_name'=>$ongoings->vendor_name, 'delivery_date'=>$ongoings->delivery_date, 'time_slot'=>$ongoings->time_slot,'payment_method'=>$ongoings->payment_method,'payment_status'=>$ongoings->payment_status,'paid_by_wallet'=>$ongoings->paid_by_wallet,'surgecharge'=>$ongoings->surgecharge,'nightcharge'=>$ongoings->nightcharge,'convcharge'=>$ongoings->convcharge,'gst'=>$ongoings->gst, 'cart_id'=>$ongoings->cart_id ,'price'=>$ongoings->total_price,'price_without_delivery'=>$ongoings->price_without_delivery,'delivery_charge'=>$ongoings->delivery_charge,'remaining_amount'=>$ongoings->rem_price,'coupon_discount'=>$ongoings->coupon_discount,'delivery_boy_name'=>$ongoings->delivery_boy_name,'delivery_boy_phone'=>$ongoings->delivery_boy_phone,
             'address'=>$ongoings->address,'delivery_lat'=>$ongoings->userlat,'delivery_lng'=>$ongoings->userlng,'vendor_lat'=>$ongoings->vendorlat,'vendor_lng'=>$ongoings->vendorlng,'data'=>$order);
        }
        }
        else{
             $data=array('data'=>[]);
        }
        return $data;


  }

      public function dealproduct(Request $request)
    {
        $d = Carbon::Now();
        $vendor_id = $request->vendor_id;

        $deal_p= DB::table('deal_product')
                ->join('product_varient', 'deal_product.varient_id', '=', 'product_varient.varient_id')
                ->join('product', 'product_varient.product_id', '=', 'product.product_id')
                ->select('product_varient.stock','deal_product.deal_price as price', 'product_varient.varient_image', 'product_varient.quantity','product_varient.unit', 'product_varient.price','product_varient.description' ,'product.product_name','product.product_image','product_varient.varient_id','product.product_id','deal_product.valid_to','deal_product.valid_from')
                ->groupBy('product_varient.stock','deal_product.deal_price', 'product_varient.varient_image', 'product_varient.quantity','product_varient.unit', 'product_varient.price','product_varient.description' ,'product.product_name','product.product_image','product_varient.varient_id','product.product_id','deal_product.valid_to','deal_product.valid_from')

                ->where('deal_product.valid_from','<=',$d->toDateString())
                ->where('deal_product.valid_to','>',$d->toDateString())
                ->where('deal_product.vendor_id',$vendor_id)
                ->get();


          if(count($deal_p)>0){
           $result =array();
            $i = 0;
             $j = 0;
            foreach ($deal_p as $deal_ps) {
                  array_push($result, $deal_ps);

                $val_to =  new DateTime($deal_ps->valid_to);
                $diff_in_minutes = $d->diffInMinutes($val_to);
                $totalDuration =  $d->diff($val_to)->format('%H:%I:%S');
                $result[$i]->timediff = $diff_in_minutes;
                $i++;
                $result[$j]->hoursmin= $totalDuration;
                $j++;
            }

            $message = array('status'=>'1', 'message'=>'Products found', 'data'=>$deal_p);
            return $message;
        }
        else{
            $message = array('status'=>'0', 'message'=>'Products not found', 'data'=>[]);
            return $message;
        }



    }


}
