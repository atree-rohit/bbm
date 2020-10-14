@extends('layouts.app')

@section('content')
<div class="jumbotron text-center bg-steel text-light p-4 mb-0">
    <p class="display-2">Inspect SpreadSheet Data Structure</p>
    <p class="display-4">{{count($files_array)}} Sheets found</p>
    <button type="button" class="btn btn-lg btn-success" data-toggle="modal" data-target="#addSheetModal">
    	Add
    </button>
</div>
<div class="container-fluid p-5">

	<div class="row p-3 table-secondary rounded top-row-buttons">
		<div class="col-2 h1 text-center">
			<button class="h1 btn btn-lg btn-block btn-vermillion" id="toggle_village" href="#" >Village Name</button>
		</div>
		<div class="col d-flex justify-content-around">
			@foreach($colors as $k=>$c)
				<button class="h1 btn btn-lg btn-{{ $c }}" id="{{ $k }} ">Sheet {{ $k + 1 }}</button>
			@endforeach
		</div>
		<div class="col-1">
			<button class="h1 btn btn-lg btn-block btn-brown" id="toggle_stats" href="#">Table stats</button>
		</div>
	</div>
@foreach($files_array as $file_name => $spreadsheet)
	<div class="row border border-dark rounded bg-teal-light my-2 row-eq-height">
		<div class="col-2 btn-outline-vermillion sheet_toggle_village village_name_class " data-toggle="tooltip" title="{{ $file_name }}">
			<div class="h-100 d-flex" ><p class="h2 m-auto">{{ $file_name }}</p></div>
		</div>
		<div class="col px-0 village_data_class">
			@foreach($spreadsheet as $sheet_no => $sheet)

					<table class="my-2 table table-bordered  sheet_{{ $sheet_no }} " id="{{ $file_name }}_{{ $sheet_no }}">
						@foreach($sheet as $row_no => $row)
								@if($row_no == 1)
									<tbody class="d-none">
								@endif
									@if($row_no == 0)
										<thead class="bg-secondary text-light">
											<tr>
												<th  class="bg-warning text-dark">Row No</th>
												@foreach($row as $cell)
													<th>{{ $cell }}</th>
												@endforeach
											</tr>
										</thead>
									@else
										<tr>
											<td class="table-warning">{{ $row_no }}</td>
											@foreach($row as $cell)
												@if($cell == "")
													<td class="table-danger"></td>
												@else
													<td>{{ $cell }}</td>
												@endif
											@endforeach
										</tr>
									@endif
						@endforeach
					</tbody>
					</table>
			@endforeach
		</div>
		<div class="col-1 bg-brown-light m-1 p-0 h5 sheet_toggle_stats village_stats_class">
			@foreach($spreadsheet as $sheet_no => $sheet)
				<div class="row mx-0 my-2 py-1 text-center  table-dark sheet_{{ $sheet_no }}">
					<div class="col-4 py-2 px-0 text-light">{{$sheet_no + 1}}</div>
					<div class="col-4 py-2 px-0 text-success ">{{count($sheet[0])}}</div>
					<div class="col-4 py-2 px-0 text-info">{{count($sheet) - 1}}</div>
				</div>
			@endforeach
		</div>

		<div class="col-1 btn-outline-teal">
			<div class="h-100 d-flex" >
				<p class="h2 m-auto">
					<a class="btn btn-lg btn-teal" href="{{-- {{ route("file.update", ['file' => $file_names[$file_name]]) }} --}}">Add to DB</a>
				</p>
			</div>
		</div>
	</div>
@endforeach

</div>

<!-- Modal -->
<div class="modal fade" id="addSheetModal" tabindex="-1" role="dialog" aria-labelledby="addSheetModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addSheetModalLabel">Upload Village Data Excel Spreadsheet (.xls)</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{-- {{route('file.store')}} --}}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body bg-lime-light">
						<div class="table-danger border border-danger p-3 text-center" id="custom-file-input-error">Uploaded file must be .xls</div>
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text">Upload</span>
							</div>
							<div class="custom-file">
								<input type="file" name="file" class="custom-file-input" id="inputGroupFile01">
								<label class="custom-file-label" for="inputGroupFile01">Choose file</label>
							</div>
						</div>
				</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">
	$("table").hover(
		function(){
			header_class = $(this).children("thead").attr("class");
			replace_class = "bg-dark text-warning";
			$(this).children("thead").removeClass(header_class);
			$(this).children("thead").addClass(replace_class);
		},
		function(){
			$(this).children("thead").removeClass(replace_class);
			$(this).children("thead").addClass(header_class);
		}
	);
	$("table tbody tr").hover(
		function(){
			$(this).addClass("bg-warning");
		},
		function(){
			$(this).removeClass("bg-warning");
		}
	);
	$("table thead").on("click", function(){
		$(this).parent().children("tbody").toggleClass("d-none");
	});
	$(".village_name_class").on("click", function(){
		$(this).parent().children(".village_data_class").toggleClass("d-none");
	});
	$(".top-row-buttons .btn").on("click", function(){
		btn_color = $(this).attr("class").split(" ").pop();
		btn_state = btn_color.split("-").length - 1;
		table_name = ".sheet_" + $(this).attr("id");
		console.log(table_name);
		if(btn_state == 1){
			new_btn_color = btn_color.replace("-", "-outline-");
			$(this).addClass(new_btn_color);
			$(this).removeClass(btn_color);
			$(table_name).addClass("d-none");
		}
		else{
			new_btn_color = btn_color.replace("-outline-", "-");
			$(this).addClass(new_btn_color);
			$(this).removeClass(btn_color);
			$(table_name).removeClass("d-none");
		}
	});
	$(".custom-file-input").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		if(fileName.split(".").pop() != "xls"){
			$(this).siblings(".custom-file-label").addClass("border border-danger").html(fileName);
			$("#custom-file-input-error").removeClass("d-none");
		}
		else
			$(this).siblings(".custom-file-label").addClass("selected").html(fileName);

	});
</script>
@endsection
