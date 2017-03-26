google.load('visualization', '1', {packages: ['controls']});
google.setOnLoadCallback(drawChart);

function drawChart() {
    var data = new google.visualization.DataTable();
    var initState = {selectedValues: []};
    var i = 0; //Counter for initState to fill UP TO 4 labels
    columns.forEach(function (column) {
        data.addColumn(column[0], column[1]);
    });

    data.addRows(chartData);

    var columnsTable = new google.visualization.DataTable();
    columnsTable.addColumn('number', 'colIndex');
    columnsTable.addColumn('string', 'colLabel');
    // put the columns into this data table (skip column 0)
    for (var i = 1; i < data.getNumberOfColumns(); i++) {
        columnsTable.addRow([i, data.getColumnLabel(i)]);
    }

    var counter = 0;
    columns.forEach(function (column) {
        if (counter > 0 && counter <= 3) {
            initState.selectedValues.push(column[1]);
        }
        counter++;
    });


    var chart = new google.visualization.ChartWrapper({
        chartType: 'LineChart',
        containerId: 'chart_div',
        dataTable: data,
        options: {
            title: 'Trend of Satisfaction',
            theme: 'material',
            vAxis: {ticks: [0, 25, 50, 75, 100]},
            height: 600,
            interpolateNulls: true

        }
    });

    var columnFilter = new google.visualization.ControlWrapper({
        controlType: 'CategoryFilter',
        containerId: 'colFilter_div',
        dataTable: columnsTable,
        options: {
            filterColumnLabel: 'colLabel',
            ui: {
                label: 'Key Words',
                allowTyping: false,
                allowMultiple: true,
                allowNone: false,
                selectedValuesLayout: 'belowStacked'
            }
        },
        state: initState
    });

    var dateSlider = new google.visualization.ControlWrapper({
        controlType: 'DateRangeFilter',
        containerId: 'dateSlider_div',
        options: {
            filterColumnLabel: 'Date'
        }
    });

    var dashboard = new google.visualization.Dashboard(
        document.getElementById('dashboard_div'));


    function setChartView() {
        var state = columnFilter.getState();
        var row;
        var view = {
            columns: [0]
        };
        for (var i = 0; i < state.selectedValues.length; i++) {
            row = columnsTable.getFilteredRows([{column: 1, value: state.selectedValues[i]}])[0];
            view.columns.push(columnsTable.getValue(row, 0));
        }
        // sort the indices into their original order
        view.columns.sort(function (a, b) {
            return (a - b);
        });
        chart.setView(view);
        chart.draw();
    }

    google.visualization.events.addListener(columnFilter, 'statechange', setChartView);


    google.visualization.events.addListener(chart, 'select', function () {
        var selection = chart.getChart().getSelection();
        if (selection[0] !== undefined && selection[0].row !== null) {
            selectHandler(data.getColumnLabel(selection[0].column), data.getValue(selection[0].row, 0))
        }
    });


    function selectHandler(label, key) {

        console.log(label);
        console.log(key);

        $("#reviewModal").modal();
        var keyWord = label;
        var date = key;
        var aroundDate = formatDate(date);
        $(".modal-body").empty();

        $.get("/api/reviews-with-keyword/" + campaignId + "/" + aroundDate + "/" + keyWord, function (data) {
            $("#reviewModal").modal();
            var upperKeyword = ucfirst(keyWord);
            $(".modal-title").text("Reviews for "+ upperKeyword);

            data.forEach(function (arrayItem) {
                var highlightedText = arrayItem.text.replace(keyWord, "<mark>"+keyWord+"</mark>");
                highlightedText = highlightedText.replace(upperKeyword, "<mark>"+upperKeyword+"</mark>");
                var source = arrayItem.source == 2 ? "TripAdvior" : "Yelp";

                $(".modal-body").append("<p>" + highlightedText + "<br><b>"+arrayItem.author+" - "+arrayItem.date.substring(0,10)+" "+source+"</b></p><hr>");
            });

        });
    }


    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }

    function ucfirst (str) {
        //  discuss at: http://locutus.io/php/ucfirst/
        // original by: Kevin van Zonneveld (http://kvz.io)
        // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
        // improved by: Brett Zamir (http://brett-zamir.me)
        //   example 1: ucfirst('kevin van zonneveld')
        //   returns 1: 'Kevin van zonneveld'
        str += ''
        var f = str.charAt(0)
            .toUpperCase()
        return f + str.substr(1)
    }

    dashboard.bind(dateSlider, chart);
    dashboard.draw(data);

    setChartView();
    columnFilter.draw();
}
