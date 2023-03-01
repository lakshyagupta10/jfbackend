@extends('pharmacy.layout.app')

@section ('content')
<div class="content-wrapper">
          <div class="row">
		  <div class="col-md-2">
		  </div>
            
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Update Area</h4>
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
                  <form class="forms-sample" action="{{route('pharmacyUpdatearea',$area->vendor_area_id)}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
                    <div class="form-group">
                    <label for="exampleFormControlSelect3">Area</label>
                    <select class="form-control form-control-sm" id="exampleFormControlSelect3 " name="area_id">
                   
                      @foreach($area1 as $ar)
		          	<option value="{{$ar->area_id}}" @if($ar->area_id == $area->area_id) selected @endif>{{$ar->area_name}}</option>
		              @endforeach
                      
                      
                    </select>
                    </div>
                    

                    
                    <div class="form-group">
                      <label for="deliverycharge">Delivery Charge</label>
                      <input type="text" class="form-control" id="deliverycharge" name="delivery_charge" placeholder="Enter Delivery Charge"  value="{{$area->delivery_charge}}">
                    </div>
                   
                      
                    <button type="submit" class="btn btn-success mr-2">Submit</button>
              
                     <a href="{{route('pharmacyarea')}}" class="btn btn-light">Cancel</a>
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