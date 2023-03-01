<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class VendorCategoryController extends Controller
{
    public function vendorlist(Request $request)
    {
                 $admin_email=Session::get('admin');
        
                    $admin=DB::table('admin')
                    ->where('admin_email',$admin_email)
                    ->first();
    	         $city= DB::table('vendor_category')
    	                   ->orderby('seq')
    	 		          ->get();
    	 		 $parbanner = DB::table('par_banner')
    	 		 ->first();
    	 		 $subsbanner = DB::table('subs_banner')
    	 		 ->first();
    	 		 $bigbanner = DB::table('big_banner')
    	 		 ->first();
    	 		 $top_message_banner = DB::table('top_message_banner')
    	 		 ->first();
    	 		 $closed_banner = DB::table('closed_banner')
    	 		 ->first();
    	 		 
    	 		 
    	         return view('admin.vendor_category.categorylisting',
    	         compact("admin_email","city","admin","parbanner","subsbanner","bigbanner","top_message_banner","closed_banner"));
    	         
    	         



    }
        public function update(Request $request)

    {

         $vendorcategories= DB::table('vendor_category')
 		          ->get();


        foreach ($vendorcategories as $vendorcategory) {

            foreach ($request->order as $order) {

                if ($order['id'] == $vendorcategory->vendor_category_id) {

                    DB::table('vendor_category')
                                ->where('vendor_category_id', $order['id'])
                                ->update(['seq'=>$order['position']]);

                }

            }

        }

        

         return response('Update Successfully.', 200);

    }
    public function addvendor(Request $request)
    {
       
         $admin_email=Session::get('admin');
         $admin=DB::table('admin')
                    ->where('admin_email',$admin_email)
                    ->first();
                    $ui = DB::table('UI_Vendor')
                        ->where('id','!=','3')
                        ->where('id','!=','4')
                        ->get();
    	return view('admin.vendor_category.add_category',compact("admin_email","admin","ui"));

    }
    public function addnewvendor(Request $request)
    {
         $this->validate($request,[
               'vendor_category' => 'required',
               'city_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'

           ]);
    	
    	$vendor_category=$request->vendor_category;
    	$ui=$request->ui;
        $created_at=date('d-m-Y h:i a');;
        $date = date('d-m-Y');
        $seq=DB::table('vendor_category')
                    ->max('seq');
        $newseq=$seq+1;
        if($request->hasFile('city_image'))
        {
        	      	$city_image = $request->city_image;
			        $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
			        $fileName = str_replace(" ", "-", $fileName);
			        $city_image->move('city/images/'.$date.'/', $fileName);
			        $city_image = 'city/images/'.$date.'/'.$fileName;

      }
      else
      {
      	$city_image = 'N/A';
      }

      $insert = DB::table('vendor_category')
    				->insert(['category_name'=>$vendor_category,'category_image'=>$city_image,'ui_type'=>$ui,'seq'=>$newseq]);
     return redirect()->back()->withErrors('successfully');

}
     
    public function editvendor(Request $request)
    {
   
	 $admin_email=Session::get('admin');
	 $vendor_category_id=$request->vendor_category_id;
	 $city= DB::table('vendor_category')
	            ->where('vendor_category_id',$vendor_category_id)
                ->first();
     $admin=DB::table('admin')
                ->where('admin_email',$admin_email)
                ->first();
                 $ui = DB::table('UI_Vendor')
                        ->get();
	 return view('admin.vendor_category.edit_category',compact("admin_email","city","vendor_category_id","admin","ui"));
}
    public function moveupvendor(Request $request)
    {
     $vendor_category_id = $request->vendor_category_id;
	 $admin_email=Session::get('admin');
	 $vendor_category_id=$request->vendor_category_id;
	 $city2= DB::table('vendor_category')
	            ->where('vendor_category_id',$vendor_category_id)
                ->first();
    $maxseq = DB::table('vendor_category')
	            ->max('seq');
    $seq=$city2->seq;
    if($seq==1)
    {
        $newseq=$maxseq;
    }
    else
    {
        $newseq=$seq-1;
    }
     $update = DB::table('vendor_category')
                                ->where('seq', $newseq)
                                ->update(['seq'=>0]);
     $update1 = DB::table('vendor_category')
                                ->where('seq', $seq)
                                ->update(['seq'=>$newseq]);
     $update2 = DB::table('vendor_category')
                                ->where('seq', 0)
                                ->update(['seq'=>$seq]);
      return redirect()->back();
        
    }
    public function movedownvendor(Request $request)
    {
     $vendor_category_id = $request->vendor_category_id;
	 $admin_email=Session::get('admin');
	 $vendor_category_id=$request->vendor_category_id;
	 $city2= DB::table('vendor_category')
	            ->where('vendor_category_id',$vendor_category_id)
                ->first();
    $maxseq = DB::table('vendor_category')
	            ->max('seq');
    $seq=$city2->seq;
    if($seq==$maxseq)
    {
        $newseq=1;
    }
    else
    {
        $newseq=$seq+1;
    }
     $update = DB::table('vendor_category')
                                ->where('seq', $newseq)
                                ->update(['seq'=>0]);
     $update1 = DB::table('vendor_category')
                                ->where('seq', $seq)
                                ->update(['seq'=>$newseq]);
     $update2 = DB::table('vendor_category')
                                ->where('seq', 0)
                                ->update(['seq'=>$seq]);
         return redirect()->back();

        
    }
    public function updatevendor(Request $request)
    {
        
        
        $vendor_category_id = $request->vendor_category_id;
        $vendor_category=$request->vendor_category;
    	$ui=$request->ui;
        $created_at=date('d-m-Y h:i a');;
        $date = date('d-m-Y');
        $old_city_image = $request->old_city_image;


        $getImage = DB::table('vendor_category')
                     ->where('vendor_category_id', $vendor_category_id)
                    ->first();

        $image = $getImage->category_image;  

        if($request->hasFile('city_image')){
             if(file_exists($image)){
                unlink($image);
            }
            $city_image = $request->city_image;
            $fileName = date('dmyhisa').'-'.$city_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $city_image->move('city/image/'.$date.'/', $fileName);
            $city_image = 'city/image/'.$date.'/'.$fileName;
        }
        else{
            $city_image = $old_city_image;
        }

        $update = DB::table('vendor_category')
                                ->where('vendor_category_id', $vendor_category_id)
                                ->update(['category_name'=>$vendor_category,'category_image'=>$city_image,'ui_type'=>$ui]);

        if($update){

             

            return redirect()->back()->withErrors(' updated successfully');
        }
        else{
            return redirect()->back()->withErrors("something wents wrong.");
        }
    }
     public function deletevendor(Request $request)
    {
        
        $vendor_category_id=$request->vendor_category_id;

        $getfile=DB::table('vendor_category')
                ->where('vendor_category_id',$vendor_category_id)
                ->first();

        $city_image=$getfile->category_image;

    	$delete=DB::table('vendor_category')->where('vendor_category_id',$request->vendor_category_id)->delete();
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
    public function hidevendor(Request $request)
    {
        
        $vendor_category_id=$request->vendor_category_id;

        $getfile=DB::table('vendor_category')
                ->where('vendor_category_id',$vendor_category_id)
                ->first();

        if($getfile->hide==0){
            $update = DB::table('vendor_category')
                ->where('vendor_category_id',$vendor_category_id)
                ->update(['hide'=>1]);
         return redirect()->back();
        }
        else
            $update = DB::table('vendor_category')
                ->where('vendor_category_id',$vendor_category_id)
                ->update(['hide'=>0]);
         return redirect()->back();
    }
	

}

