@extends('layouts.app')

@section('style')
	{{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css"> --}}
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/fh-3.1.7/r-2.2.6/sc-2.0.3/datatables.min.css"/>
@endsection

@section('content')
	<div class="container">
	    <table class="table" id="analysisTable">
	    	<thead>
		    	<tr>
		    		<th>ID</th>
		    		<th>Name</th>
		    		<th>Date</th>
		    		<th>Location</th>
		    		<th>No of species</th>
		    		<th>No of individuals</th>
		    	</tr>
	    	</thead>
	    	<tbody>
	    		@foreach($forms as $f)
	    		<tr>
	    			<td>{{ $f->id }}</td>
	    			<td>{{ $f->name }}</td>
	    			<td>{{ $f->date_cleaned }}</td>
	    			<td>{{ $f->location }}</td>
	    			<td>{{ count($f->rows->where("id_quality", "species")) }}</td>
	    			<td>{{ $f->rows->where("id_quality", "species")->sum("no_of_individuals_cleaned") }}</td>
	    		</tr>
	    		@endforeach
	    	</tbody>
	    </table>
	</div>


@endsection

@section('script')
{{-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/fh-3.1.7/r-2.2.6/sc-2.0.3/datatables.min.js"></script>
<script type="text/javascript">
	$(document).ready( function () {
	    $('#analysisTable').DataTable();
	} );
</script>

@endsection