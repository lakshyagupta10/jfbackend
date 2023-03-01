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
                                              <label for="exampleInputName1">Surge Charge(%)</label>
                                              <input type="text" class="form-control" name="surge_percent" value="{{$cityadmin->surge_percent}}" id="exampleInputName1" placeholder="Surge Charge">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Top Message</label>
                                              <input type="text" class="form-control" name="top_message" value="{{$cityadmin->top_message}}" id="exampleInputName1" placeholder="Top Message">
                                            </div>
                                           <div class="form-group list-group-item d-flex align-items-center">
                                              <label>Top Message Banner</label>
                                              <input type="hidden" name="top_banner" value="{{$top_message_banner->banner_image}}">
                                              <img src="{{url($top_message_banner->banner_image)}}" style="width:50px; height:50px; border-radius:50%"/>&nbsp; &nbsp;
                                              <div class="input-group col-xs-12">
                                              <div class="custom-file">
                                                  <input type="file" name="top_banner" class="custom-file-input" id="customFile" />
                                                  <label class="custom-file-label" for="customFile">Choose file</label>
                                                </div>      
                                              
                                              </div>
                                            </div>
                                          <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Bottom Message</label>
                                              <input type="text" class="form-control" name="bottom_message" value="{{$cityadmin->bottom_message}}" id="exampleInputName1" placeholder="Bottom Message">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label>Bottom Banner</label>
                                              <input type="hidden" name="bottom_banner" value="{{$bigbanner->banner_image}}">
                                              <img src="{{url($bigbanner->banner_image)}}" style="width:50px; height:50px; border-radius:50%"/>&nbsp; &nbsp;
                                              <div class="input-group col-xs-12">
                                              <div class="custom-file">
                                                  <input type="file" name="bottom_banner" class="custom-file-input" id="customFile" />
                                                  <label class="custom-file-label" for="customFile">Choose file</label>
                                                </div>      
                                              
                                              </div>
                                            </div>
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