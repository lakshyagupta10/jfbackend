@extends('cityadmin.layout.app')

@section ('content')


<!-- Begin Page Content -->
<div class="container-fluid">
 

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Subscription Stores</h6>
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
        {{csrf_field()}}
        <a class="btn btn-success m-auto" style="float: right;" href="{{route('add-substore')}}">Add</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
            <th>S.No</th>
            <th>Vendor Name</th>
            <th>Owner Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>logo</th>
            <th>Action</th>
            </tr>
          </thead>
         
          <tbody>
          @if(count($substores)>0)
                          @php $i=1; @endphp
                          @foreach($substores as $vendors)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$vendors->vendor_name}}</td>
                            <td>{{$vendors->owner}}</td>
                            <td>{{$vendors->vendor_phone}}</td>
                            <td>{{$vendors->vendor_email}}</td>
                            <td align="center"><img src="{{url($vendors->vendor_logo)}}" style="width: 21px;"></td>
                            <td>
                                <a href="{{route('vendorsecretlogin',$vendors->vendor_id)}}" style="width: 28px; padding-left: 6px;background-color:#000;border-color:#000;" class="btn btn-info"  style="width: 10px;padding-left: 9px;" style="color: #fff;">
                                    <i class="fa fa-user-secret" style="width: 10px;"></i>
                                </a>
							    <button type="button" style="width: 28px; padding-left: 6px;" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal{{$vendors->vendor_id}}">
							        <i class="fa fa-trash"></i>
							    </button>
							</td>

                        </tr>
                        @php $i++; @endphp
                        @endforeach
                      @else
                        <tr>
                          <td>No data found</td>
                        </tr>
                      @endif
                       
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->
</div>
</div>
@foreach($substores as $vendors)
<!-- Modal -->
<div class="modal fade" id="exampleModal{{$vendors->vendor_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Delete vendor</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
			</div>
			<div class="modal-body">
				Remove store from subscription.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<a href="{{route('delete-substore', $vendors->vendor_id)}}" class="btn btn-primary">Delete</a>
			</div>
		</div>
	</div>
</div>
@endforeach   
@endsection