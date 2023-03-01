<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Hash;
use App\Traits\SendSms;

class DriverloginController extends Controller
{
     use SendSms;
     
     
       public function delieveryboyphoneverify(Request $request)
    {
        
        $this->validate(
            $request, 
            [
                'phone' => 'required',
            ],
            [
                'phone.required' => 'Enter Mobile...',
            ]
        );
    
    	$phone = $request->phone;
   
        $created_at = Carbon::now();
        $updated_at = Carbon::now();
      
    	$checkUser = DB::table('delivery_boy')
    					->where('delivery_boy_phone', $phone)
    					->first();
        $smsby = DB::table('smsby')
              ->first();
        if($smsby->status==1){      
        // check for otp verify
    	if($checkUser && $checkUser->phone_verify==1){
    		$message = array('status'=>'1', 'message'=>'You Already register', 'data'=>[]);
            return $message;
    	}
    	
    ///////if phone not verified/////	
    	
	elseif($checkUser && $checkUser->phone_verify==0){

    						
    						
    			$chars = "0123456789";
                $otpval = "123456";
                // for ($i = 0; $i < 4; $i++){
                //     $otpval .= $chars[mt_rand(0, strlen($chars)-1)];
                // }
                
                
                // $otpmsg = $this->otpmsg($otpval,$phone);
                
                $updateOtp = DB::table('delivery_boy')
                                ->where('delivery_boy_phone', $phone)
                                ->update(['otp'=>$otpval,
                                          'phone_verify'=>1]);
    						
	    		$message = array('status'=>'1', 'message'=>'OTP Sent');
	        	return $message;
	    	}
	    	else{
	    		$message = array('status'=>'0', 'message'=>'Something went wrong');
	        return $message;
	    	}  
    	}
    	
    }

    public function driver_login(Request $request)
     {
        $phone = $request->phone;
        $device_id = $request->device_id;
        $smsby = DB::table('smsby')
              ->first();
        $getUser = DB::table('delivery_boy')
                    ->where('delivery_boy_phone', $phone)
                    ->first();
                    
        if($getUser){
                // verify phone
                $getUser = DB::table('delivery_boy')
                            ->where('delivery_boy_phone', $phone)
                            ->update(['phone_verify'=>1,
                                      'device_id'=>$device_id,
                        ]);
                            
                $delivery_boy = DB::table('delivery_boy')
                                    ->where('delivery_boy_phone', $phone)
                                    ->first();
                    
                $message = array('status'=>1, 'message'=>"Phone Verified! login successfully",'data'=>$delivery_boy);
                return $message;
            }
       
        else{
            $message = array('status'=>0, 'message'=>"User not registered");
            return $message;
        }
    }

    public function driverprofile(Request $request)
    {   
        $boy_id = $request->delivery_boy_id;
         $diver=  DB::table('delivery_boy')
                ->where('delivery_boy_id', $boy_id )
                ->first();
                        
    if($diver){
        	$message = array('status'=>'1', 'message'=>'Delivery Boy Profile', 'data'=>$diver);
	        return $message;
              }
    	else{
    		$message = array('status'=>'0', 'message'=>'Delivery Boy not found', 'data'=>[]);
	        return $message;
    	}
        
    }
 
        public function driverstatus(Request $request)
    {   
        $boy_id = $request->delivery_boy_id;
         $diver=  DB::table('delivery_boy')
                ->where('delivery_boy_id', $boy_id )
                ->select('delivery_boy_status')
                ->first();
                        
    if($diver){
        	$message = array('status'=>'1', 'message'=>'Status', 'data'=>$diver);
	        return $message;
              }
    	else{
    		$message = array('status'=>'0', 'message'=>'Something went wrong', 'data'=>[]);
	        return $message;
    	}
        
    }
   

}