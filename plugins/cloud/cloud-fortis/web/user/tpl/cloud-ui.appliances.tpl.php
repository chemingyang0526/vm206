<!--
/*
    htvcenter Enterprise developed by htvcenter Enterprise GmbH.

    All source code and content (c) Copyright 2014, htvcenter Enterprise GmbH unless specifically noted otherwise.

    This source code is released under the htvcenter Enterprise Server and Client License, unless otherwise agreed with htvcenter Enterprise GmbH.
    The latest version of this license can be found here: http://htvcenter-enterprise.com/license

    By using this software, you acknowledge having read this license and agree to be bound thereby.

                http://htvcenter-enterprise.com

    Copyright 2014, htvcenter Enterprise GmbH <info@htvcenter-enterprise.com>
*/
-->
<link href="/cloud-fortis/designplugins/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
<link href="/cloud-fortis/css/jquery.steps.css" rel="stylesheet" type="text/css">
<link href="/cloud-fortis/css/normalize.css" rel="stylesheet" type="text/css">
<link href="/cloud-fortis/css/ion.rangeSlider.css" rel="stylesheet" type="text/css">
<link href="/cloud-fortis/css/ion.rangeSlider.skinHTML5.css" rel="stylesheet" type="text/css">
<!--
<link href="/cloud-fortis/css/owl.carousel.min.css" rel="stylesheet" type="text/css">
<link href="/cloud-fortis/css/owl.theme.default.min.css" rel="stylesheet" type="text/css">
-->
<link href="/cloud-fortis/css/slick.css" rel="stylesheet" type="text/css">
<link href="/cloud-fortis/css/slick-theme.css" rel="stylesheet" type="text/css">

<script src="/cloud-fortis/js/vender/dataTables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/jquery.steps.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/ion.rangeSlider.js" type="text/javascript"></script>
<!--
<script src="/cloud-fortis/js/owl.carousel.min.js" type="text/javascript"></script>
-->
<script src="/cloud-fortis/js/slick.min.js" type="text/javascript"></script>

<style>
	#project_tab_ui { display: none; }  /* hack for tabmenu issue */
	/*  table.dataTable tbody tr.hide { display: none; } */
	/* table.dataTable tbody tr.hide td { height: 0px; } */
	/* table.dataTable tbody tr.even { height: 140px; } */
	table.dataTable .thead-default th { background-color: rgb(255,255,255); }
	table.dataTable tbody td { padding-left: 18px; padding-right: 18px; } /* for aligning with thead */
	table.dataTable tbody td i { display: block; text-align: center; }
	table.dataTable tbody td.hide { display: none; } 
	table.dataTable.table-hover tbody tr:hover { background-color: rgb(189,199,231); }


	/*table.dataTable tbody tr.even { background-color: rgb(228,233,240); }  */
	table.dataTable tbody td section.card { margin-bottom: 0; }
	table.dataTable tbody td.status.active { color: rgb(112,173,71); }
	table.dataTable tbody td.status.inactive { color: red; }
	table.dataTable .dropdown ul.dropdown-menu { padding: 3px 8px; min-width: 5em; }
	.c3-graph { height: 179px; }
	.d-inline.pull-left { margin: 3px 0; padding: 0; text-align: left; background: inherit; }
	.d-inline-block.pull-left { margin: 0; padding: 0; }
	.card-header .d-inline-block span { margin: 0 15px; }
	.card-header i { display: inline !important; }

	.wizard .form-group .item img, .wizard .form-group .item label, .wizard .form-group .item input.checkbox {
		display: block;
		text-align: center;
		margin: 0.2rem auto;
	}

	.wizard .form-group .item input.checkbox {
		width: 20px;
	}

	.wizard .owl-carousel.owl-theme > .item {
		display: inline-block;
		width: 10.5rem;
		height: 10.5rem;
		margin-bottom: 1.1rem;
		text-align: center;
	}

	.wizard .owl-carousel.owl-theme > .item.hide {
		display: none;
	}
	/*
	.wizard .owl-carousel.owl-theme {
		padding: 1.3rem 1.3rem;
	}
	*/
	.wizard label {
		font-weight: bold;
	}

	.wizard > .content > .body ul.slick-dots {
		list-style: none;
	}

	.wizard > .content > .body ul.slick-dots > li {
		display: inline-block;
	}
	/*
	#owl-nav-container .owl-prev, #owl-nav-container .owl-next {
		display: inline-block;
		font-size: 0;
		width: 21px;
		height: 34px;
		margin: 0 auto;
		-webkit-border-radius: 2px; 
		-moz-border-radius: 2px; 
		border-radius: 2px; 
		background: url(../img/arrows.png) no-repeat;
		z-index: 10;
		margin-top: 52px; 
		cursor: pointer;
	}
	*/
	/*
	#owl-nav-container .owl-prev {
		float: left;
		background-position: -260px -43px;
	}

	#owl-nav-container .owl-next {
		float: right;
		background-position: -320px -43px;
	}
	*/
	#summary-tab label {
		min-width: 7.8em;
		max-width: 35em;
		display: inline-block;
		font-weight: normal;
		vertical-align: top;
	}

	.form-group.hide {
		display: none;
	}

	.popover li.list-group-item {
		cursor: pointer;
	}

	.popover li.list-group-item:hover {
		color: #0190fe;
	}

	/*
	select, textarea, input {
		background-color: #fff;
		background-image: none;
		border: 1px solid #ccc;
		height: 1.5em; 
		min-width: 75px;
		border-radius: 2px;
		text-align: center; 
	} */
</style>
<script src="/cloud-fortis/js/c3/d3.v3.min.js" type="text/javascript"></script>
<script src="/cloud-fortis/js/c3/c3.min.js" type="text/javascript"></script>
<script type="text/javascript">
/* run DataTable.js */
var hostname = '';
var cdromlist = [];

$(document).ready(function() {

	function loadAppliancesTable() {
		var url = "/cloud-fortis/user/index.php?cloud_ui=appliances&table=appliances_table";

		$.ajax({
			url : url,
			type: "GET",
			cache: false,
			async: true,
			dataType: "html",
			success : function (data) {
				$('.popover').popover('hide');
				$("#table-appliances").empty().append(data);
				// $("#cloud_appliances").load(function () {

					var dt = $("#cloud_appliances").DataTable( {
						"columns": [
							{ "visible": false },
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							null,
							{ "orderable": false }
						],
						"order": [], //  [[0, 'asc']],
						"bLengthChange": false,
						"pageLength": 10,
						"search": {
							"regex": true
						},
						"bAutoWidth": true,
						"fnDrawCallback": function( oSettings ) {
							$(".toggle-graph a").popover({
								html: true,
								placement: "bottom", /*
								content: function() {
									return $('#popover-content').html();
								} */
							});

							$(".toggle-graph a").on("shown.bs.popover", function() {

								hostname = $(this).data('hostname');
								console.log("212 hostname: "+hostname);

								$('.cdrom').click(function(){

									// hostname = $(this).closest('tr').find('.hostnamee').text();
									// hostnameglobal = hostname;
									//if ( $(this).text() == 'Insert CD' ) {
										var action = 'getlist';
										cdromsender(hostname, action);
										
										$('#isofilez').html('');
										for( i in cdromlist ) {
											var file = cdromlist[i];
											var filerow = '<tr class="htmlobject_tr odd last"><td class="htmlobject_td name"><a href="#" class="file">'+file+'</a></td></tr>';
											$('#isofilez').append(filerow);
										}
										
										$('#filepicker').show();
									//}

								});

								$('.cdromeject').click(function() {

									// hostname = $(this).closest('tr').find('.hostnamee').text();
									// hostnameglobal = hostname;
									if ( $(this).text() == 'Eject CD' ) {
										var action = 'eject';
										cdromsender(hostname, action);
									}
								});

								$('.filepickclose').click(function(){
									hostname = '';
								});

								$('.file').on('click', function() {
									var filetext = $(this).text();
									cdromsender(hostname, 'insert', filetext);
								});

								$("li.list-group-item").on("click", function () { 

									var title = $(this).find("span").text();
									var rel = $(this).attr("rel");
									var isVolumesmpopup = $(this).hasClass('editvolumesmpopup');

									if (rel && !isVolumesmpopup) {
										$("#confirm-appliance-actions").modal('show');

										$.ajax({
											url : rel,
											type: "GET",
											cache: false,
											async: true,
											dataType: "html",
											success : function (data) {
												$("#confirm-appliance-actions h3").text(title);
												$("#confirm-appliance-actions .modal-body").empty().append(data);
											}
										});
										return false; // to prevent two click events
									} else {
										if (isVolumesmpopup) {
											// hostname = rel;
											$("#modal-volume").modal('show');

											var url = "/cloud-fortis/user/index.php?cloud_ui=appliances&action=volumedata&hostname=" + hostname;
											
											$.ajax({
												url : url,
												type: "GET",
												cache: false,
												async: true,
												dataType: "html",
												success : function (data) {

													$("#modal-volume h3").text("Edit Volumes");
													$("#modal-volume .modal-body table tbody").empty().append(data);
												
													var num = '';
													var linktext = '';

													$('.voladd').on('click', function() {
														num = $(this).closest('tr').attr('num');
														linktext = $(this).text();
														$('#modal-volumeadd').modal();
													});

													$('#addvolumebtnvv').click(function(){
														$('#modal-volumeadd').modal('hide');
														var sizevol = $('#volumeselect').val();
														var url = "/cloud-fortis/user/index.php?cloud_ui=appliances&action=volumedataadd&hostname="+hostname+"&num="+num+"&sizevol="+sizevol;
														
														$.ajax({
															url : url,
															type: "GET",
															cache: false,
															async: true,
															dataType: "html",
															success : function (data) {
																if (data == 'no disk space') {
																	alert('You have not got free space for this volume creation');
																	$('#modal-volume').modal('hide');
																	return;
																}
																$("#modal-volume .modal-body table tbody").empty().append(data);
																
																alert('Volume created succesful');
															}
														}); 
														//setInterval(updatevolumes(linktext), 20000);
													});
												}
											});
											return false;
										} else {
											return true;
										}
									}
								});
							});
						}
					} );
				//});
			}
		}); 
	}

	$('#create-vm-modal').on('shown.bs.modal', function (e) {

		var url = '/cloud-fortis/user/index.php?cloud_ui=create_modal';

		$.ajax({
			url : url,
			type: "GET",
			cache: false,
			async: true,
			dataType: "html",
			success : function (data) {
				$("#create-vm-modal .modal-body").empty().append(data.slice(0,-20));
				$("#create-vm-modal .modal-body input").addClass("form-control").addClass("require").removeClass("text");
				$("#create-vm-modal .modal-body select").addClass("form-control").addClass("require");

				var form = $("#create-vm-form");

				$.validator.addMethod("HOSTNAME", function(value, element) {
					return this.optional(element) || /^[a-z0-9]{1,}[a-z0-9-]*[a-z0-9]$/i.test( value );
				}, 'Please use only alphanumeric characters for the name; Dash(-) is allowed only in the middle.');


				form.validate({
					errorPlacement: function errorPlacement(error, element) { element.before(error); },
					rules: {
						cloud_hostname_input: "HOSTNAME",
					}
				}); 

				form.children("div").steps({
					headerTag: "h3",
					bodyTag: "section",
					transitionEffect: "slideLeft",
					onStepChanging: function (event, currentIndex, newIndex)
					{
						form.validate().settings.ignore = ":disabled,:hidden";

						if (newIndex == 4) {
							makeSummary();
						}

						return form.valid();
					},
					onStepChanged: function(event, currentIndex, priorIndex)
					{
						// if (currentIndex == 3) {
						//	initiateOwlCarousel();
						// }
					},
					onFinishing: function (event, currentIndex)
					{
						form.validate().settings.ignore = ":disabled";

						$('<input />')
							.attr('type', 'hidden')
							.attr('name', 'response[submit]')
							.attr('data-message', '')
							.addClass("submit")
							.val("submit")
							.appendTo(form);

						return form.valid();
					},
					onFinished: function (event, currentIndex)
					{
						return form.submit();
					}
				}); 


				$("#cloud_disk_select").hide().after("<input class='hide' id='cloud_disk_input' type='text'></input>");
				$("#cloud_cpu_select").hide().after("<input class='hide' id='cloud_cpu_input' type='text'></input>");
				$("#cloud_memory_select").hide().after("<input class='hide' id='cloud_memory_input' type='text'></input>");

				var values_disk = $.map($('#cloud_disk_select option') ,function(option) { return (parseInt(option.value) / 1000); });
				var values_cpu = $.map($('#cloud_cpu_select option') ,function(option) { return option.value; });
				var values_memory = $.map($('#cloud_memory_select option') ,function(option) { return (parseInt(option.value) / 1024); }); 

				$("#cloud_disk_input").ionRangeSlider({
					hide_min_max: false,
					keyboard: true,
					min: values_disk[0],
					max: values_disk.slice(-1)[0],
					values: values_disk,
					type: 'single',
					step: 1,
					grid: true,
					postfix: " GB",
					onChange: function () {
						$("#cloud_disk_select").val(parseInt($("#cloud_disk_input").val()) * 1000);
					}
				});

				$("#cloud_cpu_input").ionRangeSlider({
					hide_min_max: false,
					keyboard: true,
					min: values_cpu[0],
					max: values_cpu.slice(-1)[0],
					values: values_cpu,
					type: 'single',
					step: 1,
					grid: true,
					onChange: function () {
						$("#cloud_cpu_select").val($("#cloud_cpu_input").val());
					}
				});

				$("#cloud_memory_input").ionRangeSlider({
					hide_min_max: false,
					keyboard: true,
					min: values_memory[0],
					max: values_memory.slice(-1)[0],
					values: values_memory,
					type: 'single',
					step: 1,
					grid: true,
					postfix: " GB",
					onChange: function () {
						$("#cloud_memory_select").val(parseInt($("#cloud_memory_input").val()) * 1024);
					}
				});

				function searchApp(txt) {
					var txt_lc = txt.toLowerCase();
					var found_match = false;
					

					$(".owl-carousel .item").each(function () {
						$(this).removeClass("matched");

						var app_name_lc = $(this).contents().filter(function() {
							return this.nodeType == 3;
						}).text().trim().toLowerCase();

						if (app_name_lc.indexOf(txt_lc) == -1) {
							$(this).addClass("hide");
						} else {
							$(this).removeClass("hide");
							found_match = true;
						}
					});

					if (!found_match) {
						$(".owl-carousel .item").removeClass("hide");
					} else {

					}
				}

				var delay = (function(){
					var timer = 0;
					return function(callback, ms){
						clearTimeout (timer);
						timer = setTimeout(callback, ms);
					};
				})();

				$("#search-app").keyup(function() {
					var txt = $(this).val();

					delay(function() {
						searchApp(txt);
					}, 300);
				});
			}
		});
	}); 



	function makeSummary() {
		var apps = [];
		var html = '';
		html += '<label>VM Name: </label><label>' + $("#cloud_hostname_input").val() + '</label><br/>';
		// html += '<label>VM Description: </label><label>' + $("#vmDesc").val() + '</label><br/>';
		html += '<label>VM Type: </label><label>' + $("#cloud_virtualization_select option:selected").text() + '</label><br/>';
		html += '<label>VM Iamge: </label><label>' + $("#cloud_image_select option:selected").text() + '</label><br/>';
		html += '<label>VM Kernel: </label><label>' + $("#cloud_kernel_select option:selected").text() + '</label><br/>';
		html += '<label>DIsk: </label><label>' + (parseInt($("#cloud_disk_select").val()) / 1000) + ' GB</label><br/>';
		html += '<label>CPU: </label><label>' + $("#cloud_cpu_select").val() + '</label><br/>';
		html += '<label>Memory: </label><label>' + (parseInt($("#cloud_memory_select").val()) / 1024) + ' GB</label><br/>';
		html += '<label>Network: </label><label>' + $("#cloud_ip_select_0 option:selected").text() + '</label><br/>';

		$(".owl-carousel div.item:not(.slick-cloned) input.checkbox:checked").each(function () {
			var elem = $(this).parent("div.item").first().contents().filter(function() {
							    return this.nodeType == 3;
							}).text().trim().toLowerCase();

			if (apps.indexOf(elem) == -1) {
				apps.push(elem); 
			}
		});
		html += '<label>Apps: </label><label>' + (apps.length > 0 ? apps.join(", ") : "No App Selected") + '</span><br/>';
		$("#summary-tab p").empty().append(html);
	}

	function setChecks(e) {
		
		$(".owl-carousel label").on('click',function () {
			var rel = $(this).attr("rel");

			$(".owl-carousel input.checkbox[name='"+rel+"']").each(function () {
				this.checked = !this.checked;
			});
		});

		$(".owl-carousel input.checkbox").on('click',function () {
			var rel = $(this).attr("name");
			var chckd = this.checked;

			$(".owl-carousel input.checkbox[name='"+rel+"']").each(function () {
				this.checked = chckd;
			});
		});
	}
/*
	function initiateOwlCarousel() {
		if ($(".owl-carousel.uninitiated").length > 0) {
			$(".owl-carousel.uninitiated").slick({
				dots: true,
				infinite: true,
				slidesToShow: 4
			});

			$(".owl-carousel").removeClass("uninitiated");
			setChecks();
		} else {
			$(window).trigger('resize'); 
		}
	}
*/
/*
	function format (d) {
		// `d` is the original data object for the row
		return	'<section class="card">'+
					'<div class="card-header">'+
						'<div class="d-inline-block col-sm-3 text-center">'+
							'<span>'+d[1]+'</span>'+
						'</div>'+
						'<div class="d-inline-block col-sm-9 text-center">'+
							'<span><a href="#"><i class="fa fa-play"></i> Start</a></span>'+
							'<span><a href="#"><i class="fa fa-stop"></i> Stop</a></span>'+
							'<span><a href="#"><i class="fa fa-server"></i> noVNC</a></span>'+
							'<span><a href="#"><i class="fa fa-cogs"></i> Edit Compute</a></span>'+
							'<span><a href="#"><i class="fa fa-pencil"></i> Edit Volume</a></span>'+
							'<span><a href="#"><i class="fa fa-eraser"></i> Remove</a></span>'+
						'</div>'+
					'</div>'+
					'<div class="card-block">'+
						'<div class="d-block col-sm-3 text-center pull-left">'+
							'<section class="card">'+
								'<div class="card-header">'+
									'<div class="bg-faded"><span><strong>VM Details</strong></span></div>'+
								'</div>'+
								'<div class="card-block">'+
									'<div class="bg-faded">'+
										'<div class="d-inline col-sm-6 pull-left"><strong>VM Name:</strong></div>'+
										'<div class="d-inline col-sm-6 pull-left">' + d[1] +'</div>'+
									'</div>'+
									'<div class="bg-faded">'+
										'<div class="d-inline col-sm-6 pull-left"><strong>VM IP Address</strong></div>'+
										'<div class="d-inline col-sm-6 pull-left">' + d[2] +'</div>'+
									'</div>'+
									'<div class="bg-faded">'+
										'<div class="d-inline col-sm-6 pull-left"><strong>Image Name:</strong></div>'+
										'<div class="d-inline col-sm-6 pull-left">' + d[3] +'</div>'+
									'</div>'+
									'<div class="bg-faded">'+
										'<div class="d-inline col-sm-6 pull-left"><strong>Requested by:</strong></div>'+
										'<div class="d-inline col-sm-6 pull-left">' + d[8] +'</div>'+
									'</div>'+
								'</div>'+
							'</section>'+
						'</div>'+
						'<div class="d-block col-sm-8 text-center pull-left">'+
							'<div id="cpu-usage-' + d[0] + '" class="c3-graph col-sm-6 d-inline-block pull-left"></div>'+
							'<div id="memory-usage-' + d[0] + '" class="c3-graph col-sm-6 d-inline-block pull-left"></div>'+
						'</div>'+
					'</div>'+
				'</section>';

	}
*/
	// reload the table every 5 seconds
	function reloadAppliancesTable() {
	    var t;
	    window.onload = resetTimer;
	    window.onmousemove = resetTimer;
	    window.onmousedown = resetTimer; // catches touchscreen presses
	    window.onclick = resetTimer;     // catches touchpad clicks
	    window.onscroll = resetTimer;    // catches scrolling with arrow keys
	    window.onkeypress = resetTimer;

	    function resetTimer() {
	        clearInterval(t);
	        t = setInterval( function () {
				loadAppliancesTable();
			}, 4000 ); // time is in milliseconds
	    }
	}
	loadAppliancesTable();
	reloadAppliancesTable();

	/* This and function format is for adding an expanded row by clicking on the ... button  

	$(".toggle-graph a").click(function () {

		var tr = $(this).closest('tr');
		var row = dt.row( tr );
		var row_id = tr.attr("id");

		if (row.child.isShown()) {
			tr.removeClass('details');
			row.child.hide();
		} else {
			tr.addClass('details');
			row.child( format(row.data()) ).show();
		}

		var cpu_data_request = $.ajax({
			url : "api.php?action=get_cpu_usage&request="+row_id,
			type: "GET",
			cache: false,
			async: true,
			dataType: "json",
		});

		
		cpu_data_request.done(function(data) {

			var chart_cpu = c3.generate({
				bindto: "#cpu-usage-" + row_id,
				data: {
					columns: [
						['cpu used'].concat(data)
					],
					type: 'spline'
				},
				legend: {
					show: true,
					position: 'inset'
				}
			});
		});

		cpu_data_request.fail(function( jqXHR, textStatus ) {
			alert( "CPU datae request failed: " + textStatus );
		});

		var memory_data_request = $.ajax({
			url : "api.php?action=get_memory_usage&request="+row_id,
			type: "GET",
			cache: false,
			async: true,
			dataType: "json",
		});

		memory_data_request.done(function(data) {

			var chart_memory = c3.generate({
				bindto: "#memory-usage-" + row_id,
				data: {
					columns: [
						['memory used'].concat(data)
					],
					type: 'spline'
				},
				legend: {
					show: true,
					position: 'inset'
				}
			});
		});

		memory_data_request.fail(function( jqXHR, textStatus ) {
			alert( "Memory datae request failed: " + textStatus );
		});
		
	});
	*/
});

function cdromsender(hostname, action, isofile) {
		
	var url = "/cloud-fortis/user/index.php?cloud_ui=appliances&action=cdrom&hostname=" + hostname;
	url = url + "&cdaction=" + action;
	url = url + "&isofile=" + isofile;
	var row = '';
	$('.hostnamee').each(function(){
		if ( $(this).text() == hostname ) {
			row = $(this).closest('tr');
		}
	});
	
	$.ajax({
		url : url,
		type: "GET",
		cache: false,
		async: false,
		dataType: "html",
		success : function (data) {
			if (action == 'getlist') {
				list = data.split(';');
				for (i in list) {
					cdromlist[i] = list[i];
				}
			}

			if (action == 'insert') {
				if (data == 'Insert succesful') {
					alert(data);
					$('#filepicker').hide();
					row.find('.cdrom').hide();
					row.find('.cdromeject').show();
				} else {
					alert('Can\'t insert iso file, please, read documentation first');
					$('#filepicker').hide();
				}
				
			}

			if (action == 'eject') {
				if (data == 'Eject succesful') {
					alert(data);
					row.find('.cdromeject').hide();
					row.find('.cdrom').show();
				} else {
					alert('Can\'t eject cd');
				}
			}	
		}
	});
}


function get_state( id ) {
/*
	var data = $.ajax({
		url : "api.php?action=state&request="+id,
		type: "POST",
		cache: false,
		async: false,
		dataType: "json",
		success : function () { }
	}).responseText;
	elem = document.getElementById(id);
	elem.innerHTML = data + "\n" + elem.innerHTML;
	window.setTimeout("get_state( "+id+" )", 100);
*/
}


/*
	window.onload = function() {
		var th = $('#cloud_appliances').height();
		$('#htvcenter_enterprise_footer').css("top",th + 220);

	};


	$(document).ready(function(){
		$("#cloudpopupInfoClose").click(function(){
			clouddisablePopup();
		});
		$("#cloudbackgroundPopup").click(function(){
			clouddisablePopup();
		});
	});


	var cloudpopupStatus = 0;
	function cloudloadPopup(){
		if(cloudpopupStatus==0){
			$("#cloudbackgroundPopup").css({
				"opacity": "0.7"
			});
			$("#cloudbackgroundPopup").fadeIn("slow");
			$("#cloudpopupInfo").fadeIn("slow");
			cloudpopupStatus = 1;
		}
	}

	function clouddisablePopup(){
		if(cloudpopupStatus==1){
			$("#cloudbackgroundPopup").fadeOut("slow");
			$("#cloudpopupInfo").fadeOut("slow");
			cloudpopupStatus = 0;
		}
	}


	function cloudcenterPopup(){
		var windowWidth = document.documentElement.clientWidth;
		var windowHeight = document.documentElement.clientHeight;
		var popupHeight = $("#cloudpopupInfo").height();
		var popupWidth = $("#cloudpopupInfo").width();
		$("#cloudpopupInfo").css({
			"position": "absolute",
			"top": "120px",
			"left": "400px"
		});
		$("#cloudbackgroundPopup").css({
			"height": windowHeight + 20
		});
	}


	function cloudopenPopup(cr_id) {
		cloudcenterPopup();
		cloudloadPopup();
		cloudget_info_box(cr_id);
	}



	function cloudget_info_box(cr_id) {
		$.ajax({
			url: "/cloud-fortis/user/api.php?action=request_details&cr_id=" + cr_id,
			cache: false,
			async: false,
			dataType: "text",
			success: function(response) {
				$("#cloudinfoArea").html(response);
			}
		});
	}

*/
</script>
<!--
<div id="cloudpopupInfo">
	<a id="cloudpopupInfoClose">x</a>
	<h1>{cr_details_title}</h1>
	<div id="cloudinfoScrollArea">
		<p id="cloudinfoArea">
		</p>
	</div>
</div>
<div id="cloudbackgroundPopup"></div>
//-->

<div class="cat__content">
	<cat-page>
	<div class="row">
		<div class="col-sm-12">
			<section class="card">	
				<div class="card-header">
					<div class="panel-heading"> <!-- pull-right -->
						<h3 class="text-black d-inline"><strong>Virtual Machines</strong></h3>
						<!--
						<a class="btn btn-primary" href="index.php?project_tab_ui=3&cloud_ui=create" target="_blank">New Instance <i class="fa fa-plus"></i></a>
						-->
						<div class="pull-right d-inline">
							<a class="btn btn-sm btn-primary" data-toggle="modal" data-target="#create-vm-modal">Create New Virtual Machine <i class="fa fa-plus"></i></a>
						</div>
					</div>
				</div>
				<div id="table-appliances" class="card-block">
				</div>
			</section>
		</div>
	</div>
	</cat-page>
</div>

<div id="create-vm-modal" class="modal" data-backdrop="static">
	<div class="modal-content">
		<div class="modal-header">
			<h3 class="text-black">Create New Virtual Machine</h3>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
		</div>
	</div>
</div>

<div id="confirm-appliance-actions" class="modal" data-backdrop="static">
	<div class="modal-content">
		<div class="modal-header">
			<h3 class="text-black"></h3>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
		</div>
		<div class="modal-footer">
			<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
		</div>
	</div>
</div>

<div id="modal-volume" class="modal" data-backdrop="static">
	<div class="modal-content">
		<div class="modal-header">
			<h3 class="text-black">Loading...</h3>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<table class="table table-striped table-hover" id="moredisktbl"><tbody>
				<!--
				<tr class="warning">
					<td>Type</td><td>Name</td><td>Size</td><td class="text-center">Action</td>
				</tr>
				-->
			</tbody></table>
		</div>
		</div>
		<div class="modal-footer">
			<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
		</div>
	</div><!-- /.modal-content -->
</div>

<div id="modal-volumeadd" class="modal" data-backdrop="static">
	<div class="modal-content">
		<div class="modal-header">
			<h3 class="text-black">Add one more volume</h3>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<p style="display:none"> You can add few volumes here. Available space for it is <span id="freembsp">{freemb}</span> </p>
			<div class="moredisk">
				<span>Input volume information:</span><br/>
				<input  type="text" id="namevolumeinput"/><br/>
				<div class="selecto">
					<select id="typevolumeselect">
						<option value="raw">raw</option>
					</select>
				</div>
				<div class="selecto">
					<label>Size:</label> 	{volumeselect}
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
			<button class="btn btn-primary" type="button" id="addvolumebtnvv">Add</button>
		</div>
	</div>
</div>

<div class="function-box" style="display: none" id="filepicker">
	<div onmouseup="document.getElementById('filepicker').onmousedown = null;" onmousedown="Drag.init(document.getElementById('filepicker'));" onclick="MousePosition.init();" id="caption" class="functionbox-capation-box">
		<div class="functionbox-capation">
			Select iso file
			<input type="button" onclick="document.getElementById('filepicker').style.display = 'none';" value="X" class="functionbox-closebutton" id="close" class="filepickclose">
		</div>
	</div>
	<div id="canvas">
<table border="1" id="Table" class="filepicker_table">
<tbody id="isofilez">

</tbody></table>
</div>
</div>

<div id="volumepopup" class="modal-dialog">
<div class="panel">
                    
                                <!-- Classic Form Wizard -->
                                <!--===================================================-->
                                <div id="demo-cls-wz">
                    
                                    <!--Nav-->
                                    <ul class="wz-nav-off wz-icon-inline wz-classic">
                                        <li class="col-xs-3 bg-info active">
                                            <a href="#demo-cls-tab1" data-toggle="tab" aria-expanded="true">
                                                <span class="icon-wrap icon-wrap-xs bg-trans-dark"><i class="fa fa-cloud"></i></span> Fortis
                                            </a>
                                        </li>
                                        <div class="volumepopupclass"><a id="volumepopupclose"><i class="fa fa-icon fa-close"></i></a></div>
                                        
                                    </ul>
                    
                                    <!--Progress bar-->
                                    <div class="progress progress-sm progress-striped active">
                                        <div class="progress-bar progress-bar-info" style="width: 100%;"></div>
                                    </div>
                    
                    
                                    <!--Form-->
                                    <div class="form-horizontal mar-top">
                                        <div class="panel-body">
                                            <div class="tab-content">
                    
                                                <!--First tab-->
                                                <div class="tab-pane active in" id="demo-cls-tab1">
                                                    <div id="storageform">
                                                    
                                                    </div>
                                                </div>
                    
                                                
                                            </div>
                                        </div>
                    
                    
                                    </div>
                                </div>
                                <!--===================================================-->
                                <!-- End Classic Form Wizard -->
                    
                            </div>
</div>
