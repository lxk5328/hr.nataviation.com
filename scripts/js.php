<script type='text/javascript'>
	var limitDate = "<?php echo $limitDate; ?>";
	var limitMaxDate = "<?php echo $maxLimitDate; ?>";
	var scheduleDate = "<?php echo $scheduleDate; ?>";
	var scheduleBaseDate = "<?php echo $scheduleBaseDate; ?>";
	var scheduleMode = "<?php echo $projectOptionDefault; ?>";
	var scheduleEditMode = "<?php echo $scheduleEditMode; ?>";
	var scheduleCreateMode = "<?php echo $scheduleCreateMode; ?>";
	var viewGlobalSchedule = "<?php echo $viewGlobalSchedule; ?>";
	var scheduleCurrentWeek = "<?php echo $scheduleCurrentWeek; ?>";
	var scheduleOpenWorkFlow = "<?php echo $scheduleOpenWorkFlow; ?>";

	var positionNameArray = [];
	var employeeNameArray = [];
	var locationNameArray = [];

	var bizTitleArray = [];
	var positionArray = [];
	var employeeArray = [];
	var locationArray = [];
	var divArray = [0,0,0];

	var positionDiv;
	var locationDiv;
	var employeeDiv;
	var currentDiv;

	var employeeSearchFlag;
	var globalEmployeeID;
	var globalVEmployee;
	var globalFirstName;
	var globalLastName;
	var permissionID;
	var authorized;
	var dataTable;
	var rowIndex;
	var txtID;
	var txt;

	var totalSeconds = 0;
	var timer;

	function checkTimeForm(form) {
		if (form.start_date.value == "") { alert("Please enter a start date."); form.start_date.focus(); return false; }
		if (form.hours.value == "") { alert("Please enter the number of hours."); form.hours.focus(); return false; }
		if (!form.hours.value.match(/\d+/)) { alert("Please enter the number of hours."); form.hours.focus(); return false; }
		return true;
	}

	function splitTime(s, e) {
	  var h = (((getTimeDiff(s, e) / 1000) / 60) / 60);
	  if (e < s) { h+= 24; }
	  return h;
	}

	function getTimeDiff(s, e) {
	  var d1 = new Date(0);
	  d1.setHours(parseInt(s.toString().substr(0, 2), 10));
	  d1.setMinutes(parseInt(s.toString().substr(2, 2), 10));

	  var d2 = new Date(0);
	  d2.setHours(parseInt(e.toString().substr(0, 2), 10));
	  d2.setMinutes(parseInt(e.toString().substr(2, 2), 10));
	  return d2.getTime() - d1.getTime();
	}

	function checkContactsForm(form) {
		if (form.ContactName.value == "") { alert("Please enter the emergency contact name."); form.ContactName.focus(); return false; }
		if (form.Phone.value == "") { alert("Please enter the emergency contact phone number."); form.Phone.focus(); return false; }
		return true;
	}

	function checkForm(form) {
		if (form.employee_id.value == "") { alert("Please enter your Login ID."); form.employee_id.focus(); return false; }
		if (form.password.value == "") { alert("Please enter your password."); form.password.focus(); return false; }
		return true;
	}

	function checkPasswordForm(form) {
		alert("WTF");
		if (form.password.value != form.password2.value) { alert("Passwords do not match. Please correct the problem."); form.password.focus(); return false; }
		return true;
	}

	function resetLocations() {
		$("#locations_div").css({"display" : "none" });
		$("#dt_display_div").css({ "display" : "block" });
	}

	function setTxtVals(id, v) {
		if (v) {
			if ($('#sSat').is(':checked')) { $("#sat_" + id).val(v); }//else { $("#sat_" + id).val(""); }
	    	if ($('#sSun').is(':checked')) { $("#sun_" + id).val(v); }//else { $("#sun_" + id).val(""); }
	    	if ($('#sMon').is(':checked')) { $("#mon_" + id).val(v); }//else { $("#mon_" + id).val(""); }
	    	if ($('#sTue').is(':checked')) { $("#tue_" + id).val(v); }//else { $("#tue_" + id).val(""); }
	    	if ($('#sWed').is(':checked')) { $("#wed_" + id).val(v); }//else { $("#wed_" + id).val(""); }
	    	if ($('#sThu').is(':checked')) { $("#thu_" + id).val(v); }//else { $("#thu_" + id).val(""); }
	    	if ($('#sFri').is(':checked')) { $("#fri_" + id).val(v); }//else { $("#fri_" + id).val(""); }
	    } else {
	    	$("#sat_" + id).val("");
	    	$("#sun_" + id).val("");
	    	$("#mon_" + id).val("");
	    	$("#tue_" + id).val("");
	    	$("#wed_" + id).val("");
	    	$("#thu_" + id).val("");
	    	$("#fri_" + id).val("");
	    }
	}

	function deletePositionRow(p, id) {
		var index;

		$("#" + p + "_tr_" + id).remove();
		index = positionArray.indexOf(parseInt(id));
		if (index > -1) {
			positionArray.splice(index, 1); 
			bizTitleArray.splice(index, 1);
		}
	}

	function deletePermissionRow(p, id) {
		var index;

		$("#" + p + "_tr_" + id).remove();
		if (p == "position") {
			index = positionArray.indexOf(id);
			if (index > -1) { positionArray.splice(index, 1); }
		} else if (p == "employee") {
			index = employeeArray.indexOf(id);
			if (index > -1) { employeeArray.splice(index, 1); }
		} else if (p == "location") {
			index = locationArray.indexOf(id);
			if (index > -1) { locationArray.splice(index, 1); }
		}
	}

	function initPositionRow(p, b, id, t, a) {
		if (a) { a = "<img onclick='javascript:deletePositionRow(\"" + p + "\", \"" + id + "\");' border='0' src='/images/delete.png' style='float: right; cursor: pointer;' />"; }
		$('#positions_table tr:last').after("<tr id='" + p + "_tr_" + id + "'><td colspan='2'>" + t + " &nbsp; " + a + "</td></tr>");
	}

	function initPermissionRow(p, id, t, a) {
		if (a) { a = "<img onclick='javascript:deletePermissionRow(\"" + p + "\", \"" + id + "\");' border='0' src='/images/delete.png' style='float: right; cursor: pointer;' />"; }
		$('#permissions_table tr:last').after("<tr id='" + p + "_tr_" + id + "'><td colspan='2'>" + t + " &nbsp; " + a + "</td></tr>");
	}

	function addPositionRow(p) {
		var b = $("#business_title").val();
		var t = $("#business_title :selected").text();
		var id = Math.random().toString(36).substr(2, 15);

		if (bizTitleArray.indexOf(b) < 0) { positionArray.push(id); bizTitleArray.push(b); } else { return; }


		$('#positions_table tr:last').after("<tr id='" + p + "_tr_" + id + "'><td colspan='2'>" + t + " &nbsp; <img onclick='javascript:deletePositionRow(\"" + p + "\", \"" + id + "\");' border='0' src='/images/delete.png' style='float: right; cursor: pointer;' /></td></tr>");
	}

	function addPermissionRow(p) {
		var id;
		var t;

		if (p == "position") {
			id = $("#position").val(); t = $("#position :selected").text();
			if (positionArray === null || positionArray.indexOf(id) < 0) { if (positionArray === null) { positionArray = []; } positionArray.push(id); } else { return; }
		} else if (p == "employee") {
			id = $("#employee_list").val(); t = $("#employee_list :selected").text();
			if (employeeArray === null || employeeArray.indexOf(id) < 0) { if (employeeArray === null) { employeeArray = []; } employeeArray.push(id); } else { return; }
		} else if (p == "location") {
			id = $("#location").val(); t = $("#location :selected").text();
			if (locationArray === null || locationArray.indexOf(id) < 0) { if (locationArray === null) { locationArray = []; } locationArray.push(id); } else { return; }
		}

		$('#permissions_table tr:last').after("<tr id='" + p + "_tr_" + id + "'><td colspan='2'>" + t + " &nbsp; <img onclick='javascript:deletePermissionRow(\"" + p + "\", \"" + id + "\");' border='0' src='/images/delete.png' style='float: right; cursor: pointer;' /></td></tr>");
	}

	function loadPermissionRow() {
		if (divArray[0] == 0) { divArray[0] = 1; return 0; }
		if (divArray[1] == 0) { divArray[1] = 1; return 1; }
		if (divArray[2] == 0) { divArray[2] = 1; return 2; }
	}

	function reorderPermissionRows() {
		var x = 0;
		var z = 0;

		for (z = 0; z < (divArray.length - 1); z++) {
			if (divArray[z] == 0 && divArray[(z + 1)] == 1) {
				divArray[(z + 1)] = 0; divArray[z] = 1; x++;
				$("#permissions_" + z + "_display_div").html($("#permissions_" + (z + 1) + "_display_div").html());
				$("#permissions_" + (z + 1) + "_display_div").html("");
			}
		}

		if (positionDiv > currentDiv) { positionDiv--; }
		if (employeeDiv > currentDiv) { employeeDiv--; }
		if (locationDiv > currentDiv) { locationDiv--; }
	}

	function employeeSearch() {
		var peURL = "/scripts/pe.php?xargs=NAS2018&action=EMPLOYEE-SEARCH&employee=" + $("#employee").val();
		$.get(peURL, function(data) { $("#employee_search_results_div").html(data); });
	}

	function positionSearch() {
		var positionsSearchURL = "/scripts/positions.php?xargs=NAS2018&action=POSITIONS-SEARCH&pid=" + $("#pid").val() + "&s=" + $("#position_search_txt").val();
		$.get(positionsSearchURL, function(data) {
			$("#position_list_div").css({ "display" : "inline" });
			if (data) {
				$("#position_list_div").html(data);
				$("#position_add_div").css({ "display" : "inline" });
			} else {
				$("#position_list_div").html("No available match found... please try again.");
				$("#position_add_div").css({ "display" : "none" });
			}
		});
	}

	function toggleFlag() {
		if ($("#permission_position").is(':checked') && $("#permission_location").is(':checked') && $("#permission_employee").is(':checked')) { return false; }
		if (!$("#permission_position").is(':checked') && !$("#permission_location").is(':checked') && !$("#permission_employee").is(':checked')) { return true; }
		return false;
	}

	function initPositionSearchDiv(l) {
		$("#position_search_div").html("<input type='text' size='30' name='position_search_txt' id='position_search_txt' placeholder='Search...' /> <input type='button' id='position_search_submit' value='SEARCH' />");
		//if (l) { loadPositionList(false); }
	}

	function loadScheduleChangeMode(m, c) {
		if ($("#shift_schedule_date").val() == scheduleBaseDate) { $("#supersede_div").css({ "display" : "none" }); } else { $("#supersede_div").css({ "display" : "inline"}); }
		if ($("#supersede_div").css('display') == "none" ) { $("#supersede").prop('checked', false); }
		var scheduleChangeURL = "/scripts/schedule.php?xargs=NAS2018&action=CHANGE-SCHEDULE&l=" + $("#project option:selected").text().split(" ")[0] + "&lc=" + c + "&s=" + $("#shift_schedule_date").val();

	    $.get(scheduleChangeURL, function(data) {
	    	$("#schedule_display_div").html(data);
	    	$("#schedule_edit_div").css({ "display" : "none" });
	    	scheduleCreateMode = $("#scm").val();

			if (scheduleCreateMode) {
				$("#schedule_create_div").css({ "display" : "inline" });
			} else {
				$("#schedule_create_mode_div").html("&nbsp;&nbsp;<font size='4' face='Arial'>Schedule change awaiting approval.</font>").css({ "display" : "inline" });
			}

			if (m != "") { $("#project").css({ "display" : "inline" }); }
			$("#schedule_widget_div").css({ "display" : "inline" });

			var s0 = $("#shift_schedule_date").val(); 
			var s1 = new Date(s0.replace(/-/g, '/')); 
			s1.setDate(s1.getDate()+6);
			s1 = new Date(s1.getTime() - (s1.getTimezoneOffset() * 60000 )).toISOString().split("T")[0];
			$("#schedule_week_div").html("&nbsp;&nbsp;<font size='3'>Week of <b>" + s0 + "</b> to <b>" + s1 + "</b></font>");
	    });
	}

	function loadPositionList(s) {
		if (s) { $("#position_search_div").html(""); }
    	var positionsListURL = "/scripts/positions.php?xargs=NAS2018&action=POSITIONS-LIST&pid=" + $("#pid").val();
	    $.get(positionsListURL, function(data) { $("#position_list_div").html(data); $("#position_add_div").css({ "display" : "inline" }); });
	}

	function changePassword() {
		passwdURL = "/scripts/x.php?xargs=NAS2018&action=USER-PASSWORD-RESET&passwd=" + $("#passwd").val();
	    $.get(passwdURL, function(data) { $("#change_passwd_div").css({ "display" : "none" }); });
	}

	function checkDirector(d) {
		if (d) { return 1; } else { return 0; }
	}

	function countTimer() {
	   ++totalSeconds;
	   var hour = Math.floor(totalSeconds /3600);
	   var minute = Math.floor((totalSeconds - hour*3600)/60);
	   var seconds = ("00" + (totalSeconds - (hour*3600 + minute*60))).slice(-2);

	   if (hour == 0) {
	   	$("#shift_clock_display_div").html("<b>SHIFT CLOCK:</b> " + minute + ":" + seconds);
	   } else {
	   	$("#shift_clock_display_div").html("<b>SHIFT CLOCK:</b> " + hour + ":" + minute + ":" + seconds);
	   }
	}

	$(document).ready(function() {
		var director = "<?php echo $_SESSION['user']->checkDirector(); ?>";
		var employmentAppDisplay = "<?php echo $employmentAppDisplay; ?>";
		var shiftReportAvailable = "<?php echo $shiftReportAvailable; ?>";
		var updateAttendanceMode = "<?php echo $updateAttendanceMode; ?>";
		var scheduleWeekDisplay = "<?php echo $scheduleWeekDisplay; ?>";
		var permissionsDisplay = "<?php echo $permissionsDisplay; ?>";
		var viewGlobalSchedule = "<?php echo $viewGlobalSchedule; ?>";
		var terminationDisplay = "<?php echo $terminationDisplay; ?>";
		var editGlobalSchedule = "<?php echo $editGlobalSchedule; ?>";
		var attendanceDisplay = "<?php echo $attendanceDisplay; ?>";
		var positionsDisplay = "<?php echo $positionsDisplay; ?>";
		var locationsDisplay = "<?php echo $locationsDisplay; ?>";
		var shiftDisplayDate = "<?php echo $shiftDisplayDate; ?>";
		var shiftBudgetMode = "<?php echo $shiftBudgetMode; ?>";
		var overrideDisplay = "<?php echo $overrideDisplay; ?>";
		var employeeDisplay = "<?php echo $employeeDisplay; ?>";
		var messagesDisplay = "<?php echo $messagesDisplay; ?>";
		var securityDisplay = "<?php echo $securityDisplay; ?>";
		var scheduleDisplay = "<?php echo $scheduleDisplay ?>";
		var baseServiceRow = "<?php echo $baseServiceRow; ?>";
		var profileDisplay = "<?php echo $profileDisplay; ?>";
		var reportsDisplay = "<?php echo $reportsDisplay; ?>";
		var censusDisplay = "<?php echo $censusDisplay; ?>";
		var issuesDisplay = "<?php echo $issuesDisplay; ?>";
		var rulesDisplay = "<?php echo $rulesDisplay; ?>";
		var serviceRow = "<?php echo $serviceRow ?>";
		var manager = "<?php echo $manager; ?>";
		var scheduleProjectDisplay = false;
		var locationsArray = new Array();
		var employeeSearchFlag = false;
		var authorized = false;
		var permissionRow = 0;

		<?php if (openWorkFlowCheck()) { ?>
		//$("#task_list_image").transition('set looping').transition('bounce', '4000ms').transition('stop').transition('remove looping');
		$("#task_list_image").transition('bounce', '4000ms');
		<?php } ?>

		shiftReportAvailable = true;
		if (shiftReportAvailable) { $("#shift_control_div").css({ "display" : "inline" }); }
		if (attendanceDisplay) { $("#shift_date_div").html("<b><font size='4' face='Arial'>Shift Report for " + shiftDisplayDate + "</font></b>"); }
		if (attendanceDisplay && !shiftReportAvailable && updateAttendanceMode) {
			$("#shift_control_div").css({ "display" : "inline" });
			$("#shift_override").css({ "display" : "none" });
		}
		if (shiftBudgetMode) { $("#shift_override").css({ "display" : "none" }); }

		$("#schedule_week_div").html(scheduleWeekDisplay);
		jQuery('video').on('ended', function() { $("#video_agree_div").css({ "display" : "inline" }); });

		if ($("#reason").val() == "") { $("#voluntary_codes_tr").hide(); $("#involuntary_codes_tr").hide(); }
		$("#services").click(function() { top.location.href = "http://w3.nataviation.com/services.php"; });
		$("#company").click(function() { top.location.href = "http://w3.nataviation.com/company.php"; });
		$("#customers").click(function() { top.location.href = "http://w3.nataviation.com/customers.php"; });
		$("#employment").click(function() { top.location.href = "http://w3.nataviation.com/employment.php"; });
		$("#locations").click(function() { top.location.href = "http://w3.nataviation.com/locations.php"; });
		$("#logout").click(function() {top.location.href = "/scripts/exit.php"; });

		$('.menu a').on('click', function() {
	        $('.menu a').removeClass('active');
	        $(this).addClass('active');
	    });

	    $("#station_log_button").click(function() {
	    	$.post( "/scripts/x.php", { xargs: "NAS2018", action: "STATION-LOG", message: $("#message").val(), l: $("#l").val() });
	    	$("#message_display").html("<b>System update successful.");
	    });

	    $("#reason").change(function() {
	    	if ($("#reason").val() == "") { $("#voluntary_codes_tr").hide(); $("#involuntary_codes_tr").hide(); }
	    	if ($("#reason").val() == "voluntary") { $("#voluntary_codes_tr").show(); $("#involuntary_codes_tr").hide(); }
	    	if ($("#reason").val() == "involuntary") { $("#voluntary_codes_tr").hide(); $("#involuntary_codes_tr").show(); }
	    });

	    $("#shift_delete").click(function() {
	    	var id = txtID.substring(4);
	    	setTxtVals(id, "");
	    });

	    $("#shift_override").click(function() {
	    	$(this).css({ "display" : "none" });
	    	$("#override_div").css({ "display" : "block" });
	    	$("#override_description").focus();
	    });

	    $("#shift_copy").click(function() {
	    	if (txt.match(/\d{4}\s-\s\d{4}/)) {
	    		var id = txtID.substring(4);
	    		setTxtVals(id, txt);
	    	} else {
	    		alert("Required format: #### - ####");
	    		$("#" + txtID).focus();
	    	}
	    });

	    $(document.body).on('change', "#reason", function() {
	    	reasonURL = "/scripts/termination.php?xargs=NAS2018&action=REASONS&r=" + $("#reason").val();
	    	$.get(reasonURL, function(data) {
	    		$("#type_id_div").html(data);
	    	});
	    });

	    $(document.body).on('click', "#add_service", function() {
	    	$("#shift_services").append(baseServiceRow);
	    });

	    $(document.body).on('click', "#punch_clock_location_submit", function() {
	    	clockURL = "/scripts/attendance.php?xargs=NAS2018&action=CLOCK&l=" + $("#clock_location").val();
	    	$.get(clockURL, function(data) {
	    		$("#punch_clock_display").html("");
	    		timer = setInterval(countTimer, 1000);
	    		$("#clock_link").html("Clock out");
	    	});
	    });

	    $("#punch_clock").click(function() {
	    	clockURL = "/scripts/attendance.php?xargs=NAS2018&action=INITIALIZE";
	    	$.get(clockURL, function(data) { 
				if ($("#clock_link").html() == "Clock out") {
		    		$("#clock_link").html("Clock in");
		    		clearInterval(timer);
		    	} else {
		    		timer = setInterval(countTimer, 1000);
		    		$("#clock_link").html("Clock out");
		    	}
		    });
	    });

	    $("#time_off_cancel").click(function() { top.location.href="/default.php"; });
	    $(document.body).on('keydown', "#position_search_txt", function(e) { if (e.which == 13) { positionSearch(); } });
	    $(document.body).on('keydown', "#passwd", function(e) { if (e.which == 13) { changePassword(); } });
	    $(document.body).on('click', "#issues_cancel", function() { top.location.href="/display.php?xapp=ISSUES"; });
	    $(document.body).on('click', "#position_search_submit", function() { positionSearch(); });
	    $(document.body).on('click', "#position_add", function() { addPositionRow("position"); });


	    $(document.body).on('click', "#position_search", function() {
	    	$("#position_list_div").html("");
	    	$("#position_add_div").css({ "display" : "none" });
	    	initPositionSearchDiv(false);
	    });

	    $("#contacts_cancel").click(function() { top.location.href="/default.php"; });
	    $(document.body).on('click', "#position_list", function() { loadPositionList(true); });
	    $(document.body).on('focus', "#employee", function() { employeeSearchFlag = true; });
	    $(document.body).on('click', "#employee_application_cancel", function() { top.location.href="/applications.php"; });
		$(document.body).on('click', "#location_permissions_add", function() { addPermissionRow("location"); });	    	    
	    $(document.body).on('click', "#position_permissions_add", function() { addPermissionRow("position"); });
	    $(document.body).on('click', "#permissions_modify_cancel", function() { top.location.href = "/permissions.php"; });
	    $(document.body).on('click', "#positions_modify_cancel", function() { top.location.href = "/positions.php"; });
	    $(document.body).on('click', "#employee_permissions_add", function() { addPermissionRow("employee"); });

	    $(document.body).on('click', "#permission_position", function() {
	    	if ($(this).is(':checked')) {
	    		positionDiv = loadPermissionRow();
	    		var ppURL = "/scripts/pp.php?xargs=NAS2018&action=POSITION-PERMISSIONS&pid=" + permissionID;
			    $.get(ppURL, function(data) { $("#permissions_" + positionDiv + "_display_div").html(data); });
	    	} else {
		    	if (positionArray != null && positionArray.length > 0) {
		    		$(this).prop('checked', true);
		    	} else {
		    		currentDiv = positionDiv;
		    		divArray[positionDiv] = 0;
		    		$(this).prop('checked', false);
		    		$("#permissions_" + positionDiv + "_display_div").html("");
		    		reorderPermissionRows();
		    	}
			}
	    	if (toggleFlag()) { $('.dropdown-toggle').dropdown('toggle'); }
	    });

	    $(document.body).on('click', "#message_view_all", function() { top.location.href="/messages.php?xapp=MESSAGE-FILTER"; });
	    $(document.body).on('click', "#message_view_open", function() { top.location.href="/messages.php?xapp=MESSAGE-FILTER&message_filter=OPEN"; });

	    $(document.body).on('click', "#messages_unlock", function() {
	    	if ($(this).val() == "LOCK") {
	    		$("#messages_submit").css({ "display" : "inline" });
	    		$("#messages_unlock").val("UNLOCK");
	    	} else {
		    	var unlockURL = "/scripts/messages.php?xargs=NAS2018&action=UNLOCK&p=" + $("#wf_process_id").val() + "&r=" + $("#id").val();
		    	$.get(unlockURL, function(data) {
		    		$("#messages_submit").css({ "display" : "none" });
		    		$("#messages_unlock").val("LOCK");
		    	});
		    }
	    });

	    $(document.body).on('click', "#positions_submit", function() {
	    	$("#position_array").val(bizTitleArray);
	    	$("#positions_ajax_div").html("System update successful.");
	    	$.post( "/scripts/x.php", { xargs: "NAS2018", action: "POSITIONS-MODIFY", pid: $("#pid").val(), Description: $("#Description").val(), ShirtStyleID: $("ShirtStyleID").val(), IsRegionalMgr: $("#IsRegionalMgr").is(':checked'), IsManager: $("#IsManager").is(':checked'), IsTSA: $("#IsTSA").is(':checked'), FAADocs: $("#FAADocs").is(':checked'), position_array: $("#position_array").val() });
	    });

	    $(document.body).on('click', "#permissions_submit", function() { 
	    	$("#position_array").val(positionArray);
	    	$("#location_array").val(locationArray);
	    	$("#employee_array").val(employeeArray);
	    	$("#permissions_ajax_div").html("System update successful.");
	    	$.post( "/scripts/x.php", { xargs: "NAS2018", action: "PERMISSIONS-MODIFY", pid: $("#pid").val(), description: $("#description").val(), position_array: $("#position_array").val(), location_array: $("#location_array").val(), employee_array: $("#employee_array").val() });
	    });

	    $(document.body).on('click', "#employee_search", function() { employeeSearch(); });
	    $(document.body).on('submit', "#permissions_form", function(e) {
	    	e.preventDefault();
	    	if (employeeSearchFlag) { return employeeSearch(); }
	    });

	    
	    $(document.body).on('click', "#permission_location", function() {
	    	if ($(this).is(':checked')) {
	    		locationDiv = loadPermissionRow();
	    		var plURL = "/scripts/pl.php?xargs=NAS2018&action=LOCATION-PERMISSIONS&pid=" + permissionID;
			    $.get(plURL, function(data) { $("#permissions_" + locationDiv + "_display_div").html(data); });
	    	} else {
		    	if (locationArray != null && locationArray.length > 0) {
		    		$(this).prop('checked', true);
		    	} else {
		    		currentDiv = locationDiv;
		    		divArray[locationDiv] = 0;
		    		$(this).prop('checked', false);
		    		$("#permissions_" + locationDiv + "_display_div").html("");
		    		reorderPermissionRows();
		    	}
			}
	    	if (toggleFlag()) { $('.dropdown-toggle').dropdown('toggle'); }
	    });

	    $(document.body).on('click', "#permission_employee", function() {
 			if ($(this).is(':checked')) {
	    		employeeDiv = loadPermissionRow();
	    		var plURL = "/scripts/pe.php?xargs=NAS2018&action=EMPLOYEE-PERMISSIONS&pid=" + permissionID;
			    $.get(plURL, function(data) { $("#permissions_" + employeeDiv + "_display_div").html(data); });
	    	} else {
		    	if (employeeArray != null && employeeArray.length > 0) {
		    		$(this).prop('checked', true);
		    	} else {
		    		currentDiv = employeeDiv;
		    		divArray[employeeDiv] = 0;
		    		$(this).prop('checked', false);
		    		$("#permissions_" + employeeDiv + "_display_div").html("");
		    		reorderPermissionRows();
		    	}
			}
	    	if (toggleFlag()) { $('.dropdown-toggle').dropdown('toggle'); }
	    });

	    $(document.body).on('focus', 'input[type="text"]', function() {
	    	var t = $(this).val();
	    	employeeSearchFlag = false;
	    	txt = t; txtID = $(this).attr('id');
	    });

	    $(document.body).on('blur', 'input[type="text"]', function() {
	    	var f = ['sat_', 'sun_', 'mon_', 'tue_', 'wed_', 'thu_', 'fri_'];
	    	var t = $(this).val();
	    	var tt = 0;
	    	var h = 0;
	    	var t1;
	    	var t2;
	    	var id;
	    	var w;

	    	employeeSearchFlag = false;
	    	txt = t; txtID = $(this).attr('id');

	    	if (txtID.split("_")[0] == "hours") {
	    		if (isNaN(t)) { alert("Please enter the hours in numeric format."); $(this).focus(); }
	    		return;
	    	}

	    	if (txtID == "search" || txtID == "shift_schedule_date") { return; }
	    	if ( $("#project").css('display') != "none") {
		    	if (txtID.match(/[a-z]+_\d+/)) {
		    		id = txtID.substring(4);

		    		for (z = 0; z < f.length; z++) {
		    			st = $("#" + f[z] + id).val();
		    			if (st.match(/\d{4}\s-\s\d{4}/)) {
				    		t1 = st.split("-")[0].trim();
				    		t2 = st.split("-")[1].trim();
				    		h += parseInt(splitTime(t1, t2));
				    	}
			    	}
			    	$("#total_time_div_" + id).html(h);
			    	$('.ttd').each(function() { tt += parseInt($(this).html()); });
			    	$("#total_schedule_hours_div").html(tt);

			    	tt = 0;
			    	w = txtID.substring(0,4);

			    	$('input[type="text"]').each(function() {
        				id = $(this).attr("id");
        				if (id.indexOf(w) != -1 && $(this).val() != "") { 
        					st = $(this).val();
			    			if (st.match(/\d{4}\s-\s\d{4}/)) {
					    		t1 = st.split("-")[0].trim();
					    		t2 = st.split("-")[1].trim();
					    		tt += parseInt(splitTime(t1, t2));
					    	}
					    }
        			});
        			$("#" + w + "tt").html(tt);
			    }
			}
		});

	    $("#employee_id").val("");
	    $("#password").val("");
	    $("#search").focus();

	    $("#search_icon").click(function() {
	    	$("#searchForm").submit();
	    });

	    $("#view_delta_date").val(limitDate);
	    $("#view_delta_date").datepicker({ dateFormat: 'yy-mm-dd', minDate: new Date('2018-6-28'), maxDate: new Date(limitMaxDate) });

	    $("#start_date").datepicker({ dateFormat: 'yy-mm-dd', minDate: new Date() });
	    $("#end_date").datepicker({ dateFormat: 'yy-mm-dd', minDate: new Date() });

	    $("#start_date").change(function() {
	    	if ($("#end_date").val() == "") {
	    		$("#end_date").val($("#start_date").val());
	    	}
	    });

	    $(document.body).on('click', "#shift_schedule_change", function() { loadScheduleChangeMode(manager, $("#lc").val()); });
	    $("#shift_schedule_cancel").click(function() { top.location.href="/schedule.php"; });
	    $("#shift_schedule_date").val(scheduleDate);
		$("#shift_schedule_date").datepicker({ 
			dateFormat: 'yy-mm-dd', 
			minDate: new Date('2018-6-30'), 
		    beforeShowDay: function(date) { if(date.getDay() == 6) { return [true]; } else { return [false]; }}
		});

		$("#change_passwd_cancel").click(function() { $("#change_passwd_div").css({ "display" : "none" }); });
	    $("#profile_submit").click(function() { top.location.href="/profile.php?xapp=PROFILE&p=" + $("#view_profile").val(); });
	    $("#employee_submit").click(function() { top.location.href="/employees.php?xapp=PROFILE&v=" + $("#employee_view").val(); });
	    $("#change_passwd_submit").click(function() { changePassword(); });

	    $("#employee_profile_submit").click(function() {
	    	if ($("#view_profile").val() == "4") {
	    		if ($("#passwd").val() == "") {
	    			alert("Please enter a new password.");
	    			$("#passwd").focus();
	    		} else {
	    			$("#employee_profile_submit").attr('disabled', true);
	    			$("#passwd").css({ "display" : "none" });

	    			passwdURL = "/scripts/x.php?xargs=NAS2018&action=ADMIN-PASSWORD-RESET&eid=" + globalEmployeeID + "&passwd=" + $("#passwd").val();
	    			$.get(passwdURL, function(data) { 
	    				$("#passwd").css({ "display" : "none" }); 
	    				$("#view_profile").val($("#globalVEmployee").val());
	    				$("#employee_profile_submit").attr('disabled', false);
	    			});

	    			$("#login_status_div").html("ENABLED");
	    			$("#account_color").css({ "background-color" : "#cecece" });
	    			if ($("#view_profile option[value='5']").length == 0 ) { $("#view_profile").append($('<option></option>').attr("value", '5').text('Suspend')); }
	    			
	    		}
	    		return;
	    	}

	    	if ($("#view_profile").val() == "5") {
	    		suspendURL = "/scripts/x.php?xargs=NAS2018&action=SUSPEND&eid=" + globalEmployeeID;
	    		$.get(suspendURL, function(data) { $("#view_profile").val($("#globalVEmployee").val()); });
	    		$("#account_color").css({ "background-color" : "#ff0000" });
	    		$("#view_profile option[value='5']").remove();
	    		$("#login_status_div").html("SUSPENDED");
	    		return;
	    	}

	      	profileURL = "/scripts/profile.php?xargs=NAS2018&action=NASPROFILE&eid=" + globalEmployeeID + "&p=" + $("#view_profile").val() + "&l=" + globalLastName + "&f=" + globalFirstName;
			$.get(profileURL, function(data) { $("#profile_div").html(data); });
	    });

	    $("#rules_cancel").click(function() {
	    	$("#requirements_add_div").css({ "display" : "none" });
	    	$("#rules_buttons_div").css({ "display" : "inline" });
	    	$("#requirements_div").css({ "display" : "none" });
	    	$("#dt_display_div").css({ "display" : "block" });
	    });

	    $("#action_cancel").click(function() { top.location.href="/default.php"; })
	    $("#integration_cancel").click(function() { top.location.href="/default.php"; });
	    $("#employee_cancel").click(function() { top.location.href="/employees.php"; });
	    $("#profile_cancel").click(function() { top.location.href="/profile.php"; });
	    $("#census_cancel").click(function() {
	    	if ($("#profile_div").css('display') == 'none') {
	    		top.location.href="/display.php?xapp=CENSUS";
	    	} else {
	    		$("#profile_div").css({"display" : "none" });
	    		$("#dt_display_div").css({ "display" : "block" });
	    	}
	    });

	    $("#shift_calc").click(function() {
	    	if (txt.match(/\d{4}/)) {
		    	var t = parseInt(txt);
		    	var h = parseInt($("#sHours").val());
		    	var id = txtID.substring(4);
		    	var v;

		    	if ((h == 8 && t < 1600) || (h == 7 && t < 1700) || (h == 6 && t < 1800)) {
		    		v = (t + (h * 100));
		    	} else {
		    		v = ((h * 100) - (2400 - t));
		    	}

		    	v = ("0000" + v).slice(-4);
		    	setTxtVals(id, txt + " - " + v);
		    } else {
		    	alert("Required format: ####");
		    	$("#" + txtID).focus();
		    }
	    });

	    $(document.body).on('click', "#shift_schedule_submit", function() {
	    	if (director || $("#project").css('display') == "none") {
	    		top.location.href="/schedule.php?l=" + $("#project").val() + "&s=" + $("#shift_schedule_date").val();
	    	} else {
	    		loadScheduleChangeMode(manager, $("#project").val());
	    	}
	    });

	    $("#shift_schedule_save").click(function() {
	    	var saveFlag = true;
	    	$('input[type="text"]').each(function(index) {
	    		var id = $(this).attr('id');
	    		if (id.match(/[a-z]+_\d+/) && !id.match(/notes_\d+/)) {
	    			if ($("#" + id).val() != "" && !$("#" + id).val().match(/\d{4}\s-\s\d{4}/)) {
	    				alert("Required format: #### - ####");
    					$("#" + id).focus();
    					saveFlag = false;
    					return;
    				}
	    		}
	    	});
	    	if (saveFlag) { $("#shift_schedule_form")[0].submit(); }
	    });

	    $("#change_passwd").click(function() {
	    	$("#change_passwd_div").css({ "display" : "block" });
	    });

	    $("#rule_add").click(function() {
	    	$("#requirements_add_div").css({ "display" : "block" });
	    	$("#rules_buttons_div").css({ "display" : "none" });
	    	$("#requirements_div").css({ "display" : "none" });
	    	$("#dt_display_div").css({ "display" : "none" });
	    });

			$("#at_add").click(function() {
	    	$("#at_add_div").css({ "display" : "block" });
	    	$("#at_table_div").css({ "display" : "none" });
	    	//$("#requirements_div").css({ "display" : "none" });
	    	//$("#dt_display_div").css({ "display" : "none" });
	    });

	    $("#issue_add").click(function() {
	    	$("#issues_add_div").css({ "display" : "block" });
	    	$("#issues_buttons_div").css({ "display" : "none" });
	    	$("#issues_div").css({ "display" : "none" });
	    	$("#dt_display_div").css({ "display" : "none" });
	    });

	    $(document.body).on('change', "#airport", function() { top.location.href="/attendance.php?l=" + $("#airport").val(); });
	    $(document.body).on('click', "#issues_modify_cancel", function() { top.location.href="/display.php?xapp=ISSUES"; });
	    $(document.body).on('click', "#rules_modify_cancel", function() { top.location.href="/display.php?xapp=SYSTEM-RULES"; });
	    $(document.body).on('click', "#messages_cancel", function() { top.location.href="/messages.php?xapp=MESSAGES"; });
	    $(document.body).on('click', "#location_cancel", function() { resetLocations(); });
	    $("#census_submit").click(function() { top.location.href="/display.php?xapp=CENSUS&view_delta=" + $("#view_delta").val() + "&view_delta_date=" + $("#view_delta_date").val(); });

	    $(document.body).on('click', "#rules_journal", function() {
	    	var rulesURL = "/scripts/rules.php?xargs=NAS2018&action=JOURNAL&rid=" + $("#rid").val();
		    $.get(rulesURL, function(data) { $("#rules_journal_div").html(data); });
	    });

	    $(document.body).on('click', "#schedule_approve", function() {
	    	top.location.href = "/scripts/x.php?xargs=NAS2018&action=SCHEDULE-APPROVE&id=" + $("#id").val() + "&l=" + $("#l").val() + "&lc=0&s=" + $("#s").val() + "&notes=" + $("#notes").val();
	    });

	    $(document.body).on('click', "#schedule_details", function() {
	    	var sdURL = "/scripts/schedule.php?xargs=NAS2018&action=DETAIL-SCHEDULE&l=" + $("#l").val() + "&lc=0&s=" + $("#s").val();
		    $.get(sdURL, function(data) {
		    	$("#schedule_details_div").html(data);
		    	$("#schedule_control_div").css({ "display" : "inline" });
		    	$("#schedule_details_button_div").css({ "display" : "none" });
		    });
	    });

	    $(document.body).on('click', "#schedule_reject", function() {
	    	var rURL = "/scripts/x.php?xargs=NAS2018&action=SCHEDULE-REJECT&id=" + $("#id").val() + "&l=" + $("#l").val() + "&notes=" + $("#notes").val();
		    $.get(rURL, function(data) { top.location.href="/default.php?xapp=SCHEDULE-REJECT"; });
	    });

	    $(document.body).on('click', "#schedule_update", function() {
	    	var rURL = "/scripts/x.php?xargs=NAS2018&action=SCHEDULE-UPDATE&id=" + $("#id").val() + "&l=" + $("#l").val() + "&notes=" + $("#notes").val();
		    $.get(rURL, function(data) { top.location.href="/default.php?xapp=SCHEDULE-UPDATE"; });
	    });

	    $(document.body).on('click', "#issues_journal", function() {
	    	var issuesURL = "/scripts/issues.php?xargs=NAS2018&action=JOURNAL&iid=" + $("#iid").val();
		    $.get(issuesURL, function(data) { $("#issues_journal_div").html(data); });
	    });

	    $(document.body).on('change', "#view_profile", function() {
	    	if (employeeDisplay && $(this).val() == "4") {
	    		$("#passwd").css({ "display" : "inline" });
	    	}
	    });

	    dataTable = $("#sh_table_id").DataTable({
	    	select: true,
	        bDestroy: true,
	        deferRender: true,
	        "scrollX": true,
	        "pagingType": "simple_numbers",
	        language: { search: "" },
	        "lengthMenu": [[10, 25, 50, 200, -1], [10, 25, 50, 250, "All"]],
	        "pageLength": 10, 
	        "buttons": [
	          {
	            extend: 'copyHtml5',
	            exportOptions: { rows: ':visible', columns: ':visible' }
	          },
	          {
	            extend: 'excelHtml5',
	            exportOptions: { rows: ':visible', columns: ':visible' }
	          },
	          {
	            extend: 'csvHtml5',
	            exportOptions: { rows: ':visible', columns: ':visible' }
	          },
	          {
	            extend: 'colvis',
	            collectionLayout: 'two-column'
	          }
	        ],
	        "fnInitComplete":function() {  }
	    })
	    $('.dataTables_filter input').attr("placeholder", "Search ...");
	    dataTable.buttons().container().appendTo($('div.eight.column:eq(0)', dataTable.table().container()));

	    <?php if ($dtScroll) { ?>
	    var w = $("#container_table").css('width');
	    $(".dataTables_wrapper").css({"width" : w});
		<?php } ?>


		<?php 

		echo "$(\"#sh_table_id\").DataTable().draw();\n";
		if ($locationsDisplay) {
			for ($x = 0; $x < sizeof($locationsArray); $x++) {
				echo "locationsArray[" . $x . "] = \"" . $locationsArray[$x][12] . "\";\n";
			}
		}

		?>

		$('#sh_table_id tbody').on( 'click', 'tr', function () {
			if (securityDisplay) { return; }
			if (censusDisplay) {
				var rowArray = $(this).html().split("<td>");
				var row = rowArray[0].split(">")[1];
				var censusID = row.match(/\d+/);
				var profileURL;

				$("#dt_display_div").css({ "display" : "none" });
				$("#profile_div").css({ "display" : "block" });

				<?php if (isset($_REQUEST['view_delta'])) { ?>
					profileURL = "/scripts/profile.php?xargs=NAS2018&action=PROFILE&delta=1&cid=" + censusID;
				<?php } else { ?>
					profileURL = "/scripts/profile.php?xargs=NAS2018&action=PROFILE&delta=0&cid=" + censusID;
				<?php } ?>
				$.get(profileURL, function(data) { $("#profile_div").html(data); });
			}

			if (employmentAppDisplay) {
				var rowArray = $(this).html().split("<td>");
				var row = rowArray[0].split(">")[1];
				var appID = row.match(/\d+/);
				var profileURL;

				$("#dt_display_div").css({ "display" : "none" });
				$("#employee_application_div").css({ "display" : "block" });

				profileURL = "/scripts/profile.php?xargs=NAS2018&action=APPLICATION&aid=" + appID;
				$.get(profileURL, function(data) { $("#employee_application_div").html(data); });
			}

			if (employeeDisplay) {
				var rowArray = $(this).html().split("<td>");
				var employeeID = rowArray[0].split(">")[1].split("<")[0];
				var lastName = rowArray[1].split("<")[0];
				var firstName = rowArray[2].split("<")[0];
				var profileURL;

				$("#dt_display_div").css({ "display" : "none" });
				$("#profile_div").css({ "display" : "block" });

				$("#employee_view_div").css({ "display" : "none" });
				$("#employee_control_div").css({ "display" : "inline" });

				<?php if (isset($_REQUEST['p']) && $_REQUEST['p'] != "") { ?>
	      			profileURL = "/scripts/profile.php?xargs=NAS2018&action=NASPROFILE&eid=" + employeeID + "&p=<?php echo $_REQUEST['p'] ?>&l=" + lastName + "&f=" + firstName;
	      			$("#view_profile").val(<?php echo $_REQUEST['p'] ?>);
	      		<?php } else { ?>
	      			profileURL = "/scripts/profile.php?xargs=NAS2018&action=NASPROFILE&eid=" + employeeID + "&p=1&l=" + lastName + "&f=" + firstName;
	      		<?php } ?>
				$.get(profileURL, function(data) { 
					$("#profile_div").html(data);
					if ($("#login_status_div").html() == "SUSPENDED") { $("#view_profile option[value='5']").remove(); }
				});

				globalEmployeeID = employeeID;
				globalFirstName = firstName;
				globalLastName = lastName;
			}

			if (locationsDisplay) {
				rowIndex = $(this).index();
				var rowArray = $(this).html().split("<td>");
				var locationCode = rowArray[6].split("<")[0];
				var locationURL = "/scripts/locations.php?xargs=NAS2018&action=LOCATIONS&locationCode=" + locationCode + "&p=" + dataTable.page();

				$("#dt_display_div").css({ "display" : "none" });
				$("#locations_div").css({ "display" : "block" });
				$.get(locationURL, function(data) { $("#locations_div").html(data); });
			}

			if (messagesDisplay) {
				rowIndex = $(this).index();
				var rowData = $(this).html().split("<td ");
				//if (rowData[3].split(">")[1].split("<")[0].length) { return; }

				var rowArray = $(this).html().split("<td>");
				var messageID = rowArray[0].split(">")[1].split("<")[0];
				var messageURL = "/scripts/messages.php?xargs=NAS2018&action=MESSAGES&messageID=" + messageID;

				$("#dt_display_div").css({ "display" : "none" });
				$("#messages_div").css({ "display" : "block" });
				$("#station_log_div").css({ "display" : "none "});
				$("#message_view_control_div").css({ "display" : "none" });
				$.get(messageURL, function(data) { $("#messages_div").html(data); });
			}

	      	if (rulesDisplay) {
				var rowArray = $(this).html().split("<td>");
				var row = rowArray[0].split(">")[1];
				var ruleID = row.match(/\d+/);

	      		$("#requirements_add_div").css({ "display" : "none" });
		    	$("#rules_buttons_div").css({ "display" : "inline" });
		    	$("#requirements_div").css({ "display" : "block" });
		    	$("#dt_display_div").css({ "display" : "none" });
		    	$("#rules_journal_div").html("");

		    	var rulesURL = "/scripts/rules.php?xargs=NAS2018&action=RULES&rid=" + ruleID;
		    	$.get(rulesURL, function(data) { $("#requirements_div").html(data); });
	      	}

	      	if (issuesDisplay) {
	      		var rowArray = $(this).html().split("<td>");
				var row = rowArray[0].split(">")[1];
				var issueID = row.match(/\d+/);

	      		$("#issues_add_div").css({ "display" : "none" });
		    	$("#issues_buttons_div").css({ "display" : "inline" });
		    	$("#issues_div").css({ "display" : "block" });
		    	$("#dt_display_div").css({ "display" : "none" });
		    	$("#issues_journal_div").html("");

		    	var issuesURL = "/scripts/issues.php?xargs=NAS2018&action=ISSUES&iid=" + issueID;
		    	$.get(issuesURL, function(data) { $("#issues_div").html(data); });
	      	}

	      	if (positionsDisplay) {
	      		var rowArray = $(this).html().split("<td>");
				var row = rowArray[0].split(">")[1];
				positionID = row.match(/\d+/);

				$("#positions_div").css({ "display" : "inline" });
	      		$("#dt_display_div").css({ "display" : "none" });

		    	var positionURL = "/scripts/positions.php?xargs=NAS2018&action=POSITIONS&pid=" + positionID;
		    	$.get(positionURL, function(data) { positionArray
		    		$("#positions_div").html(data);positionArray

			    	bizTitleArray = $("#position_array").val().split(",");
			    	authorized = $("#authorized").val();

		    		if (bizTitleArray[0] != "") {
		    			initPositionSearchDiv(true);

			    		for (var z = 0; z < bizTitleArray.length; z++) {
			    			if (bizTitleArray[z] != "") {
			    				positionArray[z] = z;
			    				initPositionRow("position", bizTitleArray[z], z, (bizTitleArray[z]).toUpperCase(), authorized);
			    			}
			    		}
		    		}
		    	});
	      	}

		    if (permissionsDisplay) {
	      		var rowArray = $(this).html().split("<td>");
				var row = rowArray[0].split(">")[1];
				permissionID = row.match(/\d+/);

	      		$("#permissions_div").css({ "display" : "inline" });
	      		$("#dt_display_div").css({ "display" : "none" });

		    	var permissionURL = "/scripts/permissions.php?xargs=NAS2018&action=PERMISSIONS&pid=" + permissionID;
		    	$.get(permissionURL, function(data) { 
		    		$("#permissions_div").html(data);

		    		if ($("#employee_array").val() != "") { employeeArray = $("#employee_array").val().split(","); } else { employeeArray = null; }
		    		if ($("#position_array").val() != "") { positionArray = $("#position_array").val().split(","); } else { positionArray = null; }
		    		if ($("#location_array").val() != "") { locationArray = $("#location_array").val().split(","); } else { locationArray = null; }
		    		authorized = $("#authorized").val();

		    		if (positionArray != null && positionArray[0] != "") {
		    			positionDiv = loadPermissionRow();
	    				var ppURL = "/scripts/pp.php?xargs=NAS2018&action=POSITION-PERMISSIONS&pid=" + permissionID;
			    		$.get(ppURL, function(data) { $("#permissions_" + positionDiv + "_display_div").html(data); });
			    		for (var z = 0; z < positionArray.length; z++) {
			    			if (positionArray[z] != "") {
			    				positionNameArray[z] = positionArray[z].split("|")[1];
			    				positionArray[z] = positionArray[z].split("|")[0];
			    				initPermissionRow("position", positionArray[z], positionNameArray[z], authorized);
			    			}
			    		}
		    			$("#permission_position").prop('checked', true);
		    		}

		    		if (employeeArray != null && employeeArray[0] != "") {
		    			employeeDiv = loadPermissionRow();
	    				var peURL = "/scripts/pe.php?xargs=NAS2018&action=EMPLOYEE-PERMISSIONS&pid=" + permissionID;
			    		$.get(peURL, function(data) { $("#permissions_" + employeeDiv + "_display_div").html(data); });
			    		for (var z = 0; z < employeeArray.length; z++) {
			    			if (employeeArray[z] != "") {
			    				employeeNameArray[z] = employeeArray[z].split("|")[1];
			    				employeeArray[z] = employeeArray[z].split("|")[0];
			    				initPermissionRow("employee", employeeArray[z], employeeNameArray[z], authorized);
			    			}
			    		}
		    			$("#permission_employee").prop('checked', true);
		    		}

		    		if (locationArray != null && locationArray[0] != "") {
		    			locationDiv = loadPermissionRow();
	    				var plURL = "/scripts/pl.php?xargs=NAS2018&action=LOCATION-PERMISSIONS&pid=" + permissionID;
			    		$.get(plURL, function(data) { $("#permissions_" + locationDiv + "_display_div").html(data); });
			    		for (var z = 0; z < locationArray.length; z++) {
			    			if (locationArray[z] != "") {
			    				locationNameArray[z] = locationArray[z].split("|")[1];
			    				locationArray[z] = locationArray[z].split("|")[0];
			    				initPermissionRow("location", locationArray[z], locationNameArray[z], authorized);
			    			}
			    		}
		    			$("#permission_location").prop('checked', true);
		    		}
		    	});
	      	}
	    });

	    if (profileDisplay) {
      		var profileURL;
      		<?php if (isset($_REQUEST['p']) && $_REQUEST['p'] != "") { ?>
      			profileURL = "/scripts/profile.php?xargs=NAS2018&action=NASPROFILE&eid=<?php echo $_SESSION['user']->getEmployeeID(); ?>&p=<?php echo $_REQUEST['p'] ?>";
      			$("#view_profile").val(<?php echo $_REQUEST['p'] ?>);
      		<?php } else { ?>
      			profileURL = "/scripts/profile.php?xargs=NAS2018&action=PROFILE&delta=0&cid=<?php echo $_SESSION['user']->getCensusID(); ?>";
      		<?php } ?>
			$.get(profileURL, function(data) { $("#profile_div").html(data); });
      	}

      	if (reportsDisplay) {
      		var reportsURL;
      		reportsURL = "/scripts/reports.php?xargs=NAS2018&action=POSITIONS";
      		$.get(reportsURL, function(data) { $("#reports_display_div").html(data); });
      	}

      	if (viewGlobalSchedule || checkDirector("<?php echo $_SESSION['user']->checkDirector(); ?>")) { $("#project").css({ "display" : "inline" }); }
      	if (scheduleEditMode) { $("#schedule_edit_div").css({ "display" : "inline" }); }

      	if (scheduleCreateMode) {
      		$("#schedule_create_div").css({ "display" : "inline" });
      		$("#schedule_widget_div").css({ "display" : "inline" });
      	}
      	
		if (scheduleOpenWorkFlow && scheduleCurrentWeek) {
			$("#schedule_edit_div").html("&nbsp;&nbsp;<font size='4' face='Arial'>Schedule change awaiting approval.</font>").css({ "display" : "inline" });
		}

		if ("<?php echo $shiftClockTime; ?>") {
			var shiftTotalTime = "<?php echo $shiftTotalTime; ?>";
			var t0 = new Date("<?php echo $shiftClockTime ?>");
			var t1 = new Date();

			if (shiftTotalTime == "") { shiftTotalTime = 0; } else { shiftTotalTime = parseInt(shiftTotalTime); }
			if ("<?php echo $shiftTotalTime; ?>") {
				totalSeconds = parseInt(((t1 - t0)  / 1000) + shiftTotalTime);
			} else {
				totalSeconds = parseInt((t1 - t0) / 1000);
			}
			timer = setInterval(countTimer, 1000);
		}

	});
</script>
