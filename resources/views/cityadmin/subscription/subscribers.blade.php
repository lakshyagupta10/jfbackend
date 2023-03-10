@extends('cityadmin.layout.app')

@section ('content')


<!-- Begin Page Content -->
<div class="container-fluid">
 

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">User Details</h6>
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
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
                <th>S.No</th>
                <th>User Name</th>
                <th>User Number</th>
                <th>Wallet Amount</th>
                <th>Action</th>
            </tr>
          </thead>
         
          <tbody>
          @if(count($subscribers)>0)
                          @php $i=1; @endphp
                          @foreach($subscribers as $user)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$user->user_name}}</td>
                            <td>{{$user->user_phone}}</td>
                            <td>{{$user->wallet_credits}}</td>
                            
                            <td>
                               <a href="{{route('delete-subscriber',$user->user_id)}}" style="width: 100%; padding-left: 6px;" class="btn btn-danger" style="width: 10px;padding-left: 9px;" style="color: #fff;"><i class="fa fa-trash" style="width: 10px;"></i></a>
							
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
   
@endsection