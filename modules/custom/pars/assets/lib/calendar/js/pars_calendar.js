jQuery(document).ready(function($) {

	function showLoader() {
		var $loader = jQuery(document.createElement("div")).attr("id", "pars-calendar-ajax-loader").show();
		jQuery("#block-calendarblock div.content").append($loader);
	}
	function hideLoader() {
		jQuery("#pars-calendar-ajax-loader").remove();
	}

	showLoader();

	/*===================== TOOLTIP ============================*/
	var viewportWidth = jQuery(window).width();
	var viewportHeight = jQuery(window).height();
	var narrowScreen = false;

	jQuery(window).resize(function() {
		viewportWidth = jQuery(window).width();
		viewportHeight = jQuery(window).height();
		setWeekdaysWidth();
		adjustViewsHeight();
	});
	setWeekdaysWidth();
	var $tooltip;
	jQuery('#pars-calendar-block .day-cell-event').live("mouseover", function(ev) {
		var $cell = jQuery(this);
		$tooltip = jQuery(jQuery(this).find(".pars-tooltip"));
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

	jQuery('#pars-calendar-block .day-cell-event .pars-tooltip.activeTooltip').live("mouseover", function(){
		jQuery(this).removeClass("inactiveTooltip");
		jQuery(this).removeClass("tdTooltipHover");
		jQuery(this).addClass("tooltipHover");
		jQuery(this).addClass("activeTooltip");
	}).live("mouseout", function() {
		jQuery(this).removeClass("tooltipHover")
		if(!jQuery(this).hasClass("tdTooltipHover")){
			jQuery(this).addClass("inactiveTooltip");
		}
	});
	/*================== NAVIGATION ========================*/
	jQuery("#pars-calendar-block .calendar-nav").live("click", function(e) {
		showLoader();
		var date = jQuery(this).closest("a").data("goto");
		var language = jQuery(this).closest("a").data("lang");
		$.ajax({
			url: "/calendar-navigation?date=" + date + "&language=" + language,
			type: "GET",
			dataType: "json",
			success: function(data) {
				showLoader();
				jQuery("#block-calendarblock div.content").html(data.calendar);
				jQuery("#pars-calendar-read-more").attr("href", jQuery(".pars-calendar-block-title a").attr("href"));
				setWeekdaysWidth();
			}
		});

		e.preventDefault();
		return false;
	});

	//============= BACK TO MONTHLY VIEW LINK ==========================
	jQuery("#pars-calendar-block .pars-calendar-daily-view-margin .level-up").live("click", function(e) {
		showLoader();

		var date = jQuery("#pars-calendar-block-wrapper .pars-calendar-block-title a").data("goto");
		var language = jQuery("#pars-calendar-block").attr("lang");
		$.ajax({
      url: "/calendar-navigation?date=" + date + "&language=" + language,
			type: "GET",
			dataType: "json",
			success: function(data) {
				showLoader();
				jQuery("#block-calendarblock div.content").html(data.calendar);
				jQuery("#pars-calendar-read-more").attr("href", jQuery(".pars-calendar-block-title a").attr("href"));
				setWeekdaysWidth();
			}
		});

		e.preventDefault();
		return false;
	});

	// Prevent link around navbar
	jQuery("#pars-calendar-block th.nav a").live("click", function(e) {
		e.preventDefault();
		return false;
	});
	/*============= READ MORE LINK ===============*/
	jQuery("#pars-calendar-read-more").attr("href", jQuery(".pars-calendar-block-title a").attr("href"));

	hideLoader();

	//============= DISABLE TOOLTIP CLICK AND HOVER ====================
	jQuery("#pars-calendar-block .day-cell-event .pars-tooltip").live("click, mouseover", function(e) {
		e.preventDefault();
		return false;
	});

	//======================== MOBILE TOUCH EVENT FIXES ======================

	// Daily view
	jQuery("#pars-calendar-block .day-cell-event").live('touchstart', function(e) {
	  jQuery(this).trigger("click");
	  e.preventDefault();
	});
	// GO back link
	jQuery("#pars-calendar-block .pars-calendar-daily-view-margin .level-up").live('touchstart', function(e) {
		  jQuery(this).trigger("click");
		  e.preventDefault();
	});

	/*======== HELPER FUNCTIONS ==================*/

	/**
	 * Trim weekday's name
	 */
	function setWeekdaysWidth() {
		if(viewportWidth <= 780) {
			jQuery("#pars-calendar-block-wrapper #pars-calendar-block .day-title-cell").each(function() {
				jQuery(this).html(jQuery(this).attr("data-name").substr(0, 3));
			});
		}
		else {
			jQuery("#pars-calendar-block-wrapper #pars-calendar-block .day-title-cell").each(function() {
				jQuery(this).html(jQuery(this).attr("data-name"));
			});
		}
	}

	/**
	 * Adjusts height of monthly/daily view on resize, rotate etc.
	 */
	function adjustViewsHeight() {
		$blockHeight = jQuery("#pars-calendar-block-wrapper").height();
		$title = jQuery("#block-pars-calendar-pars-calendar .pars-calendar-view-title");
		var offset = $title.height() + parseInt($title.css("marginTop")) + parseInt($title.css("marginBottom") + parseInt(jQuery("#pars-calendar-block-wrapper .pars-calendar-daily-view").css("margin-bottom")));
		jQuery("#pars-calendar-view").css("height", parseInt($blockHeight - offset) + "px");
	}
});
