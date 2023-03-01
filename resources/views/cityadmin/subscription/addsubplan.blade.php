@extends('cityadmin.layout.app')

@section ('content')



        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
<div id="general" class="mb-5">
									<h4><i class="far fa-user fa-fw"></i>Add Sub plan</h4>
									<div class="card">
										<div class="list-group list-group-flush">
											 <form class="forms-sample" action="{{route('addsubplan')}}" method="post" enctype="multipart/form-data">
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
                                            <label for="exampleInputName1">Start Time</label>
                                              <input type="text" class="form-control" name="start_time" id="exampleInputName1" placeholder="HH:MM">
                                            <label for="exampleInputName1">End Time</label>
                                              <input type="text" class="form-control" name="end_time"  id="exampleInputName1" placeholder="HH:MM">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Subscription Description</label>
                                              <input type="text" class="form-control" name="description" id="exampleInputName1" placeholder="description">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Subscription Name</label>
                                              <input type="text" class="form-control" name="plans" id="exampleInputName1" placeholder="Subscription Name">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Validity Days</label>
                                              <input type="text" class="form-control" name="days" id="exampleInputName1" placeholder="Days">
                                            </div>
                                            <div class="form-group list-group-item d-flex align-items-center">
                                              <label for="exampleInputName1">Charges</label>
                                              <input type="text" class="form-control" name="amount" id="exampleInputName1" placeholder="Charges">
                                            </div>
                                           <div class="form-group list-group-item d-flex align-items-center">
                                              <label>Subscription Banner</label>
                                              <input type="hidden" name="banner">
                                              <img style="width:50px; height:50px; border-radius:50%"/>&nbsp; &nbsp;
                                              <div class="input-group col-xs-12">
                                              <div class="custom-file">
                                                  <input type="file" name="banner" class="custom-file-input" id="customFile" />
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
