<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Hash;
use Session;

class StoreOrderController extends Controller
{
       public function store_order_detials(Request $request)
    {
        $cart_id=$request->cart_id;
        $vendor_id=$request->vendor_id;
        if($vendor_id=='54'){
        $todayorder  =   DB::table('orders')
						   ->join('vendor', 'orders.vendor_id','=', 'vendor.vendor_id')
                           ->join('tbl_user', 'orders.user_id', '=', 'tbl_user.user_id')
    	                    ->join('user_address','orders.address_id', '=', 'user_address.address_id')
    	                   // ->join('user_address.area_id','=', 'area.area_id')
    	                    ->leftJoin('delivery_boy','orders.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->select('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','orders.pres','tbl_user.id_proof','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','orders.surgecharge','orders.nightcharge','orders.convcharge','orders.gst')
                          ->groupBy('orders.cart_id')
                          ->groupBy('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','tbl_user.id_proof','orders.surgecharge','orders.nightcharge','orders.convcharge','orders.gst')
                            ->where('orders.payment_status','!=', 'NULL')
                            ->where('orders.cart_id',$cart_id)
                            ->where('orders.vendor_id',$vendor_id)
                            ->where('orders.ui_type','=', '1')
    	                    ->orderBy('user_id')
                           ->first();
                           if($todayorder){
                              $carts=array();
                                   $details = DB::table('order_details')
                                        ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                        ->join('product','product_varient.product_id', '=', 'product.product_id')
                                        ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('vendor.vendor_id','vendor.vendor_name')
                                            ->where('order_cart_id',$cart_id)
                                            ->groupby('vendor.vendor_id','vendor.vendor_name')
                                            ->get();
                                    $details2 = DB::table('order_details')
                                            ->where('order_cart_id',$cart_id)
                                            ->get();
                                    $todayorder->totalitems=count($details2);
                                    foreach($details as $vendor)
                                    {
                                        $vendordetails  =   DB::table('order_details')
                                             ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                             ->join('product','product_varient.product_id', '=', 'product.product_id')
                                             ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'),'order_details.add_to_basket')
                                            ->where('order_details.order_cart_id',$cart_id)
                                            ->where('vendor.vendor_id',$vendor->vendor_id)
                                            ->groupBy('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty','order_details.add_to_basket')
                                            ->get();
                                            foreach($vendordetails as $detail)
                                            {
                                            $basketproducts=[];
                                            if($detail->add_to_basket==1)
                                                {
                                                    $basketproducts=array($detail);
                                                }
                                            }
                                            $storestatus = DB::table('orders')
                                            ->select('cancel_by_store','approve_by_store')
                                            ->where('cart_id',$cart_id)
                                            ->where('vendor_id',$vendor->vendor_id)
                                            ->first();
                                             $vendor->store_status="Waiting";
                                            if($storestatus->approve_by_store>0)
                                            {
                                             $vendor->store_status="Approved";
                                            }
                                            if($storestatus->cancel_by_store>0)
                                            {
                                             $vendor->store_status="Cancelled";
                                            }

                                        $vendor->vendordetails=$vendordetails;
                                        $order = DB::table('orders')
                                        ->where('cart_id',$cart_id)
                                        ->where('vendor_id',$vendor->vendor_id)
                                        ->first();
                                        $instruction=DB::table('order_instructions')
                                        ->where('order_id',$order->order_id)
                                        ->first();
                                        if($instruction){
                                        $vendor->instruction=$instruction->instruction;
                                        }
                                    }
                                            $data[]=array('pres'=>$todayorder->pres,'id_proof'=>$todayorder->id_proof,'order_id'=>$todayorder->order_id, 'user_id'=>$todayorder->user_id, 'delivery_date'=>$todayorder->delivery_date,'user_name'=>$todayorder->user_name, 'dboy_id'=>$todayorder->dboy_id, 'delivery_charge'=>$todayorder->delivery_charge, 'total_price'=>$todayorder->total_price,'total_product_mrp'=>$todayorder->total_products_mrp,'delivery_boy_name'=>$todayorder->delivery_boy_name,'order_status'=>$todayorder->order_status,'cart_id'=>$cart_id,'user_number'=>$todayorder->user_number,'address'=>$todayorder->address , 'time_slot'=>$todayorder->time_slot,'paid_by_wallet'=>$todayorder->paid_by_wallet,'remaining_price'=>$todayorder->rem_price,'price_without_delivery'=>$todayorder->price_without_delivery,'coupon_discount'=>$todayorder->coupon_discount,'payment_method'=>$todayorder->payment_method,'payment_status'=>$todayorder->payment_status,'delivery_boy_num'=>$todayorder->delivery_boy_phone,'total_items'=>$todayorder->totalitems,'order_details'=>$details,'basket_products'=>$basketproducts,'surgecharge'=>$todayorder->surgecharge,'nightcharge'=>$todayorder->nightcharge,'convcharge'=>$todayorder->convcharge,'gst'=>$todayorder->gst);
                                            $carts[]=$todayorder->cart_id;
                                          }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
                                        }
                                        else{
                                          $todayorder  =   DB::table('orders')
                                                 ->join('vendor', 'orders.vendor_id','=', 'vendor.vendor_id')
                                                             ->join('tbl_user', 'orders.user_id', '=', 'tbl_user.user_id')
                                                            ->join('user_address','orders.address_id', '=', 'user_address.address_id')
                                                           // ->join('user_address.area_id','=', 'area.area_id')
                                                            ->leftJoin('delivery_boy','orders.dboy_id', '=','delivery_boy.delivery_boy_id')
                                                            ->select('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','orders.pres','tbl_user.id_proof','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','orders.surgecharge','orders.nightcharge','orders.convcharge','orders.gst')
                                                            ->groupBy('orders.cart_id')
                                                            ->groupBy('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','tbl_user.id_proof','orders.surgecharge','orders.nightcharge','orders.convcharge','orders.gst')
                                                              ->where('orders.payment_status','!=', 'NULL')
                                                              ->where('orders.cart_id',$cart_id)
                                                              ->where('orders.vendor_id',$vendor_id)
                                                              ->where('orders.ui_type','=', '1')
                                                            ->orderBy('user_id')
                                                             ->first();
                                                             if($todayorder){
                                                                $carts=array();
                                                                     $details = DB::table('order_details')
                                                                          ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                                                          ->join('product','product_varient.product_id', '=', 'product.product_id')
                                                                          ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                                                               ->select('vendor.vendor_id','vendor.vendor_name')
                                                                              ->where('order_cart_id',$cart_id)
                                                                              ->where('vendor.vendor_id',$vendor_id)
                                                                              ->groupby('vendor.vendor_id','vendor.vendor_name')
                                                                              ->get();
                                                                      $details2 = DB::table('order_details')
                                                                              ->where('order_cart_id',$cart_id)
                                                                              ->get();
                                                                      $todayorder->totalitems=count($details2);
                                                                      foreach($details as $vendor)
                                                                      {
                                                                          $vendordetails  =   DB::table('order_details')
                                                                               ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                                                               ->join('product','product_varient.product_id', '=', 'product.product_id')
                                                                               ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                                                               ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'),'order_details.add_to_basket')
                                                                              ->where('order_details.order_cart_id',$cart_id)
                                                                              ->where('vendor.vendor_id',$vendor->vendor_id)
                                                                              ->groupBy('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty','order_details.add_to_basket')
                                                                              ->get();
                                                                              foreach($vendordetails as $detail)
                                                                              {
                                                                              $basketproducts=[];
                                                                              if($detail->add_to_basket==1)
                                                                                  {
                                                                                      $basketproducts=array($detail);
                                                                                  }
                                                                              }
                                                                              $storestatus = DB::table('orders')
                                                                              ->select('cancel_by_store','approve_by_store')
                                                                              ->where('cart_id',$cart_id)
                                                                              ->where('vendor_id',$vendor->vendor_id)
                                                                              ->first();
                                                                               $vendor->store_status="Waiting";
                                                                              if($storestatus->approve_by_store>0)
                                                                              {
                                                                               $vendor->store_status="Approved";
                                                                              }
                                                                              if($storestatus->cancel_by_store>0)
                                                                              {
                                                                               $vendor->store_status="Cancelled";
                                                                              }

                                                                          $vendor->vendordetails=$vendordetails;
                                                                          $order = DB::table('orders')
                                                                          ->where('cart_id',$cart_id)
                                                                          ->where('vendor_id',$vendor->vendor_id)
                                                                          ->first();
                                                                          $instruction=DB::table('order_instructions')
                                                                          ->where('order_id',$order->order_id)
                                                                          ->first();
                                                                          if($instruction){
                                                                          $vendor->instruction=$instruction->instruction;
                                                                          }
                                                                      }
                                                                              $data[]=array('pres'=>$todayorder->pres,'id_proof'=>$todayorder->id_proof,'order_id'=>$todayorder->order_id, 'user_id'=>$todayorder->user_id, 'delivery_date'=>$todayorder->delivery_date,'user_name'=>$todayorder->user_name, 'dboy_id'=>$todayorder->dboy_id, 'delivery_charge'=>$todayorder->delivery_charge, 'total_price'=>$todayorder->total_price,'total_product_mrp'=>$todayorder->total_products_mrp,'delivery_boy_name'=>$todayorder->delivery_boy_name,'order_status'=>$todayorder->order_status,'cart_id'=>$cart_id,'user_number'=>$todayorder->user_number,'address'=>$todayorder->address , 'time_slot'=>$todayorder->time_slot,'paid_by_wallet'=>$todayorder->paid_by_wallet,'remaining_price'=>$todayorder->rem_price,'price_without_delivery'=>$todayorder->price_without_delivery,'coupon_discount'=>$todayorder->coupon_discount,'payment_method'=>$todayorder->payment_method,'payment_status'=>$todayorder->payment_status,'delivery_boy_num'=>$todayorder->delivery_boy_phone,'total_items'=>$todayorder->totalitems,'order_details'=>$details,'basket_products'=>$basketproducts,'surgecharge'=>$todayorder->surgecharge,'nightcharge'=>$todayorder->nightcharge,'convcharge'=>$todayorder->convcharge,'gst'=>$todayorder->gst);
                                                                              $carts[]=$todayorder->cart_id;
                                                                            }
                                                                    else{
                                                                    $data[]=array('order_details'=>'no orders found');
                                                                            }
                                        }
                               return $data;
    }

   public function store_today_order(Request $request)
    {


    	$currentDate = date('Y-m-d');
        $day = 1;
       $current2 = date('Y-m-d', strtotime($currentDate.' + '.$day.' days'));

    	 $vendor_id = $request->vendor_id;
    	 if($request->vendor_id=='54')
    	  {    	  $todayorder  =   DB::table('orders')
						   ->join('vendor', 'orders.vendor_id','=', 'vendor.vendor_id')
                           ->join('tbl_user', 'orders.user_id', '=', 'tbl_user.user_id')
    	                    ->join('user_address','orders.address_id', '=', 'user_address.address_id')
    	                   // ->join('user_address.area_id','=', 'area.area_id')
    	                    ->leftJoin('delivery_boy','orders.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->select('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','orders.pres','tbl_user.id_proof','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','orders.surgecharge','orders.nightcharge','orders.convcharge','orders.gst')
                          ->groupBy('orders.cart_id')
                          ->groupBy('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','tbl_user.id_proof','orders.surgecharge','orders.nightcharge','orders.convcharge','orders.gst')
                            ->where('orders.payment_status','!=', 'NULL')
                           ->where('orders.order_status','!=', 'Cancelled')
                            ->where('orders.ui_type','=', '1')
                            ->where('orders.order_status','!=', 'Completed')
                             ->where('orders.vendor_id',54)
                             ->whereDate('orders.delivery_date', $currentDate)
    	                    ->orderBy('user_id')
                           ->get();
                           $todayorder->groupBy('orders.cart_id');
                           if(count($todayorder)>0){
                              $carts=array();
                            foreach($todayorder as $ords){
                                   $cart_id = $ords->cart_id;
                                   $details = DB::table('order_details')
                                        ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                        ->join('product','product_varient.product_id', '=', 'product.product_id')
                                        ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('vendor.vendor_id','vendor.vendor_name')
                                            ->where('order_cart_id',$cart_id)
                                            ->groupby('vendor.vendor_id','vendor.vendor_name')
                                            ->get();
                                    $details2 = DB::table('order_details')
                                            ->where('order_cart_id',$cart_id)
                                            ->get();
                                    $ords->totalitems=count($details2);
                                    foreach($details as $vendor)
                                    {
                                        $vendordetails  =   DB::table('order_details')
                                             ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                             ->join('product','product_varient.product_id', '=', 'product.product_id')
                                             ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'),'order_details.add_to_basket')
                                            ->where('order_details.order_cart_id',$cart_id)
                                            ->where('vendor.vendor_id',$vendor->vendor_id)
                                            ->groupBy('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty','order_details.add_to_basket')
                                            ->get();
                                            foreach($vendordetails as $detail)
                                            {
                                            $basketproducts=[];
                                            if($detail->add_to_basket==1)
                                                {
                                                    $basketproducts=array($detail);
                                                }
                                            }
                                            $storestatus = DB::table('orders')
                                            ->select('cancel_by_store','approve_by_store')
                                            ->where('cart_id',$cart_id)
                                            ->where('vendor_id',$vendor->vendor_id)
                                            ->first();
                                             $vendor->store_status="Waiting";
                                            if($storestatus->approve_by_store>0)
                                            {
                                             $vendor->store_status="Approved";
                                            }
                                            if($storestatus->cancel_by_store>0)
                                            {
                                             $vendor->store_status="Cancelled";
                                            }
                                        $vendor->vendordetails=$vendordetails;
                                        $order = DB::table('orders')
                                        ->where('cart_id',$cart_id)
                                        ->where('vendor_id',$vendor->vendor_id)
                                        ->first();
                                        $instruction=DB::table('order_instructions')
                                        ->where('order_id',$order->order_id)
                                        ->first();
                                        if($instruction){
                                        $vendor->instruction=$instruction->instruction;}
                                    }
                                    if(in_array($ords->cart_id,$carts))
                                    {
                                    }
                                    else{
                                            $data[]=array('pres'=>$ords->pres,'id_proof'=>$ords->id_proof,'order_id'=>$ords->order_id, 'user_id'=>$ords->user_id, 'delivery_date'=>$ords->delivery_date,'user_name'=>$ords->user_name, 'dboy_id'=>$ords->dboy_id, 'delivery_charge'=>$ords->delivery_charge, 'total_price'=>$ords->total_price,'total_product_mrp'=>$ords->total_products_mrp,'delivery_boy_name'=>$ords->delivery_boy_name,'order_status'=>$ords->order_status,'cart_id'=>$cart_id,'user_number'=>$ords->user_number,'address'=>$ords->address , 'time_slot'=>$ords->time_slot,'paid_by_wallet'=>$ords->paid_by_wallet,'remaining_price'=>$ords->rem_price,'price_without_delivery'=>$ords->price_without_delivery,'coupon_discount'=>$ords->coupon_discount,'payment_method'=>$ords->payment_method,'payment_status'=>$ords->payment_status,'delivery_boy_num'=>$ords->delivery_boy_phone,'total_items'=>$ords->totalitems,'order_details'=>$details,'basket_products'=>$basketproducts,'surgecharge'=>$ords->surgecharge,'nightcharge'=>$ords->nightcharge,'convcharge'=>$ords->convcharge,'gst'=>$ords->gst);
                                            $carts[]=$ords->cart_id;
                                    }
                                          }
                                          }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
                               return $data;

    	 }
    	 else{
    	         	      	  $todayorder  =   DB::table('orders')
						   ->join('vendor', 'orders.vendor_id','=', 'vendor.vendor_id')
                           ->join('tbl_user', 'orders.user_id', '=', 'tbl_user.user_id')
    	                    ->join('user_address','orders.address_id', '=', 'user_address.address_id')
    	                   // ->join('user_address.area_id','=', 'area.area_id')
    	                    ->leftJoin('delivery_boy','orders.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->select('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','orders.pres','tbl_user.id_proof','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','orders.gst')
                          ->groupBy('orders.cart_id')
                          ->groupBy('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','tbl_user.id_proof','orders.gst')
                            ->where('orders.payment_status','!=', 'NULL')
                           ->where('orders.order_status','!=', 'Cancelled')
                            ->where('orders.ui_type','=', '1')
                            ->where('orders.order_status','!=', 'Completed')
                            ->where('orders.vendor_id',$vendor_id)
                             ->whereDate('orders.delivery_date', $currentDate)
    	                    ->orderBy('user_id')
                           ->get();
                           $todayorder->groupBy('orders.cart_id');
                           if(count($todayorder)>0){
                              $carts=array();
                            foreach($todayorder as $ords){
                                   $cart_id = $ords->cart_id;
                                   $details = DB::table('order_details')
                                        ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                        ->join('product','product_varient.product_id', '=', 'product.product_id')
                                        ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('vendor.vendor_id','vendor.vendor_name')
                                            ->where('order_cart_id',$cart_id)
                                            ->where('vendor.vendor_id',$vendor_id)
                                            ->groupby('vendor.vendor_id','vendor.vendor_name')
                                            ->get();
                                    $details2 = DB::table('order_details')
                                            ->where('order_cart_id',$cart_id)
                                            ->get();
                                    $ords->totalitems=count($details2);
                                    foreach($details as $vendor)
                                    {
                                        $vendordetails  =   DB::table('order_details')
                                             ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                             ->join('product','product_varient.product_id', '=', 'product.product_id')
                                             ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'),'order_details.add_to_basket')
                                            ->where('order_details.order_cart_id',$cart_id)
                                            ->where('vendor.vendor_id',$vendor->vendor_id)
                                            ->groupBy('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty','order_details.add_to_basket')
                                            ->get();
                                            foreach($vendordetails as $detail)
                                            {
                                            $basketproducts=[];
                                            if($detail->add_to_basket==1)
                                                {
                                                    $basketproducts=array($detail);
                                                }
                                            }
                                        $vendor->vendordetails=$vendordetails;
                                         $order = DB::table('orders')
                                        ->where('cart_id',$cart_id)
                                        ->where('vendor_id',$vendor->vendor_id)
                                        ->first();
                                        $instruction=DB::table('order_instructions')
                                        ->where('order_id',$order->order_id)
                                        ->first();
                                        if($instruction){
                                        $vendor->instruction=$instruction->instruction;}
                                    }
                                    if(in_array($ords->cart_id,$carts))
                                    {
                                    }
                                    else{
                                            $data[]=array('pres'=>$ords->pres,'id_proof'=>$ords->id_proof,'order_id'=>$ords->order_id, 'user_id'=>$ords->user_id, 'delivery_date'=>$ords->delivery_date,'user_name'=>$ords->user_name, 'dboy_id'=>$ords->dboy_id, 'delivery_charge'=>$ords->delivery_charge, 'total_price'=>$ords->total_price,'total_product_mrp'=>$ords->total_products_mrp,'delivery_boy_name'=>$ords->delivery_boy_name,'order_status'=>$ords->order_status,'cart_id'=>$cart_id,'user_number'=>$ords->user_number,'address'=>$ords->address , 'time_slot'=>$ords->time_slot,'paid_by_wallet'=>$ords->paid_by_wallet,'remaining_price'=>$ords->rem_price,'price_without_delivery'=>$ords->price_without_delivery,'coupon_discount'=>$ords->coupon_discount,'payment_method'=>$ords->payment_method,'payment_status'=>$ords->payment_status,'delivery_boy_num'=>$ords->delivery_boy_phone,'total_items'=>$ords->totalitems,'order_details'=>$details,'basket_products'=>$basketproducts,'gst'=>$ords->gst);
                                            $carts[]=$ords->cart_id;
                                    }
                                          }
                                          }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
                          return $data;

    	 }

      }
      public function store_next_day_order(Request $request)
      {


        $currentDate = date('Y-m-d');
        $day = 1;
        $end = date('Y-m-d', strtotime($currentDate.' + '.$day.' days'));

           $vendor_id = $request->vendor_id;
           $nextdayorder  =   DB::table('orders')
           ->join('tbl_user', 'orders.user_id', '=', 'tbl_user.user_id')
           ->join('user_address','orders.address_id', '=', 'user_address.address_id')
           ->join('area', 'user_address.area_id','=', 'area.area_id')
           ->join('vendor_area', 'area.area_id','=', 'vendor_area.area_id')
      ->join('vendor', 'vendor_area.vendor_id','=', 'vendor.vendor_id')
           ->leftJoin('delivery_boy','orders.dboy_id', '=','delivery_boy.delivery_boy_id')
           ->select('area.area_id','orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','vendor.vendor_name','orders.gst')
           ->groupBy('area.area_id','orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','vendor.vendor_name','orders.gst')
           ->whereDate('orders.delivery_date', $end)
            ->where('orders.payment_status','!=', 'NULL')
           ->where('orders.vendor_id', $vendor_id)
           ->where('orders.order_status','!=', 'Cancelled')
            ->where('orders.ui_type','=', '1')
            ->where('orders.order_status','!=', 'Completed')
           ->orderBy('user_id')
           ->get();

           if(count($nextdayorder)>0){
            foreach($nextdayorder as $ords){
                   $cart_id = $ords->cart_id;
               $details  =   DB::table('order_details')
                             ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                             ->join('product','product_varient.product_id', '=', 'product.product_id')
                             ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'))
                            ->where('order_details.order_cart_id',$cart_id)
                            ->groupBy('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty')
                            ->get();





                            $data[]=array('area_id'=>$ords->area_id,'order_id'=>$ords->order_id, 'user_id'=>$ords->user_id, 'delivery_date'=>$ords->delivery_date,'user_name'=>$ords->user_name, 'dboy_id'=>$ords->dboy_id, 'delivery_charge'=>$ords->delivery_charge, 'total_price'=>$ords->total_price,'total_product_mrp'=>$ords->total_products_mrp,'delivery_boy_name'=>$ords->delivery_boy_name,'order_status'=>$ords->order_status,'cart_id'=>$cart_id,'user_number'=>$ords->user_number,'address'=>$ords->address , 'time_slot'=>$ords->time_slot,'paid_by_wallet'=>$ords->paid_by_wallet,'remaining_price'=>$ords->rem_price,'price_without_delivery'=>$ords->price_without_delivery,'coupon_discount'=>$ords->coupon_discount,'vendor_name'=>$ords->vendor_name,'payment_method'=>$ords->payment_method,'payment_status'=>$ords->payment_status,'delivery_boy_num'=>$ords->delivery_boy_phone,'order_details'=>$details,'gst'=>$ords->gst);
                          }
                          }
                  else{
                  $data[]=array('order_details'=>'no orders found');
                          }
                          return $data;
        }



          public function store_complete_order(Request $request)
          {

            $vendor_id = $request->vendor_id;
            if($request->vendor_id=='54'){
               $todayorder  =   DB::table('orders')
						   ->join('vendor', 'orders.vendor_id','=', 'vendor.vendor_id')
                           ->join('tbl_user', 'orders.user_id', '=', 'tbl_user.user_id')
    	                    ->join('user_address','orders.address_id', '=', 'user_address.address_id')
    	                   // ->join('user_address.area_id','=', 'area.area_id')
    	                    ->leftJoin('delivery_boy','orders.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->select('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','orders.pres','tbl_user.id_proof','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','orders.surgecharge','orders.nightcharge','orders.convcharge','orders.gst')
                          ->groupBy('orders.cart_id')
                          ->groupBy('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','tbl_user.id_proof','orders.surgecharge','orders.nightcharge','orders.convcharge','orders.gst')
                            ->where('orders.payment_status','!=', 'NULL')
                           ->where('orders.order_status','!=', 'Cancelled')
                            ->where('orders.ui_type','=', '1')
                            ->where('orders.order_status', 'Completed')
    	                    ->orderBy('user_id')
                           ->get();
                           $todayorder->groupBy('orders.cart_id');
                           if(count($todayorder)>0){
                              $carts=array();
                            foreach($todayorder as $ords){
                                   $cart_id = $ords->cart_id;
                                   $details = DB::table('order_details')
                                        ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                        ->join('product','product_varient.product_id', '=', 'product.product_id')
                                        ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('vendor.vendor_id','vendor.vendor_name')
                                            ->where('order_cart_id',$cart_id)
                                            ->groupby('vendor.vendor_id','vendor.vendor_name')
                                            ->get();
                                    foreach($details as $vendor)
                                    {
                                        $vendordetails  =   DB::table('order_details')
                                             ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                             ->join('product','product_varient.product_id', '=', 'product.product_id')
                                             ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'),'order_details.add_to_basket')
                                            ->where('order_details.order_cart_id',$cart_id)
                                            ->where('vendor.vendor_id',$vendor->vendor_id)
                                            ->groupBy('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty','order_details.add_to_basket')
                                            ->get();
                                            foreach($vendordetails as $detail)
                                            {
                                            $basketproducts=[];
                                            if($detail->add_to_basket==1)
                                                {
                                                    $basketproducts=array($detail);
                                                }
                                            }
                                            $storestatus = DB::table('orders')
                                            ->select('cancel_by_store','approve_by_store')
                                            ->where('cart_id',$cart_id)
                                            ->where('vendor_id',$vendor->vendor_id)
                                            ->first();
                                             $vendor->store_status="Waiting";
                                            if($storestatus->approve_by_store>0)
                                            {
                                             $vendor->store_status="Approved";
                                            }
                                            if($storestatus->cancel_by_store>0)
                                            {
                                             $vendor->store_status="Cancelled";
                                            }
                                        $vendor->vendordetails=$vendordetails;
                                    }
                                    if(in_array($ords->cart_id,$carts))
                                    {
                                    }
                                    else{
                                            $data[]=array('pres'=>$ords->pres,'id_proof'=>$ords->id_proof,'order_id'=>$ords->order_id, 'user_id'=>$ords->user_id, 'delivery_date'=>$ords->delivery_date,'user_name'=>$ords->user_name, 'dboy_id'=>$ords->dboy_id, 'delivery_charge'=>$ords->delivery_charge, 'total_price'=>$ords->total_price,'total_product_mrp'=>$ords->total_products_mrp,'delivery_boy_name'=>$ords->delivery_boy_name,'order_status'=>$ords->order_status,'cart_id'=>$cart_id,'user_number'=>$ords->user_number,'address'=>$ords->address , 'time_slot'=>$ords->time_slot,'paid_by_wallet'=>$ords->paid_by_wallet,'remaining_price'=>$ords->rem_price,'price_without_delivery'=>$ords->price_without_delivery,'coupon_discount'=>$ords->coupon_discount,'payment_method'=>$ords->payment_method,'payment_status'=>$ords->payment_status,'delivery_boy_num'=>$ords->delivery_boy_phone,'order_details'=>$details,'basket_products'=>$basketproducts,'surgecharge'=>$ords->surgecharge,'nightcharge'=>$ords->nightcharge,'convcharge'=>$ords->convcharge,'gst'=>$ords->gst);
                                            $carts[]=$ords->cart_id;
                                    }
                                          }
                                          }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
            }
            else{
            $todayorder  =   DB::table('orders')
						   ->join('vendor', 'orders.vendor_id','=', 'vendor.vendor_id')
                           ->join('tbl_user', 'orders.user_id', '=', 'tbl_user.user_id')
    	                    ->join('user_address','orders.address_id', '=', 'user_address.address_id')
    	                   // ->join('user_address.area_id','=', 'area.area_id')
    	                    ->leftJoin('delivery_boy','orders.dboy_id', '=','delivery_boy.delivery_boy_id')
                          ->select('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','orders.pres','tbl_user.id_proof','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','orders.gst')
                          ->groupBy('orders.cart_id')
                          ->groupBy('orders.order_id','orders.user_id','orders.delivery_date','orders.payment_method','orders.payment_status','tbl_user.user_name','orders.dboy_id','orders.delivery_charge', 'orders.total_price','orders.total_products_mrp','orders.delivery_charge','delivery_boy.delivery_boy_name','delivery_boy.delivery_boy_phone','orders.dboy_id','orders.order_status','orders.cart_id','orders.delivery_date','user_address.user_number','user_address.user_name','user_address.address','orders.time_slot','orders.delivery_charge','orders.paid_by_wallet','orders.rem_price','orders.price_without_delivery','orders.coupon_discount','orders.pres','tbl_user.id_proof','orders.gst')
                            ->where('orders.payment_status','!=', 'NULL')
                           ->where('orders.order_status','!=', 'Cancelled')
                            ->where('orders.ui_type','=', '1')
                            ->where('orders.order_status', 'Completed')
                            ->where('orders.vendor_id',$vendor_id)
    	                    ->orderBy('user_id')
                           ->get();
                           $todayorder->groupBy('orders.cart_id');
                           if(count($todayorder)>0){
                              $carts=array();
                            foreach($todayorder as $ords){
                                   $cart_id = $ords->cart_id;
                                   $details = DB::table('order_details')
                                        ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                        ->join('product','product_varient.product_id', '=', 'product.product_id')
                                        ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('vendor.vendor_id','vendor.vendor_name')
                                            ->where('order_cart_id',$cart_id)
                                            ->where('vendor.vendor_id',$vendor_id)
                                            ->groupby('vendor.vendor_id','vendor.vendor_name')
                                            ->get();
                                    foreach($details as $vendor)
                                    {
                                        $vendordetails  =   DB::table('order_details')
                                             ->join('product_varient', 'order_details.varient_id', '=', 'product_varient.varient_id')
                                             ->join('product','product_varient.product_id', '=', 'product.product_id')
                                             ->join('vendor','product.vendor_id','=','vendor.vendor_id')
                                             ->select('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty', DB::raw('SUM(order_details.qty) as total_items'),'order_details.add_to_basket')
                                            ->where('order_details.order_cart_id',$cart_id)
                                            ->where('vendor.vendor_id',$vendor->vendor_id)
                                            ->groupBy('product.product_name','product_varient.price','product_varient.price','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','order_details.varient_id','order_details.store_order_id','order_details.qty','order_details.add_to_basket')
                                            ->get();
                                            foreach($vendordetails as $detail)
                                            {
                                            $basketproducts=[];
                                            if($detail->add_to_basket==1)
                                                {
                                                    $basketproducts=array($detail);
                                                }
                                            }
                                        $vendor->vendordetails=$vendordetails;
                                    }
                                    if(in_array($ords->cart_id,$carts))
                                    {
                                    }
                                    else{
                                            $data[]=array('pres'=>$ords->pres,'id_proof'=>$ords->id_proof,'order_id'=>$ords->order_id, 'user_id'=>$ords->user_id, 'delivery_date'=>$ords->delivery_date,'user_name'=>$ords->user_name, 'dboy_id'=>$ords->dboy_id, 'delivery_charge'=>$ords->delivery_charge, 'total_price'=>$ords->total_price,'total_product_mrp'=>$ords->total_products_mrp,'delivery_boy_name'=>$ords->delivery_boy_name,'order_status'=>$ords->order_status,'cart_id'=>$cart_id,'user_number'=>$ords->user_number,'address'=>$ords->address , 'time_slot'=>$ords->time_slot,'paid_by_wallet'=>$ords->paid_by_wallet,'remaining_price'=>$ords->rem_price,'price_without_delivery'=>$ords->price_without_delivery,'coupon_discount'=>$ords->coupon_discount,'payment_method'=>$ords->payment_method,'payment_status'=>$ords->payment_status,'delivery_boy_num'=>$ords->delivery_boy_phone,'order_details'=>$details,'basket_products'=>$basketproducts,'gst'=>$ords->gst);
                                            $carts[]=$ords->cart_id;
                                    }
                                          }
                                          }
                                  else{
                                  $data[]=array('order_details'=>'no orders found');
                                          }
            }
             return $data;
         }

          public function store_cancel_order(Request $request)
          {
             $order_id = $request->order_id;
             $vendor_id = $request->vendor_id;
             $vendor = DB::table('vendor')
             ->where('vendor_id',$vendor_id)
             ->first();
             $orders = DB::table('orders')
                       ->where('order_id',$order_id)
                        ->first();
             $user = $orders->user_id;
             $vendorname=$vendor->vendor_name;
             $update = DB::table('orders')
                       ->where('order_id',$order_id)
                       ->update(['cancel_by_store'=>1,
                       'order_status'=>"Cancelled"
                       ]);
                                   $notification_title = "Order Cancelled";

                       $getDevice = DB::table('tbl_user')
                         ->where('user_id', $user)
                        ->select('device_id')
                        ->first();
                          $created_at = Carbon::now();

                          $notification_text = "Hi, we apologise your order from .$vendorname. has been cancelled, try again later ";

                       if($update)

                       {
                                $getFcm = DB::table('fcm_key')
                                 ->first();

                            $getFcmKey = $getFcm->user_app_key;
                            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                            $token = $getDevice->device_id;


                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
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




                $results = json_decode($result);


                $mess = array('status'=>'1', 'message'=>'Order Cancelled', 'data'=>$update);
                        return $mess;
                }
                 else{


                        $message = array('status'=>'0', 'message'=>'unsucessful', 'data'=>[]);
                        return $message;
                    }
          }
           public function store_approve_order(Request $request)
          {
             $order_id = $request->order_id;
             $vendor_id = $request->vendor_id;
             $vendor = DB::table('vendor')
             ->where('vendor_id',$vendor_id)
             ->first();
             $orders = DB::table('orders')
                       ->where('order_id',$order_id)
                        ->first();
             $user = $orders->user_id;
             $vendorname=$vendor->vendor_name;
             $update = DB::table('orders')
                       ->where('order_id',$order_id)
                       ->update(['approve_by_store'=>1,
                       ]);
                                   $notification_title = "Order Approved";

                       $getDevice = DB::table('tbl_user')
                         ->where('user_id', $user)
                        ->select('device_id')
                        ->first();
                          $created_at = Carbon::now();

                          $notification_text = "Hi, your order from .$vendorname. has been approved";

                       if($update)

                       {
                                $getFcm = DB::table('fcm_key')
                                 ->first();

                            $getFcmKey = $getFcm->user_app_key;
                            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                            $token = $getDevice->device_id;


                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
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




                $results = json_decode($result);


                $mess = array('status'=>'1', 'message'=>'Order Approved', 'data'=>$update);
                        return $mess;
                }
                 else{


                        $message = array('status'=>'0', 'message'=>'unsucessful', 'data'=>[]);
                        return $message;
                    }
          }


          public function assigned_store_order(Request $request)
         {

             $delivery_id = $request->delivery_boy_id;
             $order_id = $request->order_id;
             $cart_id = DB::table('orders')
                        ->select('cart_id')
                        ->where('order_id',$order_id)
                        ->first();
             $update = DB::table('orders')
                       ->where('cart_id',$cart_id->cart_id)
                       ->update(['dboy_id'=>$delivery_id,
                       'order_status'=>"Confirmed"
                       ]);
                       $notification_title = "New Order Assign";

                       $getDevice = DB::table('delivery_boy')
                         ->where('delivery_boy_id', $delivery_id)
                        ->select('device_id','delivery_boy_name')
                        ->first();
                        $delivery_boy_name = $getDevice->delivery_boy_name;
                          $created_at = Carbon::now();

                          $notification_text = "Hi, .$delivery_boy_name. you received new order please check the details ";

                       if($update)

                       {
                                $getFcm = DB::table('fcm_key')
                                 ->first();

                            $getFcmKey = $getFcm->dboy_app_key;
                            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                            $token = $getDevice->device_id;


                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
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




                $results = json_decode($result);


                $mess = array('status'=>'1', 'message'=>'Delivery boy assigned successfully', 'data'=>$update);
                        return $mess;
                }
                 else{


                        $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
                        return $message;
                    }


         }

         public function store_delivery_boy(Request $request)
         {

             $vendor_id = $request->vendor_id;
             $vendordelivery_boy =  DB::table('delivery_boy')
             ->select('delivery_boy.delivery_boy_id' , 'delivery_boy.delivery_boy_name' ,'delivery_boy.vendor_id' ,'delivery_boy.delivery_boy_image' ,'delivery_boy.delivery_boy_phone' , 'delivery_boy.delivery_boy_pass', 'delivery_boy.lat' ,'delivery_boy.lng' ,'delivery_boy.device_id','delivery_boy.delivery_boy_status','delivery_boy.is_confirmed','delivery_boy.otp','delivery_boy.phone_verify','delivery_boy.cityadmin_id','delivery_boy.dboy_comission','delivery_boy.created_at','delivery_boy.updated_at')
             ->GroupBy('delivery_boy.delivery_boy_id' , 'delivery_boy.delivery_boy_name' ,'delivery_boy.vendor_id' ,'delivery_boy.delivery_boy_image' ,'delivery_boy.delivery_boy_phone' , 'delivery_boy.delivery_boy_pass', 'delivery_boy.lat' ,'delivery_boy.lng' ,'delivery_boy.device_id','delivery_boy.delivery_boy_status','delivery_boy.is_confirmed','delivery_boy.otp','delivery_boy.phone_verify','delivery_boy.cityadmin_id','delivery_boy.dboy_comission','delivery_boy.created_at','delivery_boy.updated_at' )
             ->where('delivery_boy.delivery_boy_status', 'online')
               ->where('delivery_boy.vendor_id', $vendor_id)
   ->get();
$cityadmindelivery_boy =  DB::table('delivery_boy')
               ->join('delivery_boy_vendor', 'delivery_boy.delivery_boy_id', '=', 'delivery_boy_vendor.delivery_boy_id')
               ->select('delivery_boy.delivery_boy_id' , 'delivery_boy.delivery_boy_name' ,'delivery_boy_vendor.vendor_id' ,'delivery_boy.delivery_boy_image' ,'delivery_boy.delivery_boy_phone' , 'delivery_boy.delivery_boy_pass', 'delivery_boy.lat' ,'delivery_boy.lng' ,'delivery_boy.device_id','delivery_boy.delivery_boy_status','delivery_boy.is_confirmed','delivery_boy.otp','delivery_boy.phone_verify','delivery_boy.cityadmin_id','delivery_boy.dboy_comission','delivery_boy.created_at','delivery_boy.updated_at')
               ->GroupBy('delivery_boy.delivery_boy_id' , 'delivery_boy.delivery_boy_name' ,'delivery_boy_vendor.vendor_id' ,'delivery_boy.delivery_boy_image' ,'delivery_boy.delivery_boy_phone' , 'delivery_boy.delivery_boy_pass', 'delivery_boy.lat' ,'delivery_boy.lng' ,'delivery_boy.device_id','delivery_boy.delivery_boy_status','delivery_boy.is_confirmed','delivery_boy.otp','delivery_boy.phone_verify','delivery_boy.cityadmin_id','delivery_boy.dboy_comission','delivery_boy.created_at','delivery_boy.updated_at' )
     ->where('delivery_boy.delivery_boy_status', 'online')
     ->where('delivery_boy_vendor.vendor_id', $vendor_id)
     ->get();
     $arr1 = json_decode($vendordelivery_boy);
     $arr2 = json_decode($cityadmindelivery_boy);
     $data = array_merge($arr1, $arr2);

                       if($data)	{
                        $mess = array('status'=>'1', 'message'=>'data found', 'data'=>$data);
                        return $mess;
                     }
                    else
                     {
                        $message = array('status'=>'0', 'message'=>'not available delivery boy', 'data'=>[]);
                        return $message;
                     }


         }


}
