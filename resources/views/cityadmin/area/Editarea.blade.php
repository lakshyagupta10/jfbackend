@extends('cityadmin.layout.app')

@section ('content')
<div class="content-wrapper">
          <div class="row">
		  <div class="col-md-2">
		  </div>
            
            <div class="col-md-8 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Update Delivery Radius</h4>
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
                  <form class="forms-sample" action="{{route('update-area',$vendor->vendor_id)}}" method="post" enctype="multipart/form-data">
                      {{csrf_field()}}
                    <div class="form-group">
                      <label for="exampleInputName1">Vendor Name</label>
                       <input type="hidden" name ="cityadmin_id" value = "{{$cityadmin->cityadmin_id}}">
                      <input type="text" class="form-control" id="exampleInputName1" value="{{$vendor->vendor_name}}" name="vendor_name" placeholder="Enter vendor Name">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Vendor Location</label>
                      <input type="text" class="form-control" id="exampleInputName1" value="{{$vendor->vendor_loc}}" name="vendor_loc" placeholder="Enter vendor Location">
                    </div>
                    <div class="form-group">
                      <label for="exampleInputName1">Delivery Radius</label>
                      <input type="text" class="form-control" id="exampleInputName1" value="{{$vendor->delivery_range}}" name="delivery_range" placeholder="Enter Delivery Radius">
                    </div>
                    <button onclick="resetmap()" class="btn btn-success mr-2">Reset</button>
                    <div class="form-group">
                      <input id="latlngarray" type="hidden" class="form-control" id="exampleInputName1"  name="latlngarray" value="{{$vendor->latlngarray}}" placeholder="Latlngarray">
                    </div>
                    </div>
                           <div id="map-canvas" style="width:auto;height:350px"></div>
                    <p id="info"></p>
                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                    
                     <a href="{{route('area')}}" class="btn btn-light">Cancel</a>
                  </form>
                </div>
              </div>
            </div>
             <div class="col-md-2">
		  </div>
     
          </div>
        </div>
       </div> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
        	$(document).ready(function(){
        	
                $(".des_price").hide();
                
        		$(".img").on('change', function(){
        	        $(".des_price").show();
        			
        	});
        	});
</script>
 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRQIhsGS3xrGeopcSPW70zal2yNRIQAJc"></script> 
<script>
var shape;
function resetmap()
{
    document.getElementById('latlngarray').value="N/A";
    setmap();
}
window.onload = function setmap()  {
    var myLatLng = new google.maps.LatLng({{$vendor->lat}}, {{$vendor->lng}});
    var mapOptions = {
        zoom: 12,
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.RoadMap
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'),
                                  mapOptions);
                var latlngarray = document.getElementById('latlngarray').value;
                if(latlngarray=="N/A")
                    {
                    var radius = {{$vendor->delivery_range}};
                    var latitude = {{$vendor->lat}};
                    var longitude = {{$vendor->lng}};
                    //Degrees to radians
                    var d2r = Math.PI / 180;
                    //  Radians to degrees
                    var r2d = 180 / Math.PI;
                    // Earth radius is 3,963 miles
                    var cLat = (radius / 6371) * r2d;
                    var cLng = cLat / Math.cos(latitude * d2r);
                    //Store points in array
                    var points = [];
                    var bounds = new google.maps.LatLngBounds();
                    // Calculate the points
                    // Work around 360 points on circle
                    for(var i = 0; i<360;i+=360/13) {

                        var theta = Math.PI * (i / 180);

                        // Calculate next X point
                        circleY = longitude + (cLng * Math.cos(theta));
                        //console.log("CircleY:"+circleY);
                        // Calculate next Y point
                        circleX = latitude + (cLat * Math.sin(theta));
                        //console.log("circleX:"+circleX);
                        // Add point to array
                        var aPoint = new google.maps.LatLng(circleX, circleY);
                        points.push(aPoint);
                        bounds.extend(aPoint);
                    }
    // var triangleCoords = [
    //     new google.maps.LatLng(moveTowards({{$vendor->lat}},{{$vendor->delivery_range}}))),
    //     new google.maps.LatLng(33.5104882, -111.9627875),
    //     new google.maps.LatLng(33.5004686, -111.9027061)

    // ];

    // Construct the polygon

    shape = new google.maps.Polygon({
        paths: points,
        draggable: true,
        editable: true,
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35
    });
     getPolygonCoords();
    }
    else
    {
         var coordinates = [];
         points =document.getElementById('latlngarray').value;
         var newpoints = JSON.parse(points);
         for(i=0;i<newpoints.length;i++)
         {
            var aPoint = new google.maps.LatLng(newpoints[i].lat, newpoints[i].lng);
            coordinates.push(aPoint);
         }
         console.log(typeof(newpoints))
         console.log(coordinates)
         shape = new google.maps.Polygon({
         paths: coordinates,
         draggable: true,
         editable: true,
         strokeColor: '#FF0000',
         strokeOpacity: 0.8,
         strokeWeight: 2,
         fillColor: '#FF0000',
         fillOpacity: 0.35
     });
     getPolygonCoords();
    }
    shape.setMap(map);
    google.maps.event.addListener(shape, "dragend", getPolygonCoords);
    google.maps.event.addListener(shape.getPath(), "insert_at", getPolygonCoords);
    google.maps.event.addListener(shape.getPath(), "remove_at", getPolygonCoords);
    google.maps.event.addListener(shape.getPath(), "set_at", getPolygonCoords);
};

function getPolygonCoords() {
   var newpoints = [];
   var len = shape.getPath().getLength();
    for (var i = 0; i < len; i++) {
        var xy = shape.getPath().getAt(i);
        var item = { "lat" : xy.lat(), "lng":xy.lng()};
        newpoints.push(item);
    }
    console.log(newpoints);
    document.getElementById('latlngarray').value = JSON.stringify(newpoints);
}
</script>
@endsection