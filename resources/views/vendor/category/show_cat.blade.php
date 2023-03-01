@extends('vendor.layout.app')

@section ('content')


<!-- Begin Page Content -->
<div class="container-fluid">
 

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">All Categories</h6>
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
        <a class="btn btn-success m-auto" style="float: right;" href="{{route('vendorAddCategory')}}">Add</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="example6" width="100%" cellspacing="0">
          <thead>
            <tr>
            <th>S.No</th>
            <th>Category Name</th>
            <th>Category Image</th>
            <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
            <th>S.No</th>
            <th>Category Name</th>
            <th>Category Image</th>
            <th>Action</th>
            </tr>
          </tfoot>
          <tbody>
          @if(count($vendorCategory)>0)
          @php $i=1; @endphp
          @foreach($vendorCategory as $adminCategories)
        <tr>
            <td>{{$i}}</td>
            <td>{{$adminCategories->category_name}}</td>
            
            <td align="center"><img src="{{url($adminCategories->category_image)}}" style="width: 43px;"></td>
              <td>
              <a href="{{route('vendorEditCategory', [$adminCategories->category_id])}}" class="btn btn-primary">Edit</a>
              <a href="{{route('vendorDeleteCategory', [$adminCategories->category_id])}}" class="btn btn-danger">Delete</a>
              <form class="form-inline" style="display: inline-block;" action="{{route('moveupcat',$adminCategories->category_id)}}" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <button type="submit" style="width: 28px; padding-left: 6px;" class="btn btn-info"   style="color: #fff;"><i class="fa fa-chevron-up" style="width: 10px;"></i></button>    

                </form>
                <form class="form-inline" style="display: inline-block;" action="{{route('movedowncat',$adminCategories->category_id)}}" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <button type="submit" style="width: 28px; padding-left: 6px;" class="btn btn-info"   style="color: #fff;"><i class="fa fa-chevron-down" style="width: 10px;"></i></button>
    			</form>
				@if($adminCategories->hide)
                <a href="{{route('hidecat',$adminCategories->category_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-dark"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-eye-slash" style="width: 10px;"></i></a>
                @else
                <a href="{{route('hidecat',$adminCategories->category_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-light"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-eye" style="width: 10px;"></i></a>
                @endif
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
        {!! $vendorCategory->links("pagination::bootstrap-4") !!}
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->
</div>


@endsection