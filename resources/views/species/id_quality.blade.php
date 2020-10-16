@extends('layouts.app')

@section('content')
@php
	$col = $_GET["col"] ?? "scientific_name";
@endphp
<div class="container-fluid border border-info bg-light p-5">
	<table class="table table-hover table-dark table-sm" id="species_table">
		<thead>
			<tr>
				<th>{{ ucwords(str_replace("_", " ", $col)) }}</th>
				<th>Count</th>
			</tr>
		</thead>
	</table>
	<a class="btn btn-lg btn-success" id="modalBtn">Add ID Quality</a>
</div>
<div class="modal" tabindex="-1" role="dialog" id="edit_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="rowForm" action="{{ url("/species/id_quality_update") }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
				<div class="modal-body">
					@csrf

					<div class="form-group">
						<label for="names">Names</label>
						<textarea type="text" class="form-control" name="names" id="names"> </textarea>
					</div>

					<div class="form-group">
						<label for="id_quality">ID Quality</label>
						<input type="text" class="form-control" name="id_quality" id="id_quality" value="">
						<small id="id_quality_help" class="form-text text-muted">species | genus | family | mismatch | flag</small>
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
	$(document).ready(function () {
		var data = @json($data);

		var table = $("#species_table").DataTable({
			"data": data,
			"scrollY": true,
			"scrollX": false,
			"fixedHeader": true,
			"lengthMenu": [100,250],
			"order": [[ 0, "asc" ]],
		});

		$("#species_table tbody").on('click', "tr", function(){
			$(this).toggleClass("table-secondary text-success selected");
		});

		$("#modalBtn").on('click', function(){
			var names = [];
			$(".selected").each(function(k,v){
				names.push($(v).children("td").html());
			})

			$("#edit_modal #names").val(JSON.stringify(names));

			$("#edit_modal").modal("show");
		})

			// $("#edit_modal .delete").attr("action", "{{ url("/") }}" + "/species/" + row_data.id);

	});
</script>
@endsection
