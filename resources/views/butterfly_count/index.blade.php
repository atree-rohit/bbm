@extends('layouts.app')

@section('style')
	{{-- <link href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script> --}}
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.js"></script>
@endsection

@section('content')
<div class="container-fluid border border-info bg-light p-5">
	<table class="table table-hover table-dark table-sm" id="count_data_table"></table>
</div>
<div class="modal" tabindex="-1" role="dialog" id="edit_modal">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="row">
				<div class="col">
					<form id="countForm" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
						<div class="modal-body">
							@csrf
							{{ method_field('PUT') }}
							<table class="table table-sm">

								@foreach($formFields as $f)
								<tr>
									<td>{{$f[1]}}</td>
									<td><input type="text" class="form-control" name="{{$f[0]}}" id="{{$f[0]}}" value=""></td>
								</tr>
								@endforeach
								<tr>
									<td>Duplicate</td>
									<td>
										<div class="custom-control custom-switch">
											<label class="pr-5">No</label>
											<input type="checkbox" class="custom-control-input" id="customSwitch1" name="duplicate">
											<label class="custom-control-label" for="customSwitch1">Yes</label>
										</div></td>
									</tr>
								</table>


							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-primary" id="saveBtn">Save changes</button>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							</div>
						</form>
					</div>
					<div class="col">
						<table class="table bg-info table-sm" id="">
							<thead>
								<tr>
									<th>ID</th>
									<th>SL No</th>
									<th>Common Name</th>
									<th>Scientific Name</th>
									<th>Count</th>
									<th>Remarks</th>
								</tr>

							</thead>
							<tbody id="species_table"></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endsection

	@section('script')
	<script type="text/javascript">
		var data = @json($forms);

		$(document).ready(function () {
			var table = $("#count_data_table").DataTable({
				"data": data,
				"scrollY": true,
				"scrollX": false,
				"fixedHeader": true,
				"lengthMenu": [100,250],
				"order": [[ 0, "asc" ]],
				"columns": [
					{"title": "ID", "data": "id"},
					{"title": "Name", "data": "name"},
					{"title": "Affilation", "data": "affilation"},
					{"title": "Phone", "data": "phone"},
					{"title": "Email", "data": "email"},
					{"title": "Team Members", "data": "team_members"},
					{"title": "Link", "data": "photo_link"},
					{"title": "Coordinates", "data": "coordinates"},
					{"title": "Date", "data": "date"},
					{"title": "Altitude", "data": "altitude"},
					{"title": "Distance", "data": "distance"},
					{"title": "Weather", "data": "weather"},
					{"title": "Comments", "data": "comments"},
					{"title": "filename", "data": "filename"},
					{"title": "Species", "data": "rows_count"},
					{"title": "Duplicate", "data": "duplicate"},
					// {"title": "Created at", "data": "created_at"}
				],
				// "dom": 'Bfrtip',
				// "buttons": ['csv'],
				// "initComplete": function() {
				// 	table.buttons().container().appendTo('#count_data_table_wrapper .col-md-6:eq(0)');
				// 	$("#count_data_table").show();
				// },
			});
			// table.buttons().container().appendTo('#count_data_table_wrapper .col-md-6:eq(0)');

			$("#count_data_table tbody").on('click', "tr", function(){
				var row_data = $("#count_data_table").DataTable().row(this).data();
				console.log(row_data);
				@foreach($formFields as $f)
				$("#edit_modal #{{$f[0]}}").val(row_data.{{$f[0]}});
				@endforeach
				var species_rows = "";
				$.each(row_data.rows, function(rk, r){
					species_rows += "<tr>";
					species_rows += "<td>"+r.id+"</td>";
					species_rows += "<td>"+r.sl_no+"</td>";
					species_rows += "<td>"+r.common_name+"</td>";
					species_rows += "<td>"+r.scientific_name+"</td>";
					species_rows += "<td>"+r.no_of_individuals+"</td>";
					species_rows += "<td>"+r.remarks+"</td>";
					species_rows += "</tr>";
				});
				$("#edit_modal #species_table").html(species_rows);


				$("#countForm").attr("action", "{{ url("/") }}" + "/butterfly_count/" + row_data.id);

				$("#edit_modal").modal("show");



			});


		});
	</script>
	@endsection
