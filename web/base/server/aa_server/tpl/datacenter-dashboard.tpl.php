<!--
/*
	htvcenter Enterprise developed by HTBase Corp.

	All source code and content (c) Copyright 2015, HTBase Corp unless specifically noted otherwise.

	This source code is released under the htvcenter Enterprise Server and Client License, unless otherwise agreed with HTBase Corp.

	By using this software, you acknowledge having read this license and agree to be bound thereby.

				http://www.htbase.com

	Copyright 2015, HTBase Corp <contact@htbase.com>
*/
-->
<link type="text/css" href="{baseurl}/css/c3/c3.min.css" rel="stylesheet">
<style>
	.row {
		margin-left: -12px;
		margin-right: -12px;
	}
	#demo-set-btn {
		display: none;
	}
	.c3-chart-arcs text { fill: #000; }
	.c3-chart-arcs-title {
		font-size: 1.8em;
		font-weight: 600;
	}
	.c3-line {
		stroke-width: 3px;
	}
	/*
	.c3-chart-arcs-background { // for gauge chart background color 
		fill: #dfdfdf;
		stroke: none;
	}
	*/
	.panel-body {
		padding: 5px 20px;
	}
	#eventsboxes span {
		height: 7.7rem;
		vertical-align: middle;
		display: table-cell;
		padding: 0 5px;
	}
	#eventsboxes span i {
		margin-left: 15px;
	}
	.eventcount {
		top: 0px;
		display:block;
		text-align: center;
		height: 5.7rem;
		cursor: pointer;
	}
	.eventico {
		top: 0;
	}
	.eventword {
		top: 0;
	}
	.media-heading {
		margin-top: 5px;
		margin-bottom: 5px;
	}
	.media-left {
		padding-right: 0px;
	}
	#container .row .table {
		margin-bottom: 0px;
	}
	#container .row .table tr td {
		border-top: 0px;
		border-bottom: 1px solid rgba(0, 0, 0, 0.05);
	}
	.progress-bar-light {
		background: transparent;
	}
	.panel-body .row {
		margin-left: -12px;
		margin-right: -12px;
	}
	hr { 
		display: inline-block;
		margin-top: 0.5em;
		margin-bottom: 0.5em;
		margin-left: 0.3em;
		margin-right: 0;
		border-style: solid;
		border-width: 2px;
		width: 24px;
		float: right;
	}
    hr.health-files {
        border-color: rgb(72, 204, 132);
    }
    hr.total, hr.health-files, hr.inactive {
        border-color: #dfdfdf;
    }
    hr.cloud-host, hr.active {
        border-color: #41bee9 ;
    }
    hr.endangered-files, hr.och-host {
        border-color: rgb(255, 99, 132);
    }
    hr.missing-files, hr.och-vm {
        border-color: rgb(255, 205, 86);
    }
    hr.networking {
        border-color: rgb(75, 192, 192);
    }
    hr.nextthing {
        border-color: rgb(153, 102, 255);
    }
</style>
<script src="{baseurl}/js/c3/d3.v3.min.js" type="text/javascript"></script>
<script src="{baseurl}/js/c3/c3.min.js" type="text/javascript"></script>
<script src="{baseurl}/js/fetch-cloud-report.js" type="text/javascript"></script>
<script type="text/javascript">
	var mainomaino = true;
	//<![CDATA[
	var lang_inventory_servers = "{lang_inventory_servers}";
	var lang_inventory_storages = "{lang_inventory_storages}";
	var diagramshow = true;
	var d = new Date();
	var month = d.getMonth(); 
	var numyear = d.getYear();
	var year = d.getFullYear();
	var monthcurrentname = '';
	var monthlastname = '';
	var yearcurrent = '';
	var yearold = '';
	var charts = [];

	var chartColors = {
		red: 'rgb(251, 67, 74)',
		orange: 'rgb(255, 159, 64)',
		yellow: 'rgb(255, 225, 86)',
		green: 'rgb(72, 204, 132)',
		blue: 'rgb(54, 162, 235)',
		purple: 'rgb(153, 102, 255)',
		grey: 'rgb(201, 203, 207)',
		teal: 'rgb(172,205,236)',
		moss: 'rgb(167,182,27)'
	};

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

	$(document).ready(function(){

		//var flagmain = true;
		//var sizes = ["{mempercent}%", "{swappercent}%", "{hddpercent}%"];
		//var esxstorages = "{esxstoragespercent}";
		
		//if (flagmain == true) {
		//	 $('.progress-bar').each(function(i) {
		//		 i.target.css('width', sizes[i]);
			//$(this).css('width', sizes[i]);
		//	 });
			// --- end progress animation ---
		// }

		// givedashboard(month, year);

		var hosts = {hosts};
		var vms = {vms};

		var memfree = parseInt({memavailable});
		var memused = parseInt({memconsumed});
		var memtotal = memfree + memused;

		var stotal = parseFloat("{stotal}".split(" ")[0]).toFixed(1);
		var sused =  parseFloat("{sused}".split(" ")[0]).toFixed(1);
		var sfree = (stotal - sused).toFixed(1);

		var cputotal = parseInt({cpuavailable});
		var cpuload = parseInt({cpuconsumed});
		var cpufree = cputotal - cpuload;

		var vmtotal = parseInt({allvmcount});
		var vmactive = parseInt({activeallvm});
		var vminactive = parseInt({inactiveallvm});

		var allfiles = parseInt({allfiles});
		var healthfiles = parseInt({healthfiles});
		var endangeredfiles = parseInt({endangeredfiles});
		var missingfiles = parseInt({missingfiles});

		make_c3('donut','Disk', [["total",stotal],["free",sfree],["used",sused]], "GB", true);
		make_c3('donut','Memory', [["total",memtotal],["used",memused],["free",memfree]], "MB", true);
		make_c3('donut','CPU', [["total",cputotal],["free",cpufree],["used",cpuload]], "", true);
		make_c3('donut','Network', [["total",0],["free",0],["used",0]], "", true);

		get_event_status();
		get_cloud_charge_back();
		make_c3('donut','server', hosts, "", 'right');
		make_c3('donut','storage',[["total", allfiles],["health files",healthfiles],["endangered files",endangeredfiles],["missing files", missingfiles]],"", 'right');

		make_vmstable(vmactive, vminactive, vms);

		datacenter_load();
		setInterval(datacenter_load, 10000);
	});

	function get_event_status() {

		$.ajax({
			url: "api.php?action=get_top_status",
			cache: false,
			async: false,
			dataType: "text",
			success: function(response) {
				if(response != '') {
					var status_array = response.split("@");
					var event_error = parseInt(status_array[6]);
					var event_active = parseInt({allcountwarnings});
					var events_messages = event_active + event_error;

					$("#events_critical").html(event_error);
					$('#events_active').html(event_active);
					$("#events_messages").html(events_messages);
					
					if (event_active == 0) {
						$('.badge-warning').hide();
					} else {
						$('.badge-warning').html(event_active);
					}

					if (event_error == 0) {
						$('.badge-danger').hide();
					} else {
						$('.badge-danger').html(event_error);
					}

					if (events_messages == 0) {
						$('.badge-purple').hide();
					} else {
						$('.badge-purple').html(events_messages);
					}
				}
			}
		});
		setTimeout("get_event_status()", 5000);
	}

	function format_label(data) {
		var units = ['TB','GB','MB'];
		var idx = units.indexOf(data[1]);

		if (data[0] > 1024 && idx > 0) {
			return parseFloat(data[0] / 1024).toFixed(1) + " " + units[idx - 1];
		} else {
			return data[0] + " " + data[1];
		}
	}

	function make_c3(type, binding, donutdata, unit, showlegend) {
		var max_val = Math.max(donutdata[0][1],donutdata[1][1]);
		var min_val = max_val / 18;
		var data = [];
		var values = [];
		var bindto = "#chartdiv-inventory-" + binding; 
		$('#chartdiv-inventory-'+binding).closest('.dashboard').find('.panel-title').text(binding);

		for (var i = 0; i < donutdata.length; i++) {
			if (donutdata[i][0] != 'total') {
				data.push([donutdata[i][0],Math.max(donutdata[i][1],min_val)]);
				values.push(donutdata[i][1]);
			}
		} 

		if (Math.max.apply(null, values) == 0) { // force drawing a %100 pie or dunut when all values are zero
			data[0][1] = 1;
		}

		var chart = c3.generate({
			bindto: bindto,
			data: {
				columns: data,
				type: type,
				colors: {
					free: seriesColors[0],
					inactive: seriesColors[0],
					used: seriesColors[1],
					active: seriesColors[1],
					load: seriesColors[1],
					'Cloud Host': seriesColors[1],
					'OCH Host': seriesColors[4],
					'OCH VM': seriesColors[3],
					'health files': seriesColors[4],
					'endangered files': seriesColors[2],
					'missing files': seriesColors[3]
					/* paused: seriesColors[2] */
				},
				onclick: function (d, i) { console.log("onclick", d, i); },
				onmouseover: function (d, i) { 
					for (var k = 0; k < donutdata.length; k++) {
						if (donutdata[k][0] == d.name) {
							d3.select(bindto+' .c3-chart-arcs-title').node().innerHTML = format_label([donutdata[k][1], unit]);
						}
					}
				},
				onmouseout: function (d, i) {
					d3.select(bindto+' .c3-chart-arcs-title').node().innerHTML = format_label([donutdata[0][1], unit]);
				},
			},
			donut: {
				title: format_label([donutdata[0][1], unit]),
				label: {
					format: function (value, ratio, id) {
						for (var k = 0; k < donutdata.length; k++) {
							if (donutdata[k][0] == id) {
								return format_label([donutdata[k][1], unit]);
							}
						}
						return '--'; 
					}
				}
			}, /*
			pie: {
				label: {
					format: function (value, ratio, id) {
						for (var k = 0; k < donutdata.length; k++) {
							if (donutdata[k][0] == id) {
								return donutdata[k][1] + ' ' + unit;
							}
						}
						return '--'; 
					}
				}
			}, */
			legend: {
				show: showlegend,
				position: (showlegend == 'right' ? 'right' : 'bottom')
			},
			transition: {
				duration: 1500
			},
			tooltip: {
				show: false,
				/*  data onmouseout and data onmouseout is doing similar things already
				format: {
					value: function (value, ratio, id, index) { return value + ' ' + unit; }
				} */
			},
			padding: {
				left: 100 // add 100px for some spacing
			},
		});
		/* code to customize legend
		d3.select(bindto).selectAll('.c3-legend-item').select('text').each(function () {
			var legend = d3.select(this);
			legend.text(legend.text().replace("_"," "));
		}); */
		charts.push(chart);
	}

	function parse_label(str) {
		return str.replace("KVM", "OCH").replace(" (localboot)","").replace(" (networkboot)","");
	}

	function make_vmstable(vmactive, vminactive, vms) {
		var html = '<tr><td>Active<hr class="active"></td><td>'+vmactive+'</td><td>Inactive<hr class="inactive"></td><td>'+vminactive+'</td></tr>';
		
		for (var i = 0; i < Math.floor((vms.length + 1) / 2); i++) {
			var label = parse_label(vms[i * 2][0]);
			html += '<tr><td>'+label+'<hr class="' + label + '"></td><td>' + vms[i * 2][1] + '</td>';
			
			if (i * 2 + 1 <= vms.length - 1) { 
				label =  parse_label(vms[i * 2 + 1][0]);
				html += '<td>'+label+'<hr class="' + label + '"></td><td>' + vms[i * 2 + 1][1] + '</td>';
			} else {
				html += '<td></td><td></td>';
			}
			html += '</tr>';
		}
		$("#vmstable tbody").append(html);
	}

	function datacenter_load() {
		var bindto = "datacenter-loading";
		var stats = htvcenter.get_datacenter_load();
		var dc_load = [['overall'],['server'],['storage']];
		var xaxis_labels = ['x']; 
		var idx;
		
		if(stats != null) {
			$.each(stats, function(k,v) {
				// idx = parseInt(k)+1;
				xaxis_labels.push(parseInt(k));
				dc_load[0].push(parseFloat(v['datacenter_load_overall'])+0.01);
				dc_load[1].push(parseFloat(v['datacenter_load_server']));
				dc_load[2].push(parseFloat(v['datacenter_load_storage']));
			});
		}
		make_c3_timeseries(bindto, [xaxis_labels, dc_load[0], dc_load[1], dc_load[2]]);
		// setTimeout("datacenter_load", 10000);
	}

	function make_c3_timeseries(bindto, data) {

		$("#"+bindto).empty();

		var chart2 = c3.generate({
			bindto: '#'+bindto,
			data: {
				x: 'x',
				columns: data,
				type: 'spline',
				colors: {
					overall:	seriesColors[4],
					server:		seriesColors[5],
					storage:	seriesColors[1],
				}
			},
			axis: {
				x: {
					label: {
						text: 'mins last hour'
					},
					tick: {
						format: d3.format('.1f')
					}
				},
				y: {
					label: {
						text: 'loading'
					},
					tick: {
						format: d3.format('.2f')
					}
				}
			},
			grid: {
				y:  {
					show: true
				}
			},
			point: {
				show: false
			},
			tooltip: {
				show: false
			}
		});
		// return chart2;
	}

	function get_cloud_charge_back() {
		var this_month = new Date();
		var curr_month = new Date();
		this_month.setDate(1);
		curr_month.setDate(1);
		var column_x_yearly  = ['x'];
		var deferred = [];
		var total_monthly   = ['total'];
		var cpu_monthly     = ['cpu'];
		var storage_monthly = ['storage'];
		var memory_monthly  = ['memory'];
		var virtual_monthly = ['virtualization'];
		var network_monthly = ['networking'];
		
		for (var i = 0; i <= this_month.getMonth(); i++) {
			curr_month.setMonth(i);
			curr_month.setDate(1);
			column_x_yearly.push(parseDate(curr_month,'Y-M-D'));
			deferred.push(get_monthly_data(parseDate(curr_month,'Y'), parseDate(curr_month,'mon')));
		}

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

			// current_year_monthly_spent("#current-year-monthly-spent", [column_x_yearly, total_monthly]);
			var chart = current_year_monthly_spent_by_resource("#current-year-monthly-spent-by-resource", [column_x_yearly, cpu_monthly, storage_monthly, memory_monthly, virtual_monthly, network_monthly]);
			
			charts.push(chart);
		});
	}
</script>

<div id="prenutanix">
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Maestro Storage</h3>
				</div>
				<div class="panel-body" style="min-height: 27.6rem;">
					<div class="panel-heading">
					</div>
					<div class="row">
						<div class="col-lg-12 pad-no">
							<div id="chartdiv-inventory-storage" class="c3-chart pad-no" style="height: 19.5rem;"></div>
						</div>
						<!--
						<div class="col-ss-12 col-md-5 pad-no">
							<table class="table table-bordered table-hover table-stripped" style="width: 100%;">
								<tbody>
									<tr><td>Health Files<hr class="health-files"></td><td>{healthfiles}</td></tr>
									<tr><td>Endangered Files<hr class="endangered-files"></td><td>{endangeredfiles}</td></tr>
									<tr><td>Missing Files<hr class="missing-files"></td><td>{missingfiles}</td></tr>
								</tbody>
							</table>
						</div>
						-->
					</div>
					<div style="display: none;">
						<h4><i class="fa fa-cogs"></i> CPU & memory:</h4>
						<ul class="storage-list">
							<li><b>CPU:</b> {cpu}</li>
							<li><b>Memory used:</b> {memused}</li>
							<li><b>Memory total:</b> {memtotal}</li>
						</ul>
						<div class="progress memoryprogress">
							<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {mempercent}%;">
								<span class="sr-only">{mempercent}% used</span>
							</div>
						</div>
						<ul class="storage-list">
							<li><b>Swap used:</b> {swapused}</li>
							<li><b>Swap total:</b> {swaptotal}</li>
						</ul>
						<div class="progress memoryprogress swapprogress">
							<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {swappercent}%;">
								<span class="sr-only">{swappercent}% used</span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel storagespanel" style="display: none;">
				<h2 class="dash"><i class="fa fa-hdd-o"></i> Maestro Storage</h2>
				<div id="storageareas">
					<div class="onestorage">
						<div class="row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<div class="esxileft leftstorageblock">
									{sfree}<br>
									<span>free (physical)</span>
								</div>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 esxright sizeroside">
								<div class="totalinfor">
									<b>Used:</b> {sused}<br>
									<b>Total:</b> {stotal} <br>
								</div>
							</div>
							<div class="progress hddprogress nutanixprogress">
								<div style="width: {spercent}%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class="progress-bar">
									<span class="sr-only">{spercent}% used</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Datacenter Load</h3>
				</div>
				<div class="panel-body" style="height:27.6rem;">
					<div class="row">
						<div id="datacenter-loading" class="c3-chart pad-no" style="height: 26.6rem;"></div>
					</div>
				</div>
			</div>

			<div class="panel">
				<!-- Start: Event table -->
				<div class="panel-heading">
					<h3 class="panel-title">{events_headline}</h3>
				</div>
				<a>
					<div id="eventsboxes" class="panel-body row" style="min-height: 27.6rem;">
						<div id="warningeventbox" style="height: 7.5rem; margin: 1rem;">
							<div class="col-xs-2 pad-no">
								<span><i class="fa fa-envelope eventico fa-2x"></i></span>
							</div>
							<div class="col-xs-10 pad-no">
								<span class="eventcount" id="events_active"></span>
								<span class="eventword">messages</span>
							</div>
						</div>
						<div id="erroreventbox" style="height: 7.5rem; margin: 1rem;">
							<div class="col-xs-2 pad-no">
								<span><i class="fa fa-exclamation-triangle eventico fa-2x"></i></span>
							</div>
							<div class="col-xs-10 pad-no">
								<span class="eventcount" id="events_critical"></span>
								<span class="eventword">errors</span>
							</div>
						</div>
						<div id="messageeventbox" style="height: 7.5rem; margin: 1rem;">
							<div class="col-xs-2 pad-no">
								<span><i class="fa fa-bell eventico fa-2x"></i></span>
							</div>
							<div class="col-xs-10 pad-no">
								<span class="eventcount" id="events_messages"></span>
								<span class="eventword">all events</span>
							</div>
						</div>
					</div>
				</a>
			</div>
			<!--
			<div class="panel" style="display:none;">
				<h2 class="dash">
					{load_headline}
					<small>{load_current}</small>
				</h2>
				<table class="table">
					<tr>
						<td class="width0">{datacenter_load_overall}</td>
						<td>
							<div class="bar-01 chart-bar ">
								<div class="bar">
									<label>0.43</label>
								</div>
								
							</div>
						</td>
					</tr>
					<tr>
						<td>{appliance_load_overall}</td>
						<td>
							<div class="bar-02 chart-bar">
								<div class="peak"></div>
								<div class="bar">
									<label>0.43</label>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>{storage_load_overall}</td>
						<td>
							<div class="bar-03 chart-bar">
								<div class="peak"></div>
								<div class="bar">
									<label>0.43</label>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div
			-->
		</div>

		<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
			<!--Network Line Chart-->
			<!--===================================================-->
			<!-- <div id="demo-panel-network" class="panel" style="min-height: 34.7rem;">  -->
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Resource Consumption</h3>
				</div>
				<div class="panel-body row" style="min-height: 27.6rem;">
					<div class="col-sm-12 col-md-6 col-lg-3 dashboard pull-left">
						<div class="panel-heading">
							<h3 class="panel-title">Services</h3>
						</div>
						<div class="row">
							<div id="chartdiv-inventory-Memory" class="c3-chart pad-no" style="height: 21.5rem;"></div>
							<!-- <div id="chartdiv-inventory-memory-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>

					<div class="col-sm-12 col-md-6 col-lg-3 dashboard pull-left">
						<div class="panel-heading">
							<h3 class="panel-title">Services</h3>
						</div>
						<div class="row">
							<div id="chartdiv-inventory-CPU" class="c3-chart pad-no" style="height: 21.5rem;"></div>
							<!-- <div id="chartdiv-inventory-cpu-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>

					<div class="col-sm-12 col-md-6 col-lg-3 dashboard pull-left">
						<div class="panel-heading">
							<h3 class="panel-title">Services</h3>
						</div>
						<div class="row">
							<div id="chartdiv-inventory-Disk" class="c3-chart pad-no" style="height: 21.5rem;"></div>
							<!-- <div id="chartdiv-inventory-disk-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>

					<div class="col-sm-12 col-md-6 col-lg-3 dashboard pull-left">
						<div class="panel-heading">
							<h3 class="panel-title">Services</h3>
						</div>
						<div class="row">
							<div id="chartdiv-inventory-Network" class="c3-chart pad-no" style="height: 21.5rem;"></div>
							<!-- div id="chartdiv-inventory-network-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>
				</div>
			</div>
			<!-- Start: Inventory overview -->
			<div class="row">
				<div class="col-sm-12 col-md-6 col-lg-6">
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">Host Summary</h3>
						</div>
						<div class="panel-body" style="min-height: 27.6rem;">
							<div class="panel-heading">
							</div>
							<div class="row">
								<div class="col-sm-12 col-md-12 pad-no">
									<div id="chartdiv-inventory-server" class="c3-chart pad-no" style="height: 21.5rem;"></div>
								</div>
								<!--
								<div class="col-sm-12 col-md-5 pad-no">
									<table id="datacenter-tbl" class="table table-bordered table-hover table-stripped" style="width: 100%;">
										<tbody>
											<tr><td>Cloud Host<hr class="cloud-host"></td><td></td></tr>
											<tr><td>OCH Host<hr class="och-host"></td><td></td></tr>
											<tr><td>OCH VM<hr class="och-vm"></td><td></td></tr>
										</tbody>
									</table>
								</div>
								-->
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-md-6 col-lg-6">
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">VM Summary</h3>
						</div>
						<div class="panel-body" style="min-height: 27.6rem;">
							<div class="panel-heading">
							</div>
							<div class="row">
								<!--
								<div class="col-sm-12 col-md-7 pad-no">
									<div id="chartdiv-inventory-vm" class="c3-chart pad-no" style="height: 21.5rem;"></div>
								</div>
								-->
								<div class="col-xs-12">
									<table id="vmstable" class="table table-bordered table-hover table-stripped" style="width: 100%;">
										<tbody>
										<!--
											<tr><td>Inactive<hr class="inactive"></td><td>{inactiveallvm}</td></tr>
											<tr><td>Active<hr class="active"></td><td>{activeallvm}</td></tr> -->
											<!-- <tr><td>Inactive<hr class="endangered-files"></td><td></td></tr> -->
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--===================================================-->
			<!--End network line chart-->
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Cloud Charge Back</h3>
				</div>

				<div class="panel-body row" style="height: 27.6rem;">
					<div id="current-year-monthly-spent-by-resource" style="height: 26.6rem;"></div>
				</div>
			</div>
		</div>
<!--
		<div class="col-sm-12 col-md-6 col-lg-6">
			<div class="panel">
			<h2 class="dash">{load_headline}
				<small>{load_last_hour}</small>
				<span class="pull-right">
					<a class="widget-action refresh-load-chart" href="#">
						<span class="halflings-icon refresh"><i></i></span>
					</a>
				</span>
			</h2>
			<div id="chartdiv-load" style="height:220px; width:100%;"></div>
			</div>
		</div>
-->
	</div> <!-- <div class="row"> -->
</div> <!-- <div id="prenutanix"> -->

<div id="nutanix">
	<h2 id="closenutanix"><i class="fa fa-close"></i> CLOSE</h2>
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
			<div class="window esxi panel panel-bordered-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Hypervisor Summary</h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<div class="esxileft">
								<b>ESXi</b> <br/>
								<span>hypervisor</span>
							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 esxiright">
							<div class="esxright sizeroside ttt">
								<b>{esxversion}</b> <br/>
								<span>version</span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="window storagemainwindow panel panel-bordered-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Storage Summary</h3>
				</div>
				<div class="panel-body">
					{summary}
				</div>
			</div>

			<div class="panel panel-bordered-primary">
				<div class="panel-heading">
					<h3 class="panel-title">VM Summary </h3>
					<span class="badge badge-info bbadger">
						<a class="serversdetail" href="index.php?base=appliance">Servers detail</a>
					</span>
				</div>

				<div class="panel-body">
					<div class="row">
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<div class="esxileft">
								<b>{esxvmcount}</b> <br/>
								<span>VMs</span>
							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 esxiright">
							<div class="vmsside">
								<b>{esxvmactive}</b> active<br/>
								<b>{esxvminactive}</b> inactive<br/>
								<b>{esxvmimport}</b> import
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-bordered-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Hardware Summary</h3>
					 <span class="badge badge-info bbadger"><a class="esxhostsdetail" href="index.php?plugin=vmware-esx&controller=vmware-esx-vm">Hosts detail</a></span>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<div class="esxileft">
								<b>{esxhosts}</b> <br/>
								<span>Hosts</span>
							</div>
						</div>
					<!--
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="esxileft">
							<b>1</b> <br/>
							<span>Block</span>
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 esxright">
						<div class="hardwr vmsside">
							<b>MX3050</b><br/>
							<span>model</span>
						</div>
					</div>
				-->
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
			<div class="window criticaleventwindow panel panel-bordered-pink">
				<div class="panel-heading">
					<div class="panel-control">
						<i class="fa fa-exclamation-triangle fa-lg fa-fw text-pink"></i>
						<span class="badge badge-pink">{esxerrorcount}</span>
					</div>
					<h3 class="panel-title">Critical Alerts</h3>
				</div>
				<div class="eventcontent">
					{esxeventerrors}
				</div>
				<div class="linkeventside pinko">
					<a href="index.php?base=event">View all alerts</a>
				</div>
			</div>

			<div class="window warningeventwindow panel panel-bordered-warning">
				<div class="panel-heading">
					<div class="panel-control">
						<i class="fa fa-bell fa-lg fa-fw text-warning"></i>
						<span class="badge badge-warning">{esxwarningcount}</span>
					</div>
					<h3 class="panel-title">Warning Alerts</h3>
				</div>
				<div class="eventcontent">
					{esxeventwarnings}
				</div>
				<div class="linkeventside warningo">
					<a href="index.php?base=event">View all alerts</a>
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
			<div class="window alleventswindow panel panel-bordered-info">
				<div class="panel-heading">
					<div class="panel-control">
						<i class="fa fa-envelope fa-lg fa-fw text-info"></i>
					</div>
					<h3 class="panel-title">Events</h3>
				</div>
				<div class="eventcontent">
					{esxeventsall}
				</div>
				<div class="linkeventside evento">
					<a href="index.php?base=event">View all events</a>
				</div>
			</div>
		</div>
	</div>
</div>


<div id="preeventsall" style="display: none">{preeventsall}</div>
<div id="preeventserror" style="display: none">{preeventserror}</div>
<div id="preeventsnotice" style="display: none">{preeventsnotice}</div>