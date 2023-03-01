@extends('admin.layout.app')

@section ('content')



        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
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
            	<form action="{{route('adminNotificationSend')}}" method="post" enctype="multipart/form-data">   
    			{{csrf_field()}}
    			
                    <div class="form-group">
                      <label for="exampleInputName1">Notification Title<sup>*</sup></label>
                      <input type="text" class="form-control" id="exampleInputName1" name="notification_title">
                    </div>
                    
                    <div class="form-group">
                      <label for="exampleInputName1">Message<sup>*</sup></label>
                      <textarea class="form-control" id="exampleInputName1" placeholder="Notification Text" name="notification_text"></textarea>
                    </div>
    			<div class="modal-footer">
    			    <button type="submit" class="btn btn-success mr-2">Send Notification</button>
    			</div>
    			</form>
                                			
                                			
@endsection                                			
                                			