<?php

namespace App\Http\Controllers\Cityadmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class areaController extends Controller
{
    public function area(Request $request)
    {
    	if(Session::has('cityadmin'))
          {
              

                 $cityadmin_email=Session::get('cityadmin');
        
                    $cityadmin=DB::table('cityadmin')
                    ->where('cityadmin_email',$cityadmin_email)
                    ->first();
    	         $vendor= DB::table('vendor')
    	 		         ->paginate(10);
    	         return view('cityadmin.area.area',compact("cityadmin_email","vendor","cityadmin"));
          }
        else
             {
                return redirect()->route('cityadminlogin')->withErrors('please login first');
             }


    }
    
    public function Addarea(Request $request)
    {
    if(Session::has('cityadmin'))
     {
         
         $cityadmin_email=Session::get('cityadmin');
         $cityadmin=DB::table('cityadmin')
                    ->where('cityadmin_email',$cityadmin_email)
                    ->first();
         $city_id =  $cityadmin->city_id;
         $city = DB::table('city')
               ->join('cityadmin','city.city_id','=','cityadmin.city_id')
               ->get();
               $map1 = DB::table('map_API')
             ->first();
         $map = $map1->map_api_key;     
         $mapset = DB::table('map_settings')
                ->first();
        $mapbox = DB::table('mapbox')
                ->first();
                    
    	return view('cityadmin.area.Addarea',compact("cityadmin_email","cityadmin","city","map1","mapset","mapbox","map"));
         }
    else
         {
            return redirect()->route('cityadminlogin')->withErrors('please login first');
         }

    }
    
    public function AddInsertarea(Request $request)
    {
                   $this->validate(
            $request,
                [
                    'area_name' => 'required',
                    'delivery_charge' => 'required',
                ],
                [
                    'area_name.required' => 'Enter area name.',
                    'delivery_charge.required' => 'Enter delivery charge .',
                ]
        );
    if(Session::has('cityadmin'))
     {	
         
    	$area_name=$request->area_name;
    	$delivery_charge=$request->delivery_charge;
        $created_at=date('d-m-Y h:i a');
        $cityadmin_email=Session::get('cityadmin');
         $cityadmin=DB::table('cityadmin')
                    ->where('cityadmin_email',$cityadmin_email)
                    ->first();
        $cityadmin_id = $cityadmin->cityadmin_id;
        
        $checkmap = DB::table('map_API')
                  ->first();
         $mapset= DB::table('map_settings')
                ->first();
        

        if($mapset->mapbox == 0 && $mapset->google_map == 1){        
        $response = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$area_name."&key=".$checkmap->map_api_key));
        
         $lat = $response->results[0]->geometry->location->lat;
         $lng = $response->results[0]->geometry->location->lng;
        }
        else{
           $lat = $request->lat;
           $lng = $request->lng;  
        }

      $insert = DB::table('area')
    				->insert(['area_name'=>$area_name, 'cityadmin_id'=>$cityadmin_id, 'delivery_charge'=>$delivery_charge, 'created_at'=>$created_at]);
     return redirect()->back()->withErrors('successfully');
      }
     else
      {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
      }
}
    
    public function Editarea(Request $request)
    {
    if(Session::has('cityadmin'))
     {
        
	 $cityadmin_email=Session::get('cityadmin');
	 $vendor_id=$request->id;
	 $vendor= DB::table('vendor')
	            ->where('vendor_id',$vendor_id)
                ->first();
     $cityadmin=DB::table('cityadmin')
                ->where('cityadmin_email',$cityadmin_email)
                ->first();
                
        $map1 = DB::table('map_API')
             ->first();
         $map = $map1->map_api_key;     
         $mapset = DB::table('map_settings')
                ->first();
        $mapbox = DB::table('mapbox')
                ->first();        
	 return view('cityadmin.area.Editarea',compact("cityadmin_email","vendor","vendor_id","cityadmin","map1","mapset","mapbox","map"));
      }
    else
      {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
      }
}

    public function Updatearea(Request $request)
    {
    if(Session::has('cityadmin'))
     {
         
        $vendor_id = $request->id;
        $vendor_name=$request->vendor_name;
        $delivery_range=$request->delivery_range;
        $vendor_loc=$request->vendor_loc;
		$latlngarray = $request->latlngarray;
        $updated_at = date("d-m-y h:i a");
       $checkmap = DB::table('map_API')
                  ->first();
         $mapset= DB::table('map_settings')
                ->first();
        //         if($mapset->mapbox == 0 && $mapset->google_map == 1){        
        // $response = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$area_name."&key=".$checkmap->map_api_key));
        
        //  $lat = $response->results[0]->geometry->location->lat;
        //  $lng = $response->results[0]->geometry->location->lng;
        // }
        // else{
        //   $lat = $request->lat;
        //   $lng = $request->lng;  
        // }
       
        $update = DB::table('vendor')
                                ->where('vendor_id', $vendor_id)
                                ->update(['vendor_name'=>$vendor_name,'vendor_loc'=>$vendor_loc,'delivery_range'=>$delivery_range,'latlngarray'=>$latlngarray]);

        if($update){

             

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
        }
      }
     else
      {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
      }    
    }
    
    public function Deletearea(Request $request)
    {
        
     if(Session::has('cityadmin'))
     {   
    
        $area_id=$request->id;

        $getfile=DB::table('area')
                ->where('area_id',$area_id)
                ->first();
                
                 $delivery_boy =   DB::table('delivery_boy_area')
                ->where('area_id',$area_id)
                ->get();  
                
                  foreach($delivery_boy as $delivery_boy1) {
            
            $dboy_id = $delivery_boy1->delivery_boy_id;
            
            
               $getdelivery_boy =   DB::table('delivery_boy_area')
                            ->where('delivery_boy_id',$dboy_id )
                            ->get();  
            
            if(count($getdelivery_boy)==1){
            $delet = DB::table('delivery_boy')
                            ->where('delivery_boy_id',$dboy_id )
                            ->delete(); 
            }
            }
             
            
                $delete=DB::table('area')->where('area_id',$request->id)->delete();
    	        DB::table('delivery_boy_area')->where('area_id',$request->id)->delete();
        if($delete)
        {
        
                 
        return redirect()->back()->withErrors('delete successfully');

        }
        else
        {
           return redirect()->back()->withErrors('unsuccessfull delete'); 
        }

      }
    else
      {
        return redirect()->route('cityadminlogin')->withErrors('please login first');
      }

    }
	

}

