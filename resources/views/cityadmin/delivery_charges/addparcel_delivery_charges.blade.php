@extends('cityadmin.layout.app')

@section ('content')
<div class="content-wrapper">
          <div class="row">
		  <div class="col-md-2">
		  </div>
            
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Add Parcel Delivery Charges</h4>
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
                  <form class="forms-sample" action="{{route('AddNewparcel_delivery_charges')}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
                    <div class="form-group">
                      <label for="exampleInputName1">Range Start(km)</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="range_start" placeholder="Range Start" required>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Range End(km)</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="range_end" placeholder="Range End" required>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Charges</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="charges" placeholder="charges" required>
                    </div>
                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                    <!--
                    <button class="btn btn-light">Cancel</button>
                    -->
                     <a href="{{route('delivery_charges')}}" class="btn btn-light">Cancel</a>
                  </form>
                </div>
              </div>
            </div>
             <div class="col-md-2">
		  </div>
     
          </div>
        </div>
       </div> 
@endsection