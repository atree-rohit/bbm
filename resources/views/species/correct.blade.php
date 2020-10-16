@extends('layouts.app')

@section('content')
@php
	$col = $_GET["col"] ?? "scientific_name";
@endphp
<div class="container border border-info bg-light p-5">
	@if(session()->has('success'))
		<div class="alert alert-success h1">
			{{ session()->get('success') }} Records updated
		</div>
@endif

	<table class="table table-hover table-dark table-sm" id="species_table">
		<thead>
			<tr>
				<th>{{ ucwords(str_replace("_", " ", $col)) }}</th>
				<th>Common Name</th>
				<th>Count</th>
			</tr>
		</thead>
	</table>
</div>
<div class="modal" tabindex="-1" role="dialog" id="edit_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="rowForm" action="{{ url("/species/correct_update") }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
				<div class="modal-body">
					@csrf

					<div class="form-group">
						<label for="col">Column</label>
						<input type="text" class="form-control" name="col" id="col" value="{{$col}}" readonly>
					</div>

					<div class="form-group">
						<label for="names">Names</label>
						<textarea type="text" class="form-control" name="names" id="names"> </textarea>
					</div>

					<div class="form-group border border-success table-success">
						<label for="corrected">Corrected</label>
						<input type="text" class="form-control" name="corrected" id="corrected" value="">
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success" id="saveBtn">Save changes</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
	var data = @json($data);

	$(document).ready(function () {
		var table = $("#species_table").DataTable({
			"data": data,
			"scrollY": true,
			"scrollX": false,
			"fixedHeader": true,
			"lengthMenu": [100,250],
			"order": [[ 0, "asc" ]],
			// "columns": [
			// {"title": column, "data": column},
			// {"title": "Count", "data": "count"},
			// {"title": "Created at", "data": "created_at"}
			// ],
			// dom: 'Bfrtip',
			// buttons: ['csv']
		});
		$("#species_table tbody").on('click', "tr", function(){
			var row_data = $("#species_table").DataTable().row(this).data();
			$("#edit_modal #names").val(row_data[0]);

			$("#edit_modal").modal("show");



		});

	});
</script>
@endsection
