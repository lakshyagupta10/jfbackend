<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Carbon\Carbon;

class CategoryController extends Controller
{
    public function category(Request $request)
    {
     if(Session::has('vendor'))
     {
        $vendor_email=Session::get('vendor');
    	
        $vendor=DB::table('vendor')
        ->where('vendor_email',$vendor_email)
        ->first();	
        
        $vendorCategory = DB::table('tbl_category')
                         ->where('vendor_id',$vendor->vendor_id)
                         ->orderBy('seq')
    			         ->paginate(10);
        return view('vendor.category.show_cat',compact("vendorCategory", "vendor_email", "vendor"));
     }
	else
	 {
			return redirect()->route('vendorlogin')->withErrors('please login first');
	 }	
    }
    
     public function vendorAddCategory(Request $request)
    {
     if(Session::has('vendor'))
      {
       
        $vendor_email=Session::get('vendor');
    	$vendorCategory = DB::table('tbl_category')
    			         ->get();
    	$homeCategory = DB::table('tbl_category')
    	                 ->where('home', 1)
    			         ->count();		         
        $vendor=DB::table('vendor')
        ->where('vendor_email',$vendor_email)
        ->first();
        return view('vendor.category.add_category',compact("vendorCategory", "vendor_email","homeCategory", "vendor"));
     }
	else
	 {
			return redirect()->route('vendorlogin')->withErrors('please login first');
	 }
    }
    
     public function vendorAddNewCategory(Request $request)
    {
     if(Session::has('vendor'))
      {
       
        $category_name = $request->category_name;
        $vendor_id = $request->vendor_id;
        $homecat = $request->homecat;
       
        $created_at = Carbon::now();
        $updated_at = Carbon::now();
        $date=date('d-m-Y');
        
        $homeCategory = DB::table('tbl_category')
    	                 ->where('home', '1')
    			         ->get();
    			         
    if(count($homeCategory) < 3){		         
    			         
        if($homecat=="")
        {
            $homecat=0;
        }
    }
        else{
            $homecat=0;
        }
        
        $this->validate(
            $request,
                [
                    'category_name' => 'required',
                    'category_image' => 'required|mimes:jpeg,png,jpg|max:400',
                ],
                [
                    'category_name.required' => 'Enter category name.',
                    'category_image.required' => 'Choose category image.',
                ]
        );

        

        

        if($request->hasFile('category_image')){
            $category_image = $request->category_image;
            $fileName = $category_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $category_image->move('images/category/'.$date.'/', $fileName);
            $category_image = 'images/category/'.$date.'/'.$fileName;
        }
        else{
            $category_image = 'N/A';
        }

        $insertCategory = DB::table('tbl_category')
                            ->insert([
                                'vendor_id'=>$vendor_id,
                                'category_name'=>$category_name,
                                'category_image'=>$category_image,
                                'home'=>$homecat,
                                'created_at'=>$created_at,
                                'updated_at'=>$updated_at
                            ]);
        
        if($insertCategory){
            return redirect()->back()->withErrors('category added successfully');
        }
        else{
            return redirect()->back()->withErrors("Something wents wrong");
        }
      
     }
	else
	 {
			return redirect()->route('vendorlogin')->withErrors('please login first');
	 }
    }
    
    public function vendorEditCategory(Request $request)
    {
       
     if(Session::has('vendor'))
      {
        
    	$category_id = $request->category_id;

    	$category = DB::table('tbl_category')
        	          ->where('category_id', $category_id)
        			  ->first();
        $vendor_email=Session::get('vendor');
        	
        $vendor=DB::table('vendor')
        ->where('vendor_email',$vendor_email)
        ->first();
        

        return view('vendor.category.update_cat',compact("category","vendor_email","vendor"));
		 }
	else
		 {
			return redirect()->route('vendorlogin')->withErrors('please login first');
		 }
    }

    public function vendorUpdateCategory(Request $request)
    {
     if(Session::has('vendor'))
     {
       
        $homeCategory = DB::table('tbl_category')
    	                 ->where('home', 1)
    			         ->get();	
        $category_id = $request->category_id;
        $category_name = $request->category_name;
        $vendor_id = $request->vendor_id;
        $homecat = $request->homecat;
        
        $updated_at = Carbon::now();
        $date = date('d-m-Y');
        if(count($homeCategory) < 3){		         
    			         
        if($homecat=="")
        {
            $homecat=0;
        }
    }
        else{
            $homecat=0;
        }
        $this->validate(
            $request,
                [
                    'category_name' => 'required',
                    'category_image' => 'mimes:jpeg,png,jpg|max:400',
                   
                ],
                [
                    'category_name.required' => 'Enter category name.',
                    'category_image.required' => 'Choose category icon.',
                    
                ]
        );

    	

        $getCategory = DB::table('tbl_category')
                    ->where('category_id',$category_id)
                    ->first();

        $image = $getCategory->category_image;

        if($request->hasFile('category_image')){
            if(file_exists($image)){
                unlink($image);
            }
            $category_image = $request->category_image;
            $fileName =$category_image->getClientOriginalName();
            $fileName = str_replace(" ", "-", $fileName);
            $category_image->move('images/category/'.$date.'/', $fileName);
            $category_image = 'images/category/'.$date.'/'.$fileName;
        }
        else{
            $category_image = $getCategory->category_image;
        }
        $updateCategory = DB::table('tbl_category')
                            ->where('category_id', $category_id)
                            ->update([
                                 'vendor_id'=>$vendor_id,
                                'category_name'=>$category_name,
                                'category_image'=>$category_image,
                                'home'=>$homecat,
                                'updated_at'=>$updated_at
                            ]);
        
        if($updateCategory){
            return redirect()->back()->withErrors('category updated successfully');
        }
        else{
            return redirect()->back()->withErrors("Something wents wrong");
        }
       
			 }
		else
			 {
				return redirect()->route('vendorlogin')->withErrors('please login first');
			 }
    }
    
    
    
     public function vendorDeleteCategory(Request $request)
    {
     if(Session::has('vendor'))
      {
        
        $category_id=$request->category_id;
    	$delete=DB::table('tbl_category')->where('category_id',$request->category_id)->delete();

    $getsub=DB::table('subcat')->where('category_id',$category_id)->first();
    if($getsub != null){
$delete1=DB::table('subcat')->where('subcat_id',$getsub->subcat_id)->delete();
$deletepro=DB::table('product')->where('subcat_id',$getsub->subcat_id)->first();
     DB::table('product_varient')->where('product_id',$deletepro->product_id)->delete();
     DB::table('product')->where('subcat_id',$getsub->subcat_id)->delete();

        if($delete)
        {
        
         
        return redirect()->back()->withErrors('Delete successfully');

        }
        else
        {
           return redirect()->back()->withErrors('unsuccessfull delete'); 
        }
    }else{
        return redirect()->back()->withErrors('Delete successfully');
    }
			 }
		else
			 {
				return redirect()->route('vendorlogin')->withErrors('please login first');
			 }

    }
        public function moveupcat(Request $request)
    {
     $category_id = $request->category_id;
     $category = DB::table('tbl_category')
                    ->select('vendor_id','seq')
                    ->where('category_id',$category_id)
                    ->first();
    $maxseq = DB::table('tbl_category')
                ->where('vendor_id',$category->vendor_id)
	            ->max('seq');
    $seq=$category->seq;
    if($seq==1)
    {
        $newseq=$maxseq;
    }
    else
    {
        $newseq=$seq-1;
    }
     $update = DB::table('tbl_category')
                                ->where('vendor_id',$category->vendor_id)
                                ->where('seq', $newseq)
                                ->update(['seq'=>0]);
     $update1 = DB::table('tbl_category')
                                 ->where('vendor_id',$category->vendor_id)
                                ->where('seq', $seq)
                                ->update(['seq'=>$newseq]);
     $update2 = DB::table('tbl_category')
                                ->where('vendor_id',$category->vendor_id)
                                ->where('seq', 0)
                                ->update(['seq'=>$seq]);
      return redirect()->back();
        
    }
    public function movedowncat(Request $request)
    {
     $category_id = $request->category_id;
     $category = DB::table('tbl_category')
                    ->select('vendor_id','seq')
                    ->where('category_id',$category_id)
                    ->first();
    $maxseq = DB::table('tbl_category')
                ->where('vendor_id',$category->vendor_id)
	            ->max('seq');
    $seq=$category->seq;
    if($seq==$maxseq)
    {
        $newseq=1;
    }
    else
    {
        $newseq=$seq+1;
    }
     $update = DB::table('tbl_category')
                                ->where('vendor_id',$category->vendor_id)
                                ->where('seq', $newseq)
                                ->update(['seq'=>0]);
     $update1 = DB::table('tbl_category')
                                ->where('vendor_id',$category->vendor_id)
                                ->where('seq', $seq)
                                ->update(['seq'=>$newseq]);
     $update2 = DB::table('tbl_category')
                                ->where('vendor_id',$category->vendor_id)
                                ->where('seq', 0)
                                ->update(['seq'=>$seq]);
         return redirect()->back();

        
    }
        public function hidecat(Request $request)
    {
        
        $category_id=$request->category_id;

        $getfile=DB::table('tbl_category')
                ->where('category_id',$category_id)
                ->first();

        if($getfile->hide==0){
            $update = DB::table('tbl_category')
                ->where('category_id',$category_id)
                ->update(['hide'=>1]);
         return redirect()->back();
        }
        else
            $update = DB::table('tbl_category')
                ->where('category_id',$category_id)
                ->update(['hide'=>0]);
         return redirect()->back();
    }


}