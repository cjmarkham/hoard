var chart_getData
,   chart_dataInterval = 5000
,   chart_dataPeriod = 'minute'
,   chart_dataRequest = null
,   chart_bucket = ''
;


$(document).ready(function () {

    var dashboard_chart = $('#dashboard_chart');
    if (dashboard_chart.length === 0) return;

    // Get chart context
    var ctx = document.getElementById("dashboard_chart").getContext("2d");
    var chart = new Chart(ctx);
    var chart_timeout = null;

    // Chart generator
    chart_getData = function (p, interval) {

        if (interval) {
            chart_dataInterval = interval * 1000;
        }

        if (p) chart_dataPeriod = p;
        if (chart_dataRequest) chart_dataRequest.abort();

        clearTimeout(chart_timeout);
        chart_dataRequest = $.get('/stats?period=' + chart_dataPeriod + '&steps=6&bucket=' + chart_bucket, function (json) {

            // Split the lines
            var data = json['data'];
            var chartData = {
                'labels': [],
                'datasets': []
            };

            var events = [];

            // Get Events
            $.each(data, function (range_index, group) {
                //chartData['labels'].push(range_index);
                chartData['labels'].push('');
//                $.each(group['events'], function (event_key, value) {
//                    if (jQuery.inArray(event_key, events) < 0) {
//                        events.push(event_key);
//                    }
//                });
            });

            // Create Data Sets
            var group_data = [];
            $.each(data, function (range_index, group) {
                group_data.push(group.count);
            });
            chartData['datasets'].push(
                {
                    fillColor : "rgba(220,220,220,0.5)",
                    strokeColor : "rgba(220,220,220,1)",
                    pointColor : "rgba(220,220,220,1)",
                    pointStrokeColor : "#fff",
                    data : group_data
                }
            );

            // Create the chart
            chart.Line(chartData, {
                'pointDot': false,
                'animation': false
            });
            chart_timeout = setTimeout(chart_getData, chart_dataInterval);

        }, 'json');

    }
    chart_getData(3600, 60);

});
