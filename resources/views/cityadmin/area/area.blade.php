@extends('cityadmin.layout.app')

@section ('content')


<!-- Begin Page Content -->
<div class="container-fluid">


  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Vendor</h6>
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
                            <form action="{{ route('searchvendor') }}" method="post">
        {{csrf_field()}}
<input type="text" value=""  name="vendorname" class="form-control" placeholder="Enter Vendor Name" style="width: 20%; display: inline;">
    <button type="submit" class="btn btn-success btn-md" value="Search" style="margin-top: -5px;"><i class="fa fa-search"></i></button>
</form>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
            <th>S.No</th>
            <th>Vendor Name</th>
            <th>Location</th>
            <th>Delivery Radius</th>
            <th>Action</th>
            </tr>
          </thead>
         
          <tbody>
          @if(count($vendor)>0)
                          @php $i=1; @endphp
                          @foreach($vendor as $vendors)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$vendors->vendor_name}}</td>
                            <td>{{$vendors->vendor_loc}}</td>
                            <td>{{$vendors->delivery_range}}</td>
                            <td>
                                <a href="{{route('edit-area',$vendors->vendor_id)}}" style="width: 28px; padding-left: 6px;" class="btn btn-info" style="width: 10px;padding-left: 9px;" style="color: #fff;">
                                    <i class="fa fa-edit" style="width: 10px;"></i>
                                </a>
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
                {!! $vendor->links("pagination::bootstrap-4") !!}
      </div>
    </div>
    </div>

</div>
<!-- /.container-fluid -->
</div>
</div>
 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRQIhsGS3xrGeopcSPW70zal2yNRIQAJc"></script> 
<script>
var bermudaTriangle;
window.onload = function()  {
    var myLatLng = new google.maps.LatLng(33.5190755, -111.9253654);
    var mapOptions = {
        zoom: 12,
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.RoadMap
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'),
                                  mapOptions);


    var triangleCoords = [
        new google.maps.LatLng(33.5362475, -111.9267386),
        new google.maps.LatLng(33.5104882, -111.9627875),
        new google.maps.LatLng(33.5004686, -111.9027061)

    ];

    // Construct the polygon
    bermudaTriangle = new google.maps.Polygon({
        paths: triangleCoords,
        draggable: true,
        editable: true,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35
    });

    bermudaTriangle.setMap(map);
    google.maps.event.addListener(bermudaTriangle, "dragend", getPolygonCoords);
    google.maps.event.addListener(bermudaTriangle.getPath(), "insert_at", getPolygonCoords);
    google.maps.event.addListener(bermudaTriangle.getPath(), "remove_at", getPolygonCoords);
    google.maps.event.addListener(bermudaTriangle.getPath(), "set_at", getPolygonCoords);
};

function getPolygonCoords() {
    var len = bermudaTriangle.getPath().getLength();
    var htmlStr = "";
    for (var i = 0; i < len; i++) {
        htmlStr += bermudaTriangle.getPath().getAt(i).toUrlValue(5) + "<br>";
    }
    document.getElementById('info').innerHTML = htmlStr;
}
</script>
 
@endsection