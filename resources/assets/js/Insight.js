google.charts.load('current', {'packages': ['corechart']});
google.charts.setOnLoadCallback(drawBarChart);


function drawBarChart() {
    var data = google.visualization.arrayToDataTable([
        ['Key Word', 'Positive', 'Negative'],
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


google.charts.setOnLoadCallback(drawPieChart);

function drawPieChart() {
    var data = google.visualization.arrayToDataTable([
        ['Key Words', 'Most Positive'],
        ['Rooms', 20],
        ['Staff', 10],
        ['Food', 15],
        ['Lounge', 17],
        ['Facilities', 19]
    ]);

    var options = {
        title: 'Positive key words'
    };

    var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
    chart.draw(data, options);
}

$(window).resize(function(){
    drawBarChart();
    drawPieChart();
});