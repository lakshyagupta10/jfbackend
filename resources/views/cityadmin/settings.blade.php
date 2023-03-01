@extends('cityadmin.layout.app')

@section ('content')



        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
<div id="general" class="mb-5">
									<h4><i class="far fa-user fa-fw"></i> Settings</h4>
									<div class="card">
										<div class="list-group list-group-flush">
											 <form class="forms-sample" action="{{route('cityadminsettings',[$cityadmin->cityadmin_id])}}" method="post" enctype="multipart/form-data">
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
                                        <div class="form-group list-group-item d-flex align-items-center">
                                            <label for="exampleInputName1">App On/OFF</label>
                                                @if($cityadmin->status == 1)
                                                    <input type="checkbox" class="form-control" name="status" checked id="exampleInputName1">
                                                @else
                                                    <input type="checkbox" class="form-control" name="status" id="exampleInputName1">
                                                @endif
                                            <label for="exampleInputName1">Opening Time</label>
                                                    <input type="text" class="form-control" name="opening_time" value="{{$cityadmin->opening_time}}" id="exampleInputName1" placeholder="HH:MM">
                                            <label for="exampleInputName1">Closing Time</label>
                                                    <input type="text" class="form-control" name="closing_time" value="{{$cityadmin->closing_time}}" id="exampleInputName1" placeholder="HH:MM">
                                        </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Surge</label>
                                              @if($cityadmin->surge == 1)
                                             <input type="checkbox" class="form-control" name="surge" checked id="exampleInputName1">
                                              @else
                                            <input type="checkbox" class="form-control" name="surge" id="exampleInputName1">
                                            @endif
                                              <label for="exampleInputName1">Surge Charge</label>
                                              <input type="text" class="form-control" name="surge_percent" value="{{$cityadmin->surge_percent}}" id="exampleInputName1" placeholder="Surge Charge">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                            <label for="exampleInputName1">Surge Charge Message</label>
                                            <input type="text" class="form-control" name="surgechargemessage" value="{{$city->surgechargemessage}}" placeholder="Surge Charge Message">
                                          </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Night Charges</label>
                                              @if($cityadmin->night == 1)
                                             <input type="checkbox" class="form-control" name="night" checked id="exampleInputName1">
                                              @else
                                            <input type="checkbox" class="form-control" name="night" id="exampleInputName1">
                                            @endif
                                              <label for="exampleInputName1">Night Charge</label>
                                              <input type="text" class="form-control" name="night_charge" value="{{$cityadmin->night_charge}}" id="exampleInputName1" placeholder="Night Charge">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Convenience Charge</label>
                                              @if($cityadmin->conv == 1)
                                             <input type="checkbox" class="form-control" name="conv" checked id="exampleInputName1">
                                              @else
                                            <input type="checkbox" class="form-control" name="conv" id="exampleInputName1">
                                            @endif
                                              <label for="exampleInputName1">Convenience Charge</label>
                                              <input type="text" class="form-control" name="conv_charge" value="{{$cityadmin->conv_charge}}" id="exampleInputName1" placeholder="Convenience Charge">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Charges per vendors for multiple orders</label>
                                              <input type="text" class="form-control" name="extrapervendor" placeholder="Extra Charges per vendor" value="{{$city->extrapervendor}}" required>
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Maximum amount allowed in cash</label>
                                              <input type="text" class="form-control" name="maxincash" value="{{$cityadmin->maxincash}}" id="exampleInputName1" placeholder="Max amount allowed in cash">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Top Message</label>
                                              <input type="text" class="form-control" name="top_message" value="{{$cityadmin->top_message}}" id="exampleInputName1" placeholder="Top Message">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Timeslot message</label>
                                              <textarea class="form-control" name="timeslotmessage" required>{{$city->timeslotmessage}}</textarea>
                                            </div>
                                           <!--<div class="form-group list-group-item d-flex align-items-center">-->
                                           <!--   <label>Top Message Banner</label>-->
                                           <!--   <input type="hidden" name="top_banner" value="{{$top_message_banner->banner_image}}">-->
                                           <!--   <img src="{{url($top_message_banner->banner_image)}}" style="width:50px; height:50px; border-radius:50%"/>&nbsp; &nbsp;-->
                                           <!--   <div class="input-group col-xs-12">-->
                                           <!--   <div class="custom-file">-->
                                           <!--       <input type="file" name="top_banner" class="custom-file-input" id="customFile" />-->
                                           <!--       <label class="custom-file-label" for="customFile">Choose file</label>-->
                                           <!--     </div>      -->

                                           <!--   </div>-->
                                           <!-- </div>-->
                                          <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Bottom Message</label>
                                              <input type="text" class="form-control" name="bottom_message" value="{{$cityadmin->bottom_message}}" id="exampleInputName1" placeholder="Bottom Message">
                                            </div>
                                            <!--<div class="form-group list-group-item d-flex align-items-center">-->
                                            <!--  <label>Bottom Banner</label>-->
                                            <!--  <input type="hidden" name="bottom_banner" value="{{$bigbanner->banner_image}}">-->
                                            <!--  <img src="{{url($bigbanner->banner_image)}}" style="width:50px; height:50px; border-radius:50%"/>&nbsp; &nbsp;-->
                                            <!--  <div class="input-group col-xs-12">-->
                                            <!--  <div class="custom-file">-->
                                            <!--      <input type="file" name="bottom_banner" class="custom-file-input" id="customFile" />-->
                                            <!--      <label class="custom-file-label" for="customFile">Choose file</label>-->
                                            <!--    </div>      -->

                                            <div class="form-group list-group-item d-flex align-items-center">
                                            <button type="submit" class="btn btn-success width-100">Submit</button>
                                           <!--  <button class="btn btn-light">Cancel</button> -->
                                           </div>
                                          </form>
                                          </div>
                                        </div>
										</div>

									</div>

@endsection
