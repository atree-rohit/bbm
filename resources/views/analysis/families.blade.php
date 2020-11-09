@extends('layouts.app')

@section('style')
@endsection

@section('content')
	<div class="container-fluid p-3 border border-danger">
        <div class="row ">
    		<canvas id="myChart" width="200" height="150" class="bg-light col-6"></canvas>
            <canvas id="myChart1" width="200" height="150" class="bg-light col-6"></canvas>
    	</div>

    </div>
@endsection

@section('script')
{{-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0" crossorigin="anonymous"></script>
<script type="text/javascript">

	var ctx = document.getElementById('myChart');
	var myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: @json(array_keys($family_data)),
        plugins: [ChartDataLabels],
        datasets: [ {
            label: 'Species',
            data: @json(array_column($family_data, "species")),
            backgroundColor: ["#f9a", "#fbb", "#fdc", "#efc", "#bed", "#cce"],
            borderWidth: 1
        }]
    }
});
        var ctx1 = document.getElementById('myChart1');
    var myChart1 = new Chart(ctx1, {
    type: 'pie',
    data: {
        labels: @json(array_keys($family_data)),
        plugins: [ChartDataLabels],
        datasets: [{
            label: 'Individuals',
            data: @json(array_column($family_data, "individuals")),
            backgroundColor: ["#f9a", "#fbb", "#fdc", "#efc", "#bed", "#cce"]
        }]
    }
});
</script>

@endsection
