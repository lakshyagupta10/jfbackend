<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Hash;
use App\Traits\SendMail;
use App\Traits\SendSms;
 
class DriverOrderController extends Controller
{
use SendMail;
 use SendSms;
    public function completed_orders(Request $request)
     {
         
        $delivery_boy_id = $request->delivery_boy_id;
    	 		   
        $ord =DB::table('orders')
             ->join('tbl_user', 'orders.user_id', '=','tbl_user.user_id')
             ->join('vendor', 'orders.vendor_id', '=', 'vendor.vendor_id')
             ->join('user_address', 'orders.address_id','=','user_address.address_id')
             ->join('delivery_boy', 'orders.dboy_id', '=','delivery_boy.delivery_boy_id')
             ->select('orders.order_status','orders.cart_id','tbl_user.user_name', 'tbl_user.user_phone', 'orders.delivery_date', 'orders.total_price','orders.delivery_charge','orders.rem_price','orders.payment_status','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.time_slot', 'vendor.vendor_name','vendor.vendor_phone','vendor.lat as vendor_lat','vendor.lng as vendor_lng','user_address.lat as userlat', 'user_address.lng as userlng', 'delivery_boy.lat as dboy_lat', 'delivery_boy.lng as dboy_lng', 'user_address.user_number', 'user_address.houseno','user_address.state','user_address.street','user_address.pincode')
             ->groupBy('orders.order_status','orders.cart_id','tbl_user.user_name', 'tbl_user.user_phone', 'orders.delivery_date', 'orders.total_price','orders.delivery_charge','orders.rem_price','orders.payment_status','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.time_slot', 'vendor.vendor_name','vendor.vendor_phone','vendor_lat','vendor_lng','userlat', 'userlng', 'dboy_lat', 'dboy_lng', 'user_address.user_number', 'user_address.houseno','user_address.state','user_address.street','user_address.pincode')
             ->where('orders.order_status' , 'completed')
             ->where('orders.ui_type' , '1')
             ->where('orders.dboy_id',$delivery_boy_id)
             ->orderBy('orders.delivery_date', 'desc')
             ->get();
       
       if(count($ord)>0){
      foreach($ord as $ords){
             $cart_id = $ords->cart_id;    
         $details  =   DB::table('order_details')
    	                ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
    	                ->join('product','product_varient.product_id', '=', 'product.product_id')
    	                ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'))
    	                ->groupBy('order_details.order_cart_id','product.product_id','product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty')
    	               ->where('order_details.order_cart_id',$cart_id)
    	               ->get(); 
                  
        
        $data[]=array('user_address'=>$ords->houseno.','.$ords->pincode.','.$ords->state.','.$ords->street ,'order_status'=>$ords->order_status,'vendor_name'=>$ords->vendor_name, 'vendor_lat'=>$ords->vendor_lat, 'vendor_lng'=>$ords->vendor_lng,'user_lat'=>$ords->userlat, 'user_lng'=>$ords->userlng, 'dboy_lat'=>$ords->dboy_lat, 'dboy_lng'=>$ords->dboy_lng, 'cart_id'=>$cart_id,'user_name'=>$ords->user_name, 'user_phone'=>$ords->user_phone, 'remaining_price'=>$ords->rem_price,'delivery_boy_name'=>$ords->delivery_boy_name,'delivery_boy_phone'=>$ords->delivery_boy_phone,'delivery_date'=>$ords->delivery_date,'time_slot'=>$ords->time_slot,'order_details'=>$details); 
        }
        }
        else{
            $data[]=array('order_details'=>'no orders found');
        }
        return $data;     
    }  
    
     public function ordersfortoday(Request $request)
     {
        $date = date('Y-m-d');
        $delivery_boy_id = $request->delivery_boy_id;
    	 		   
        $ord =DB::table('orders')
             ->join('tbl_user', 'orders.user_id', '=','tbl_user.user_id')
             ->join('vendor', 'orders.vendor_id', '=', 'vendor.vendor_id')
             ->join('user_address', 'orders.address_id','=','user_address.address_id')
             ->join('delivery_boy', 'orders.dboy_id', '=','delivery_boy.delivery_boy_id')
             ->select('orders.order_id','orders.user_id','orders.order_status','orders.cart_id','tbl_user.user_name', 'tbl_user.user_phone', 'orders.delivery_date', 'orders.total_price','orders.delivery_charge','orders.rem_price','orders.payment_status','orders.payment_method','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.time_slot', 'vendor.vendor_loc as vendor_address', 'vendor.vendor_name','vendor.vendor_phone','vendor.lat as vendor_lat','vendor.lng as vendor_lng','user_address.lat as userlat', 'user_address.lng as userlng', 'delivery_boy.lat as dboy_lat', 'delivery_boy.lng as dboy_lng', 'user_address.user_number',  'user_address.houseno','user_address.pincode','user_address.houseno','user_address.street','user_address.state')
             ->groupBy('orders.order_id','orders.user_id','orders.order_status','orders.cart_id','tbl_user.user_name', 'tbl_user.user_phone', 'orders.delivery_date', 'orders.total_price','orders.delivery_charge','orders.rem_price','orders.payment_status','orders.payment_method','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.time_slot', 'vendor.vendor_loc', 'vendor.vendor_name','vendor.vendor_phone','vendor.lat','vendor.lng','user_address.lat', 'user_address.lng', 'delivery_boy.lat', 'delivery_boy.lng', 'user_address.user_number',  'user_address.houseno','user_address.pincode','user_address.houseno','user_address.street','user_address.state')
             ->where('orders.order_status','!=', 'completed')
             ->where('orders.order_status','!=', 'Cancelled')
              ->where('orders.ui_type' , '1')
             ->where('orders.vendor_id','!=',0)
             ->where('orders.dboy_id',$delivery_boy_id)
             ->where('orders.delivery_date', $date)
             ->orderBy('orders.time_slot', 'ASC')
             ->get();
       
       if(count($ord)>0){
      $carts=array();
      foreach($ord as $ords){
             $cart_id = $ords->cart_id;    
         $details  =   DB::table('order_details')
    	               ->where('order_cart_id',$cart_id)
    	               ->sum('order_details.qty');
    	  $description =  DB::table('order_details')
    	                ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
    	                ->join('product','product_varient.product_id', '=', 'product.product_id')
    	                ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'))
    	                ->groupBy('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty')
    	               ->where('order_details.order_cart_id',$cart_id)
    	               ->get();             
            if(in_array($ords->cart_id,$carts))
            {
            }
            else{
        
        $data[]=array('order_id'=>$ords->order_id,'payment_method'=>$ords->payment_method, 'payment_status'=>$ords->payment_status,'user_id'=>$ords->user_id,'user_address'=>$ords->houseno.','.$ords->street.','.$ords->pincode.','.$ords->state ,'order_status'=>$ords->order_status,'vendor_name'=>$ords->vendor_name, 'vendor_lat'=>$ords->vendor_lat, 'vendor_lng'=>$ords->vendor_lng, 'vendor_address'=>$ords->vendor_address, 'user_lat'=>$ords->userlat, 'user_lng'=>$ords->userlng, 'dboy_lat'=>$ords->dboy_lat, 'dboy_lng'=>$ords->dboy_lng, 'cart_id'=>$cart_id,'user_name'=>$ords->user_name, 'user_phone'=>$ords->user_phone, 'remaining_price'=>$ords->rem_price,'delivery_boy_name'=>$ords->delivery_boy_name,'delivery_boy_phone'=>$ords->delivery_boy_phone,'delivery_date'=>$ords->delivery_date,'time_slot'=>$ords->time_slot,'total_items'=>$details,'order_details'=>$description); 
             $carts[]=$ords->cart_id;
        }
      }
        }
        else{
            $data[]=array('no_order'=>'no orders found');
        }
        return $data;     
    } 
    public function ordersfortodaydetails(Request $request)
    {
        $date = date('Y-m-d');
        $delivery_boy_id = $request->delivery_boy_id;
    	$order_id=$request->order_id; 		   
        $ords = DB::table('orders')
        ->where('order_id',$order_id)
        ->first();
        if($ords){
             $cart_id = $ords->cart_id;    
             $details = DB::table('order_details')
                                        ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                        ->join('product','product_varient.product_id', '=', 'product.product_id')
                                        ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('vendor.vendor_id','vendor.vendor_loc as vendor_address', 'vendor.vendor_name','vendor.vendor_phone','vendor.lat as vendor_lat','vendor.lng as vendor_lng')
                                            ->where('order_cart_id',$cart_id)
                                            ->groupby('vendor.vendor_id','vendor_address', 'vendor.vendor_name','vendor.vendor_phone','vendor_lat','vendor_lng')
                                            ->get();
                                foreach($details as $vendor){
            $description  =   DB::table('order_details')
    	               ->where('order_cart_id',$cart_id)
    	               ->sum('order_details.qty');
    	  $vendordetails =  DB::table('order_details')
    	                ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
    	                ->join('product','product_varient.product_id', '=', 'product.product_id')
    	                ->join('vendor','product.vendor_id','vendor.vendor_id')
    	                ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty','vendor.vendor_id','vendor.vendor_name','vendor.lat','vendor.lng','vendor.vendor_loc','vendor.vendor_phone', DB::raw('SUM(order_details.qty) as total_items'))
    	                ->groupBy('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty','vendor.vendor_id','vendor.vendor_name','vendor.lat','vendor.lng','vendor.vendor_loc','vendor.vendor_phone')
    	               ->where('order_details.order_cart_id',$cart_id)
    	               ->where('vendor.vendor_id',$vendor->vendor_id)
    	               ->get();    
    	               $vendor->vendordetails=$vendordetails;
                                }
                                        $data[]=array('order_details'=>$details); 
       }
       else{
            $data[]=array('no_order'=>'no orders found');
        }
        return $data; 
    }
    public function ordersfornextday(Request $request)
     {
         $date = date('Y-m-d');
         $day = 1;
         $next_date = date('Y-m-d', strtotime($date.' + '.$day.' days'));
         $delivery_boy_id = $request->delivery_boy_id;
    	 		   
        $ord =DB::table('orders')
             ->join('tbl_user', 'orders.user_id', '=','tbl_user.user_id')
             ->join('vendor', 'orders.vendor_id', '=', 'vendor.vendor_id')
             ->join('user_address', 'orders.address_id','=','user_address.address_id')
             ->join('delivery_boy', 'orders.dboy_id', '=','delivery_boy.delivery_boy_id')
             ->select('orders.order_status','orders.cart_id','tbl_user.user_name', 'tbl_user.user_phone', 'orders.delivery_date', 'orders.total_price','orders.delivery_charge','orders.rem_price','orders.payment_status','orders.payment_method','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.time_slot', 'vendor.vendor_loc as vendor_address', 'vendor.vendor_name','vendor.vendor_phone','vendor.lat as vendor_lat','vendor.lng as vendor_lng','user_address.lat as userlat', 'user_address.lng as userlng', 'delivery_boy.lat as dboy_lat', 'delivery_boy.lng as dboy_lng', 'user_address.user_number',  'user_address.houseno','user_address.pincode','user_address.houseno','user_address.street','user_address.state')
             ->groupBy('orders.order_status','orders.cart_id','tbl_user.user_name', 'tbl_user.user_phone', 'orders.delivery_date', 'orders.total_price','orders.delivery_charge','orders.rem_price','orders.payment_status','orders.payment_method','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.time_slot', 'vendor.vendor_loc as vendor_address', 'vendor.vendor_name','vendor.vendor_phone','vendor.lat as vendor_lat','vendor.lng as vendor_lng','user_address.lat as userlat', 'user_address.lng as userlng', 'delivery_boy.lat as dboy_lat', 'delivery_boy.lng as dboy_lng', 'user_address.user_number',  'user_address.houseno','user_address.pincode','user_address.houseno','user_address.street','user_address.state')
             ->where('orders.order_status','!=', 'completed')
             ->where('orders.order_status','!=', 'Cancelled')
             ->where('orders.vendor_id','!=',0)
              ->where('orders.ui_type' , '1')
             ->where('orders.dboy_id',$delivery_boy_id)
             ->whereDate('orders.delivery_date', $next_date)
             ->orderBy('orders.time_slot', 'ASC')
             ->get();
       
       if(count($ord)>0){
      foreach($ord as $ords){
             $cart_id = $ords->cart_id;    
         $details  =   DB::table('order_details')
    	               ->where('order_cart_id',$cart_id)
    	               ->sum('order_details.qty');
         $description =  DB::table('order_details')
    	                ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
    	                ->join('product','product_varient.product_id', '=', 'product.product_id')
    	                ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'))
    	               ->where('order_details.order_cart_id',$cart_id)
    	               ->get();     
        
        $data[]=array('payment_method'=>$ords->payment_method, 'payment_status'=>$ords->payment_status,'user_address'=>$ords->houseno.','.$ords->street.','.$ords->pincode.','.$ords->state ,'order_status'=>$ords->order_status,'vendor_name'=>$ords->vendor_name, 'vendor_lat'=>$ords->vendor_lat, 'vendor_lng'=>$ords->vendor_lng, 'vendor_address'=>$ords->vendor_address, 'user_lat'=>$ords->userlat, 'user_lng'=>$ords->userlng, 'dboy_lat'=>$ords->dboy_lat, 'dboy_lng'=>$ords->dboy_lng, 'cart_id'=>$cart_id,'user_name'=>$ords->user_name, 'user_phone'=>$ords->user_phone, 'remaining_price'=>$ords->rem_price,'delivery_boy_name'=>$ords->delivery_boy_name,'delivery_boy_phone'=>$ords->delivery_boy_phone,'delivery_date'=>$ords->delivery_date,'time_slot'=>$ords->time_slot,'total_items'=>$details,'order_details'=>$description); 
        }
        }
        else{
            $data[]=array('no_order'=>'no orders found');
        }
        return $data;     
    } 
    
    public function delivery_accepted(Request $request)
    {
       $cart_id= $request->cart_id;
       $ord = DB::table('orders')
            ->where('cart_id',$cart_id)
            ->first();
        $vendor_id = $ord->vendor_id;
        $user_id=$ord->user_id;    
       $var= DB::table('order_details')
           ->where('order_cart_id', $cart_id)
           ->get();
        $price2=0;
        $ph = DB::table('tbl_user')
                   ->select('user_phone','wallet_credits')
                   ->where('user_id',$ord->user_id)
                   ->first();
         $user_phone = $ph->user_phone;   
         foreach ($var as $h){
         $varient_id = $h->varient_id;
         $p = DB::table('product_varient')
            ->join('product','product_varient.product_id','=','product.product_id')
           ->where('product_varient.varient_id',$varient_id)
         ->first();

        $price = $p->price;
        $order_qty = $h->qty;
        $price2+= $price*$order_qty;
        $unit[] = $p->unit;
        $qty[]= $p->quantity;
        $p_name[] = $p->product_name."(".$p->quantity.$p->unit.")*".$order_qty;
        $prod_name = implode(',',$p_name);
        }
        $currency = DB::table('currency')
                  ->first();
      $status = 'Delivery Accepted';
      $update= DB::table('orders')
              ->where('cart_id',$cart_id)
              ->update(['order_status'=>$status]);
              
        if($update){
            
           
    	   $message = array('status'=>'1', 'message'=>'Order Accepted By Delivery Boy');
        	return $message;
    	          }          
            else{
             $message = array('status'=>'0', 'message'=>'something went wrong');
        	return $message;
      }       
              
    }
    
    
     public function delivery_out(Request $request)
    {
       $cart_id= $request->cart_id;
       $ord = DB::table('orders')
            ->where('cart_id',$cart_id)
            ->first();
        $vendor_id = $ord->vendor_id;
        $user_id=$ord->user_id;    
       $var= DB::table('order_details')
           ->where('order_cart_id', $cart_id)
           ->get();
        $price2=0;
        $ph = DB::table('tbl_user')
                  ->select('user_phone','wallet_credits')
                  ->where('user_id',$ord->user_id)
                  ->first();
        $user_phone = $ph->user_phone;   
        foreach ($var as $h){
        $varient_id = $h->varient_id;
        $p = DB::table('product_varient')
           
            ->join('product','product_varient.product_id','=','product.product_id')
           ->where('product_varient.varient_id',$varient_id)
           ->first();
            $price = $p->price;
        $order_qty = $h->qty;
        $price2+= $price*$order_qty;
        $unit[] = $p->unit;
        $qty[]= $p->quantity;
        $p_name[] = $p->product_name."(".$p->quantity.$p->unit.")*".$order_qty;
        $prod_name = implode(',',$p_name);
        }
        $currency = DB::table('currency')
                  ->first();
               
       $status = 'Out For Delivery';
       $update= DB::table('orders')
              ->where('cart_id',$cart_id)
              ->update(['order_status'=>$status]);
              
        if($update){
            
               
            $sms = DB::table('notificationby')
                      ->select('sms','app')
                      ->where('user_id',$ord->user_id)
                      ->first();
            $sms_status = $sms->sms;
            $sms_api_key=  DB::table('msg91')
    	              ->select('api_key', 'sender_id')
                      ->first();
            $api_key = $sms_api_key->api_key;
            $sender_id = $sms_api_key->sender_id;
                if($sms_status == 1){
                $successmsg = $this->delout($cart_id, $prod_name, $price2,$currency,$ord,$user_phone);
                }
                
                //////send app notification////
                if($sms->app == 1){
                    if($ord->payment_method=="COD" || $ord->payment_method=="cod"){
                $notification_title = "Out For Delivery";
                $notification_text = "Out For Delivery: Your order id #".$cart_id." contains of " .$prod_name." of price ".$currency->currency_sign." ".$price2. " is Out For Delivery.Get ready with ".$currency->currency_sign." ".$ord->rem_price. " cash.";
                
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
                    else{
                        
                        $notification_title = "Out For Delivery";
                        $notification_text = "Out For Delivery: Your order id #".$cart_id." contains of " .$prod_name." of price " .$currency->currency_sign." ".$price2. " is Out For Delivery.Get ready.";
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
                }
                      /////send mail
            $email = DB::table('notificationby')
                  ->select('email')
                  ->where('user_id',$ord->user_id)
                  ->first();
            $email_status = $email->email; 
            $rem_price = $ord->rem_price;
            if($email_status == 1){
                if($ord->payment_method=="COD" || $ord->payment_method=="cod"){
                    $q = DB::table('tbl_user')
                              ->select('user_email','user_name')
                              ->where('user_id',$ord->user_id)
                              ->first();
                    $user_email = $q->user_email;   
                    $user_name = $q->user_name;
                      $successmail = $this->coddeloutMail($cart_id, $prod_name, $price2,$user_email, $user_name,$rem_price);
                    }
                    else{
                    $q = DB::table('tbl_user')
                              ->select('user_email','user_name')
                              ->where('user_id',$ord->user_id)
                              ->first();
                    $user_email = $q->user_email;   
                    $user_name = $q->user_name;
                         $successmail = $this->deloutMail($cart_id, $prod_name, $price2,$user_email, $user_name,$rem_price);
                    }
              }
    	   $message = array('status'=>'1', 'message'=>'out for delivery');
        	return $message;
    	          }          
            else{
             $message = array('status'=>'0', 'message'=>'something went wrong');
        	return $message;
       }       
              
    }
    
    public function delivery_completed(Request $request)
    {
        $created_at = Carbon::now();
       $cart_id= $request->cart_id;
       $cash_amount= $request->cash_amount;
       $delivery_boy_id= $request->delivery_boy_id;
       $currency = DB::table('currency')
            ->first();
        $ord = DB::table('orders')
            ->where('cart_id',$cart_id)
            ->first();
          $order_date =  $ord->delivery_date;
          $product_price =  $ord->total_products_mrp;
		  $vendor_id=$ord->vendor_id;
		  $payment_method=$ord->payment_method;
          $user_id = $ord->user_id; 
          $vendor = DB::table('vendor')
                    ->select('vendor_name','comission')
                    ->where('vendor_id',$vendor_id)
                    ->first();
                    $vendor_name = $vendor->vendor_name;
                    $comission = $vendor->comission;
                    $comission_value = ($comission/100)* $product_price;
    $delivery_boy_comission = DB::table('delivery_boy')
                            ->select('delivery_boy_name','dboy_comission')
                            ->where('delivery_boy_id',$delivery_boy_id)
                            ->first();  
            
            $dboy_comission = $delivery_boy_comission->dboy_comission;
            $dboy_comission_value = ($dboy_comission/100)* $product_price;
            
            if($request->user_signature){
            $user_signature = $request->user_signature;
            $user_signature = str_replace('data:image/png;base64,', '', $user_signature);
            $fileName = str_replace(" ", "-", $user_signature);
            $fileName = date('dmyHis').'user_signature'.'.'.'png';
            $fileName = str_replace(" ", "-", $fileName);
            \File::put(public_path(). '/images/user/signature/' . $fileName, base64_decode($user_signature));
            $user_signature = 'public/images/user/signature/'.$fileName;
        }
            else{
                $user_signature = 'N/A';
            }    
       $var= DB::table('order_details')
           ->where('order_cart_id', $cart_id)
           ->get();
        $price2=0;
        $ph = DB::table('tbl_user')
                  ->select('user_phone','wallet_credits','rewards','user_name')
                  ->where('user_id',$ord->user_id)
                  ->first();
        $user_phone = $ph->user_phone;
        $user_name = $ph->user_name;
        $rewards = $ph->rewards;
        foreach ($var as $h){
        $varient_id = $h->varient_id;
       $p = DB::table('product_varient')
           
            ->join('product','product_varient.product_id','=','product.product_id')
           ->where('product_varient.varient_id',$varient_id)
           ->first();
        $price = $p->price;   
        $order_qty = $h->qty;
        $price2+= $price*$order_qty;
        $unit[] = $p->unit;
        $qty[]= $p->quantity;
        $p_name[] = $p->product_name."(".$p->quantity.$p->unit.")*".$order_qty;
        $prod_name = implode(',',$p_name);
        }
        
       $status = 'Completed';
       $update= DB::table('orders')
              ->where('cart_id',$cart_id)
              ->update(['order_status'=>$status,'user_signature'=>$user_signature]);
              
        if($update){
                   
            $sms = DB::table('notificationby')
                      ->select('sms','app')
                      ->where('user_id',$ord->user_id)
                      ->first();
            $sms_status = $sms->sms;
            $sms_api_key=  DB::table('msg91')
    	              ->select('api_key', 'sender_id')
                      ->first();
            $api_key = $sms_api_key->api_key;
            $sender_id = $sms_api_key->sender_id;
                if($sms_status == 1){
                    $successmsg = $this->delcomsms($cart_id, $prod_name, $price2,$currency,$user_phone); 
                   
                }
                ////send notification to app///
                if($sms->app == 1){
                $notification_title = "Order Delivered";
                $notification_text = "Delivery Completed: Your order id #".$cart_id." contains of " .$prod_name." of price ".$currency->currency_sign." ".$price2." is Delivered Successfully.";
                
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
            /////send mail
            $email = DB::table('notificationby')
                  ->select('email')
                  ->where('user_id',$ord->user_id)
                  ->first();
            $email_status = $email->email;       
            if($email_status == 1){
                    $q = DB::table('tbl_user')
                              ->select('user_email','user_name')
                              ->where('user_id',$ord->user_id)
                              ->first();
                    $user_email = $q->user_email;             
                    $user_name =$q->user_name;
                    $successmail = $this->delcomMail($cart_id, $prod_name, $price2,$user_email,$user_name); 
              }
			  ////rewards earned////
           $checkre =DB::table('reward_points')
                    ->where('min_cart_value','<=',$ord->total_price)
                    ->orderBy('min_cart_value','desc')
                    ->first();
            if($checkre){       
           $reward_point = $checkre->reward_point;
           $updated_reward_point = $reward_point + $rewards;
           
           $inreward = DB::table('tbl_user')
                     ->where('user_id',$user_id)
                     ->update(['rewards'=>$updated_reward_point]);
           
           $cartreward = DB::table('reward_history')
                     ->insert(['cart_id'=>$cart_id, 'reward_points'=>$reward_point, 'user_id'=>$user_id,'total_amount'=>$ord->total_price,'created_at'=>$created_at]);
            $comission = DB::table('comission')   
                        ->insert(['cart_id'=>$cart_id, 'vendor_id'=>$vendor_id, 'vendor_name'=>$vendor_name,'comission_price'=>$comission_value,'order_date'=>$order_date,'user_name'=>$user_name,'status'=>'Pending','total_price'=>$product_price,'payment_method'=>$payment_method]);
            $cash_collect = DB::table('cash_collect')   
                        ->insert(['cart_id'=>$cart_id, 'vendor_id'=>$vendor_id,'user_id'=>$user_id,'amount'=>$cash_amount,'date_of_collection'=>$created_at,'delivery_boy_id'=>$delivery_boy_id,'created_at'=>$created_at,'no_of_orders'=>1]);
            $dboy_comission = DB::table('delivery_boy_comission')   
                        ->insert(['cart_id'=>$cart_id, 'vendor_id'=>$vendor_id,'comission_price'=>$dboy_comission_value,'order_date'=>$order_date,'user_name'=>$user_name,'status'=>'Pending','total_price'=>$product_price,'payment_method'=>$payment_method,'delivery_boy_id'=>$delivery_boy_id]);           
            }
    	   $message = array('status'=>'1', 'message'=>'Delivery Completed');
        	return $message;
    	          }          
            else{
             $message = array('status'=>'0', 'message'=>'something went wrong');
        	return $message;
       }       
              
    }
    
    public function cashcollect (Request $request)
    {
        $delivery_boy_id = $request->delivery_boy_id;
        $cash =  DB::table('cash_collect')
                ->select(DB::raw('SUM(amount) as sum') ,DB::raw('count(no_of_orders) as count'))
                ->where('delivery_boy_id',$delivery_boy_id)
                ->first();
    
            if($cash)
            {
                $message = array('status'=>'1', 'message'=>'Total Cash', 'data'=>$cash);
        	    return $message;
            }
            else
            {
                $message = array('status'=>'0', 'message'=>'data found');
        	    return $message;
            }
    }
}