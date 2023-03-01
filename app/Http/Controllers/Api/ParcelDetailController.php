<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use DateTime;
use Carbon\Carbon;
use App\Traits\SendMail;
use App\Traits\SendSms;

class ParcelDetailController extends Controller
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
 public function parcel_detail(Request $request)
    {
        // $weight= $request->weight;
        // $length = $request->length;
        // $height= $request->height;
        // $width = $request->width;
        // $pickup_time = $request->pickup_time;
        $pickup_date = date('d-m-Y');
        // $city_id = $request->city_id;
        $lat = $request->lat;
        $content = $request->content;
        $lng = $request->lng;
        $description = $request->description;

        // $source_pincode = $request->source_pincode;
        // $source_houseno = $request->source_houseno;
        // $source_landmark = $request->source_landmark;
        $source_address = $request->source_address;
        // $source_state = $request->source_state;
        // $source_city = $request->source_city;
        $source_lat = $request->source_lat;
        $source_lng = $request->source_lng;
        $source_phone = $request->source_phone;
        $source_name = $request->source_name;

        // $destination_pincode = $request->destination_pincode;
        // $destination_houseno = $request->destination_houseno;
        // $destination_landmark = $request->destination_landmark;
        $destination_address = $request->destination_address;
        // $destination_state = $request->destination_state;
        // $destination_city = $request->destination_city;
        $destination_lat = $request->destination_lat;
        $destination_lng = $request->destination_lng;
        $destination_phone = $request->destination_phone;
        $destination_name = $request->destination_name;
        $user_id= $request->user_id;
        $vendor_id= $request->vendor_id;
    $dist=$this->getDistance($source_lat,$source_lng,$destination_lat,$destination_lng);
    $parcel_charges = DB::table('parcel_delivery_charges')
    ->where('range_start','<=',$dist)
    ->where('range_end','>=',$dist)
    ->first();
    if(!$parcel_charges){
      $parcelcharges=DB::table('parcel_delivery_charges')
      ->get();
      $parcel_charges=$parcelcharges->last();
    }
    $charges = $parcel_charges->charges;
    // $time = $response_a['rows']['0']['elements']['0']['duration']['text'];
    //  $dist = $response_a;
    //  $time = $response_a;

    // return array('distance' => $dist, 'time' => $time);

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


$source_addres = DB::table('source_address')
->insertGetId([
        // 'source_pincode'=>$source_pincode,
        // 'source_houseno'=>$source_houseno,
        // 'source_landmark'=>$source_landmark,
        'source_add'=>$source_address,
        // 'source_state'=>$source_state,
        // 'source_city'=>$source_city,
        'source_lat'=>$source_lat,
        'source_lng'=>$source_lng,
        'source_phone'=>$source_phone,
        'source_name'=>$source_name,
        ]);

$destination_addres = DB::table('destination_address')
->insertGetId([
        // 'destination_pincode'=>$destination_pincode,
        // 'destination_houseno'=>$destination_houseno,
        // 'destination_landmark'=>$destination_landmark,
        'destination_add'=>$destination_address,
        // 'destination_state'=>$destination_state,
        // 'destination_city'=>$destination_city,
         'destination_lat'=>$destination_lat,
        'destination_lng'=>$destination_lng,
        'destination_phone'=>$destination_phone,
        'destination_name'=>$destination_name
        ]);


        $insert = DB::table('parcel_details')
->insert([
        'source_address_id'=>$source_addres,
        'destination_address_id'=>$destination_addres,
        'cart_id'=>$cart_id,
        'user_id'=>$user_id,
        'vendor_id'=>$vendor_id,
        // 'weight'=>$weight,
        // 'length'=>$length,
        // 'height'=>$height,
        // 'width'=>$width,
        // 'pickup_time'=>$pickup_time,
        'pickup_date'=>$pickup_date,
        // 'city_id'=>$city_id,
        'lat'=>$lat,
        'lng'=>$lng,
        'charges'=>$charges,
        'distance'=>$dist,
        'description'=>$description
        ]);

        if($insert){
        	$message = array('status'=>'1', 'message'=>'Proceed to payment', 'data'=>$insert,'cart_id'=>$cart_id,'charges'=>$charges,'distance'=>$dist,'description'=>$content.' instruction : '.$description);
        	return $message;
        }

        else{
        	$message = array('status'=>'0', 'message'=>'insertion failed', 'data'=>[]);
        	return $message;
        }
    }

 public function parcel_orderplaced(Request $request)
    {
        $cart_id=$request->cart_id;
        $payment_method= $request->payment_method;
        $payment_status = $request->payment_status;
        $wallet = $request->wallet;
        $total_price= $request->total_price;

        $orderr = DB::table('parcel_details')
           ->where('cart_id', $cart_id)
           ->first();
        $vendor_id = $orderr->vendor_id;
         $user_id= $orderr->user_id;

         $price2 = $orderr->rem_price;
        $ph = DB::table('tbl_user')
                  ->select('user_phone','wallet_credits')
                  ->where('user_id',$user_id)
                  ->first();
        $user_phone = $ph->user_phone;

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

          $oo = DB::table('parcel_details')
           ->where('cart_id',$cart_id)
            ->update([
            'wallet'=>$walletamt,
            'rem_price'=>$rem_amount,
            'total_price'=>$total_price,
            'payment_status'=>$payment_status,
            'payment_method'=>$payment_method
            ]);

            $sms = DB::table('notificationby')
                      ->select('sms')
                      ->where('user_id',$user_id)
                      ->first();
            $sms_status = $sms->sms;

                if($sms_status == 1){

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


              }
             if($email->app ==1){
                  $notification_title = "WooHoo! Your Order is Placed";
                $notification_text = "Order Successfully Placed: Your order id #'.$cart_id.' ";

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
                $orderr1 = DB::table('parcel_details')
                       ->where('cart_id', $cart_id)
                       ->first();

                ///////send notification to vendor//////

                $notification_title = "WooHoo ! You Got a New Order";
                $notification_text = "you got an order cart id #'.$cart_id.'";

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
            $oo = DB::table('parcel_details')
           ->where('cart_id',$cart_id)
            ->update([
            'wallet'=>$walletamt,
            'rem_price'=>$rem_amount,
            'total_price'=>$total_price,
            'payment_method'=>$payment_method,
            'payment_status'=>'success'
            ]);
            $sms = DB::table('notificationby')
                      ->select('sms')
                      ->where('user_id',$user_id)
                      ->first();
            $sms_status = $sms->sms;
                if($sms_status == 1){

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


              }
            if($email->app == 1){
                  $notification_title = "WooHoo! Your Order is Placed";
                $notification_text = "Order Successfully Placed: Your order id #'.$cart_id.'";

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
            $orderr1 = DB::table('parcel_details')
           ->where('cart_id', $cart_id)
           ->first();

              ///////send notification to vendor//////

                $notification_title = "WooHoo ! You Got a New Order";
                $notification_text = "you got an order cart id #'.$cart_id.' ";

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
              $oo = DB::table('parcel_details')
           ->where('cart_id',$cart_id)
            ->update([
            'wallet'=>0,
            'rem_price'=>$rem_amount,
            'total_price'=>$total_price,
            'payment_method'=>NULL,
            'payment_status'=>'failed'
            ]);
        	$message = array('status'=>'0', 'message'=>'Payment Failed');
        	return $message;
         }
      }
    }

    public function parcel_after_order_reward_msg(Request $request)
    {
      $created_at = Carbon::now();
        $cart_id = $request->cart_id;
        $check = DB::table('parcel_details')
               ->where('cart_id',$cart_id)
               ->first();
        $p=$check->total_price;
        $user_id=$check->user_id;
        $currency = DB::table('currency')
                  ->first();
        $cc = DB::table('reward_points')
            ->where('min_cart_value',"<=",$p)
            ->orderBy("min_cart_value", "DESC")
            ->first();
          $text1 = "You will get ".$cc->reward_point." reward points once the Order is Completed .";

          $cc2 = DB::table('reward_points')
            ->where('min_cart_value',">",$cc->min_cart_value)
            ->orderBy("min_cart_value", "ASC")
            ->first();

         if($cc2){
            $nu = $cc2->min_cart_value - $p;

            $text2="Add items of ".$currency->currency_sign." ".$nu." more to get ".$cc2->reward_point." reward points.";
         }
         else{
             $text2 = "";
         }


         if($cc){
            $message = array('status'=>'1', 'message'=>'You Got a Rewards Points', 'line1'=>$text1, 'line2'=>$text2);
            return $message;
            }
        else{
            $message = array('status'=>'0', 'message'=>'no rewards with this order', 'data'=>[]);
            return $message;
        }
    }

    public function parcel_user_completed_order(Request $request)
     {

        $user_id = $request->user_id;

        $completeorder  =   DB::table('parcel_details')
                        ->join('tbl_user', 'parcel_details.user_id', '=', 'tbl_user.user_id')
                         ->join('source_address','parcel_details.source_address_id', '=', 'source_address.source_address_id')
                         ->join('destination_address','parcel_details.destination_address_id', '=', 'destination_address.destination_address_id')
                        //   ->join('parcel_city', 'parcel_details.city_id','=', 'parcel_city.city_id')
                          ->join('vendor', 'parcel_details.vendor_id','=', 'vendor.vendor_id')
                          ->Join('delivery_boy','parcel_details.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->where('parcel_details.order_status',"Completed")
                          ->where('parcel_details.user_id',$user_id)
                          ->orderBy('parcel_details.pickup_time', 'desc')
                          ->get();

                          if(count($completeorder)>0){
                            foreach($completeorder as $ords){
                            $data[]=array('parcel_id'=>$ords->parcel_id,'source_address_id'=>$ords->source_address_id,'source_lat'=>$ords->source_lat,'source_lng'=>$ords->source_lng,'source_phone'=>$ords->source_phone,'source_name'=>$ords->source_name,'destination_lat'=>$ords->destination_lat,'destination_lng'=>$ords->destination_lng,'destination_name'=>$ords->destination_name,'destination_phone'=>$ords->destination_phone,'destination_address_id'=>$ords->destination_address_id,'cart_id'=>$ords->cart_id,'user_id'=>$ords->user_id,'vendor_id'=>$ords->vendor_id,'weight'=>$ords->weight,'length'=>$ords->length,'height'=>$ords->height,'width'=>$ords->width,'pickup_time'=>$ords->pickup_time,'pickup_date'=>$ords->pickup_date,'lat'=>$ords->lat,'lng'=>$ords->lng,'charges'=>$ords->charges,'distance'=>$ords->distance,'payment_method'=>$ords->payment_method,'order_status'=>$ords->order_status,'payment_status'=>$ords->payment_status,'wallet'=>$ords->wallet,'dboy_id'=>$ords->dboy_id,'user_name'=>$ords->user_name,'user_email'=>$ords->user_email,'user_image'=>$ords->user_image,'user_phone'=>$ords->user_phone,'user_password'=>$ords->user_password,'device_id'=>$ords->device_id,'wallet_credits'=>$ords->wallet_credits,'rewards'=>$ords->rewards,'otp'=>$ords->otp,'phone_verified'=>$ords->phone_verified,'referral_code'=>$ords->referral_code,'source_pincode'=>$ords->source_pincode,'source_houseno'=>$ords->source_houseno,'source_landmark'=>$ords->source_landmark,'source_add'=>$ords->source_add,'source_state'=>$ords->source_state,'destination_pincode'=>$ords->destination_pincode,'destination_houseno'=>$ords->destination_houseno,'destination_landmark'=>$ords->destination_landmark,'destination_add'=>$ords->destination_add,'destination_state'=>$ords->destination_state,'vendor_name'=>$ords->vendor_name,'owner'=>$ords->owner,'vendor_email'=>$ords->vendor_email,'vendor_phone'=>$ords->vendor_phone,'vendor_logo'=>$ords->vendor_logo,'vendor_loc'=>$ords->vendor_loc,'lat'=>$ords->lat,'lng'=>$ords->lng,'opening_time'=>$ords->opening_time,'closing_time'=>$ords->closing_time,'vendor_pass'=>$ords->vendor_pass,'vendor_category_id'=>$ords->vendor_category_id,'comission'=>$ords->comission,'delivery_range'=>$ords->delivery_range,'ui_type'=>$ords->ui_type,'online_status'=>$ords->online_status,'delivery_boy_id'=>$ords->delivery_boy_id,'delivery_boy_name'=>$ords->delivery_boy_name,'delivery_boy_phone'=>$ords->delivery_boy_phone,'delivery_boy_pass'=>$ords->delivery_boy_pass,'is_confirmed'=>$ords->is_confirmed,'phone_verify'=>$ords->phone_verify,'dboy_comission'=>$ords->dboy_comission,'surgecharge'=>$ords->surgecharge,'nightcharge'=>$ords->nightcharge,'convcharge'=>$ords->convcharge);
                                          }
                                        }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
                                          return $data;

    }

      public function parcel_user_cancel_order(Request $request)
     {

        $user_id = $request->user_id;

        $completeorder  =   DB::table('parcel_details')
                        ->join('tbl_user', 'parcel_details.user_id', '=', 'tbl_user.user_id')
                         ->join('source_address','parcel_details.source_address_id', '=', 'source_address.source_address_id')
                         ->join('destination_address','parcel_details.destination_address_id', '=', 'destination_address.destination_address_id')
                        //   ->join('parcel_city', 'parcel_details.city_id','=', 'parcel_city.city_id')
                          ->join('vendor', 'parcel_details.vendor_id','=', 'vendor.vendor_id')
                          ->Join('delivery_boy','parcel_details.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->where('parcel_details.order_status',"cancelled")
                          ->where('parcel_details.user_id',$user_id)
                          ->orderBy('parcel_details.pickup_time', 'desc')
                          ->get();

                          if(count($completeorder)>0){
                            foreach($completeorder as $ords){
                            $data[]=array('parcel_id'=>$ords->parcel_id,'source_address_id'=>$ords->source_address_id,'source_lat'=>$ords->source_lat,'source_lng'=>$ords->source_lng,'source_phone'=>$ords->source_phone,'source_name'=>$ords->source_name,'destination_lat'=>$ords->destination_lat,'destination_lng'=>$ords->destination_lng,'destination_name'=>$ords->destination_name,'destination_phone'=>$ords->destination_phone,'destination_address_id'=>$ords->destination_address_id,'cart_id'=>$ords->cart_id,'user_id'=>$ords->user_id,'vendor_id'=>$ords->vendor_id,'weight'=>$ords->weight,'length'=>$ords->length,'height'=>$ords->height,'width'=>$ords->width,'pickup_time'=>$ords->pickup_time,'pickup_date'=>$ords->pickup_date,'lat'=>$ords->lat,'lng'=>$ords->lng,'charges'=>$ords->charges,'distance'=>$ords->distance,'payment_method'=>$ords->payment_method,'order_status'=>$ords->order_status,'payment_status'=>$ords->payment_status,'wallet'=>$ords->wallet,'dboy_id'=>$ords->dboy_id,'user_name'=>$ords->user_name,'user_email'=>$ords->user_email,'user_image'=>$ords->user_image,'user_phone'=>$ords->user_phone,'user_password'=>$ords->user_password,'device_id'=>$ords->device_id,'wallet_credits'=>$ords->wallet_credits,'rewards'=>$ords->rewards,'otp'=>$ords->otp,'phone_verified'=>$ords->phone_verified,'referral_code'=>$ords->referral_code,'source_pincode'=>$ords->source_pincode,'source_houseno'=>$ords->source_houseno,'source_landmark'=>$ords->source_landmark,'source_add'=>$ords->source_add,'source_state'=>$ords->source_state,'destination_pincode'=>$ords->destination_pincode,'destination_houseno'=>$ords->destination_houseno,'destination_landmark'=>$ords->destination_landmark,'destination_add'=>$ords->destination_add,'destination_state'=>$ords->destination_state,'vendor_name'=>$ords->vendor_name,'owner'=>$ords->owner,'vendor_email'=>$ords->vendor_email,'vendor_phone'=>$ords->vendor_phone,'vendor_logo'=>$ords->vendor_logo,'vendor_loc'=>$ords->vendor_loc,'lat'=>$ords->lat,'lng'=>$ords->lng,'opening_time'=>$ords->opening_time,'closing_time'=>$ords->closing_time,'vendor_pass'=>$ords->vendor_pass,'vendor_category_id'=>$ords->vendor_category_id,'comission'=>$ords->comission,'delivery_range'=>$ords->delivery_range,'ui_type'=>$ords->ui_type,'online_status'=>$ords->online_status,'delivery_boy_id'=>$ords->delivery_boy_id,'delivery_boy_name'=>$ords->delivery_boy_name,'delivery_boy_phone'=>$ords->delivery_boy_phone,'delivery_boy_pass'=>$ords->delivery_boy_pass,'is_confirmed'=>$ords->is_confirmed,'phone_verify'=>$ords->phone_verify,'dboy_comission'=>$ords->dboy_comission,'surgecharge'=>$ords->surgecharge,'nightcharge'=>$ords->nightcharge,'convcharge'=>$ords->convcharge);
                                          }
                                        }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
                                          return $data;

    }

     public function parcel_user_ongoing_order(Request $request)
     {

        $user_id = $request->user_id;

        $completeorder  =   DB::table('parcel_details')
                        ->join('tbl_user', 'parcel_details.user_id', '=', 'tbl_user.user_id')
                         ->join('source_address','parcel_details.source_address_id', '=', 'source_address.source_address_id')
                         ->join('destination_address','parcel_details.destination_address_id', '=', 'destination_address.destination_address_id')
                        //   ->join('parcel_city', 'parcel_details.city_id','=', 'parcel_city.city_id')
                          ->join('vendor', 'parcel_details.vendor_id','=', 'vendor.vendor_id')
                          ->leftJoin('delivery_boy','parcel_details.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->where('parcel_details.user_id',$user_id)
                          ->orderBy('parcel_details.pickup_time', 'desc')
                          ->where('parcel_details.order_status', '!=', 'Completed')
                           ->where('parcel_details.order_status', '!=', 'Cancelled')
                          ->where('parcel_details.payment_method', '!=', NULL)
                          ->get();

                          if(count($completeorder)>0){
                            foreach($completeorder as $ords){
                            $data[]=array('parcel_id'=>$ords->parcel_id,'source_address_id'=>$ords->source_address_id,'source_lat'=>$ords->source_lat,'source_lng'=>$ords->source_lng,'source_phone'=>$ords->source_phone,'source_name'=>$ords->source_name,'destination_lat'=>$ords->destination_lat,'destination_lng'=>$ords->destination_lng,'destination_name'=>$ords->destination_name,'destination_phone'=>$ords->destination_phone,'destination_address_id'=>$ords->destination_address_id,'cart_id'=>$ords->cart_id,'user_id'=>$ords->user_id,'vendor_id'=>$ords->vendor_id,'weight'=>$ords->weight,'length'=>$ords->length,'height'=>$ords->height,'width'=>$ords->width,'pickup_time'=>$ords->pickup_time,'pickup_date'=>$ords->pickup_date,'lat'=>$ords->lat,'lng'=>$ords->lng,'charges'=>$ords->charges,'distance'=>$ords->distance,'payment_method'=>$ords->payment_method,'order_status'=>$ords->order_status,'payment_status'=>$ords->payment_status,'wallet'=>$ords->wallet,'dboy_id'=>$ords->dboy_id,'user_name'=>$ords->user_name,'user_email'=>$ords->user_email,'user_image'=>$ords->user_image,'user_phone'=>$ords->user_phone,'user_password'=>$ords->user_password,'device_id'=>$ords->device_id,'wallet_credits'=>$ords->wallet_credits,'rewards'=>$ords->rewards,'otp'=>$ords->otp,'phone_verified'=>$ords->phone_verified,'referral_code'=>$ords->referral_code,'source_pincode'=>$ords->source_pincode,'source_houseno'=>$ords->source_houseno,'source_landmark'=>$ords->source_landmark,'source_add'=>$ords->source_add,'source_state'=>$ords->source_state,'destination_pincode'=>$ords->destination_pincode,'destination_houseno'=>$ords->destination_houseno,'destination_landmark'=>$ords->destination_landmark,'destination_add'=>$ords->destination_add,'destination_state'=>$ords->destination_state,'vendor_name'=>$ords->vendor_name,'owner'=>$ords->owner,'vendor_email'=>$ords->vendor_email,'vendor_phone'=>$ords->vendor_phone,'vendor_logo'=>$ords->vendor_logo,'vendor_loc'=>$ords->vendor_loc,'lat'=>$ords->lat,'lng'=>$ords->lng,'opening_time'=>$ords->opening_time,'closing_time'=>$ords->closing_time,'vendor_pass'=>$ords->vendor_pass,'vendor_category_id'=>$ords->vendor_category_id,'comission'=>$ords->comission,'delivery_range'=>$ords->delivery_range,'ui_type'=>$ords->ui_type,'online_status'=>$ords->online_status,'delivery_boy_id'=>$ords->delivery_boy_id,'delivery_boy_name'=>$ords->delivery_boy_name,'delivery_boy_phone'=>$ords->delivery_boy_phone,'parcel_description'=>$ords->description,'surgecharge'=>$ords->surgecharge,'nightcharge'=>$ords->nightcharge,'convcharge'=>$ords->convcharge);
                                          }
                                        }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
                                          return $data;

    }
    public function parceldetails(Request $request)
     {

        $user_id = $request->user_id;
        $cart_id = $request->cart_id;

        $completeorder  =   DB::table('parcel_details')
                        ->join('tbl_user', 'parcel_details.user_id', '=', 'tbl_user.user_id')
                         ->join('source_address','parcel_details.source_address_id', '=', 'source_address.source_address_id')
                         ->join('destination_address','parcel_details.destination_address_id', '=', 'destination_address.destination_address_id')
                        //   ->join('parcel_city', 'parcel_details.city_id','=', 'parcel_city.city_id')
                          ->join('vendor', 'parcel_details.vendor_id','=', 'vendor.vendor_id')
                          ->leftJoin('delivery_boy','parcel_details.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->where('parcel_details.user_id',$user_id)
                           ->where('parcel_details.cart_id',$cart_id)
                          ->orderBy('parcel_details.pickup_time', 'desc')
                          ->where('parcel_details.order_status', '!=', 'Completed')
                           ->where('parcel_details.order_status', '!=', 'Cancelled')
                          ->where('parcel_details.payment_method', '!=', NULL)
                          ->get();

                          if(count($completeorder)>0){
                            foreach($completeorder as $ords){
                            $data[]=array('parcel_id'=>$ords->parcel_id,'source_address_id'=>$ords->source_address_id,'source_lat'=>$ords->source_lat,'source_lng'=>$ords->source_lng,'source_phone'=>$ords->source_phone,'source_name'=>$ords->source_name,'destination_lat'=>$ords->destination_lat,'destination_lng'=>$ords->destination_lng,'destination_name'=>$ords->destination_name,'destination_phone'=>$ords->destination_phone,'destination_address_id'=>$ords->destination_address_id,'cart_id'=>$ords->cart_id,'user_id'=>$ords->user_id,'vendor_id'=>$ords->vendor_id,'weight'=>$ords->weight,'length'=>$ords->length,'height'=>$ords->height,'width'=>$ords->width,'pickup_time'=>$ords->pickup_time,'pickup_date'=>$ords->pickup_date,'lat'=>$ords->lat,'lng'=>$ords->lng,'charges'=>$ords->charges,'distance'=>$ords->distance,'payment_method'=>$ords->payment_method,'order_status'=>$ords->order_status,'payment_status'=>$ords->payment_status,'wallet'=>$ords->wallet,'dboy_id'=>$ords->dboy_id,'user_name'=>$ords->user_name,'user_email'=>$ords->user_email,'user_image'=>$ords->user_image,'user_phone'=>$ords->user_phone,'user_password'=>$ords->user_password,'device_id'=>$ords->device_id,'wallet_credits'=>$ords->wallet_credits,'rewards'=>$ords->rewards,'otp'=>$ords->otp,'phone_verified'=>$ords->phone_verified,'referral_code'=>$ords->referral_code,'source_pincode'=>$ords->source_pincode,'source_houseno'=>$ords->source_houseno,'source_landmark'=>$ords->source_landmark,'source_add'=>$ords->source_add,'source_state'=>$ords->source_state,'destination_pincode'=>$ords->destination_pincode,'destination_houseno'=>$ords->destination_houseno,'destination_landmark'=>$ords->destination_landmark,'destination_add'=>$ords->destination_add,'destination_state'=>$ords->destination_state,'vendor_name'=>$ords->vendor_name,'owner'=>$ords->owner,'vendor_email'=>$ords->vendor_email,'vendor_phone'=>$ords->vendor_phone,'vendor_logo'=>$ords->vendor_logo,'vendor_loc'=>$ords->vendor_loc,'lat'=>$ords->lat,'lng'=>$ords->lng,'opening_time'=>$ords->opening_time,'closing_time'=>$ords->closing_time,'vendor_pass'=>$ords->vendor_pass,'vendor_category_id'=>$ords->vendor_category_id,'comission'=>$ords->comission,'delivery_range'=>$ords->delivery_range,'ui_type'=>$ords->ui_type,'online_status'=>$ords->online_status,'delivery_boy_id'=>$ords->delivery_boy_id,'delivery_boy_name'=>$ords->delivery_boy_name,'delivery_boy_phone'=>$ords->delivery_boy_phone,'parcel_description'=>$ords->description,'surgecharge'=>$ords->surgecharge,'nightcharge'=>$ords->nightcharge,'convcharge'=>$ords->convcharge);
                                          }
                                        }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
                                          return $data;

    }
    public function parcel_check_location(Request $request)
    {
        $vendor=DB::table('vendor')
        ->where('ui_type',4)
        ->first();
        $latlng=json_decode($vendor->latlngarray);
        foreach($latlng as $latlngs)
        {
            $vertices_x[] = $latlngs->lat;
            $vertices_y[] = $latlngs->lng;
        }
        $points_polygon = count($vertices_x) - 1;  // number vertices - zero-based array
        $longitude_x = $request->lat; // x-coordinate of the point to test
        $latitude_y = $request->lng;    // y-coordinate of the point to test

          $i = $j = $c = 0;
          for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
            if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
             ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
              $c = !$c;
          }
          if($c){
          $data=array('status'=>1,'message'=>'can be delivered');
          }
          else
          {
           $data=array('status'=>0,'message'=>'Cannot be delivered to this location');
          }
          return $data;
    }
       public function parcel_today_order(Request $request)
    {


    	$currentDate = date('d-m-Y');
        $day = 1;
       $current2 = date('d-m-Y', strtotime($currentDate.' + '.$day.' days'));


        $vendor_id = $request->vendor_id;

        $completeorder  =   DB::table('parcel_details')
                        ->join('tbl_user', 'parcel_details.user_id', '=', 'tbl_user.user_id')
                         ->join('source_address','parcel_details.source_address_id', '=', 'source_address.source_address_id')
                         ->join('destination_address','parcel_details.destination_address_id', '=', 'destination_address.destination_address_id')
                        //   ->join('parcel_city', 'parcel_details.city_id','=', 'parcel_city.city_id')
                          ->join('vendor', 'parcel_details.vendor_id','=', 'vendor.vendor_id')
                          ->leftJoin('delivery_boy','parcel_details.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->where('parcel_details.vendor_id',$vendor_id)
                          ->where('parcel_details.pickup_date', $currentDate)
                        //   ->orderBy('parcel_details.pickup_time', 'desc')
                          ->where('parcel_details.order_status', '!=', 'Completed')
                           ->where('parcel_details.order_status', '!=', 'Cancelled')
                          ->where('parcel_details.payment_method', '!=', NULL)
                          ->get();

                          if(count($completeorder)>0){
                            foreach($completeorder as $ords){
                            $data[]=array('parcel_id'=>$ords->parcel_id,'source_address_id'=>$ords->source_address_id,'source_lat'=>$ords->source_lat,'source_lng'=>$ords->source_lng,'source_phone'=>$ords->source_phone,'source_name'=>$ords->source_name,'destination_lat'=>$ords->destination_lat,'destination_lng'=>$ords->destination_lng,'destination_name'=>$ords->destination_name,'destination_phone'=>$ords->destination_phone,'destination_address_id'=>$ords->destination_address_id,'cart_id'=>$ords->cart_id,'user_id'=>$ords->user_id,'vendor_id'=>$ords->vendor_id,'weight'=>$ords->weight,'length'=>$ords->length,'height'=>$ords->height,'width'=>$ords->width,'pickup_time'=>$ords->pickup_time,'pickup_date'=>$ords->pickup_date,'lat'=>$ords->lat,'lng'=>$ords->lng,'charges'=>$ords->charges,'distance'=>$ords->distance,'payment_method'=>$ords->payment_method,'order_status'=>$ords->order_status,'payment_status'=>$ords->payment_status,'wallet'=>$ords->wallet,'dboy_id'=>$ords->dboy_id,'user_name'=>$ords->user_name,'user_email'=>$ords->user_email,'user_image'=>$ords->user_image,'user_phone'=>$ords->user_phone,'user_password'=>$ords->user_password,'device_id'=>$ords->device_id,'wallet_credits'=>$ords->wallet_credits,'rewards'=>$ords->rewards,'otp'=>$ords->otp,'phone_verified'=>$ords->phone_verified,'referral_code'=>$ords->referral_code,'source_pincode'=>$ords->source_pincode,'source_houseno'=>$ords->source_houseno,'source_landmark'=>$ords->source_landmark,'source_add'=>$ords->source_add,'source_state'=>$ords->source_state,'destination_pincode'=>$ords->destination_pincode,'destination_houseno'=>$ords->destination_houseno,'destination_landmark'=>$ords->destination_landmark,'destination_add'=>$ords->destination_add,'destination_state'=>$ords->destination_state,'vendor_name'=>$ords->vendor_name,'owner'=>$ords->owner,'vendor_email'=>$ords->vendor_email,'vendor_phone'=>$ords->vendor_phone,'vendor_logo'=>$ords->vendor_logo,'vendor_loc'=>$ords->vendor_loc,'lat'=>$ords->lat,'lng'=>$ords->lng,'opening_time'=>$ords->opening_time,'closing_time'=>$ords->closing_time,'vendor_pass'=>$ords->vendor_pass,'vendor_category_id'=>$ords->vendor_category_id,'comission'=>$ords->comission,'delivery_range'=>$ords->delivery_range,'ui_type'=>$ords->ui_type,'online_status'=>$ords->online_status,'delivery_boy_id'=>$ords->delivery_boy_id,'delivery_boy_name'=>$ords->delivery_boy_name,'delivery_boy_phone'=>$ords->delivery_boy_phone,'parcel_description'=>$ords->description,'surgecharge'=>$ords->surgecharge,'nightcharge'=>$ords->nightcharge,'convcharge'=>$ords->convcharge);
                                          }
                                        }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
                                          return $data;



      }
    public function parcel_store_order(Request $request)

         {

             $delivery_id = $request->delivery_boy_id;
             $parcel_id = $request->parcel_id;
              $update = DB::table('parcel_details')
                       ->where('parcel_id',$parcel_id)
                       ->update(['dboy_id'=>$delivery_id,
                       'order_status'=>"Confirmed"
                       ]);

                       $notification_title = "New Order Assign";

                       $getDevice = DB::table('delivery_boy')
                         ->where('delivery_boy_id', $delivery_id)
                        ->select('device_id','delivery_boy_name')
                        ->first();
                        $delivery_boy_name = $getDevice->delivery_boy_name;
                          $created_at = Carbon::now();

                          $notification_text = "Hi, .$delivery_boy_name. you received new order please check the details ";

                       if($update)

                       {
                                $getFcm = DB::table('fcm_key')
                                 ->first();

                            $getFcmKey = $getFcm->dboy_app_key;
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




                $results = json_decode($result);


                $mess = array('status'=>'1', 'message'=>'Delivery boy assigned successfully', 'data'=>$update);
                        return $mess;
                }
                 else{


                        $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
                        return $message;
                    }


         }


}
