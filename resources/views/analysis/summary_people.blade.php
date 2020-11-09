@extends('layouts.app')

@section('style')
	{{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css"> --}}
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/fh-3.1.7/r-2.2.6/sc-2.0.3/datatables.min.css"/>
@endsection

@section('content')
	<div class="container-fluid p-3 border border-dangers">
		<canvas id="myChart" width="400" height="200" class="bg-light"></canvas>
	</div>
@endsection

@section('script')
{{-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script> --}}
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/fh-3.1.7/r-2.2.6/sc-2.0.3/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0" crossorigin="anonymous"></script>
<script type="text/javascript">
	$(document).ready( function () {
	    $('#analysisTable').DataTable();
	} );

	var ctx = document.getElementById('myChart');
	var myChart = new Chart(ctx, {
    type: 'horizontalBar',
    data: {
        labels: @json(array_column($people, "name")),
        plugins: [ChartDataLabels],
        backgroundColor: "#fff",
        datasets: [{
            label: 'Lists',
            data: @json(array_column($people, "lists")),
            backgroundColor: "#faa",

        }, {
            label: 'Species',
            data: @json(array_column($people, "species")),
            backgroundColor: "#aaf",
            borderWidth: 1
        }, {
            label: 'Individuals',
            data: @json(array_column($people, "individuals")),
            backgroundColor: "#afa",
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
</script>

@endsection
