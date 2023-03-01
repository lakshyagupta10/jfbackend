<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Admin | Home</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- ================== BEGIN core-css ================== -->
	<link href="{{url('assets/css/app.min.css')}}" rel="stylesheet" />
	<!-- ================== END core-css ================== -->
</head>
<body>
	<!-- BEGIN #app -->
	<div id="app" class="app">
		
		@include('admin.layout.header')

		@include('admin.layout.sidebar')
		<!-- BEGIN #content -->
		<div id="content" class="app-content">
		
			@yield('content')
          
		</div>
		<!-- END #content -->
		
		<!-- BEGIN btn-scroll-top -->
		<a href="#" data-click="scroll-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>
		<!-- END btn-scroll-top -->
	</div>
	<!-- END #app -->
	
	<!-- ================== BEGIN core-js ================== -->
	<script src="{{url('assets/js/app.min.js')}}"></script>
	<!-- ================== END core-js ================== -->
	
	<!-- ================== BEGIN page-js ================== -->
	<script src="{{url('assets/plugins/apexcharts/dist/apexcharts.min.js')}}"></script>
	<script src="{{url('assets/js/demo/dashboard.demo.js')}}"></script>
	<!-- ================== END page-js ================== -->
	 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>
    <script type="text/javascript">

      $(function () {
        var n=$("#type").val()
        $("#table").DataTable();


        $( "#tablecontents" ).sortable({
        
          items: "tr",

          cursor: 'move',

          opacity: 0.6,

          update: function() {

              sendOrderToServer(n);

          }

        });


        function sendOrderToServer(n) {

          var order = [];
          var nurl = ""
          var token = $('meta[name="csrf-token"]').attr('content');

          $('tr.row1').each(function(index,element) {

            order.push({

              id: $(this).attr('data-id'),

              position: index+1

            });

          });
            if(n=="vendorcat"){
                nurl="{{ url('admin/vendorcat-sort') }}"
            }

          $.ajax({

            type: "POST", 

            dataType: "json", 

            url: nurl,

                data: {

              order: order,

              _token: token

            },

            success: function(response) {

                if (response.status == "success") {

                  console.log(response);

                } else {

                  console.log(response);

                }

            }

          });

        }

      });

    </script>

</body>
</html>



 
