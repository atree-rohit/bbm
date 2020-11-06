@extends('layouts.app')

@section('content')
	<div class="container">
	    <table class="table">
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
