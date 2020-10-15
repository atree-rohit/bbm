@extends('layouts.app')

@section('style')
	<link href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
@endsection

@section('content')
<div class="container-fluid border border-info bg-light p-5">
	<table class="table table-hover table-dark table-sm" id="species_table"></table>
</div>
<div class="modal" tabindex="-1" role="dialog" id="edit_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="rowForm" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
				<div class="modal-body">
					@csrf
					{{ method_field('PUT') }}
					<div class="form-group">
						<label for="name">Participant Name</label>
						<input type="text" class="form-control" name="name" id="name" value="" readonly="readonly">
					</div>
					<div class="form-group">
						<label for="sl_no">Sl. No.</label>
						<input type="text" class="form-control" name="sl_no" id="sl_no" value="">
					</div>
					<div class="form-group">
						<label for="common_name">Common Name</label>
						<input type="text" class="form-control" name="common_name" id="common_name" value="">
					</div>
					<div class="form-group">
						<label for="scientific_name">Scientific Name</label>
						<input type="text" class="form-control" name="scientific_name" id="scientific_name" value="">
					</div>
					<div class="form-group">
						<label for="no_of_individuals">Count</label>
						<input type="text" class="form-control" name="no_of_individuals" id="no_of_individuals" value="">
					</div>
					<div class="form-group">
						<label for="remarks">Remarks</label>
						<input type="text" class="form-control" name="remarks" id="remarks" value="">
					</div>
					<div class="form-group">
						<label for="filename">Filename</label>
						<input type="text" class="form-control" name="filename" id="filename" value="" readonly="readonly">
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success" id="saveBtn">Save changes</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</form>
					<form class="delete"  action="" method="POST">
						@csrf
						{{ method_field('DELETE') }}
						<input type="submit"  class="btn btn-danger" value="Delete">
					</form>
		</div>
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
	var data = @json(array_values($rows->toArray()));

	$(document).ready(function () {
		var table = $("#species_table").DataTable({
			"data": data,
			"scrollY": true,
			"scrollX": false,
			"fixedHeader": true,
			"lengthMenu": [100,250],
			"order": [[ 3, "asc" ]],
			"columns": [
			{"title": "ID", "data": "id"},
			{"title": "FormData ID", "data": "form.id"},
			{"title": "Sl No", "data": "sl_no"},
			{"title": "Common name", "data": "common_name"},
			{"title": "Species", "data": "scientific_name"},
			{"title": "Count", "data": "no_of_individuals"},
			{"title": "Remarks", "data": "remarks"},
			{"title": "File", "data": "form.filename"},
			// {"title": "Created at", "data": "created_at"}
			],
			// dom: 'Bfrtip',
			// buttons: ['csv']
		});
		$("#species_table tbody").on('click', "tr", function(){
			var row_data = $("#species_table").DataTable().row(this).data();
			$("#edit_modal #name").val(row_data.form.name);
			$("#edit_modal #sl_no").val(row_data.sl_no);
			$("#edit_modal #common_name").val(row_data.common_name);
			$("#edit_modal #scientific_name").val(row_data.scientific_name);
			$("#edit_modal #no_of_individuals").val(row_data.no_of_individuals);
			$("#edit_modal #remarks").val(row_data.remarks);
			$("#edit_modal #filename").val(row_data.form.filename);

			$("#rowForm").attr("action", "{{ url("/") }}" + "/species/" + row_data.id);
			$("#edit_modal .delete").attr("action", "{{ url("/") }}" + "/species/" + row_data.id);

			$("#edit_modal").modal("show");



		});
		$(".delete").on("submit", function(){
			return confirm("Are you sure?");
		});


	});
</script>
@endsection
