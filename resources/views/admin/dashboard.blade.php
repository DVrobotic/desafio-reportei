@extends('admin.layouts.sistema')

@section('title', 'Dashboard')

@section('content')
    <div>
        <h1>teste</h1>
        <canvas id="myChart"></canvas>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>

        var xValues = {!! json_encode($dateArray) !!};
        var yValues = {!! json_encode($mergeDateArray) !!};
        var barColors = ["gray"];
        const data = {
            labels: xValues,
            datasets: [{
                label: 'prs fechados',
                backgroundColor: barColors,
                data: yValues
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
