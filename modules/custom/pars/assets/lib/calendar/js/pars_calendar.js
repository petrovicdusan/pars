jQuery(document).ready(function($) {

	function showLoader() {
		var $loader = $(document.createElement("div")).attr("id", "pars-calendar-ajax-loader").show();
		$("#block-calendarblock div.content").append($loader);
	}
	function hideLoader() {
		jQuery("#pars-calendar-ajax-loader").remove();
	}

	showLoader();

	/*===================== TOOLTIP ============================*/
	var viewportWidth = $(window).width();
	var viewportHeight = $(window).height();
	var narrowScreen = false;

	$(window).resize(function() {
		viewportWidth = $(window).width();
		viewportHeight = $(window).height();
		setWeekdaysWidth();
		adjustViewsHeight();
	});
	setWeekdaysWidth();
	var pars_calendar_block = $('#pars-calendar-block');
	var $tooltip;
	$('#pars-calendar-block .day-cell-event').live("mouseover", function(ev) {
		var $cell = $(this);
		$tooltip = $($(this).find(".pars-tooltip"));
		var tooltipHeight = $tooltip.height();
		$tooltip.css({
			top: parseInt($cell.position().top + $cell.height()+5) +"px",
			left: parseInt($cell.position().left - 50) + "px",
			display: "block"
		});

		// If tooltip goes outside the screen, move it to the left
		if(viewportWidth <= $tooltip.offset().left + $tooltip.width()) {
			$tooltip.css("left", parseInt($tooltip.position().left - $tooltip.width() + $cell.width()) + "px");
		}
		$tooltip.removeClass("inactiveTooltip");
		$tooltip.removeClass("tooltipHover");
		$tooltip.addClass("tdTooltipHover");
		$tooltip.addClass("activeTooltip");
	}).live("mouseout", function() {
		$tooltip.removeClass("tdTooltipHover");
		if(!$tooltip.hasClass("tooltipHover")){
			$tooltip.addClass("inactiveTooltip");
		}
	});

	$('#pars-calendar-block .day-cell-event .pars-tooltip.activeTooltip').live("mouseover", function(){
		$(this).removeClass("inactiveTooltip");
		$(this).removeClass("tdTooltipHover");
		$(this).addClass("tooltipHover");
		$(this).addClass("activeTooltip");
	}).live("mouseout", function() {
		$(this).removeClass("tooltipHover")
		if(!$(this).hasClass("tdTooltipHover")){
			$(this).addClass("inactiveTooltip");
		}
	});
	/*================== NAVIGATION ========================*/
	$("#pars-calendar-block .calendar-nav").live("click", function(e) {
		showLoader();
		var date = $(this).closest("a").data("goto");
		var language = $(this).closest("a").data("lang");
		$.ajax({
			url: "/calendar-navigation?date=" + date + "&language=" + language,
			type: "GET",
			dataType: "json",
			success: function(data) {
				showLoader();
				$("#block-calendarblock div.content").html(data.calendar);
				$("#pars-calendar-read-more").attr("href", $(".pars-calendar-block-title a").attr("href"));
				setWeekdaysWidth();
			}
		});

		e.preventDefault();
		return false;
	});

	//============= BACK TO MONTHLY VIEW LINK ==========================
	$("#pars-calendar-block .pars-calendar-daily-view-margin .level-up").live("click", function(e) {
		showLoader();

		var date = $("#pars-calendar-block-wrapper .pars-calendar-block-title a").data("goto");
		var language = $("#pars-calendar-block").attr("lang");
		$.ajax({
      url: "/calendar-navigation?date=" + date + "&language=" + language,
			type: "GET",
			dataType: "json",
			success: function(data) {
				showLoader();
				$("#block-calendarblock div.content").html(data.calendar);
				$("#pars-calendar-read-more").attr("href", $(".pars-calendar-block-title a").attr("href"));
				setWeekdaysWidth();
			}
		});

		e.preventDefault();
		return false;
	});

	// Prevent link around navbar
	$("#pars-calendar-block th.nav a").live("click", function(e) {
		e.preventDefault();
		return false;
	});
	/*============= READ MORE LINK ===============*/
	$("#pars-calendar-read-more").attr("href", $(".pars-calendar-block-title a").attr("href"));

	hideLoader();

	//============= DISABLE TOOLTIP CLICK AND HOVER ====================
	$("#pars-calendar-block .day-cell-event .pars-tooltip").live("click, mouseover", function(e) {
		e.preventDefault();
		return false;
	});

	//======================== MOBILE TOUCH EVENT FIXES ======================

	// Daily view
	$("#pars-calendar-block .day-cell-event").live('touchstart', function(e) {
	  $(this).trigger("click");
	  e.preventDefault();
	});
	// GO back link
	$("#pars-calendar-block .pars-calendar-daily-view-margin .level-up").live('touchstart', function(e) {
		  $(this).trigger("click");
		  e.preventDefault();
	});

	/*======== HELPER FUNCTIONS ==================*/

	/**
	 * Trim weekday's name
	 */
	function setWeekdaysWidth() {
		if(viewportWidth <= 780) {
			$("#pars-calendar-block-wrapper #pars-calendar-block .day-title-cell").each(function() {
				$(this).html($(this).attr("data-name").substr(0, 3));
			});
		}
		else {
			$("#pars-calendar-block-wrapper #pars-calendar-block .day-title-cell").each(function() {
				$(this).html($(this).attr("data-name"));
			});
		}
	}

	/**
	 * Adjusts height of monthly/daily view on resize, rotate etc.
	 */
	function adjustViewsHeight() {
		$blockHeight = $("#pars-calendar-block-wrapper").height();
		$title = $("#block-pars-calendar-pars-calendar .pars-calendar-view-title");
		var offset = $title.height() + parseInt($title.css("marginTop")) + parseInt($title.css("marginBottom") + parseInt($("#pars-calendar-block-wrapper .pars-calendar-daily-view").css("margin-bottom")));
		$("#pars-calendar-view").css("height", parseInt($blockHeight - offset) + "px");
		$("#pars-calendar-view").slimScroll({
			position: 'right',
			railVisible: false,
			alwaysVisible: false
		});
	}
});
