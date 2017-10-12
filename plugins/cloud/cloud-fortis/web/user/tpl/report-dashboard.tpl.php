    <style>
        #project_tab_ui { display: none; }  /* hack for tabmenu issue */

        #lifetime-spent-gauge {
            display: table;
            margin: 0 auto;
        }

        #lifetime-spent-gauge span {
            display: table-cell;
            text-align: center;
            vertical-align: middle
        }
    </style>
<script src="/cloud-fortis/js/c3/d3.v3.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/c3/c3.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/chartjs/Chart.bundle.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/chartjs/utils.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/fetch-report.js" type="text/javascript"></script>
<script>

function current_year_monthly_spent(bindto, data) {
    /* data = [
        ['x', '2017-01-01', '2017-02-01', '2017-03-01', '2017-04-01', '2017-05-01', '2017-06-01', '2017-07-01', '2017-08-01'],
        ['total',             2300, 2100, 2250, 2140, 2260, 2150, 2000, 2400],
    ]; */

    var chart = c3.generate({
        bindto: bindto,
        data: {
            x: 'x',
            columns: data,
            type: 'bar',
            color: function (color, d) {
                return seriesColors[d.index];
            },
        },
        axis: {
            x:  {
                type: 'timeseries',
                tick: {
                    format: '%m/%Y'
                }
            },
            y:  {
                label: {
                    text: 'total cost ($)'
                }
            }
        },
        grid: {
            y: {
                show: true
            }
        },
        legend: {
            show: false
        }  
    });
}

function current_year_three_months_spent(bindto, data) {
    // var x_column = ['x', '2017-07-01', '2017-08-01', '2017-09-01'];
    // var y_column = ['total', 750, 1200, 1080];
    // data = [x_column, y_column];

    var chart3 = c3.generate({
        bindto: bindto,
        data: {
            x: 'x',
            columns: data,
            type: 'bar',
            color: function (color, d) {
                return seriesColors[d.index + 3];
            }
        },
        axis: {
            x: {
                type: 'timeseries',
                tick: {
                    format: '%Y-%b'
                }
            },
            y: {
                label: 'total cost ($)'
            }
        },
        bar: {
            width: {
                ratio: 0.5 // this makes bar width 50% of length between ticks
            }
      
        },
        grid: {
            y:  {
                show: true
            }
        },
        tooltip: {
            show: true,
            format: {
                value: function (value, ratio, id) {
                    var formatDecimalComma = d3.format(",.2f")
                    return "$" + formatDecimalComma(value); 
                }
            }
        },
        legend: {
            show: false
        } 
    });
}

function current_month_spent_by_resource(bindto, data) {
    // var numbers = [240,230,320,250,160];
    var labels = ["cpu","storage","memory","virtualization","networking"];
    //data = [labels,numbers];
	var numbers = data[1]
	var max = Math.max.apply(null, numbers);
	var min = max / 7;
	var normalized_numbers = [Math.max(numbers[0],min), Math.max(numbers[1],min), Math.max(numbers[2],min), Math.max(numbers[3],min), Math.max(numbers[4],min)];

    var color = Chart.helpers.color;
    var config = {
        data: {
            datasets: [{
                data: normalized_numbers,
                backgroundColor: [
                    color(seriesColors[0]).rgbString(),
                    color(seriesColors[1]).rgbString(),
                    color(seriesColors[3]).rgbString(),
                    color(seriesColors[5]).rgbString(),
                    color(seriesColors[4]).rgbString()
                ],
                label: 'dollars ($)' // for legend
            }],
            labels: data[0]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: true,
                position: 'bottom'
            },
            title: {
                display: false,
                // text: 'Mon'
            },
            scale: {
                display: true,
                reverse: false,
                ticks: {
                    callback: function(value, index, values) {
                        return '$ '+value;
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            },
            layout: {
                padding: {
                    left: 5,
                    right: 5,
                    top: 5,
                    bottom: 5
                }
                
            },
            tooltips: {
                enabled : true,
                callbacks: {
                    label: function(tooltipItems, data) {
                        return data.labels[tooltipItems.index] +': $' + numbers[tooltipItems.index];
                    }
                }
            }
        }
    };
    Chart.defaults.global.legend.labels.boxWidth = 12;
    var ctx = document.getElementById(bindto);
    window.myPolarArea = Chart.PolarArea(ctx, config);
    /* document.getElementById('js-legend').innerHTML = myPolarArea.generateLegend(); */
}

function lifetime_spent(bindto, objects) {
    var value = ['lifetime spending', 0];
    var max_val = value[1] * 2; /* show 50% gauge */

    var chart4 = c3.generate({
        bindto: bindto,
        data: {
            columns: [ value ],
            labels: true,
            type: 'gauge',
            onclick: function (d, i) { /* console.log("onclick", d, i); */ },
            onmouseover: function (d, i) { /* console.log("onmouseover", d, i); */},
            onmouseout: function (d, i) { /* console.log("onmouseout", d, i); */}
        },
        gauge: {
            max: max_val,
            label: {
                format: function(value, ratio) {
                    return '$ '+value;
                },
                show: true
            }
        },
        color: {
            pattern: [seriesColors[1]], // the three color levels for the percentage values.
        },
        legend: {
            show: true,
            position: 'bottom',
            format: function(value, ratio) {
                return '$ '+value;
            },
        },    
        transition: {
            duration: 1500
        },
        tooltip: {
            show: true,
            format: {
                value: function (value, ratio, id, index) { return value; }
            }
        }
    });
}

$(document).ready(function () {

    var this_month = new Date();
    var last_month = new Date();
    var next_month = new Date();
    this_month.setDate(1);
    last_month.setDate(1);
    last_month.setMonth(this_month.getMonth()-1);
    next_month.setDate(1);
    next_month.setMonth(this_month.getMonth()+1);

    var column_x_yearly  = ['x'];
    var column_x_3months = ['x'];
    var total_monthly   = ['total'];
    var cpu_monthly     = ['cpu'];
    var storage_monthly = ['storage'];
    var memory_monthly  = ['memory'];
    var virtual_monthly = ['virtualization'];
    var network_monthly = ['networking'];
    var deferred = [];
   
    var current_month = new Date();
    for (var i = 0; i <= this_month.getMonth(); i++) {
        current_month.setMonth(i);
        current_month.setDate(1);
        column_x_yearly.push(parseDate(current_month,'Y-M-D'));
        deferred.push(get_monthly_data(parseDate(current_month,'Y'), parseDate(current_month,'mon')));
    }
    column_x_3months.push(parseDate(last_month,'Y-M-D'), parseDate(this_month,'Y-M-D'), parseDate(next_month,'Y-M-D'));

    $.when.apply($, deferred).done(function () {
        var objects=arguments;

        for (var j = 0; j < objects.length; j++) {
            var json = JSON.parse(objects[j][0]);
            total_monthly.push(to_num(json.all));
            cpu_monthly.push(to_num(json.cpu));
            storage_monthly.push(to_num(json.storage));
            memory_monthly.push(to_num(json.memory));
            virtual_monthly.push(to_num(json.virtualization));
            network_monthly.push(to_num(json.networking));
        }

        current_year_monthly_spent("#current-year-monthly-spent", [column_x_yearly, total_monthly]);
        current_year_monthly_spent_by_resource("#current-year-monthly-spent-by-resource", [column_x_yearly, cpu_monthly, storage_monthly, memory_monthly, virtual_monthly, network_monthly]);
        var last_month_cost = parseFloat(total_monthly.slice(-2)[0]);
        var this_month_cost = parseFloat(total_monthly.slice(-1)[0]);
        
        // next month's cost projection is the average of past two month, or the last month if no data from two month ago
        next_month_cost = (last_month_cost > 0 && this_month_cost > 0 ? (this_month_cost + last_month_cost) / 2 : (this_month_cost > 0 ? this_month_cost : 0 )); 
        current_year_three_months_spent("#current-three-months-spent", [column_x_3months, ['total', last_month_cost, this_month_cost, next_month_cost]]);
        current_month_spent_by_resource("chartdiv-this-month-chart", [[cpu_monthly[0], storage_monthly[0], memory_monthly[0],virtual_monthly[0], network_monthly[0]], [cpu_monthly.slice(-1)[0], storage_monthly.slice(-1)[0], memory_monthly.slice(-1)[0], virtual_monthly.slice(-1)[0], network_monthly.slice(-1)[0]]]);
        // lifetime_spent("#lifetime-spent-gauge", objects); 

        var yearly_sum = 0.0;

        for (var k = 1; k < total_monthly.length; k++) {
            // console.log(total_monthly[k]);
            // console.log(yearly_sum);
            yearly_sum = yearly_sum + parseFloat(total_monthly[k]);
        }

        $("#yearly-spending h2").text("$" + yearly_sum.toFixed(2));
    });
});
</script>

<div class="cat__content">
    <cat-page>
    <div class="row">
        <div class="col-sm-12">
            <section class="card">  
                <div class="card-header">
                    <span class="cat__core__title">
                        <strong>Score Report Dashboard</strong>
                    </span>
                </div>
                <div class="card-block">
                    <div class="row">
                        <div class="col-sm-6 dashboard">
                            <section class="card">  
                                <div class="card-header">
                                    <span class="cat__core__title">
                                        <strong>{currentyear} Total Spent</strong>
                                    </span>
                                </div>
                                <div class="card-block">
                                    <div class="panel-heading">
                                        <div class="panel-control">
                                        </div>
                                        <h3 class="panel-title">&nbsp;</h3>
                                    </div>
                                    <div>
                                        <div id="current-year-monthly-spent"  style="height: 16rem;"></div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-sm-6 dashboard">
                            <section class="card">  
                                <div class="card-header">
                                    <span class="cat__core__title">
                                        <strong>Monthly Projection</strong>
                                    </span>
                                </div>
                                <div class="card-block">
                                    <div class="panel-heading">
                                        <div class="panel-control">
                                        </div>
                                        <h3 class="panel-title">&nbsp;</h3>
                                    </div>
                                    <div>
                                        <div id="current-three-months-spent" style="height: 16rem;"></div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 dashboard">
                            <section class="card">  
                                <div class="card-header">
                                    <span class="cat__core__title">
                                        <strong>{currentyear} Total Spent By Resource</strong>
                                    </span>
                                </div>
                                <div class="card-block">
                                    <div class="panel-heading">
                                        <div class="panel-control">
                                        </div>
                                        <h3 class="panel-title">&nbsp;</h3>
                                    </div>
                                    <div>
                                        <div id="current-year-monthly-spent-by-resource" style="height: 16rem;"></div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-sm-3 dashboard">
                            <section class="card">  
                                <div class="card-header">
                                    <span class="cat__core__title">
                                        <strong>Current Spending</strong>
                                    </span>
                                </div>
                                <div class="card-block">
                                    <div class="panel-heading">
                                        <div class="panel-control">
                                        </div>
                                        <h3 class="panel-title">&nbsp;</h3>
                                    </div>
                                    <div style="height: 16rem;">
                                        <canvas id="chartdiv-this-month-chart"></canvas>
                                    </div>
                                     <!-- <div id="js-legend" class="chart-legend"></div> -->
                                </div>
                            </section>
                        </div>
                        <div class="col-sm-3 dashboard">
                            <section class="card">  
                                <div class="card-header">
                                    <span class="cat__core__title">
                                        <strong>Current Yearly Spending</strong>
                                    </span>
                                </div>
                                <div class="card-block">
                                    <div class="panel-heading">
                                        <div class="panel-control">
                                        </div>
                                        <h3 class="panel-title">&nbsp;</h3>
                                    </div>
                                    <div>
                                        <div id="lifetime-spent-gauge" style="height: 16rem;">
                                            <span id="yearly-spending"><h2 class="text-black"></h2></span>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    </cat-page>
</div>

