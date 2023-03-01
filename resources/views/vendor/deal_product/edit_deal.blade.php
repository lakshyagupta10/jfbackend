@extends('vendor.layout.app')
<style>
sup {
    color:red;
    position: initial;
    font-size: 111%;
}
</style>
@section ('content')
<div class="content-wrapper">
          <div class="row">
		  <div class="col-md-2">
		  </div>
            
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Edit Deal products</h4>
                   @if (count($errors) > 0)
                      @if($errors->any())
                        <div class="alert alert-primary" role="alert">
                          {{$errors->first()}}
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                          </button>
                        </div>
                      @endif
                  @endif
                  <form class="forms-sample" action="{{route('UpdateDealproduct', $deal_p->deal_id)}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}

                    <div class="form-group">
                          <label class="bmd-label-floating">Select Product</label>
                          <select name="varient_id" class="form-control">
                              <option disabled selected>Select Product</option>
                              @foreach($deal as $deals)
        		          	<option value="{{$deals->varient_id}}" @if ( $deals->varient_id == $deal_p->varient_id) selected @endif>{{$deals->product_name}} ({{$deals->quantity}}{{$deals->unit}})</option>
        		              @endforeach
                              
                          </select>
                        </div>

                   
                 <div class="form-group">
                      <label for="exampleInputName1">Deal Price</label>
                      <input type="text" class="form-control" id="exampleInputName1" value="{{$deal_p->deal_price}}" name="deal_price" placeholder="Coupon Description ">
                    </div>  
                   
                   <div class="form-group">
                      <label for="exampleInputName1">Start Date</label>
                      <input type="date" class="form-control" id="exampleInputName1" value="{{$deal_p->valid_from}}"  name="valid_from" placeholder="">
                    </div> 
            
                    <div class="form-group">
                      <label for="exampleInputName1">End Date</label>
                      <input type="date" class="form-control" id="exampleInputName1" value="{{$deal_p->valid_to}}" name="valid_to" placeholder="">
                    </div>
                    
                 
                    
                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                    <!--
                    <button class="btn btn-light">Cancel</button>
                    -->
                    <a href="{{route('dealroduct')}}" class="btn btn-light">Cancel</a>
                  </form>
                </div>
              </div>
            </div>
             <div class="col-md-2">
		  </div>
     
          </div>
        </div>
       </div> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
        	$(document).ready(function(){
        	
                $(".des_price").hide();
                
        		$(".img").on('change', function(){
        	        $(".des_price").show();
        			
        	});
        	});
</script>

@endsection