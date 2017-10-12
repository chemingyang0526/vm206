var seriesColors = [
	'#dfdfdf',
	'#41bee9',
	chartColors.red,
	chartColors.yellow,
	chartColors.green,
	chartColors.orange,
	chartColors.purple,
	chartColors.moss,
	chartColors.teal
];

function current_year_monthly_spent_by_resource(bindto, data) {

    var chart2 = c3.generate({
        bindto: bindto,
        data: {
            x: 'x',
            columns: data,
            type: 'bar',
            colors: {
                cpu:            seriesColors[0],
                storage:        seriesColors[1],
                memory:         seriesColors[3],
                virtualization: seriesColors[5],
                networking:     seriesColors[4]
            },
            groups: [
                ['cpu','storage','memory','virtualization','networking']
            ],
            order: false
        },
        axis: {
            x:  {
                type: 'timeseries',
                tick: {
                    format: '%Y-%b'
                }
            },
            y:  {
                label: {
                    text: 'total cost ($)'
                }
            }
        },
        grid: {
            y:  {
                show: true
            }
        }
    });
}

function get_daily_data(year_str, month_str, day_str, type) {
    var url = '/cloud-fortis/user/index.php?report=yes';
    var dataval = 'year='+year_str+'&month='+month_str+'&day='+day_str+'&priceonly=1&type='+ type + (type != 'total' ? '&detailcategory=1' : '');
    var category = '';
    
    var rtrn = $.ajax({
            url : url,
            type: "POST",
            data: dataval,
            cache: false,
            async: true,
            dataType: "html",
        });

    return rtrn;
}

function get_monthly_data(year_str, month_str) {
    var url = '/cloud-fortis/user/index.php?report=yes';
    var dataval = 'year='+year_str+'&month='+month_str+'&forbill=1&user=All';
    var category = '';
    
    var rtrn = $.ajax({
            url : url,
            type: "POST",
            data: dataval,
            cache: false,
            async: true,
            dataType: "html",
        });

    return rtrn;
}

function parseDate(d, format) {

    if (format == 'Y') {
        return d.getFullYear();
    } else if (format == 'm') {
        return d.toLocaleString("en-us", {month: "numeric"});
    } else if (format == 'd') {
        return d.toLocaleString("en-us", {day: "numeric"});
    }  else if (format == 'mon') {
        return d.toLocaleString("en-us", {month: "short"});
    } else if (format == 'Y-M-D') {
        return d.getFullYear() + '-' + d.toLocaleString("en-us", {month: "2-digit"}) + '-' + d.toLocaleString("en-us", {day: "2-digit"});
    } else {
        return '';
    }
}

function to_num(currency) { // convert $currency to number
    return Number(currency.replace(/[^0-9\.-]+/g,""));
}