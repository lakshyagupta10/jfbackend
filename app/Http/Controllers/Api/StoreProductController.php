<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;


class StoreProductController extends Controller
{
    public function store_products(Request $request)
    {
        $vendor_id = $request->vendor_id;
        $products= DB::table('product')
        ->join('subcat','product.subcat_id', '=', 'subcat.subcat_id')
        ->join('tbl_category','subcat.category_id', '=', 'tbl_category.category_id')
        ->where('tbl_category.vendor_id', $vendor_id)
       ->get();	
       if(count($products)>0)	{                     
        $mess = array('status'=>'1', 'message'=>'data found', 'data'=>$products);
        return $mess;
     }
    else
     {
        $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
        return $message;
     }		

    }

    public function store_addproduct(Request $request)
    {
        $vendor_id = $request->vendor_id;
        $subcat= DB::table('subcat')
        ->join('tbl_category','subcat.category_id', '=', 'tbl_category.category_id')
        ->where('vendor_id', $vendor_id)
        ->get();
        if(count($subcat)>0)	{                     
            $mess = array('status'=>'1', 'message'=>'data found', 'data'=>$subcat);
            return $mess;
         }
        else
         {
            $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
            return $message;
         }

    }
    public function store_addnewproduct(Request $request)
    {
        $vendor_id = $request->vendor_id;
        $subcat_id=$request->subcat_id;
        $product_name=$request->product_name;
        $price=$request->price;
        if($request->mrp){
         $mrp = $request->mrp;
         }
         else{
            $mrp=$price;
         }
         if($request->stock){
         $stock=$request->stock;
         }
         else{
             $stock=100;
         }        
        $unit=$request->unit;
        $qty=$request->quantity;
        if($request->product_description){
        $product_description =$request->product_description;
        }
        else{
            $product_description="";
        }
        $date = date('d-m-Y');
        $created_at=date('d-m-Y h:i a');
        $product_image = $request->product_image;
        if($request->hasFile('product_image'))
        {
        $fileName = date('dmyhisa').'-'.$product_image->getClientOriginalName();
        $fileName = str_replace(" ", "-", $fileName);
        $product_image->move('product/images/'.$date.'/', $fileName);
        $product_image = 'product/images/'.$date.'/'.$fileName;
        }
        else
        {
            $product_image = "N/A";
        }
        $insert = DB::table('product')
        ->insertGetId(['subcat_id'=>$subcat_id,'product_name'=>$product_name,'product_image'=>$product_image,'created_at'=>$created_at,'vendor_id'=>$vendor_id]);
       $product = DB::table('product')->where('product_id',$insert)->first();
        if($insert){  
         $add1stvarient = DB::table('product_varient')
         ->insert(['product_id'=>$insert,'price'=>$price, 'strick_price'=>$mrp, 'varient_image'=>$product_image, 'unit'=>$unit, 'quantity'=>$qty, 'stock'=>$stock,'description'=>$product_description,'vendor_id'=>$vendor_id]);                  
            $mess = array('status'=>'1', 'message'=>'data found', 'varientdata'=>$add1stvarient ,'productdata'=>$product);
            return $mess;
         }
        else
         {
            $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
            return $message;
         }

    }


    public function store_editnewproduct(Request $request)
    {
      $product_id = $request->product_id;
      $product = DB::table('product')->where('product_id',$product_id)->first();
      $vendor_id=$product->vendor_id;
      $subcat= DB::table('subcat')
      ->join('tbl_category','subcat.category_id', '=', 'tbl_category.category_id')
      ->where('vendor_id', $vendor_id)
      ->get();

      if($product)	{                     
          $mess = array('status'=>'1', 'message'=>'data found', 'data'=>$product, 'subcat'=>$subcat);
          return $mess;
       }
      else
       {
          $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
          return $message;
       }

    }
    public function store_updatenewproduct(Request $request)
    {
      $product_id=$request->product_id;
      $subcat_id=$request->subcat_id;
      $product_name=$request->product_name;
      $product = DB::table('product')->where('product_id',$product_id)->first();
      $old_product_image=$product->product_image;
      $updated_at = date("d-m-y h:i a");
      $date=date('d-m-y');
      
      $getImage = DB::table('product')
                   ->where('product_id',$product_id)
                  ->first();

      $image = $getImage->product_image;  

      if($request->hasFile('product_image')){
           if(file_exists($image)){
              unlink($image);
          }
          $product_image = $request->product_image;
          $fileName = date('dmyhisa').'-'.$product_image->getClientOriginalName();
          $fileName = str_replace(" ", "-", $fileName);
          $product_image->move('product/images/'.$date.'/', $fileName);
          $product_image = 'product/images/'.$date.'/'.$fileName;
      }
      else{
          $product_image = $old_product_image;
      }

      $update = DB::table('product')
               ->where('product_id', $product_id)
               ->update(['subcat_id'=>$subcat_id,'product_name'=>$product_name,'product_image'=>$product_image,'updated_at'=>$updated_at]);

      if($update)	{                     
          $mess = array('status'=>'1', 'message'=>'data update', 'data'=>$update);
          return $mess;
       }
      else
       {
          $message = array('status'=>'0', 'message'=>'data not update', 'data'=>[] );
          return $message;
       }

    }
    public function store_deleteproduct(Request $request)
    {
        $product_id=$request->product_id;

    	$delete=DB::table('product')->where('product_id',$request->product_id)->delete();
        if($delete)
        {
         DB::table('product_varient')->where('product_id',$request->product_id)->delete();  
         
         $delete = array('status'=>'1', 'message'=>'Deleted Successfully');

        return $delete;
        }
        else
        {
         $delete = array('status'=>'0', 'message'=>'Unsuccessfull Delete');
         return $delete;        }
    }
    
        public function store_category(Request $request)
    {
      $vendor_id = $request->vendor_id;
    	$Category = DB::table('tbl_category')->where('vendor_id',$vendor_id)
    	               ->select('category_id','category_name','category_image')
    			         ->get();
    		         
        if($Category){
         $mess = array('status'=>'1', 'message'=>'data found', 'data'=>$Category);
         return $mess;
     }
	else
	 {
      $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
      return $message;    }
   }
           public function rest_category(Request $request)
    {
      $vendor_id = $request->vendor_id;
    	$Category = DB::table('resturant_category')->where('vendor_id',$vendor_id)
    	               ->select('resturant_cat_id','cat_name','cat_image')
    			         ->get();
    		         
        if($Category){
         $mess = array('status'=>'1', 'message'=>'data found', 'data'=>$Category);
         return $mess;
     }
	else
	 {
      $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
      return $message;    }
   }
    public function rest_addcategory(Request $request)
   {
     $vendor_id = $request->vendor_id;
     $cat_name= $request->cat_name;
    $created_at=date('d-m-Y h:i a');
    $date=date('d-m-y');
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
      $category = DB::table('resturant_category')->insertGetId(['vendor_id'=>$vendor_id,'cat_name'=>$cat_name,'cat_image'=>$category_image]);
                  
       if($category){
        $mess = array('status'=>'1', 'message'=>'category created', 'data'=>$category);
        return $mess;
    }
      else
   {
     $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
     return $message;    }
   }
public function rest_editcategory(Request $request)
{
        $category_id = $request->category_id;
        $updated_at = Carbon::now();
        $date = date('d-m-Y');
        $getCategory = DB::table('resturant_category')
                    ->where('resturant_cat_id',$category_id)
                    ->first();
        $vendor_id = $getCategory->vendor_id;
        $image = $getCategory->cat_image;
        if($request->category_name){
        $category_name = $request->category_name;}
        else
        {
         $category_name = $getCategory->cat_name;
        }
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
            $category_image = $getCategory->cat_image;
        }
        $updateCategory = DB::table('resturant_category')
                            ->where('resturant_cat_id', $category_id)
                            ->update([
                                 'vendor_id'=>$vendor_id,
                                'cat_name'=>$category_name,
                                'cat_image'=>$category_image,
                            ]);
        
        if($updateCategory){
            $msg=array('status'=>'1','message'=>'category updated successfully','data'=>$updateCategory);
            return $msg;
        }
        else{
            $msg=array('status'=>'0','message'=>"Something wents wrong");
            return $msg;
        }
}
   public function rest_deletecategory(Request $request)

{
 $category_id=$request->category_id;
    	$delete=DB::table('resturant_category')->where('resturant_cat_id',$category_id)->delete();
    	
$deletepro=DB::table('resturant_product')->where('subcat_id',$category_id)->first();
if($deletepro!=null){
     DB::table('resturant_product')->where('subcat_id',$category_id)->delete();
        if($delete)
        {
        $msg= array('status'=>'1','message'=>'Delete successfully');
        return $msg;
        }
        else
        {
           $msg=array('status'=>'0','message'=>'unsuccessfull delete'); 
            return $msg;
        }
}
    else
    {
       $msg=array('status'=>'1','message'=>'delete successfully'); 
       return $msg;
    }
}
   public function store_subcategory(Request $request)
   {
     $vendor_id = $request->vendor_id;
     $category_id = $request->category_id;
        $created_at=date('d-m-Y h:i a');
    
    if($request->category_id)
    {
        $Category = DB::table('tbl_category')
        ->where('vendor_id',$vendor_id)
       ->join('subcat', 'tbl_category.category_id', '=', 'subcat.category_id')
       ->select('subcat.subcat_id','subcat.category_id','subcat.subcat_name','subcat.subcat_image','subcat.istabacco','subcat.ispres','subcat.isid','subcat.isbasket')
       ->where('subcat.category_id',$category_id)

             ->get();
    }
    else{
    $Category = DB::table('tbl_category')->where('vendor_id',$vendor_id)
       ->join('subcat', 'tbl_category.category_id', '=', 'subcat.category_id')
       ->select('subcat.subcat_id','subcat.category_id','subcat.subcat_name','subcat.subcat_image','subcat.istabacco','subcat.ispres','subcat.isid','subcat.isbasket')

             ->get();
    }            
       if($Category){
        $mess = array('status'=>'1', 'message'=>'data found', 'data'=>$Category);
        return $mess;
    }
    
  else
   {
     $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
     return $message;    }
  }
   public function store_deletesubcategory(Request $request)

{
    $subcat_id=$request->subcat_id;

        $getfile=DB::table('subcat')
                ->where('subcat_id',$subcat_id)
                ->first();

    	$delete=DB::table('subcat')->where('subcat_id',$subcat_id)->delete();
        $deletepro=DB::table('product')->where('subcat_id',$subcat_id)->first();
        if($deletepro != null){
             DB::table('product_varient')->where('product_id',$deletepro->product_id)->delete();
             DB::table('product')->where('subcat_id',$subcat_id)->delete();
        if($delete)
        {
        $msg=array('status'=>'1','message'=>'delete successfully');
        return $msg;

        }
        else
        {
           $msg=array('status'=>'0','message'=>'unsuccessfull delete'); 
          return $msg;
        }
    }
    else
    {
       $msg=array('status'=>'1','message'=>'delete successfully'); 
       return $msg;
    }
}
 public function store_editsubcategory(Request $request)
 {
        $istabacco = $request->istabacco;
        $isid = $request->isid;
        $ispres = $request->ispres;
        $isbasket = $request->isbasket;
        $subcat_id=$request->subcat_id;
        $date = date('d-m-Y');
        $updated_at = date("d-m-y h:i a");
        $date=date('d-m-y');
        $getsubcat=DB::table('subcat')
                    ->where('subcat_id',$subcat_id)
                    ->first();
        if($request->category_id)
        {
            $category_id=$request->category_id;
        }
        else {
            $category_id=$getsubcat->category_id;
        }
        if($request->subcat_name){
        $subcat_name=$request->subcat_name;}
        else
        {
            $subcat_name=$getsubcat->subcat_name;
        }
        $old_subcat_image=$request->old_subcat_image;
        $update = DB::table('subcat')
                 ->where('subcat_id', $subcat_id)
                 ->update(['category_id'=>$category_id,'subcat_name'=>$subcat_name, 'subcat_image'=>'null','updated_at'=>$updated_at,'istabacco'=>$istabacco,'isid'=>$isid,'ispres'=>$ispres,'isbasket'=>$isbasket]);

        if($update){
            $msg=array('status'=>'1','message'=>'updated successfully','data'=>$update);
            return $msg;
        }
        else{
            $msg=array('status'=>'0','message'=>"something wents wrong.");
            return $msg;
        }     
 }
     public function store_addsubcategory(Request $request)
   {
     $istabacco = $request->istabacco;
     $isid = $request->isid;
     $ispres = $request->ispres;
     $isbasket = $request->isbasket;

     $category_id = $request->category_id;
     $subcat_name= $request->subcat_name;
    $created_at=date('d-m-Y h:i a');
      $subcategory = DB::table('subcat')->insertGetId(['category_id'=>$category_id,'subcat_name'=>$subcat_name,'created_at'=>$created_at,'istabacco'=>$istabacco,'isid'=>$isid,'ispres'=>$ispres,'isbasket'=>$isbasket]);
                  
       if($subcategory){
        $mess = array('status'=>'1', 'message'=>'subcat created', 'data'=>$subcategory);
        return $mess;
    }
      else
   {
     $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
     return $message;    }
   }
   public function store_addcategory(Request $request)
   {
     $vendor_id = $request->vendor_id;
     $cat_name= $request->cat_name;
    $created_at=date('d-m-Y h:i a');
    $date=date('d-m-y');
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
      $category = DB::table('tbl_category')->insertGetId(['vendor_id'=>$vendor_id,'category_name'=>$cat_name,'created_at'=>$created_at,'updated_at'=>$created_at,'category_image'=>$category_image]);
                  
       if($category){
        $mess = array('status'=>'1', 'message'=>'category created', 'data'=>$category);
        return $mess;
    }
      else
   {
     $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
     return $message;    }
   }
    public function store_subcategoryshow(Request $request)
  {
      $vendor_id = $request->vendor_id;
      $Category = DB::table('tbl_category')->where('vendor_id',$vendor_id)
               ->join('subcat', 'tbl_category.category_id', '=', 'subcat.category_id')
               ->select('subcat.subcat_id','subcat.subcat_name','subcat.subcat_image','tbl_category.vendor_id')

                     ->get();
                 
      if($Category){
       $mess = array('status'=>'1', 'message'=>'data found', 'data'=>$Category);
       return $mess;
   }
 else
  {
    $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[] );
    return $message;    }
 }
  public function store_editcategory(Request $request)
{
        $category_id = $request->category_id;
        $updated_at = Carbon::now();
        $date = date('d-m-Y');
        $getCategory = DB::table('tbl_category')
                    ->where('category_id',$category_id)
                    ->first();
        $vendor_id = $getCategory->vendor_id;
        $image = $getCategory->category_image;
        if($request->category_name){
        $category_name = $request->category_name;}
        else
        {
         $category_name = $getCategory->category_name;
        }
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
                                'updated_at'=>$updated_at
                            ]);
        
        if($updateCategory){
            $msg=array('status'=>'1','message'=>'category updated successfully','data'=>$updateCategory);
            return $msg;
        }
        else{
            $msg=array('status'=>'0','message'=>"Something wents wrong");
            return $msg;
        }
}
   public function store_deletecategory(Request $request)

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
        $msg= array('status'=>'1','message'=>'Delete successfully');
        return $msg;
        }
        else
        {
           $msg=array('status'=>'0','message'=>'unsuccessfull delete'); 
            return $msg;
        }
    }else{
        $msg=array('status'=>'0','message'=>'Delete successfully');
        return $msg;
    }
}
 public function store_subcategoryproduct(Request $request)
 {
   $vendor_id = $request->vendor_id;
   $subcat_id = $request->subcat_id;

   $products= DB::table('product')
   ->join('subcat','product.subcat_id', '=', 'subcat.subcat_id')
   ->join('tbl_category','subcat.category_id', '=', 'tbl_category.category_id')
   ->where('tbl_category.vendor_id', $vendor_id)
   ->where('subcat.subcat_id', $subcat_id)
  ->get();

  if(count($products)>0){
   foreach($products as $ords){
           $product_id = $ords->product_id;    
      $details  =   DB::table('product_varient')->where('product_id',$product_id)
                    
                   ->get(); 

                   $data[]=array('product_id'=>$product_id,'subcat_id'=>$ords->subcat_id,'product_name'=>$ords->product_name, 'product_image'=>$ords->product_image, 'vendor_id'=>$ords->vendor_id,'category_id'=>$ords->category_id, 'subcat_name'=>$ords->subcat_name, 'subcat_image'=>$ords->subcat_image, 'category_name'=>$ords->category_name,'category_image'=>$ords->category_image,'home'=>$ords->home,'varient_details'=>$details); 
                 }
                 }
         else{
         $data[]=array('order_details'=>'no orders found');
                 }
                 return $data;
               }
               
     public function store_updateproductvariant(Request $request)
               {
                 $product_id=$request->product_id;
                 $subcat_id=$request->subcat_id;
                 $product_name=$request->product_name;

                 $varient_id = $request->varient_id;
                 $strick_price = $request->strick_price;
                 $price=$request->price;
                 $stock=$request->stock;
                 $unit=$request->unit;
                 $quantity=$request->quantity;
                 $description =$request->description;

                 $updated_at = date("d-m-y h:i a");
                 $date=date('d-m-y');
                 
                 $getImage = DB::table('product')
                              ->where('product_id',$product_id)
                             ->first();
           
                 $image = $getImage->product_image;  
           
                 if($request->hasFile('product_image')){
                      if(file_exists($image)){
                         unlink($image);
                     }
                     $product_image = $request->product_image;
                     $fileName = date('dmyhisa').'-'.$product_image->getClientOriginalName();
                     $fileName = str_replace(" ", "-", $fileName);
                     $product_image->move('product/images/'.$date.'/', $fileName);
                     $product_image = 'product/images/'.$date.'/'.$fileName;
                 }
                 else{
                     $product_image = $image;
                 }
           
                 $update = DB::table('product')
                          ->where('product_id', $product_id)
                          ->update(['subcat_id'=>$subcat_id,'product_name'=>$product_name,'product_image'=>$product_image,'updated_at'=>$updated_at]);
           
                 $varient_update = DB::table('product_varient')
                          ->where('varient_id', $varient_id)
                          ->update(['strick_price'=>$strick_price, 'price'=>$price,'varient_image'=>$image, 'unit'=>$unit, 'quantity'=>$quantity,'description'=>$description,'stock'=>$stock]);
                 if($update)	{                     
                     $mess = array('status'=>'1', 'message'=>'data update', 'data'=>$update ,'datavarient'=>$varient_update);
                     return $mess;
                  }
                 else
                  {
                     $message = array('status'=>'0', 'message'=>'data not update', 'data'=>[] ,'datavarient'=>[]);
                     return $message;
                  }
           
               }
         public function store_addproductvariant(Request $request)
               {
                  $vendor_id=$request->vendor_id;
                 $product_id=$request->product_id;
                 $subcat_id=$request->subcat_id;
                 $product_name=$request->product_name;

                 $varient_id = $request->varient_id;
                 $price=$request->price;
                 if($request->strick_price){
                 $strick_price = $request->strick_price;
                 }
                 else{
                    $strike_price=$price;
                 }
                 if($request->stock){
                 $stock=$request->stock;
                 }
                 else{
                     $stock=100;
                 }
                 $unit=$request->unit;
                 $quantity=$request->quantity;
                 if($request->description){
                 $description =$request->description;
                 }
                 else
                 {
                     $description="";
                 }
        $date = date('d-m-Y');
        $created_at=date('d-m-Y h:i a');
        $product_image = $request->product_image;
        if($request->hasFile('product_image'))
        {
        $fileName = date('dmyhisa').'-'.$product_image->getClientOriginalName();
        $fileName = str_replace(" ", "-", $fileName);
        $product_image->move('product/images/'.$date.'/', $fileName);
        $product_image = 'product/images/'.$date.'/'.$fileName;
        }
        else
        {
            $product_image = "N/A";
        }
        $insert = DB::table('product')
        ->insertGetId(['subcat_id'=>$subcat_id,'product_name'=>$product_name,'product_image'=>$product_image,'created_at'=>$created_at,'vendor_id'=>$vendor_id]);
                 
        
        if($insert){
         
         $add1stvarient = DB::table('product_varient')
                        ->insert(['product_id'=>$insert,'price'=>$price, 'strick_price'=>$strick_price, 'varient_image'=>$product_image, 'unit'=>$unit, 'quantity'=>$quantity, 'stock'=>$stock,'description'=>$description,'vendor_id'=>$vendor_id]);
                              
                     $mess = array('status'=>'1', 'message'=>'found data', 'data'=>$insert ,'datavarient'=>$add1stvarient);
                     return $mess;
                  }
                 else
                  {
                     $message = array('status'=>'0', 'message'=>'data not update', 'data'=>[] ,'datavarient'=>[]);
                     return $message;
                  }
           
               }
}
