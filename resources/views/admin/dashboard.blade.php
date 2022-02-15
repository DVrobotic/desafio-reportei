@extends('admin.layouts.sistema')

@section('title', 'Dashboard')

@section('content')
    <div class="my-5 pb-5">
        <h4>Contribuição por devs em prs</h4>
        <canvas class="my-4" id="devsChart"></canvas>
        <h5>Totat de pull requests por dev</h5>
        @foreach($devsContribution['devPrs'] as $key => $value)
            <div class="d-block">
                <label class="d-inline">{{ $key }} : </label>
                <div class="d-inline">{{ $value }}</div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">
        <h4>tempo medio de merge total: {{ $time['allPulls']['times'] }}</h4>
        <h4>tempo medio de merge das fechadas: {{ $time['onlyClosed']['times'] }}</h4>
        <canvas height="200em" id="myChart"></canvas>
    </div>
    <div class="my-4">
        <h4 class="mt-3">Tempo médio de merge de prs antigas aplicada: {{ $prsOpenBeforeMergeTime }}</h4>
        @foreach($prsOpenBefore as $pull)
            <h5>Pull de {{ $pull->owner }}</h5>
            <label>Criada em:
                {{  date('d-m-Y', $pull->created_at) }}
            </label>
            <label>Atualmente:
                {{  $pull->open ? "Aberto" : "Fechado" }}
            </label>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        const doughnutData = {
            labels: {!! json_encode($devsContribution['contribution']->keys()) !!},
            datasets: [{
                label: 'Contribuição em Pull Requests por dev',
                data: {!! json_encode($devsContribution['contribution']->values()) !!},
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(255, 0, 0)',
                    'rgb(0, 162, 235)',
                    'rgb(150, 0, 86)',
                ],
                hoverOffset: 4
            }]
        };
        const doughnutConfig = {
            type: 'doughnut',
            data: doughnutData,
            options: {
                responsive: true,
                tooltips: {
                    callbacks: {
                        title: function(tooltipItem, data) {
                            return data['labels'][tooltipItem[0]['index']];
                        },
                        label: function(tooltipItem, data) {
                            return data['datasets'][0]['data'][tooltipItem['index']].toFixed(2) + "%";
                        },
                    }
                },
            }
        };
        var devsChartElement = document.getElementById('devsChart').getContext('2d');
        var devsChart = new Chart(devsChartElement, doughnutConfig);
    </script>

    <script>
        var openCount = {!! json_encode($data['pullsCount']['open']) !!};
        var closedCount ={!! json_encode($data['pullsCount']['closed']) !!};
        var xValues = {!! json_encode($data['dateArray']) !!};
        var closedValues = {!! json_encode($data['mergeClosedDateArray']) !!};
        var openValues = {!! json_encode($data['mergeOpenDateArray']) !!};
        var barColors = ["gray", "blue"];
        const data = {
            labels: xValues,
            datasets:
            [{
                label: 'prs fechados',
                backgroundColor: barColors[0],
                data: closedValues
            },
            {
                label: 'prs Abertos',
                backgroundColor: barColors[1],
                data: openValues
            }]
        };
        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                tooltips: {
                    enabled: true,
                    mode: 'single',
                    callbacks: {
                        title: function(tooltipItems){
                            return (tooltipItems[0].datasetIndex ? 'Pull Request Aberta em ' : 'Pull Request Fechada em ') + tooltipItems[0].label;
                        },
                        label: function (tooltipItems) {
                            return "Merge Time: " + secondsToTime(tooltipItems.value);
                        },
                        afterLabel: function(tooltipItem) {
                            return (tooltipItem.datasetIndex ?
                                "Prs abertos nesse periodo: " + openCount[tooltipItem.index] :
                                "Prs fechadas nesse periodo: " + closedCount[tooltipItem.index]);
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            stepSize: 3600*24*5,
                            callback: function(label, index, labels) {
                                return secondsToTime(label);
                            }
                        }
                    }],
                },
            },

        };
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, config);

        function secondsToTime(inputSeconds){
            var secondsInAMinute = 60;
            var secondsInAnHour  = 60 * secondsInAMinute;
            var secondsInADay    = 24 * secondsInAnHour;

            // extract days
            var days = Math.floor(inputSeconds / secondsInADay);

            // extract hours
            var hourSeconds = inputSeconds % secondsInADay;
            var hours = Math.floor(hourSeconds / secondsInAnHour);

            // extract minutes
            var minuteSeconds = hourSeconds % secondsInAnHour;
            var minutes = Math.floor(minuteSeconds / secondsInAMinute);

            // extract the remaining seconds
            var remainingSeconds = minuteSeconds % secondsInAMinute;
            var seconds =Math.floor(remainingSeconds);
            return days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
        }

    </script>
@endpush
