<?php

namespace App\Http\Controllers\Resturant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class TimeSlotController extends Controller
{

    public function resturanttimeslot(Request $request)
    {
          $vendor_email=Session::get('vendor');
        
                    $vendor=DB::table('vendor')
                    ->where('vendor_email',$vendor_email)
                    ->first();
       
        
        $city = DB::table('time_slot')
                ->where('vendor_id',$vendor->vendor_id)
                    ->first();
                
                
        return view('resturant.time_slot.time_slot', compact("vendor_email",'vendor','city'));    
        
        
    }

    
    public function resturanttimeslotupdate(Request $request)
    {
        $time_slot_id = $request->time_slot_id;
        $open_hrs = $request->open_hour;
        $close_hrs = $request->close_hour;
        $interval = $request->time_slot;
        
        $vendor_id= DB::table('time_slot')
                    ->where('time_slot_id',$time_slot_id)
                    ->first();
         $insert = DB::table('time_slot')
                    ->where('time_slot_id',$time_slot_id)
                    ->update([
                        'open_hour'=>$open_hrs,
                        'close_hour'=>$close_hrs,
                        'time_slot'=>$interval
                        ]);
     
                                 $insertVendor = DB::table('vendor')
                                      ->where('vendor_id',$vendor_id->vendor_id)
                                      ->update(['opening_time'=>$open_hrs,
                                                'closing_time'=>$close_hrs]);
                      if($insert || $insertVendor){
                       return redirect()->back()->withErrors('Updated Successfully');
                      }
                      else{
                      return redirect()->back()->withErrors('Something Went Wrong');
                      }
    }

}
