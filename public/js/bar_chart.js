google.charts.load('current', {'packages': ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var data = google.visualization.arrayToDataTable([
        ['key Word', 'Positive', 'Negative'],
        ['Rooms', 80, 20],
        ['Staff', 50, 50],
        ['Food', 30, 70],
        ['Overall experience', 60, 40]
    ]);

    var options = {
        chart: {
            title: 'Sentiment of key words (%)'
        }
    };

    var chart = new google.charts.Bar(document.getElementById('bar_chart'));

    chart.draw(data, options);
}
