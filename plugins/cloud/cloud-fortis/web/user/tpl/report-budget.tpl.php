<style>
	#project_tab_ui { display: none; }  /* hack for tabmenu issue */
	/*
	tbody {
		display: inline-block;
		width: 100%;
		overflow: auto;
		/* height: 15.4rem; 
	}
	tr {
		width: 100%;
		display: table;
	}
	::-webkit-scrollbar {
	width: 5px;
	}
	 
	::-webkit-scrollbar-track {
		-webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.3); 
		border-radius: 3px;
	}
	 
	::-webkit-scrollbar-thumb {
		border-radius: 3px;
		-webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.5); 
	}
	.plan .plan-title {
		font-size: 1.5em;
		font-weight: 100;
	}
	.plan .plan-icon {
		padding: 0.5em 0;
		opacity: 0.4;
	}
	*/
</style>
<link href="/cloud-fortis/css/jquery.steps.css" rel="stylesheet" type="text/css">
<script src="/cloud-fortis/js/c3/d3.v3.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/c3/c3.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/chartjs/Chart.bundle.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/chartjs/utils.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/fetch-report.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/jquery.steps.js" type="text/javascript"></script>
<script>
	var budgetpage = true;
	var datepickeryep = true;

function submitallprice() {

	var id = $('#budgetid').val();
	var name = $('#budgetname').val();
	var date_start = $('#budgetdatestart').val();
	var date_end = $('#budgetdateend').val();
	var cpu = $('#budgetcpu').val();
	var memory = $('#budgetmemory').val();
	var storage = $('#budgetstorage').val();
	var networking = $('#budgetnetwork').val();
	var vm = $('#budgetvm').val();
	var perval = Array();

	$('.perval').each(function(i){
		perval[i] = $(this).text();
	});

	cpu = parseInt(cpu);
	if (cpu == 'NaN') {
		cpu = 0;
	}

	memory = parseInt(memory);
	if (memory == 'NaN') {
		memory = 0;
	}

	storage = parseInt(storage);
	if (storage == 'NaN') {
		storage = 0;
	}

	networking = parseInt(networking);
	if (networking == 'NaN') {
		networking = 0;
	}

	vm = parseInt(vm);
	if (vm == 'NaN') {
		vm = 0;
	}

	id = parseInt(id);
	if (id == 'NaN') {
		id = 0;
	}

	var action = (id > 0 ? "update" : "create");
	var url = '/cloud-fortis/user/index.php?budget=yes';
	var dataval = action+'=1&name='+name+'&limit='+perval+'&date_start="'+date_start+'"&date_end="'+date_end+'"&cpu='+cpu+'&memory='+memory+'&storage='+storage+'&networking='+networking+'&vm='+vm+(id > 0 ? '&id='+id : '');
	
	$.ajax({
		url : url,
		type: "POST",
		data: dataval,
		cache: false,
		async: false,
		dataType: "html",
		success : function (data) {
			if (data.indexOf("Abort") > -1) {
				alert('Failed to create or update a budget.  Please contact the Administrator.');
			} else {
				location.reload();
			}
		}
	});
}

$(document).ready(function() {

	var url = '/cloud-fortis/user/index.php?budget=yes';
	var dataval = 'getbudgets=1';
	var bds = '';

	$.ajax({
		url : url,
		type: "POST",
		data: dataval,
		cache: false,
		async: false,
		dataType: "html",
		success : function (data) {
			if (data != 'none') { 
				bds = JSON.parse(data);
			} else {
				$("#budgets-setting").append("<span><strong>No Budget has been set up.</strong></span>");
			}
		}
	});

	var i = 0;
	var budgets = [];

	$.each(bds, function(key, serv) {
		$('#budgets').append('<option key="' + key + '" value="' + serv.id +  '" ' + (i == 0 ? 'selected' : '') + '>&nbsp;<strong>' + serv.name + '</strong>&nbsp;(' + serv.date_start + '~' + serv.date_end + ')&nbsp;</option>');

		var html =	'<table id="resources-' +  key + '" class="table table-hover table-stripped budget-resources"' + (i > 0 ? ' style="display:none;" ' : '') + '><tbody>';
		html	+=		'<tr><td style="width: 40%"><strong>' + "CPU:" +			'</strong></td><td class="cpu">'	+ serv.cpu +	'</td></tr>';
		html	+=		'<tr><td style="width: 40%"><strong>' + "Storage:" + 		'</strong></td><td class="storage">'+ serv.storage+	'</td></tr>';
		html	+=		'<tr><td style="width: 40%"><strong>' + "Memory:" + 		'</strong></td><td class="memory">'	+ serv.memory +	'</td></tr>';
		html	+=		'<tr><td style="width: 40%"><strong>' + "Virtualization:" +	'</strong></td><td class="vm">'		+ serv.vm +		'</td></tr>';
		html	+=		'<tr><td style="width: 40%"><strong>' + "Network:" +		'</strong></td><td class="network">'+ serv.network +'</td></tr>';
		html	+=		'<tr style="display: none"><td></td><td class="datestart">'	+ serv.date_start	+	'</td><tr>';
		html	+=		'<tr style="display: none"><td></td><td class="dateend">'	+ serv.date_end 	+	'</td><tr>';
		html	+=		'<tr style="display: none"><td></td><td class="id">'		+ serv.id			+	'</td><tr>';
		html	+=		'<tr style="display: none"><td></td><td class="name">'		+ serv.name			+	'</td><tr>';
		html	+=	'</tbody></table>';

		$("#budgets-setting").append(html);

		var html_alerts = '';

		if (serv.havealerts) {
			html_alerts =	'<table id="alerts-' +  key + '" class="table table-hover table-stripped budget-alerts"' + (i > 0 ? ' style="display:none;" ' : '') + '>';
			html_alerts +=		'<tbody><tr><td style="width: 50%"><strong>' + "% of Budget" + '</strong></td><td><strong>' + "Action" + '</strong></td></tr>';

			for (j = 0; j < serv.alerts.length; j++) {
				html_alerts +=	'<tr><td style="width: 50%">' + serv.alerts[j] + '</td><td><a href="#"><i class="fa fa-minus-circle" style="color:red"></i>&nbsp;Remove</a></td></tr>';
			}
			html_alerts	+=	'</tbody></table>';
		} else {
			html_alerts = 	'<label id="alerts-' + key + '" ' + (i > 0 ? ' style="display:none;" ' : '') + ' class="budget-alerts"><strong>There is no alert set up for this budget.</strong></label>';
		}

		$("#budgets-alert").append(html_alerts);

		i++;
	});

	var form = $("#create-budget-form");

	form.validate({
		errorPlacement: function errorPlacement(error, element) { element.after(error); },
		rules: {
			field: {
				required: true,
				number: true
			}
		}
	}); 

	form.steps({
		headerTag: "h3",
		bodyTag: "section",
		transitionEffect: "slideLeft",
		onInit: function(event, currentIndex)
		{
			$(".date").datepicker({
				icons: {
					date: "fa fa-calendar",
				}
			});
		},
		onStepChanging: function (event, currentIndex, newIndex)
		{
			form.validate().settings.ignore = ":disabled,:hidden";

			return $("#create-budget-form input").valid();
		},
		onFinished: function (event, currentIndex)
		{
			submitallprice();
		}
	});

	$("#create-budget-modal").on('hidden.bs.modal', function () {
		$(this).find("input[type=text]").val("");
		$(this).find("input[type=hidden]").val("");
		/* hacky reset steps */
		$("#create-budget-form-t-0").trigger("click");
		$("#create-budget-form .steps ul li:not(:first)").removeClass("done").addClass("disabled");
		/* end hacky reset steps */
	});

	$("#delete-budget-modal").on('shown.bs.modal', function () {
		$("#confirm-budget-name").text($("#budgets option:selected").text());
	});

	function refresh_alert_table() {
		var rows =  $("#table-alerts tr");

		if (rows.length <= 1) {
			$('#table-alerts').hide();
		} else {
			$('#table-alerts').show();
		}
	}

	$('#alertprice').click(function(){
		var percent = parseInt($('#percentbudg').val());
		
		if ( (percent != 'NaN') && (percent <= 100) ) {
			var matched = $("#table-alerts td.perval").filter(function () {
				return $(this).text() == $('#percentbudg').val();
			});

			if (matched.length > 0) {
				alert('The '+percent+"% alert was added previously.");
			} else {
				var row = '<tr><td class="perval">'+percent+'</td><td> <a class="remove-row"><i class="fa fa-close"></i> Remove</a></td></tr>';
				$('#table-alerts').append(row);

				$('.remove-row').on('click', function(){
					$(this).closest('tr').remove();
					refresh_alert_table();
				});
			}
		} else {
			alert('Please input an integer between 0 to 100.');
		}
		refresh_alert_table();
	});

	$("#budgets").on("change", function () {
		var key = $(this).find("option:selected").attr("key");

		$(".budget-resources").hide();
		$(".budget-alerts").hide();
		$("#resources-"+key).show();
		$("#alerts-"+key).show();

		plot_budget_total(bds[key]);
	});

	function plot_budget_total(serv) {
		var total_budget = parseInt(serv.cpu) + parseInt(serv.storage) + parseInt(serv.memory) + parseInt(serv.vm) + parseInt(serv.network);
		var column_x = ['x'];
		var total_y = ['total budgeted'];

		var date_loop	= new Date(serv.date_start);
		var date_end	= new Date(serv.date_end);

		while (date_loop <= date_end) {
			column_x.push(parseDate(date_loop, "Y-M-D"));
			total_y.push(total_budget);
			date_loop.setDate(date_loop.getDate() + 15);
		}
		time_series_chart("#budget-vs-spent-chart", [column_x, total_y]);
	}

	function time_series_chart(bindto, data) {
		var chart = c3.generate({
			bindto: bindto,
			data: {
				x: 'x',
				columns: data,
				type: 'spline',
				color: function (color, d) {
					return seriesColors[d.index];
				},
			},
			axis: {
				x:  {
					type: 'timeseries',
					tick: {
						format: '%Y-%m-%d'
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

	$("#create-budget").click(function() {
		$("#create-budget-modal modal-header h3").text("Create A New Budget");
		$("#create-budget-modal").modal("show");
	});

	$("#edit-budget").click(function() {
		var resources_key = "resources-" + $("#budgets option:selected").attr("key");
		$("#" + resources_key + " tr td:nth-of-type(2)").each( function (){
			$("#budget" + $(this).attr('class')).val($(this).text());
		});
		$("#create-budget-modal modal-header h3").text("Edit Budget");
		$("#create-budget-modal").modal("show");
	});

	$('#delete-budget-confirm').click(function(){
		$('#delete-budget-modal').modal("hide");
		var budget_id = $("#budgets").val();
		var url = '/cloud-fortis/user/index.php?budget=yes';
		var dataval = 'rembudgets=1&remid='+budget_id;
		var bds = '';

		$.ajax({
			url : url,
			type: "POST",
			data: dataval,
			cache: false,
			async: true,
			dataType: "html",
			success : function (data) {
				if (data == 'remdone') {
					alert('Budget deleted succesfully.');
					location.reload();
				} 
			}
		});
	});
});
</script>

<div class="cat__content">
	<cat-page>
	<div class="row" id="chart-row">
		<div class="col-sm-12">
			<section class="card">  
				<div class="card-header">
					<span class="cat__core__title d-inline-block" style="min-width: 650px;">
						<label class="d-inline"><strong>Budget Planning</strong></label>
						<select id="budgets" class="form-control d-inline" style="max-width: 19rem;"></select>
						&nbsp;<a href="#" id="edit-budget"><span class="small"><u><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit Budget</u></span></a>
						&nbsp;<a href="#" id="delete-budget" data-toggle="modal" data-target="#delete-budget-modal"><span class="text-danger small"><u><i class="fa fa-eraser" aria-hidden="true"></i>&nbsp;Delete Budget</u></span></a>
					</span>
					<div class="pull-right d-inline-block">
					<a id="create-budget" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;Create Budget</a>
					</div>
				</div>
				<div class="card-block">
					<div class="row">
						<div class="col-sm-4 dashboard">
							<section class="card">  
								<div class="card-block">
									<div class="panel-heading">
										<div class="panel-control">
											<h3 class="panel-title">Budget Resources</h3>
										</div>
									</div>
									<div>
										<div id="budgets-setting" style="height: 15rem;">
										</div>
									</div>
								</div>
							</section>
							<section class="card">  
								<div class="card-block">
									<div class="panel-heading">
										<div class="panel-control">
											<h3 class="panel-title">Budget Alerts</h3>
										</div>
									</div>
									<div>
										<div id="budgets-alert" style="height: 15rem;">
										</div>
									</div>
								</div>
							</section>
						</div>
						<div class="col-sm-8 dashboard">
							<section class="card">  
								<div class="card-block">
									<div class="panel-heading">
										<div class="panel-control">
											 <h3 class="panel-title">Spent vs Budget</h3>
										</div>
									</div>
									<div>
										<div id="budget-vs-spent-chart" style="height: 34.7rem;">
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

<div id="delete-budget-modal" class="modal" data-backdrop="static" style="display: none;" aria-hidden="true">
	<div class="modal-content">
		<div class="modal-header">
			<h3 class="text-black">Confirm Deleting A Budget</h3>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<p>Are you sure you want to delete this budget?&nbsp;<span id="confirm-budget-name" class="text-danger"><strong></strong></span></p>
		</div>
		<div class="modal-footer">
			<button data-dismiss="modal" class="btn btn-sm btn-default" type="button">Close</button>
			<button id="delete-budget-confirm" class="btn btn-sm btn-danger" type="button">Confirm Delete</button>
		</div>
	</div>
</div>


<div id="create-budget-modal" class="modal" data-backdrop="static" style="display: none;" aria-hidden="true">
	<div class="modal-content">
		<div class="modal-header">
			<h3 class="text-black">Create A New Budget</h3>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<form id="create-budget-form">
				<input type="hidden" id="budgetid">
				<h3>Name & Timeframe</h3>
					<section>
						<div class="col-lg-12">
							<div class="form-group">
								<label for="budgetname">Budget Name</label>
								<input type="text" class="form-control" id="budgetname" required>
							</div>
							<div class="form-group">
								<label for="budgetdatestart">Budget Start Date</label>
								<div class="input-group date">
									<span class="input-group-addon">
										<i class="fa fa-calendar" aria-hidden="true"></i>
									</span>
									<input type="text" class="form-control" id="budgetdatestart" required>
								</div>
							</div>
							<div class="form-group">
								<label for="budgetdateend">Budget End Date</label>
								<div class="input-group date">
									<span class="input-group-addon">
										<i class="fa fa-calendar" aria-hidden="true"></i>
									</span>
									<input type="text" class="form-control" id="budgetdateend" required>
								</div>
							</div>
						</div>
					</section>
				<h3>Budget Limits</h3>
					<section>
						<div class="col-lg-12">
							<div class="row">
								<div class="col-sm-4 col-md-4 col-lg-4 col-xs-4 selectype">
									<div class="panel plan">
										<div class="panel-body">
											<span class="plan-title">CPU</span>
											<div class="plan-icon">
												<!-- <i class="fa fa-desktop"></i> -->
												<img src="/cloud-fortis/img/cpu_s.png" style="max-width: 72px; height: auto;" /> 
											</div>
											<p class="text-muted pad-btm">
												<label for="budgetcpu">Monthly Price Limit in $: </label>
												<input type="text" name="input" id="budgetcpu" required class="number">
											</p>
										</div>
									</div>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-4 col-xs-4 selectype">
									<div class="panel plan">
										<div class="panel-body">
											<span class="plan-title">Memory</span>
											<div class="plan-icon">
											<img src="/cloud-fortis/img/memory_s.png" style="max-width: 72px; height: auto;" /> 
												<!-- <i class="fa fa-database"></i>  -->
											</div>
											<p class="text-muted pad-btm">
												<label for="budgetmemory">Monthly Price Limit in $: </label>
												<input type="text" name="input" id="budgetmemory" required class="number">
											</p>
										</div>
									</div>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-4 col-xs-4 selectype">
									<div class="panel plan">
										<div class="panel-body">
											<span class="plan-title">Storage</span>
											<div class="plan-icon">
												<i class="fa fa-hdd-o"></i>
											</div>
											<p class="text-muted pad-btm">
												<label for="budgetstorage">Monthly Price Limit in $: </label>
												<input type="text" name="input" id="budgetstorage" required class="number">
											</p>
										</div>
									</div>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-4 col-xs-4 selectype">
									<div class="panel plan">
										<div class="panel-body">
											<span class="plan-title">Networking</span>
											<div class="plan-icon">
												<!-- <i class="fa fa-globe"></i> -->
												<img src="/cloud-fortis/img/network_s.png" style="max-width: 72px; height: auto;" />
											</div>
											<p class="text-muted pad-btm">
												<label for="budgetnetwork">Monthly Price Limit in $: </label>
												<input type="text" name="input" id="budgetnetwork" required class="number">
											</p>
										</div>
									</div>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-4 col-xs-4 selectype">
									<div class="panel plan">
										<div class="panel-body">
											<span class="plan-title">Virtualization</span>
											<div class="plan-icon">
												<!-- <i class="fa fa-cloud"></i> -->
												<img src="/cloud-fortis/img/virtualization_s.png" style="max-width: 72px; height: auto;" />
											</div>
											<p class="text-muted pad-btm">
												<label for="budgetvm">Monthly Price Limit in $: </label>
												<input type="text" name="input" id="budgetvm" required class="number">
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
				<h3>Alerts</h3>
					<section>
						<div class="col-lg-12">
							<div class="panel plan">
								<div class="panel-body">
									<div class="form-group">
										<span>Notify me when costs exceed&nbsp;</span><input type="text" id="percentbudg" number>d<span>&nbsp;% of budgeted costs&nbsp;</span>
										<div class="d-inline pull-right"><a class="btn btn-sm btn-primary" id="alertprice">Create Alert</a></div>
									</div>
								</div>
								<div class="panel-body">
									<table class="table table-bordered table-stripped table-hover" id="table-alerts" style="display: none;">
										<tbody>
											<tr class="header"><th>% of budget</th><th>Action</th></tr>
										</tbody>
									</table>
								</div>
								<div style="display: none;">
									<a href="#" class="submitallprice">Submit</a> 
								</div>
							</div>
						</div>
					</section>
			</form>
		</div>
	</div>
</div>

