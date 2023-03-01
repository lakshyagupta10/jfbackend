@extends('cityadmin.layout.app')

@section ('content')


<!-- Begin Page Content -->
<div class="container-fluid">
 

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Delivery charges</h6>
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
        <a class="btn btn-success m-auto" style="float: right;" href="{{route('add-delivery_charges',$delivery_charge_id)}}">Add</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="example10" width="100%" cellspacing="0">
          <thead>
            <tr>
            <th>S.No</th>
            <th>Range start(km)</th>
            <th>Range end(km)</th>
            <th>Charges(₹)</th>
            <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
            <th>S.No</th>
            <th>Range start(km)</th>
            <th>Range end(km)</th>
            <th>Charges(₹)</th>
            <th>Action</th>
            </tr>
          </tfoot>
          <tbody>
          @if(count($delivery_charges)>0)
                          @php $i=1; @endphp
                          @foreach($delivery_charges as $delivery_charge)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$delivery_charge->range_start}}</td>
                            <td>{{$delivery_charge->range_end}}</td>
                            <td>{{$delivery_charge->charges}}</td>
                            <td>
                               <a href="{{route('edit-delivery_charge',$delivery_charge->id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-edit" style="width: 10px;"></i></a>
                               <a href="{{route('delete-delivery_charge',$delivery_charge->id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-danger"  style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-trash" style="width: 10px;"></i></a>
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
      <h6 class="m-0 font-weight-bold text-primary">List of Vendors</h6>
              <button class="btn btn-success m-auto" style="float: right; color:white" onclick="showvendors()" >Add New Vendor</button>
                 <form id="vendorform" style="display:none" class="forms-sample" action="{{route('add-delivery_charge_vendor',$delivery_charge_id)}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
                      <div class="form-group">
                    <select class="form-control form-control-sm" id="exampleFormControlSelect3 " name="vendor">
                     <option value="" selected>select</option>
                      @foreach($vendors as $vendor)
		          	<option value="{{$vendor->vendor_name}}">{{$vendor->vendor_name}}</option>
		              @endforeach
                    </select>
                    </div>
                    <button type="submit" class="btn btn-success mr-2">submit</button>
                  </form>
            <p>{{$vendorlist}}</p>
    </div>
  </div>

</div>
<!-- /.container-fluid -->
</div>
</div>
@endsection
<script>
function showvendors()
{
    console.log("trial");
    document.getElementById('vendorform').style.display="block";
}
</script>    