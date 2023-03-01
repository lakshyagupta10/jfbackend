@extends('cityadmin.layout.app')

@section ('content')


<!-- Begin Page Content -->
<div class="container-fluid">


  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Delivery Charges</h6>
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
        <a class="btn btn-success m-auto" style="float: right;" href="{{route('add-delivery_charge')}}">Add</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
            <th>S.No</th>
            <th>Delivery_charges</th>
            <th style="max-width: 500px;word-wrap: break-word;">Vendorlist</th>
            <th>Action</th>
            </tr>
          </thead>
         
          <tbody>
          @if(count($delivery_charges)>0)
                          @php $i=1; @endphp
                          @foreach($delivery_charges as $delivery_charge)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{!!nl2br(e($delivery_charge->deliverycharges))!!}</td>
                            <td style="max-width: 500px;word-wrap: break-word;">{{$delivery_charge->vendorlist}}</td>
                            <td>
                               <a href="{{route('edit-delivery_charges',$delivery_charge->delivery_charge_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info" style="width: 10px;padding-left: 9px;" style="color: #fff;">
                                    <i class="fa fa-edit" style="width: 10px;"></i>
                                </a>
                                @if($delivery_charge->delivery_charge_id!=0)
                               <a href="{{route('delete-delivery_charges',$delivery_charge->delivery_charge_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-danger" style="width: 10px;padding-left: 9px;" style="color: #fff;">
                                    <i class="fa fa-trash" style="width: 10px;"></i>
                                </a>
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

</div>
<!-- /.container-fluid -->
</div>
</div>
</script>
@endsection