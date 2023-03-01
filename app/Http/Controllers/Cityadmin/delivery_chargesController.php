<?php

namespace App\Http\Controllers\cityadmin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use DB;
use Session;
use Hash;
use Excel;

class delivery_chargesController extends Controller
{
    public function delivery_charges(Request $request)
    {
     if(Session::has('cityadmin'))
     {

        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $delivery_charges= DB::table('delivery_charges')
        ->select('delivery_charge_id')
        ->groupBy('delivery_charge_id')
        ->get();
        $cur=DB::table('currency')
        ->first();
        foreach($delivery_charges as $delivery_charge)
        {
            $vendorlist="";
            $deliverycharges="";
            $ven = DB::table('vendor')
                    ->select('vendor_name')
                    ->where('delivery_charge_id',$delivery_charge->delivery_charge_id)
		    ->where('vendor.ui_type','!=','4')
                    ->get()->all();
            foreach($ven as $vens)
            {
                $vendorlist.=$vens->vendor_name.',';
            }
            $vendorlist=rtrim($vendorlist, ',');
            $delivery_charge->vendorlist=$vendorlist;
            $del=DB::table('delivery_charges')
            ->where('delivery_charge_id',$delivery_charge->delivery_charge_id)
            ->get()->all();
            foreach($del as $dels)
            {
                $deliverycharges.=$dels->range_start." km"." - ".$dels->range_end." km"." @ ".$cur->currency_sign." ".$dels->charges.PHP_EOL;
            }
            $deliverycharges=rtrim($deliverycharges, PHP_EOL);
            $delivery_charge->deliverycharges=$deliverycharges;
        }
        $vendors= DB::table('vendor')
        ->where('cityadmin_id',$cityadmin->cityadmin_id)
        ->get();
        return view('cityadmin.delivery_charges.delivery_charges',compact("cityadmin_email","delivery_charges","cityadmin",'vendors'));
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
    public function Adddelivery_charge(Request $request)
    {
     if(Session::has('cityadmin'))
     {
        $delivery_charge=DB::table('delivery_charges')->max('delivery_charge_id');
                    $delivery_charge_id=$delivery_charge+1;
        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $delivery_charges= DB::table('delivery_charges')
        ->where('delivery_charge_id',$delivery_charge_id)
        ->get();
            $vendorlist="";
         $ven = DB::table('vendor')
                    ->select('vendor_name')
                    ->where('delivery_charge_id',$delivery_charge_id)
                    ->get()->all(); 
        $vendors = DB::table('vendor')
                    ->select('vendor_name')
                    ->get()->all(); 
            foreach($ven as $vens)
            {
                $vendorlist.=$vens->vendor_name.',';
            }
            $vendorlist=rtrim($vendorlist, ',');               
        return view('cityadmin.delivery_charges.Editdelivery_charges',compact("cityadmin_email","delivery_charges","cityadmin","delivery_charge_id","vendorlist","vendors"));
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     } 
    }
    
     public function Adddelivery_charges(Request $request)
    {
     if(Session::has('cityadmin'))
     {
        $delivery_charge_id = $request->id;
        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        
         return view('cityadmin.delivery_charges.adddelivery_charges',compact("cityadmin_email","cityadmin","delivery_charge_id"));
     }  
     else
         {
            return redirect()->route('cityadminlogin')->withErrors('please login first');
         }
    }
    public function add_delivery_charge_vendor(Request $request)
    {
       $delivery_charge_id = $request->id;
       $vendor = $request->vendor;
       $update = DB::table('vendor')
       ->where('vendor_name',$vendor)
       ->update(['delivery_charge_id'=>$delivery_charge_id]);
       if("update"){
            return redirect()->back()->withErrors(' updated successfully');
       }
       else
       {
            return redirect()->back()->withErrors(' something went wrong');
       }
    }
    
        public function AddNewdelivery_charges(Request $request)
    {
           $delivery_charge_id = $request->id;
           $this->validate($request,[
               'range_start' => 'required',
               'range_end' => 'required',
                'charges' => 'required',
           ]);
    if(Session::has('cityadmin'))
     {
      
        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
       
        $range_start=$request->range_start;
        $range_end=$request->range_end;
        $charges=$request->charges;

  
            $insert2 = DB::table('delivery_charges')
                  ->insert(['delivery_charge_id'=>$delivery_charge_id,'range_start'=>$range_start,'range_end'=>$range_end,'charges'=>$charges]);

                  
     if($insert2)
     {
     return redirect()->route('edit-delivery_charges',$delivery_charge_id)->withErrors('successfully Created');
    }
     }
   else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
  
  
  
    public function Editdelivery_charge(Request $request)
    {
     if(Session::has('cityadmin'))
      {	
     
       $delivery_charges_id=$request->id;
    	 $cityadmin_email=Session::get('cityadmin');
    
         $cityadmin=DB::table('cityadmin')
                ->where('cityadmin_email',$cityadmin_email)
                ->first();       
    	 $delivery_charge= DB::table('delivery_charges')
    	 		  ->where('id',$delivery_charges_id)
    	 		  ->first();
    	 
                
    	 return view('cityadmin.delivery_charges.Editdelivery_charge',compact("cityadmin_email","cityadmin","delivery_charges_id","delivery_charge"));
    
      }
     else
      {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
      }



    }
    public function Updatedelivery_charges(Request $request)
   {
       
        $id=$request->id;
        $range_start=$request->range_start;
        $range_end=$request->range_end;
        $charges=$request->charges;
            
        $value=array('id'=>$id,'range_start'=>$range_start, 'range_end'=>$range_end,'charges'=>$charges);
     
        $update = DB::table('delivery_charges')
                 ->where('id', $id)
                 ->update($value);

        if($update){

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
            }
        }
    
    public function deletedelivery_charge(Request $request)
    {
        $delivery_charge_id=$request->id;

    	$delete=DB::table('delivery_charges')->where('id',$delivery_charge_id)->delete();
        if($delete)
        {
         return redirect()->back()->withErrors(' deleted successfully');
        }
        else
        {
        return redirect()->back()->withErrors("something wents wrong.");  
        }
    }

    public function Editdelivery_charges(Request $request)
    {
     if(Session::has('cityadmin'))
     {
        $delivery_charge_id=$request->id;
        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $delivery_charges= DB::table('delivery_charges')
        ->where('delivery_charge_id',$delivery_charge_id)
        ->get();
            $vendorlist="";
         $ven = DB::table('vendor')
                    ->select('vendor_name')
                    ->where('delivery_charge_id',$delivery_charge_id)
                    ->get()->all(); 
        $vendors = DB::table('vendor')
                    ->select('vendor_name')
                    ->get()->all(); 
            foreach($ven as $vens)
            {
                $vendorlist.=$vens->vendor_name.',';
            }
            $vendorlist=rtrim($vendorlist, ',');               
        return view('cityadmin.delivery_charges.Editdelivery_charges',compact("cityadmin_email","delivery_charges","cityadmin","delivery_charge_id","vendorlist","vendors"));
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
    public function deletedelivery_charges(Request $request)
    {
        $delivery_charge_id=$request->id;
        $vendor=DB::table('vendor')->where('delivery_charge_id',$delivery_charge_id)->update(['delivery_charge_id'=>0]);
    	$delete=DB::table('delivery_charges')->where('delivery_charge_id',$delivery_charge_id)->delete();
        if($delete)
        {
         return redirect()->back()->withErrors(' deleted successfully');
        }
        else
        {
        return redirect()->back()->withErrors("something wents wrong.");  
        }
    }
  public function parcel_delivery_charges(Request $request)
    {
     if(Session::has('cityadmin'))
     {

        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        $delivery_charges= DB::table('parcel_delivery_charges')
        ->get();
        return view('cityadmin.delivery_charges.parcel_delivery_charges',compact("cityadmin_email","delivery_charges","cityadmin"));
     }
     else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
    
     public function Addparcel_delivery_charges(Request $request)
    {
     if(Session::has('cityadmin'))
     {
        
        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
        
         return view('cityadmin.delivery_charges.addparcel_delivery_charges',compact("cityadmin_email","cityadmin"));
     }  
     else
         {
            return redirect()->route('cityadminlogin')->withErrors('please login first');
         }
    }
    
    
        public function AddNewparcel_delivery_charges(Request $request)
    {
           $this->validate($request,[
               'range_start' => 'required',
               'range_end' => 'required',
                'charges' => 'required',
           ]);
    if(Session::has('cityadmin'))
     {
      
        $cityadmin_email=Session::get('cityadmin');
        $cityadmin=DB::table('cityadmin')
        ->where('cityadmin_email',$cityadmin_email)
        ->first();
       
        $range_start=$request->range_start;
        $range_end=$request->range_end;
        $charges=$request->charges;

  
            $insert2 = DB::table('parcel_delivery_charges')
                  ->insert(['range_start'=>$range_start,'range_end'=>$range_end,'charges'=>$charges]);

                  
     if($insert2)
     {
     return redirect()->back()->withErrors('successfully Created');
    }
     }
   else
     {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
     }
    }
  
  
  
    public function Editparcel_delivery_charges(Request $request)
    {
     if(Session::has('cityadmin'))
      {	
     
       $delivery_charges_id=$request->id;
    	 $cityadmin_email=Session::get('cityadmin');
    
         $cityadmin=DB::table('cityadmin')
                ->where('cityadmin_email',$cityadmin_email)
                ->first();       
    	 $delivery_charges= DB::table('parcel_delivery_charges')
    	 		  ->where('id',$delivery_charges_id)
    	 		  ->first();

         
    	 return view('cityadmin.delivery_charges.Editparcel_delivery_charges',compact("cityadmin_email","cityadmin","delivery_charges_id","delivery_charges"));
    
      }
     else
      {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
      }


    }
    public function Updateparcel_delivery_charges(Request $request)
   {
       
        $delivery_charges_id=$request->id;
        $range_start=$request->range_start;
        $range_end=$request->range_end;
        $charges=$request->charges;
            
        $value=array('id'=>$delivery_charges_id,'range_start'=>$range_start, 'range_end'=>$range_end,'charges'=>$charges);
     
        $update = DB::table('parcel_delivery_charges')
                 ->where('id', $delivery_charges_id)
                 ->update($value);

        if($update){

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
            }
        }
    public function deleteparceldelivery_charge(Request $request)
    {
        $delivery_charge_id=$request->id;

    	$delete=DB::table('parcel_delivery_charges')->where('id',$delivery_charge_id)->delete();
        if($delete)
        {
         return redirect()->back()->withErrors(' deleted successfully');
        }
        else
        {
        return redirect()->back()->withErrors("something wents wrong.");  
        }
    }
    
}
