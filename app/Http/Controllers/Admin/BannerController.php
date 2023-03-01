<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class BannerController extends Controller
{
    public function banner(Request $request)
    {
    	

                 $admin_email=Session::get('admin');
        
                    $admin=DB::table('admin')
                    ->where('admin_email',$admin_email)
                    ->first();
    	         $city= DB::table('admin_banner')
    	              
    	 		          ->get();
    	         return view('admin.banner.bannerlist',compact("admin_email","city","admin"));



    }
    public function addbanner(Request $request)
    {
       
         $admin_email=Session::get('admin');
         $admin=DB::table('admin')
                    ->where('admin_email',$admin_email)
                    ->first();
           $vendor = DB::table('vendor')
           ->get();
    	return view('admin.banner.add_banner',compact("admin_email","admin","vendor"));

    }
    public function addnewbanner(Request $request)
    {
    
    	$vendor_id=$request->vendor_id;
    	$banner_name=$request->banner_name;
    	$date = date('d-m-Y');

         
        if($request->hasFile('city_image'))
        {
        	      	$city_image = $request->city_image;
			        $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
			        $fileName = str_replace(" ", "-", $fileName);
			        $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
			        $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;

      }
      else
      {
      	$city_image = 'N/A';
      }

      $insert = DB::table('admin_banner')
    				->insert(['banner_image'=>$city_image,'vendor_id'=>$vendor_id,'banner_name'=>$banner_name]);
     return redirect()->back()->withErrors('successfully');

}
     
    public function editbanner(Request $request)
{
	 $admin_email=Session::get('admin');
	 $banner_id=$request->banner_id;
	 
	    $vendor = DB::table('vendor')
            ->get();
	 
	 $city= DB::table('admin_banner')
	            ->where('banner_id',$banner_id)
                ->first();
                
 
     $admin=DB::table('admin')
                ->where('admin_email',$admin_email)
                ->first();
	 return view('admin.banner.edit_banner',compact("admin_email","city","banner_id","admin","vendor"));
}
    public function editparbanner(Request $request)
{
	 $admin_email=Session::get('admin');
	 $banner_id=$request->banner_id;
	 
	    $vendor = DB::table('vendor')
            ->get();
	 
	 $city= DB::table('par_banner')
	            ->where('banner_id',$banner_id)
                ->first();
                
 
     $admin=DB::table('admin')
                ->where('admin_email',$admin_email)
                ->first();
	 return view('admin.banner.edit_parbanner',compact("admin_email","city","banner_id","admin","vendor"));
}
    public function editsubsbanner(Request $request)
{
	 $admin_email=Session::get('admin');
	 $banner_id=$request->banner_id;
	 
	    $vendor = DB::table('vendor')
            ->get();
	 
	 $city= DB::table('subs_banner')
	            ->where('banner_id',$banner_id)
                ->first();
                
 
     $admin=DB::table('admin')
                ->where('admin_email',$admin_email)
                ->first();
	 return view('admin.banner.edit_subsbanner',compact("admin_email","city","banner_id","admin","vendor"));
}
    public function editbigbanner(Request $request)
{
	 $admin_email=Session::get('admin');
	 $banner_id=$request->banner_id;
	 
	    $vendor = DB::table('vendor')
            ->get();
	 
	 $city= DB::table('big_banner')
	            ->where('banner_id',$banner_id)
                ->first();
                
 
     $admin=DB::table('admin')
                ->where('admin_email',$admin_email)
                ->first();
	 return view('admin.banner.edit_bigbanner',compact("admin_email","city","banner_id","admin","vendor"));
}
    public function edittopmessagebanner(Request $request)
{
	 $admin_email=Session::get('admin');
	 $banner_id=$request->banner_id;
	 
	    $vendor = DB::table('vendor')
            ->get();
	 
	 $city= DB::table('top_message_banner')
	            ->where('banner_id',$banner_id)
                ->first();
                
 
     $admin=DB::table('admin')
                ->where('admin_email',$admin_email)
                ->first();
	 return view('admin.banner.edit_topmessagebanner',compact("admin_email","city","banner_id","admin","vendor"));
}
    public function editclosedbanner(Request $request)
{
	 $admin_email=Session::get('admin');
	 $banner_id=$request->banner_id;
	 
	    $vendor = DB::table('vendor')
            ->get();
	 
	 $city= DB::table('closed_banner')
	            ->where('banner_id',$banner_id)
                ->first();
                
 
     $admin=DB::table('admin')
                ->where('admin_email',$admin_email)
                ->first();
	 return view('admin.banner.edit_closedbanner',compact("admin_email","city","banner_id","admin","vendor"));
}
public function updatebanner(Request $request)
{
    
    	$banner_id=$request->banner_id;
    	$banner_name=$request->banner_name;
    	$date = date('d-m-Y');
        $vendor_id = $request->vendor_id; 
    

        $getImage = DB::table('admin_banner')
                     ->where('banner_id', $banner_id)
                    ->first();

        $image = $getImage->banner_image;  

        if($request->hasFile('city_image')){
             if(file_exists($image)){
                unlink($image);
            }
            $city_image = $request->city_image;
            $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
            $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;
        }
        else{
            $city_image = $image;
        }

        $update = DB::table('admin_banner')
                                ->where('banner_id', $banner_id)
                                ->update(['banner_image'=>$city_image,'vendor_id'=>$vendor_id,'banner_name'=>$banner_name]);

        if($update){

             

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
        }
    }
    public function updateparbanner(Request $request)
{
    
    	$banner_id=$request->banner_id;
    	$banner_name=$request->banner_name;
    	$date = date('d-m-Y');
        $vendor_id =  $request->vendor_id;
    

        $getImage = DB::table('par_banner')
                     ->where('banner_id', $banner_id)
                    ->first();

        $image = $getImage->banner_image;  

        if($request->hasFile('city_image')){
             if(file_exists($image)){
                unlink($image);
            }
            $city_image = $request->city_image;
            $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
            $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;
        }
        else{
            $city_image = $image;
        }

        $update = DB::table('par_banner')
                                ->where('banner_id', $banner_id)
                                ->update(['banner_image'=>$city_image,'vendor_id'=>$vendor_id,'banner_name'=>$banner_name]);

        if($update){

             

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
        }
    }
        public function updatesubsbanner(Request $request)
{
    
    	$banner_id=$request->banner_id;
    	$banner_name=$request->banner_name;
    	$date = date('d-m-Y');
         
    

        $getImage = DB::table('subs_banner')
                     ->where('banner_id', $banner_id)
                    ->first();

        $image = $getImage->banner_image;  

        if($request->hasFile('city_image')){
             if(file_exists($image)){
                unlink($image);
            }
            $city_image = $request->city_image;
            $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
            $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;
        }
        else{
            $city_image = $image;
        }

        $update = DB::table('subs_banner')
                                ->where('banner_id', $banner_id)
                                ->update(['banner_image'=>$city_image,'banner_name'=>$banner_name]);

        if($update){

             

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
        }
    }
            public function updatebigbanner(Request $request)
{
    
    	$banner_id=$request->banner_id;
    	$banner_name=$request->banner_name;
    	$date = date('d-m-Y');
         
    

        $getImage = DB::table('big_banner')
                     ->where('banner_id', $banner_id)
                    ->first();

        $image = $getImage->banner_image;  

        if($request->hasFile('city_image')){
             if(file_exists($image)){
                unlink($image);
            }
            $city_image = $request->city_image;
            $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
            $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;
        }
        else{
            $city_image = $image;
        }

        $update = DB::table('big_banner')
                                ->where('banner_id', $banner_id)
                                ->update(['banner_image'=>$city_image,'banner_name'=>$banner_name]);

        if($update){

             

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
        }
    }
            public function updatetopmessagebanner(Request $request)
{
    
    	$banner_id=$request->banner_id;
    	$banner_name=$request->banner_name;
    	$date = date('d-m-Y');
         
    

        $getImage = DB::table('top_message_banner')
                     ->where('banner_id', $banner_id)
                    ->first();

        $image = $getImage->banner_image;  

        if($request->hasFile('city_image')){
             if(file_exists($image)){
                unlink($image);
            }
            $city_image = $request->city_image;
            $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
            $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;
        }
        else{
            $city_image = $image;
        }

        $update = DB::table('top_message_banner')
                                ->where('banner_id', $banner_id)
                                ->update(['banner_image'=>$city_image,'banner_name'=>$banner_name]);

        if($update){

             

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
        }
    }
            public function updateclosedbanner(Request $request)
{
    
    	$banner_id=$request->banner_id;
    	$banner_name=$request->banner_name;
    	$date = date('d-m-Y');
         
    

        $getImage = DB::table('closed_banner')
                     ->where('banner_id', $banner_id)
                    ->first();

        $image = $getImage->banner_image;  

        if($request->hasFile('city_image')){
             if(file_exists($image)){
                unlink($image);
            }
            $city_image = $request->city_image;
            $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image->move('images/admin/admin_banner/'.$date.'/', $fileName);
            $city_image = 'images/admin/admin_banner/'.$date.'/'.$fileName;
        }
        else{
            $city_image = $image;
        }

        $update = DB::table('closed_banner')
                                ->where('banner_id', $banner_id)
                                ->update(['banner_image'=>$city_image,'banner_name'=>$banner_name]);

        if($update){

             

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
        }
    }
     public function deletebanner(Request $request)
    {
        
        $banner_id=$request->id;
      
        $getfile=DB::table('admin_banner')
                 ->where('banner_id',$banner_id)
                ->first();

        $city_image=$getfile->banner_image;

    	$delete=DB::table('admin_banner')->where('banner_id',$request->id)->delete();
        if($delete)
        {
        
            if(file_exists($city_image)){
                unlink($city_image);
            }
         
        return redirect()->back()->withErrors('delete successfully');

        }
        else
        {
           return redirect()->back()->withErrors('unsuccessfull delete'); 
        }

    }
	

}

