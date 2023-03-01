@extends('admin.layout.app')

@section ('content')


<!-- Begin Page Content -->
<div class="container-fluid">


  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Vendor category</h6>
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
        <a class="btn btn-success m-auto" style="float: right;" href="{{route('addvendor')}}">Add</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <input type="text" id="type" value="vendorcat" style="display:none"></input>    
          <thead>
            <tr>
            <th>Vendor Category</th>
            <th>Category Image</th>
            <th>Action</th>
            </tr>
          </thead>
    
          <tbody id="tablecontents">
          @if(count($city)>0)
                          @php $i=1; @endphp
                          @foreach($city as $cities)
                        <tr class="row1" data-id="{{ $cities->vendor_category_id }}">
                            <td>{{$cities->category_name}}</td>
            
                            
                            <td align="center"><img src="{{url($cities->category_image)}}" style="width: 27px;"></td>
                            <td>
                               
                            <a href="{{route('editvendor',$cities->vendor_category_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-edit" style="width: 10px;"></i></a>
                           <!--<form class="form-inline" style="display: inline-block;" action="{{route('moveupvendor',$cities->vendor_category_id)}}" method="post" enctype="multipart/form-data">-->
                           <!-- {{csrf_field()}}-->
                           <!-- <button type="submit" style="width: 28px; padding-left: 6px;" class="btn btn-info"   style="color: #fff;"><i class="fa fa-chevron-up" style="width: 10px;"></i></button>    -->
                           <!-- </form>-->
       <!--                     <form class="form-inline" style="display: inline-block;" action="{{route('movedownvendor',$cities->vendor_category_id)}}" method="post" enctype="multipart/form-data">-->
       <!--                     {{csrf_field()}}-->
       <!--                     <button type="submit" style="width: 28px; padding-left: 6px;" class="btn btn-info"   style="color: #fff;"><i class="fa fa-chevron-down" style="width: 10px;"></i></button>    -->
							<!--</form>-->
							<button type="button" style="width: 28px; padding-left: 6px;" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal{{$cities->vendor_category_id}}"><i class="fa fa-trash"></i></button>
							@if($cities->hide)
                            <a href="{{route('hidevendor',$cities->vendor_category_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-dark"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-eye-slash" style="width: 10px;"></i></a>
                            @else
                            <a href="{{route('hidevendor',$cities->vendor_category_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-light"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-eye" style="width: 10px;"></i></a>
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
      </div>
    </div>
  </div>
<div class="card shadow mb-4">
    <!--<div class="card-header py-3">-->
    <!--  <h6 class="m-0 font-weight-bold text-primary">Banner List</h6>-->
    <!--  @if (count($errors) > 0)-->
    <!--              @if($errors->any())-->
    <!--                <div class="alert alert-primary" role="alert">-->
    <!--                  {{$errors->first()}}-->
    <!--                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">-->
    <!--                    <span aria-hidden="true">×</span>-->
    <!--                  </button>-->
    <!--                </div>-->
    <!--              @endif-->
    <!--          @endif-->
    <!--    <a class="btn btn-success m-auto" style="float: right;" href="{{route('addbanner')}}">Add</a>-->
    <!--</div>-->
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
            <th>Banner Name</th>
            <th>Banner Image</th>
            <th>Action</th>
            </tr>
          </thead>
    
          <tbody>
          @if($parbanner)
                        <tr>
                            <td>{{$parbanner->banner_name}}</td>
                            
                            <td align="center"><img src="{{url($parbanner->banner_image)}}" style="width: 27px;"></td>
                            <td>
                               
                            <a href="{{route('editparbanner',$parbanner->banner_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-edit" style="width: 10px;"></i></a>
                            </td>
                        </tr>
                      @else
                        <tr>
                          <td>No data found</td>
                        </tr>
                      @endif
                      @if($subsbanner)
                        <tr>
                            <td>{{$subsbanner->banner_name}}</td>
                            
                            <td align="center"><img src="{{url($subsbanner->banner_image)}}" style="width: 27px;"></td>
                            <td>
                               
                            <a href="{{route('editsubsbanner',$subsbanner->banner_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-edit" style="width: 10px;"></i></a>
                            </td>
                        </tr>
                      @else
                        <tr>
                          <td>No data found</td>
                        </tr>
                      @endif
                        @if($bigbanner)
                        <tr>
                            <td>{{$bigbanner->banner_name}}</td>
                            
                            <td align="center"><img src="{{url($bigbanner->banner_image)}}" style="width: 27px;"></td>
                            <td>
                               
                            <a href="{{route('editbigbanner',$bigbanner->banner_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-edit" style="width: 10px;"></i></a>
                            </td>
                        </tr>
                      @else
                        <tr>
                          <td>No data found</td>
                        </tr>
                      @endif
                        @if($closed_banner)
                        <tr>
                            <td>{{$closed_banner->banner_name}}</td>
                            
                            <td align="center"><img src="{{url($closed_banner->banner_image)}}" style="width: 27px;"></td>
                            <td>
                               
                            <a href="{{route('editclosedbanner',$closed_banner->banner_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-edit" style="width: 10px;"></i></a>
                            </td>
                        </tr>
                      @else
                        <tr>
                          <td>No data found</td>
                        </tr>
                      @endif
                        @if($top_message_banner)
                        <tr>
                            <td>{{$top_message_banner->banner_name}}</td>
                            
                            <td align="center"><img src="{{url($top_message_banner->banner_image)}}" style="width: 27px;"></td>
                            <td>
                               
                            <a href="{{route('edittopmessagebanner',$top_message_banner->banner_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-edit" style="width: 10px;"></i></a>
                            </td>
                        </tr>
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
@foreach($city as $cities)
<!-- Modal -->
<div class="modal fade" id="exampleModal{{$cities->vendor_category_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Delete Banner</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
			</div>
			<div class="modal-body">
				do you want to delete banner.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<a href="{{route('deletevendor',$cities->vendor_category_id)}}" class="btn btn-primary">Delete</a>
			</div>
		</div>
	</div>
</div>
@endforeach
@endsection
