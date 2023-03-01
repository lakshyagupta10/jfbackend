@extends('cityadmin.layout.app')

@section ('content')
<div class="content-wrapper">
          <div class="row">
		  <div class="col-md-2">
		  </div>
            
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Update Delivery Charges</h4>
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
                  <form class="forms-sample" action="{{route('update-delivery_charges',$delivery_charge->id)}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
                      <label for="exampleInputName1">Range start</label>
                      <input type="text" class="form-control" id="exampleInputName1" value="{{$delivery_charge->range_start}}" name="range_start" placeholder="Range Start" requirment>
                      <label for="exampleInputName1">Delivery end</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="range_end" value="{{$delivery_charge->range_end}}" requirment>
                      <label for="exampleInputName1">Charges</label>
                      <input type="text" class="form-control" id="exampleInputName1" name="charges" value="{{$delivery_charge->charges}}"requirment>
                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                    
                    <button class="btn btn-light">Cancel</button>
                    
                     <!--<a href="{{route('delivery_boy')}}" class="btn btn-light">Cancel</a>-->
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