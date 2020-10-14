@extends('layouts.app')

@section('content')
	<table class="table" id="species_table"></table>
@endsection

@section('script')
	<script type="text/javascript">
		var data = @json($rows);

		$(document).ready(function () {
			var table = $("#species_table").DataTable({
				"data": data,
				"scrollY": true,
				"scrollX": true,
				"fixedHeader": true,
				"columns": [
					{"title": "ID", "data": "id"},
					{"title": "Form ID", "data": "count_form_id"},
					{"title": "Sl No", "data": "sl_no"},
					{"title": "Common name", "data": "common_name"},
					{"title": "Species", "data": "scientific_name"},
					{"title": "Count", "data": "no_of_individuals"},
					{"title": "Remarks", "data": "remarks"},
					{"title": "Filename", "data": "form.filename"},
					{"title": "Created at", "data": "created_at"}
				]
			});
			$("#data_table_wrapper thead").addClass("bg-dark text-light");
			$("#data_table_wrapper tbody tr").hover(
				function(){
					$(this).addClass("bg-warning");
				},
				function(){
					$(this).removeClass("bg-warning");
				}
			);
		});
	</script>
@endsection
