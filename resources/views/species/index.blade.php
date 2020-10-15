@extends('layouts.app')

@section('content')
<div class="container border border-info">
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
						<label for="species">Scientific Name</label>
						<input type="text" class="form-control" name="species" id="species" value="">
					</div>
					<div class="form-group">
						<label for="no_of_individuals">Count</label>
						<input type="text" class="form-control" name="no_of_individuals" id="no_of_individuals" value="">
					</div>
					<div class="form-group">
						<label for="remarks">Remarks</label>
						<input type="text" class="form-control" name="remarks" id="remarks" value="">
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="saveBtn">Save changes</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
	var data = @json($rows);

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
			{"title": "Participant Name", "data": "form.name"},
			{"title": "Sl No", "data": "sl_no"},
			{"title": "Common name", "data": "common_name"},
			{"title": "Species", "data": "scientific_name"},
			{"title": "Count", "data": "no_of_individuals"},
			{"title": "Remarks", "data": "remarks"},
			{"title": "File", "data": "form.filename"},
			// {"title": "Created at", "data": "created_at"}
			]
		});
		$("#species_table tbody").on('click', "tr", function(){
			var row_data = $("#species_table").DataTable().row(this).data();
			$("#edit_modal #name").val(row_data.form.name);
			$("#edit_modal #sl_no").val(row_data.sl_no);
			$("#edit_modal #common_name").val(row_data.common_name);
			$("#edit_modal #species").val(row_data.species);
			$("#edit_modal #no_of_individuals").val(row_data.no_of_individuals);
			$("#edit_modal #remarks").val(row_data.remarks);
			
			$("#rowForm").attr("action", "{{ url("/") }}" + "/species/" + row_data.id);
			
			$("#edit_modal").modal("show");

			

		});
		
		
	});
</script>
@endsection
