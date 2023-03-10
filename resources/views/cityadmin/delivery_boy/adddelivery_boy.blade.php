@extends('cityadmin.layout.app')

@section ('content')
<div class="content-wrapper">
          <div class="row">
		  <div class="col-md-2">
		  </div>
            
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Add Delivery Boy</h4>
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
                  <form class="forms-sample" action="{{route('AddNewdelivery_boy')}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
                     <div class="form-group">
                    <label for="exampleFormControlSelect3">choose area</label><br>
                  
                     <select class="mdb-select colorful-select dropdown-primary md-form" multiple searchable="Search here.." name="area_id[]" required>
                      <option value="" disabled style="background: #f1f1f1;">Choose your area</option>
                       @foreach($area as $area)
		          	<option value="{{$area->area_id}}">{{$area->area_name}}</option>
		              @endforeach
                    </select>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Delivery Boy Name</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="delivery_boy_name" placeholder="Enter delivery_boy name" required>
                    </div>
                     <div class="form-group">
                      <label>Delivery Boy Image</label>  
                      
                      <div class="input-group col-xs-12">
                      <input type="file" name="delivery_boy_image"  class="file-upload-default" required>                        
                        </div>
                      </div>
                      
                     <div class="form-group">
                      <label for="exampleInputName1">Delivery Boy Phone</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="delivery_boy_phone" placeholder="Phone Number" required>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Delivery Boy Comission</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="delivery_boy_comission" placeholder="Comission in Percentage like 20%" required>
                    </div>
                     <div class="form-group">
                      <label for="exampleInputName1">Password</label>
                      <input type="password" class="form-control" id="exampleInputName1" name="password1" placeholder="Enter password" required>
                    </div>
                    
                     <div class="form-group">
                      <label for="exampleInputName1">Confirm Password</label>
                      <input type="password" class="form-control" id="exampleInputName1" name="password2" placeholder="confirm password" required>
                    </div>
                    <div class="form-group">
                    <label for="exampleFormControlSelect3">choose vendor</label><br>
                  
                     <select class="mdb-select colorful-select dropdown-primary md-form" multiple searchable="Search here.." name="vendor_id[]" required>
                      <option value="" disabled style="background: #f1f1f1;">Choose your vendor</option>
                      @foreach($vendors as $vendor)
		          	<option value="{{$vendor->vendor_id}}">{{$vendor->vendor_name}}</option>
		              @endforeach
                    </select>
                    </div>
                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                    <!--
                    <button class="btn btn-light">Cancel</button>
                    -->
                     <a href="{{route('delivery_boy')}}" class="btn btn-light">Cancel</a>
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
<script>
    // Material Select Initialization
$(document).ready(function() {
$('.mdb-select').materialSelect();
});
</script>
@endsection