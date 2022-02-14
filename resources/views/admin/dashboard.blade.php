@extends('admin.layouts.sistema')

@section('title', 'Dashboard')

@section('content')
    <div>
        <h1>tempo medio de merge: {{ $time['allPulls']['times'] }}</h1>
        <canvas id="myChart"></canvas>
    </div>
    <div class="my-4">
        <h4>Tempo médio de merge de prs antigas aplicada: {{ $prsOpenBeforeMergeTime }}</h4>
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
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Chart.js Bar Chart'
                    }
                },
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
            }
        };
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: "bar",
            data: data,
            options: config,
        });

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
