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
	#demo-set-btn {
		display: none;
	}
	.c3-chart-arcs text { fill: #000; }
	/*
	.c3-chart-arcs-background { // for gauge chart background color 
		fill: #dfdfdf;
		stroke: none;
	}
	*/
	.panel-body {
		padding: 5px 20px;
	}
	.eventcount {
		top: 5px;
		display:block;
		text-align: center;
	}
	.eventico {
		top: 0;
	}
	.eventword {
		top: 0;
	}
	.eventbox {
		cursor: pointer;
		margin-left: 1.7%;
		margin-top: 15px;
		text-align: center;
		width: 31%;
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
		red: 'rgb(255, 99, 132)',
		orange: 'rgb(255, 159, 64)',
		yellow: 'rgb(255, 205, 86)',
		green: 'rgb(75, 192, 192)',
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
		chartColors.orange
	];

	$(document).ready(function(){

		var flagmain = true;
		var sizes = ["{mempercent}%", "{swappercent}%", "{hddpercent}%"];
		var esxstorages = "{esxstoragespercent}";
		// index page actions:	
		//if (flagmain == true) {
		//	 $('.progress-bar').each(function(i) {
		//		 i.target.css('width', sizes[i]);
		//		//$(this).css('width', sizes[i]);
		//	 });
			// --- end progress animation ---
		// }

		// givedashboard(month, year);
		var memtotal = parseFloat({memtotal}).toFixed(0);
		var memused = parseFloat({memused}).toFixed(0);
		var memfree = (memtotal - memused).toFixed(0);

		var stotal = parseFloat("{stotal}".split(" ")[0]).toFixed(1);
		var sused =  parseFloat("{sused}".split(" ")[0]).toFixed(1);
		var sfree = (stotal - sused).toFixed(1);

		var cputotal = parseInt({cpunumber});
		var cpuload = parseFloat({cpuload});
		var cpufree = (cputotal - cpuload).toFixed(2);

		var vmtotal = parseInt({allvmcount});
		var vmactive = parseInt({activeallvm});
		var vminactive = parseInt({inactiveallvm});

		var allfiles = parseInt({allfiles});
		var healthfiles = parseInt({healthfiles});
		var endangeredfiles = parseInt({endangeredfiles});
		var missingfiles = parseInt({missingfiles});

		make_c3('donut','disk', [["total",stotal],["free",sfree],["used",sused]], "GB");
		make_c3('donut','memory', [["total",memtotal],["free",memfree],["used",memused]], "MB");
		make_c3('donut','cpu', [["total",cputotal],["free",cpufree],["load",cpuload]], "");
		make_c3('donut','network', [["total",0],["free",0],["used",0]], "");

		get_event_status();
		get_cloud_charge_back();
		server_doughnut();

		make_c3('pie','vm',[["total",vmtotal],["inactive",vminactive],["active",vmactive]],"");
		make_c3('donut','storage',[["total files", allfiles],["health files",healthfiles],["endangered files",endangeredfiles],["missing files", missingfiles]],"");
	});
/*
	function givedashboard(month, year, user) {
		month = parseInt(month)

		switch(month) {
			case 0:
				monthcurrentname = 'January';
				monthlastname = 'December';
				monthcurrentnameajax = 'Jan';
				monthlastnameajax = 'Dec';
				yearcurrent = year;
				yearold = parseInt(year) - 1;
			break;
			
			case 1:
				monthcurrentname = 'February';
				monthlastname = 'January';
				monthcurrentnameajax = 'Feb';
				monthlastnameajax = 'Jan';
				yearcurrent = year;
				yearold = year;
			break;
			case 2:
				monthcurrentname = 'March';
				monthlastname = 'February';
				monthcurrentnameajax = 'Mar';
				monthlastnameajax = 'Feb';
				yearcurrent = year;
				yearold = year;
			break;
			case 3:
				monthcurrentname = 'April';
				monthlastname = 'March';
				 monthcurrentnameajax = 'Apr';
				monthlastnameajax = 'Mar';
				yearcurrent = year;
				yearold = year;
			break;
			case 4:
				monthcurrentname = 'May';
				monthlastname = 'April';
				monthcurrentnameajax = 'May';
				monthlastnameajax = 'Apr';
				yearcurrent = year;
				yearold = year;
			break;
			case 5:
				monthcurrentname = 'June';
				monthlastname = 'May';
				monthcurrentnameajax = 'Jun';
				monthlastnameajax = 'May';
				yearcurrent = year;
				yearold = year;
			break;
			case 6:
				monthcurrentname = 'July';
				monthlastname = 'June';
				monthcurrentnameajax = 'Jul';
				monthlastnameajax = 'Jun';
				yearcurrent = year;
				yearold = year;
			break;
			case 7:
				monthcurrentname = 'August';
				monthlastname = 'July';
				monthcurrentnameajax = 'Aug';
				monthlastnameajax = 'Jul';
				yearcurrent = year;
				yearold = year;
			break;
			case 8:
				monthcurrentname = 'September';
				monthlastname = 'August';
				monthcurrentnameajax = 'Sep';
				monthlastnameajax = 'Aug';
				yearcurrent = year;
				yearold = year;
			break;
			case 9:
				monthcurrentname = 'October';
				monthlastname = 'September';
				monthcurrentnameajax = 'Oct';
				monthlastnameajax = 'Sep';
				yearcurrent = year;
				yearold = year;
			break;
			case 10:
				monthcurrentname = 'November';
				monthlastname = 'October';
				monthcurrentnameajax = 'Nov';
				monthlastnameajax = 'Oct';
				yearcurrent = year;
				yearold = year;
			break;
			case 11:
				monthcurrentname = 'December';
				monthlastname = 'November';
				monthcurrentnameajax = 'Dec';
				monthlastnameajax = 'Nov';
				yearcurrent = year;
				yearold = year;
			break;
		}
		renderdash(user);
	}

	function renderdash(user) {

		$('#donutrender').html('');
		$('#barcharts').html('');
		$('#cloud-content').css('min-height', '400px');
		//var legendo = ''; 
		//var legendo = [{'label':'testone', 'value':60}, {'label':'testsecond', 'value':40}];
		//console.log(yearcurrent);
		//console.log(monthcurrentnameajax);
		var url = '/cloud-fortis/user/index.php?report=yes';
		var dataval = 'year='+yearcurrent+'&month='+monthcurrentnameajax+'&priceonly=0&detailcategory=1&userdash='+user;
		var category = '';
		
		$.ajax({
			url : url,
			type: "POST",
			data: dataval,
			cache: false,
			async: false,
			dataType: "html",
			success : function (data) {
				if (data != 'none') {
					category = data;
				}
			}
		});

		category = JSON.parse(category);

		var legendonut = [];
		legendonut.push({'label':'Networking', 'value':category.network});
		legendonut.push({'label':'Virtualisation', 'value':category.virtualisation});
		legendonut.push({'label':'Memory', 'value':category.memory});
		legendonut.push({'label':'CPU', 'value':category.cpu});
		legendonut.push({'label':'Storage', 'value':category.storage});
		var priceold = '';
		var pricethis = '';
		var url = '/cloud-fortis/user/index.php?report=yes';
		var dataval = 'year='+yearcurrent+'&month='+monthcurrentnameajax+'&priceonly=true&userdash='+user;
			
		$.ajax({
			url : url,
			type: "POST",
			data: dataval,
			cache: false,
			async: false,
			dataType: "html",
			success : function (data) {
				if (data != 'none') {
					pricethis = parseFloat(data);
				}
			}
		});

		var dataval = 'year='+yearold+'&month='+monthlastnameajax+'&priceonly=true';

		$.ajax({
			url : url,
			type: "POST",
			data: dataval,
			cache: false,
			async: false,
			dataType: "html",
			success : function (data) {
				if (data != 'none') {
					priceold = parseFloat(data);
				}
			}
		});

		var prognoseprice = pricethis;

		if ( pricethis < priceold ) {
			prognoseprice = (pricethis + priceold) / 2;
		}

		var elemento = 'donutrender';

		if (typeof(mainomaino) != 'undefined') {
			var elemento = 'donutrendermaino';
		}

		if (category.network != 0 || category.virtualisation !=0 || category.memory !=0 || category.storage != 0 || category.cpu != 0) {
			Morris.Donut({
				element: elemento,
				data: legendonut,
				colors: [
					'#a6c600',
					'#177bbb',
					'#afd2f0',
					"#1fa67a", "#ffd055", "#39aacb", "#cc6165", "#c2d5a0", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc"
				],
				resize:true
			});
		} else {
			$('#donutrender').html('<p class="nodatadonut">No information available for these dates</p>');
		}

		if (priceold !=0 || pricethis != 0) {
			Morris.Bar({
				barSizeRatio:0.3,
				element: 'barcharts',
				data: [
					{ y: monthlastname, a: priceold },
					{ y: monthcurrentname, a: pricethis },
					{ y: 'Forecast', a: prognoseprice },
				],
				barColors: ['#afd2f0', '#177bbb', '#a6c600'],
				xkey: 'y',
				ykeys: ['a'],
				labels: ['Price in $']
			});
		} else {
			$('#barcharts').html('<p class="nodatabars">No information available for these dates</p>');
		}

		$('td.storage').text(category.storage);
		$('td.cpu').text(category.cpu);
		$('td.memory').text(category.memory);
		$('td.networking').text(category.network);
		$('td.virtualisationb').text(category.virtualisation);
	}
*/
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

	function make_c3(type, binding, donutdata, units) {
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
					'total files': seriesColors[0],
					'Cloud Host': seriesColors[1],
					'OCH Host': seriesColors[2],
					'OCH VM': seriesColors[3],
					'health files': seriesColors[1],
					'endangered files': seriesColors[2],
					'missing files': seriesColors[3]
					/* paused: seriesColors[2] */
				},
				onclick: function (d, i) { console.log("onclick", d, i); },
				onmouseover: function (d, i) { 
					// console.log("onmouseover", d); 
					d3.select(bindto+' .c3-chart-arcs-title').node().innerHTML = donutdata[d.index][0] + ' ' + donutdata[d.index][1] + ' ' + units;
				},
				onmouseout: function (d, i) {
					// console.log("onmouseout", d, i); 
					d3.select(bindto+' .c3-chart-arcs-title').node().innerHTML = donutdata[0][0] + ' ' + donutdata[0][1] + ' ' + units;
				},
			},
			donut: {
				title: donutdata[0][0] + ' ' + donutdata[0][1] + ' ' + units,
				label: {
					format: function (value, ratio, id) {
						for (var k = 0; k < donutdata.length; k++) {
							if (donutdata[k][0] == id) {
								return donutdata[k][1] + ' ' + units;
							}
						}
						return '--'; 
					}
				}
			},
			pie: {
				label: {
					format: function (value, ratio, id) {
						for (var k = 0; k < donutdata.length; k++) {
							if (donutdata[k][0] == id) {
								return donutdata[k][1] + ' ' + units;
							}
						}
						return '--'; 
					}
				}
			},
			legend: {
				show: true,
				position: 'bottom'
			},
			transition: {
				duration: 1500
			},
			tooltip: {
				show: false
				/*
				format: {
					value: function (value, ratio, id, index) { return value + ' ' + units; }
				}
				*/
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

	function renderDonutLegend(values,units) {
		var legend = $('<ul>');
		var size = '';

		$.each(values, function(k,v) {
			legend.append(
				$('<li>').append(
					$('<div>').addClass('legend-tile').attr('style', 'background:' + seriesColors[k]).append(v[0] + " " + v[1] + " " + units)
				)
			);
		});
		return legend;
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

		/**
	 * Build server donut chart. Does not use jqplots build-in 
	 * legend due to lack of positioning options
	 */
	function server_doughnut() {
		var server_list = htvcenter.get_server_list();
		var server_values = [];
		var virtualization, virtualization_list = [];
		var hist = {};
		
		if(server_list != false && $('#chartdiv-inventory-server').length) {
			
			$.each(server_list, function(k,server){
				virtualization_list.push(server['appliance_virtualization']);
			});
			virtualization_list.map( function (a) { if (a in hist) hist[a] ++; else hist[a] = 1; } );
			$.each(hist, function(k,v){
				if (k == 'KVM VM (localboot)' || k == 'KVM VM') {
					k = 'OCH VM';
				}

				if (k == 'ESX VM (localboot)') {
					k = 'ESX VM';
				}
				
				if (k == 'KVM Host') {
					k = 'OCH Host';
				}

				server_values.push([k,v]);
			});
			//$.jqplot('chartdiv-inventory-server', [server_values], donutOptions);
			var values = renderDonutLegend(server_values);
			make_c3('donut','server', server_values, "");
		}
	}

</script>

<div id="prenutanix">
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Maestro Storage</h3>
				</div>
				<div class="panel-body" style="height: 27.6rem;">
					
					<div class="row">
						<table class="table" style="margin-bottom: 0;">
							<tr><td>Total Files</td><td>{allfiles}</td><td>Health Files</td><td>{healthfiles}</td></tr>
							<tr><td>Endangered Files</td><td>{endangeredfiles}</td><td>Missing Files</td><td>{missingfiles}</td></tr>
						</table>
					</div>
					<div class="row">
						<div id="chartdiv-inventory-storage" class="c3-chart col-lg-12 pad-no" style="height: 19.5rem;"></div>
					</div>

					<!--
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
					-->
				</div>
			</div>
			<!--
			<div class="panel networkpanel">
						<script>
							var physicalpercent = {physicalpercent};
							var bridgepercent = {bridgepercent};
							var okchart = 'okkk';
						</script>

						<h2 class="dash"><i class="fa fa-signal"></i> Maestro Network:</h2>
						<div id="networkarea">
						<div class="row">
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 networkpie">
						<div id="demo-sparkline-pie" class="box-inline "></div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div id="sparklineinfo">
							<div class="donut-chart-legend">
								<ul>
									<li><div class="legend-tile" style="background:#2d4859"><span>Physical ({physcount})</span></div></li>
									<li><div class="legend-tile" style="background:#fe7211"><span>Bridge ({bridgecount})</span></div></li>
								</ul>
							</div>
						</div>
						</div>
						</div>
						/*  {devicelist} */
						</div>
				
			</div>
			<div class="panel storagespanel">
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
			-->
			<div id="demo-panel-network" class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Datacenter Load</h3>
				</div>
				<!--Morris line chart placeholder-->
				<div style="height:27.6rem;">
					<div id="morris-chart-network" class="morris-full-content"></div>
					<!--Chart information-->
					<div class="panel-body bg-primary" style="position:relative;z-index:2;">
						<div class="row">
							<!-- <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
								<div class="row"> -->
									<div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 pad-no">
										<!--Datacenter stat-->
										<div class="pad-ver media">
											<div class="media-left">
												<span class="icon-wrap icon-wrap-xs">
													<i class="fa fa-building-o fa-2x"></i>
												</span>
											</div>
											<div class="media-body">
												<p class="h3 text-thin media-heading datacenterp"></p>
											</div>
											<div class="text-center">
												<small class="text-uppercase signserv">Datacenter</small>
											</div>
										</div>
										<!--Progress bar-->
										<div class="progress progress-xs progress-dark-base mar-no">
											<div class="progress-bar progress-bar-light datacenterpbar"></div>
										</div>
									</div>
									<div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 pad-no">
										<!--Server stat-->
										<div class="pad-ver media">
											<div class="media-left">
												<span class="icon-wrap icon-wrap-xs">
													<i class="fa fa-server fa-2x"></i>
												</span>
											</div>
											<div class="media-body">
												<p class="h3 text-thin media-heading serverp"></p>
											</div>
											<div class="text-center">
												<small class="text-uppercase signserv">Server Load</small>
											</div>
										</div>
										<!--Progress bar-->
										<div class="progress progress-xs progress-dark-base mar-no">
											<div class="progress-bar progress-bar-light serverpbar"></div>
										</div>
									</div>
									<div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 pad-no">
										<!--Datacenter stat-->
										<div class="pad-ver media">
											<div class="media-left">
												<span class="icon-wrap icon-wrap-xs">
													<i class="fa fa-hdd-o fa-2x"></i>
												</span>
											</div>
											<div class="media-body">
												<p class="h3 text-thin media-heading storagep"></p>
											</div>
											<div class="text-center">
												<small class="text-uppercase signserv">Storage network</small>
 											</div>
										</div>
										<!--Progress bar-->
										<div class="progress progress-xs progress-dark-base mar-no">
											<div class="progress-bar progress-bar-light storagepbar"></div>
										</div>
									</div>
								<!-- </div>
							</div> -->
						</div>
					</div>
				</div>
			</div>

			<div class="panel">
				<!-- Start: Event table -->
				<div class="panel-heading">
					<h3 class="panel-title">{events_headline}</h3>
				</div>
				<a>
					<div id="eventsboxes" class="panel-body row" style="height: 27.6rem;">
						<div id="warningeventbox" class="eventbox col-xs-12 col-md-4 col-sm-4 col-lg-4" style="min-height: 13rem;">
							<span class="eventcount" id="events_active"></span>
							<i class="fa fa-envelope eventico"></i>
							<span class="eventword">messages</span>
						</div>

						<div id="erroreventbox" class="eventbox col-xs-12 col-md-4 col-sm-4 col-lg-4" style="min-height: 13rem;">
							<span class="eventcount" id="events_critical"></span>
							<i class="fa fa-exclamation-triangle eventico"></i>
							<span class="eventword">&nbsp;errors&nbsp;</span>
						</div>

						<div id="messageeventbox" class="eventbox col-xs-12 col-md-4 col-sm-4 col-lg-4" style="min-height: 13rem;">
							<span class="eventcount" id="events_messages"></span>
							<i class="fa fa-bell eventico"></i>
							<span class="eventword">all events</span>
						</div>
					</div>
				</a>
			</div>

			<div class="panel" style="display:none;">
				<!-- Start: Quicklink section -->
				<!--
					{quicklinks_headline}
					{quicklinks}
				//-->
			
				<!-- Start: Datacenter load current -->
				<h2 class="dash">
					{load_headline}
					<small>{load_current}</small>
				<!--
				<span class="pull-right">
					<a class="widget-action refresh-load-current" href="#">
						<span class="halflings-icon refresh"><i></i></span>
					</a>
				</span>
				-->
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
			</div>
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
							<div id="chartdiv-inventory-memory" class="c3-chart col-lg-12 pad-no" style="height: 21.5rem;"></div>
							<!-- <div id="chartdiv-inventory-memory-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>

					<div class="col-sm-12 col-md-6 col-lg-3 dashboard pull-left">
						<div class="panel-heading">
							<h3 class="panel-title">Services</h3>
						</div>
						<div class="row">
							<div id="chartdiv-inventory-cpu" class="c3-chart col-lg-12 pad-no" style="height: 21.5rem;"></div>
							<!-- <div id="chartdiv-inventory-cpu-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>

					<div class="col-sm-12 col-md-6 col-lg-3 dashboard pull-left">
						<div class="panel-heading">
							<h3 class="panel-title">Services</h3>
						</div>
						<div class="row">
							<div id="chartdiv-inventory-disk" class="c3-chart col-lg-12 pad-no" style="height: 21.5rem;"></div>
							<!-- <div id="chartdiv-inventory-disk-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>

					<div class="col-sm-12 col-md-6 col-lg-3 dashboard pull-left">
						<div class="panel-heading">
							<h3 class="panel-title">Services</h3>
						</div>
						<div class="row">
							<div id="chartdiv-inventory-network" class="c3-chart col-lg-12 pad-no" style="height: 21.5rem;"></div>
							<!-- div id="chartdiv-inventory-network-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>
				</div>
			</div>

			<!-- Start: Inventory overview -->
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Datacenter Summary</h3>
				</div>
				<div class="panel-body row" style="height: 27.6rem;">
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<div class="panel-heading">
							<h3 class="panel-title">Hosts</h3>
						</div>
						<div class="row">
							<div id="chartdiv-inventory-server" class="c3-chart col-lg-12 pad-no" style="height: 21.5rem;"></div>
							<!-- <div id="chartdiv-inventory-cpu-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
						<div class="panel-heading">
							<h3 class="panel-title">VM Summary</h3>
						</div>
						<div class="row">
							<div id="chartdiv-inventory-vm" class="c3-chart col-lg-12 pad-no" style="height: 21.5rem;"></div>
							<!-- <div id="chartdiv-inventory-cpu-legend" class="donut-chart-legend col-lg-4"></div> -->
						</div>
					</div>
					<!--
					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 withlegend">
						<div id="chartdiv-inventory-server-legend" class="donut-chart-legend"></div>	
					</div>
					-->
					<!--
					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 withchart vm-summary-chart">
						<div class="panel panel-bordered-primary allvmmain">
							<div class="panel-heading"><h3 class="panel-title">VM Summary </h3></div>
							<div class="panel-body">
								<div class="row">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<div class="esxileft"><b>{allvmcount}</b> <br><span>VMs</span></div>
									</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 esxiright">
										<div class="vmsside">
											<span class="roundbullet greenbullet"></span><b>{activeallvm}</b> active<br>
											<a href="index.php?report=report_inactive"><span class="roundbullet yellowbullet"></span><b>{inactiveallvm}</b> inactive</a><br>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 withlegend vm-summary-legend">
						<div id="chartdiv-inventory-server-storage" class="donut-chart-legend"></div>
					</div> -->

				</div>
			</div>

			<!--===================================================-->
			<!--End network line chart-->
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Cloud Charge Back</h3>
				</div>

				<div class="panel-body row" style="height: 27.6rem;">
					<div id="current-year-monthly-spent-by-resource" style="height: 24rem;"></div>
				<!--
					<div class="mainuserdash text-center row">
						<div class="col-xs-4 col-sm-4 col-lg-4 col-md-4 text-center">
						<select id="reportuserdashmain">
							{hidenuser}
						</select>
						</div>
						<div class="col-xs-4 col-sm-4 col-lg-4 col-md-4 text-center">
							<select id="reportmonthdashmain">
								<option value="0">January</option>
								<option value="1">February</option>
								<option value="2">March</option>
								<option value="3">April</option>
								<option value="4">May</option>
								<option value="5">June</option>
								<option value="6">July</option>
								<option value="7">August</option>
								<option value="8">September</option>
								<option value="9">October</option>
								<option value="10">November</option>
								<option value="11">December</option>
							</select>
						</div>
						 <div class="col-xs-4 col-sm-4 col-lg-4 col-md-4 text-center">
								 <select id="reportyeardashmain">{reportyear}</select>
						 </div>
					</div>
					<div class="maindonutrenderrr">
						<div id="donutrendermaino">
						</div>
						<div id="totalamauntmain">
							<b>Total Amount:</b> <span id="mval"></span>
						</div>
					</div>
					-->
				</div>
			</div>

		</div>

		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="display:none">
			<div class="panel">
			<!-- Start: Datacenter load chart -->
			<h2 class="dash">{load_headline}
				<small>{load_last_hour}</small>
				<!--
				<span class="pull-right">
					<a class="widget-action refresh-load-chart" href="#">
						<span class="halflings-icon refresh"><i></i></span>
					</a>
				</span>
				-->
			</h2>	
			<div id="chartdiv-load" style="height:220px; width:100%;"></div>
			</div>
		</div>
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