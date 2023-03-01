<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class SearchController extends Controller
{
        public function googleautocomplete(Request $request)
        {
    $keyword = $request->keyword;
    $keyword = urlencode($keyword);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input='.$keyword.'&key=AIzaSyCRQIhsGS3xrGeopcSPW70zal2yNRIQAJc&components=country%3AIN');
    $result = curl_exec($ch);
    return json_encode($result);
}

    public function resturantsearchingFor(Request $request)
    {
        $keyword = $request->prod_name;
         $lat = $request->lat;
       $lng = $request->lng;
        // $vendor_id = $request->vendor_id;

        $searchclass = DB::table('resturant_product')
                      ->join('vendor','resturant_product.vendor_id','=','vendor.vendor_id' )
                      ->select('vendor.*','resturant_product.product_id','resturant_product.subcat_id',DB::raw("6371 * acos(cos(radians(".$lat . "))
                    * cos(radians(vendor.lat))
                    * cos(radians(vendor.lng) - radians(" . $lng . "))
                    + sin(radians(" .$lat. "))
                    * sin(radians(vendor.lat))) AS distance"))
                    // ->groupBy('vendor.vendor_name','vendor.lat', 'vendor.lng')
                    ->where('resturant_product.product_name', 'like', '%'.$keyword.'%')
                  ->orderBy('distance')
                      ->get();

  if(count($searchclass)>0){
         foreach($searchclass as $searchclasss){
           $prod = DB::table('resturant_product')
                        ->where('product_id',$searchclasss->product_id)
                      ->get();
         if(count($prod)>0){
            $result =array();
            $i = 0;

            foreach ($prod as $prods) {
                array_push($result, $prods);

                $app = json_decode($prods->product_id);
                $apps = array($app);
                $app =  DB::table('resturant_variant')
                     ->join ('resturant_product', 'resturant_variant.product_id', '=', 'resturant_product.product_id')
                     ->whereIn('resturant_variant.product_id', $apps)
                     ->get();

                $result[$i]->varients = $app;
                $i++;

            }

         }
            $subcat = DB::table('resturant_category')
                        ->where('resturant_cat_id',$searchclasss->subcat_id)
                      ->get();

             $cat = DB::table('resturant_category')
             ->join('vendor','resturant_category.vendor_id','=','vendor.vendor_id' )
              ->select('resturant_category.*','vendor.vendor_name',DB::raw("6371 * acos(cos(radians(".$lat . "))
                    * cos(radians(vendor.lat))
                    * cos(radians(vendor.lng) - radians(" . $lng . "))
                    + sin(radians(" .$lat. "))
                    * sin(radians(vendor.lat))) AS distance"))
                    ->orderBy('distance')
                ->where('resturant_category.resturant_cat_id',$searchclasss->subcat_id)
                ->get();



        $data=array('status'=>'1','message'=>'Stores Found', 'stores'=>$searchclass, 'category'=>$cat, 'products'=>$prod);
           }
        }
        else{
            $data[]=array('status'=>'0','message'=>'Stores Found');
        }
        return $data;

    }

     public function searchingFor(Request $request)
    {
        $keyword = $request->prod_name;
         $lat = $request->lat;
       $lng = $request->lng;
        // $vendor_id = $request->vendor_id;

        $searchclass = DB::table('product')
                      ->join('vendor','product.vendor_id','=','vendor.vendor_id' )
                      ->select('vendor.*','product.product_id','product.subcat_id',DB::raw("6371 * acos(cos(radians(".$lat . "))
                    * cos(radians(vendor.lat))
                    * cos(radians(vendor.lng) - radians(" . $lng . "))
                    + sin(radians(" .$lat. "))
                    * sin(radians(vendor.lat))) AS distance"))
                    // ->groupBy('vendor.vendor_name','vendor.lat', 'vendor.lng')
                    ->where('product.product_name', 'like', '%'.$keyword.'%')
                  ->orderBy('distance')
                      ->get();

  if(count($searchclass)>0){
         foreach($searchclass as $searchclasss){
           $prod = DB::table('product')
                        ->where('product_id',$searchclasss->product_id)
                      ->get();
         if(count($prod)>0){
            $result =array();
            $i = 0;

            foreach ($prod as $prods) {
                array_push($result, $prods);

                $app = json_decode($prods->product_id);
                $apps = array($app);
                $app =  DB::table('product_varient')
                     ->join ('product', 'product_varient.product_id', '=', 'product.product_id')
                     ->whereIn('product_varient.product_id', $apps)
                     ->get();

                $result[$i]->varients = $app;
                $i++;

            }

         }
            $subcat = DB::table('subcat')
                        ->where('subcat_id',$searchclasss->subcat_id)
                      ->get();

             $cat = DB::table('tbl_category')
             ->join('vendor','tbl_category.vendor_id','=','vendor.vendor_id' )
              ->join('subcat','tbl_category.category_id','=','subcat.category_id' )
              ->select('tbl_category.*','vendor.vendor_name',DB::raw("6371 * acos(cos(radians(".$lat . "))
                    * cos(radians(vendor.lat))
                    * cos(radians(vendor.lng) - radians(" . $lng . "))
                    + sin(radians(" .$lat. "))
                    * sin(radians(vendor.lat))) AS distance"))
                    ->orderBy('distance')
                ->where('subcat.subcat_id',$searchclasss->subcat_id)
                ->get();



        $data=array('status'=>'1','message'=>'Stores Found', 'stores'=>$searchclass, 'category'=>$cat, 'subcat'=>$subcat, 'products'=>$prod);
           }
        }
        else{
            $data[]=array('status'=>'0','message'=>'No Stores Found');
        }
        return $data;

    }
    public function searching(Request $request)
    {
        $keyword = $request->prod_name;
         $lat = $request->lat;
       $lng = $request->lng;
        // $vendor_id = $request->vendor_id;

        $searchclass = DB::table('product')
                      ->join('vendor','product.vendor_id','=','vendor.vendor_id' )
                      ->join('vendor_category','vendor.vendor_category_id','vendor_category.vendor_category_id')
                      ->select('vendor.*','product.product_id','product.subcat_id','product.product_name','vendor_category.category_name',DB::raw("6371 * acos(cos(radians(".$lat . "))
                    * cos(radians(vendor.lat))
                    * cos(radians(vendor.lng) - radians(" . $lng . "))
                    + sin(radians(" .$lat. "))
                    * sin(radians(vendor.lat))) AS distance"))
                    // ->groupBy('vendor.vendor_name','vendor.lat', 'vendor.lng')
                    ->where('product.product_name', 'like', '%'.$keyword.'%')
                  ->orderBy('distance')
                      ->get();
                      foreach($searchclass as $product){
        $product->str1=$product->product_name;
        $product->str2=$product->vendor_name;}

        $searchclass2 = DB::table('resturant_product')
              ->join('vendor','resturant_product.vendor_id','=','vendor.vendor_id')
              ->join('vendor_category','vendor.vendor_category_id','vendor_category.vendor_category_id')
              ->select('vendor.*','resturant_product.product_id','resturant_product.subcat_id','resturant_product.product_name','vendor_category.category_name',DB::raw("6371 * acos(cos(radians(".$lat . "))
            * cos(radians(vendor.lat))
            * cos(radians(vendor.lng) - radians(" . $lng . "))
            + sin(radians(" .$lat. "))
            * sin(radians(vendor.lat))) AS distance"))
            // ->groupBy('vendor.vendor_name','vendor.lat', 'vendor.lng')
            ->where('resturant_product.product_name', 'like', '%'.$keyword.'%')
          ->orderBy('distance')
              ->get();
            foreach($searchclass2 as $product){
        $product->str1=$product->product_name;
        $product->str2=$product->vendor_name;}

        $searchclass3 = DB::table('vendor')
              ->join('vendor_category','vendor.vendor_category_id','vendor_category.vendor_category_id')
              ->select('vendor.*','vendor_category.category_name',DB::raw("6371 * acos(cos(radians(".$lat . "))
            * cos(radians(lat))
            * cos(radians(lng) - radians(" . $lng . "))
            + sin(radians(" .$lat. "))
            * sin(radians(lat))) AS distance"))
            // ->groupBy('vendor.vendor_name','vendor.lat', 'vendor.lng')
            ->where('vendor_name', 'like', '%'.$keyword.'%')
          ->orderBy('distance')
              ->get();
          foreach($searchclass3 as $product){
        $product->str1=$product->vendor_name;
        $product->str2=$product->category_name;}

             $cat = DB::table('tbl_category')
             ->join('vendor','tbl_category.vendor_id','=','vendor.vendor_id' )
              ->join('subcat','tbl_category.category_id','=','subcat.category_id' )
              ->select('tbl_category.*','vendor.*',DB::raw("6371 * acos(cos(radians(".$lat . "))
                    * cos(radians(vendor.lat))
                    * cos(radians(vendor.lng) - radians(" . $lng . "))
                    + sin(radians(" .$lat. "))
                    * sin(radians(vendor.lat))) AS distance"))
                    ->orderBy('distance')
                ->where('subcat.subcat_name', 'like', '%'.$keyword.'%')
                ->get();
                            foreach($cat as $cats){
        $cats->str1=$cats->category_name;
        $cats->str2=$cats->vendor_name;}

             $restcat = DB::table('resturant_category')
             ->join('vendor','resturant_category.vendor_id','=','vendor.vendor_id' )
              ->select('resturant_category.*','vendor.*',DB::raw("6371 * acos(cos(radians(".$lat . "))
                    * cos(radians(vendor.lat))
                    * cos(radians(vendor.lng) - radians(" . $lng . "))
                    + sin(radians(" .$lat. "))
                    * sin(radians(vendor.lat))) AS distance"))
                    ->orderBy('distance')
                ->where('resturant_category.cat_name','like', '%'.$keyword.'%')
                ->get();
                foreach($restcat as $restcats){
        $restcats->str1=$restcats->cat_name;
        $restcats->str2=$restcats->vendor_name;}
  if(count($searchclass)>0||count($searchclass2)>0||count($searchclass3)>0||count($cat)>0||count($restcat)>0){
//          foreach($searchclass as $searchclasss){
//           $prod = DB::table('product')
//                         ->where('product_id',$searchclasss->product_id)
//                       ->get();
//          if(count($prod)>0){
//             $result =array();
//             $i = 0;

//             foreach ($prod as $prods) {
//                 array_push($result, $prods);

//                 $app = json_decode($prods->product_id);
//                 $apps = array($app);
//                 $app =  DB::table('product_varient')
//                      ->join ('product', 'product_varient.product_id', '=', 'product.product_id')
//                      ->whereIn('product_varient.product_id', $apps)
//                      ->get();

//                 $result[$i]->varients = $app;
//                 $i++;

//             }

//          }
//             $subcat = DB::table('subcat')
//                         ->where('subcat_id',$searchclasss->subcat_id)
//                       ->get();

//              $cat = DB::table('tbl_category')
//              ->join('vendor','tbl_category.vendor_id','=','vendor.vendor_id' )
//               ->join('subcat','tbl_category.category_id','=','subcat.category_id' )
//               ->select('tbl_category.*','vendor.vendor_name',DB::raw("6371 * acos(cos(radians(".$lat . "))
//                     * cos(radians(vendor.lat))
//                     * cos(radians(vendor.lng) - radians(" . $lng . "))
//                     + sin(radians(" .$lat. "))
//                     * sin(radians(vendor.lat))) AS distance"))
//                     ->orderBy('distance')
//                 ->where('subcat.subcat_id',$searchclasss->subcat_id)
//                 ->get();



        // $data=array('status'=>'1','message'=>'Stores Found', 'stores'=>$searchclass, 'category'=>$cat, 'subcat'=>$subcat, 'products'=>$prod);
        //   }
                $data=array('status'=>'1','message'=>'Found', 'product'=>$searchclass,'restproduct'=>$searchclass2, 'vendor'=>$searchclass3, 'cat'=>$cat, 'restcat'=>$restcat);
        }
        else{
            $data[]=array('status'=>'0','message'=>'No Stores Found');
        }
        return $data;

    }
      public function searchingrest(Request $request)
    {
        $keyword = $request->prod_name;
         $lat = $request->lat;
       $lng = $request->lng;

        $searchclass3 = DB::table('vendor')
              ->join('vendor_category','vendor.vendor_category_id','vendor_category.vendor_category_id')
              ->select('vendor.*','vendor_category.category_name',DB::raw("6371 * acos(cos(radians(".$lat . "))
            * cos(radians(lat))
            * cos(radians(lng) - radians(" . $lng . "))
            + sin(radians(" .$lat. "))
            * sin(radians(lat))) AS distance"))
            ->where('vendor_name', 'like', '%'.$keyword.'%')
            ->where('vendor.ui_type', 2)
          ->orderBy('distance')
              ->get();
          foreach($searchclass3 as $product){
        $product->str1=$product->vendor_name;
        $product->str2=$product->category_name;}

  if(count($searchclass3)>0){
      $data=array('status'=>'1','message'=>'Found', 'vendor'=>$searchclass3);
        }
        else{
            $data[]=array('status'=>'0','message'=>'No Stores Found');
        }
        return $data;

    }
        public function restsearching(Request $request)
    {
        $keyword = $request->prod_name;
        $vendor_id = $request->vendor_id;

        $searchclass2 = DB::table('resturant_product')
              ->join('vendor','resturant_product.vendor_id','=','vendor.vendor_id')
              ->join('vendor_category','vendor.vendor_category_id','vendor_category.vendor_category_id')
              ->select('vendor.*','resturant_product.product_id','resturant_product.subcat_id','resturant_product.product_name','vendor_category.category_name')
            ->where('resturant_product.product_name', 'like', '%'.$keyword.'%')
            ->where('vendor.vendor_id', $vendor_id)
              ->get();
            foreach($searchclass2 as $product){
        $product->str1=$product->product_name;
        $product->str2=$product->vendor_name;}

             $restcat = DB::table('resturant_category')
             ->join('vendor','resturant_category.vendor_id','=','vendor.vendor_id' )
              ->select('resturant_category.*','vendor.*')
                ->where('resturant_category.cat_name','like', '%'.$keyword.'%')
                ->where('vendor.vendor_id', $vendor_id)
                ->get();
                foreach($restcat as $restcats){
        $restcats->str1=$restcats->cat_name;
        $restcats->str2=$restcats->vendor_name;}
  if(count($searchclass2)>0||count($restcat)>0){

                $data=array('status'=>'1','message'=>'Found', 'restproduct'=>$searchclass2, 'restcat'=>$restcat);
        }
        else{
            $data[]=array('status'=>'0','message'=>'No Stores Found');
        }
        return $data;

    }
    public function storesearch(Request $request)
{
    $keyword = $request->prod_name;
    $vendor_id = $request->vendor_id;

    $searchclass2 = DB::table('product')
          ->join('vendor','product.vendor_id','=','vendor.vendor_id')
          ->join('vendor_category','vendor.vendor_category_id','vendor_category.vendor_category_id')
          ->select('vendor.vendor_id','vendor.vendor_name','product.*','vendor_category.category_name')
        ->where('product.product_name', 'like', '%'.$keyword.'%')
        ->where('vendor.vendor_id', $vendor_id)
          ->get();
        foreach($searchclass2 as $product){
          $varients = DB::table('product_varient')
                    ->where('product_id',$product->product_id)
                    ->get();
    $product->data = $varients;
    $product->products_image = $product->product_image;
    $product->str1=$product->product_name;
    $product->str2=$product->category_name;}

         $restcat = DB::table('tbl_category')
         ->join('vendor','tbl_category.vendor_id','=','vendor.vendor_id' )
          ->select('tbl_category.*','vendor.vendor_id','vendor.vendor_name')
            ->where('tbl_category.category_name','like', '%'.$keyword.'%')
            ->where('vendor.vendor_id', $vendor_id)
            ->get();
            foreach($restcat as $restcats){
    $restcats->data=[];
    $restcats->str1=$restcats->category_name;
    $restcats->str2=$restcats->vendor_name;}
if(count($searchclass2)>0||count($restcat)>0){

            $data=array('status'=>'1','message'=>'Found', 'product'=>$searchclass2, 'cat'=>$restcat);
    }
    else{
        $data[]=array('status'=>'0','message'=>'No Stores Found');
    }
    return $data;

}
}
