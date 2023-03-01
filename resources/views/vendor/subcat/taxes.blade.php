@extends('vendor.layout.app')

@section ('content')


<!-- Begin Page Content -->
<div class="container-fluid">


  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Sub | Categories</h6>
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
                           <form action="{{ route('searchsubcat') }}" method="post">
        {{csrf_field()}}
<input type="text" value=""  name="subcatname" class="form-control" placeholder="Enter SubCategory Name" style="width: 20%; display: inline;">
    <button type="submit" class="btn btn-success btn-md" value="Search" style="margin-top: -5px;"><i class="fa fa-search"></i></button>
</form>
        <a class="btn btn-success m-auto" style="float: right;" href="{{route('vendorAddsubcat')}}">Add</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="example7" width="100%" cellspacing="0">
          <thead>
            <tr>
            <th>Subcat id</th>
            <th>subcat Name</th>
            <th>Tax Slab(%)</th>

            <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
            <th>Subcat id</th>
            <th>subcat Name</th>
            <th>Tax Slab(%)</th>

            <th>Action</th>
            </tr>
          </tfoot>
          <tbody>
          @if(count($subcat)>0)
                          @php $i=1; @endphp
                          @foreach($subcat as $subcats)
                        <tr>
                            <td>{{$subcats->subcat_id}}</td>
                            <td>{{$subcats->subcat_name}}</td>
                            <td>{{$subcats->tax_slab}}</td>

                            <td>
                               <a href="{{route('vendorEditsubcat',$subcats->subcat_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-edit" style="width: 10px;"></i></a>

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
              {{ $subcat->links() }}
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->
</div>
</div>
@foreach($subcat as $subcats)
<!-- Modal -->
<div class="modal fade" id="exampleModal{{$subcats->subcat_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Delete subcat</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete subcat.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<a href="{{route('vendordeletesubcat', $subcats->subcat_id)}}" class="btn btn-primary">Delete</a>
			</div>
		</div>
	</div>
</div>
@endforeach
@endsection
