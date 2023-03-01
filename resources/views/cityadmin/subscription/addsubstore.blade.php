@extends('cityadmin.layout.app')

@section ('content')
<div class="row">
  
<div class="col-md-3"></div>
		 <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title"> Add Subscription Store</h4>
                  <!-- <p class="card-description">
                    Basic form elements
                  </p> -->
                  <form class="forms-sample" action="{{route('addsubstore')}}" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                     @if (count($errors) > 0)
                    @if($errors->any())
                   <div class="alert alert-primary" role="alert">
                  <strong>SUCCESS : </strong>{{$errors->first()}}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
                  </button>
                  </div>
                  @endif
                 @endif
                    <div class="form-group">
                    <label for="exampleFormControlSelect3">Select Vendor</label>
                    
                    <select class="form-control form-control-sm" id="exampleFormControlSelect3 " name="vendor_id">
                        <option value="0">No vendor Selected</option>
                      @foreach($vendors as $vendor)
		          	<option value="{{$vendor->vendor_id}}">{{$vendor->vendor_name}}</option>
		              @endforeach
		              
                    </select>
                    </div>
                    <button type="submit" class="btn btn-success mr-2">Add</button>
                   
                  </form>
                </div>
              </div>
            </div>
  <div class="col-md-3"></div>

</div>
<!-- /.container-fluid -->
@endsection