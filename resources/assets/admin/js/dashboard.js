(function ($) {
	
	
	// Bar Chart ------------------------------------------------------------------------- //
//	var barChart = {
//		barChartDefaults: {
//            type: 'bar',
//            tooltipFillColor: "rgba(51, 51, 51, 0.55)",
//            //data: [{x:'2016-12-25', y:20}, {x:'2016-12-26', y:10}],
//            data: {
//                labels: ["Devices"],
//                datasets: [{
//                    data: [{x:'2016-12-25', y:20}, {x:'2016-12-26', y:10}]
//                }]
//            },
//            options: {
//                legend: false,
//                responsive: false
//            }
//        },
//        init: function ($el) {
//            var self = this;
//            $el = $($el);
//
//            $.ajax({
//                url: 'admin/dashboard/active_inactive_licenses_graph',
//                success: function (response) {
//                    
////                    $('#active_inactive_licenses_graph_quantity').text(total);
////                    $('#active_inactive_licenses_graph_quantity').parent().find('.fa-square').css('color','#ECEFF1');
////                    
////                    $('#active_inactive_licenses_graph_active').text(response.active);
////                    $('#active_inactive_licenses_graph_active').parent().find('.fa-square').css('color','#64B5F6');
////                    
////                    $('#active_inactive_licenses_graph_inactive').text(response.inactive);
////                    $('#active_inactive_licenses_graph_inactive').parent().find('.fa-square').css('color','#EF5350');
//
//                    // must have 4 elements
////                    self._defaults.data.datasets[0].data = [response.active, response.inactive, total==0?1:0, 0];
//
//                    new Chart($el.find('#canvas'), self.barChartDefaults);
//                }
//            });
//        }
//    };
//	barChart.init($('#bar_graph'));
	// ----------------------------------------------------------------------------------- //
	
	
	// Pie Chart ------------------------------------------------------------------------- //
	var activeInactiveLicensesGraph = {
        _defaults: {
            type: 'doughnut',
            tooltipFillColor: "rgba(51, 51, 51, 0.55)",
            data: {
                labels: ["Active","Inactive"],
                datasets: [{
                    data: [],
                    backgroundColor: [
                    		"#64B5F6",
                        	"#EF5350",
                    		"#ECEFF1",
                    		"#ECEFF1"
                    ],
                    hoverBackgroundColor: [
                    		"#90CAF9",
                    		"#E57373",
                    		"#ECEFF1",
                    		"#ECEFF1"
                    ]
                }]
            },
            options: {
                legend: false,
                responsive: false
            }
        },
        init: function ($el) {
            var self = this;
            $el = $($el);

            $.ajax({
                url: 'admin/dashboard/active_inactive_licenses_graph',
                success: function (response) {
                		
                		var total = parseInt(response.inactive) + parseInt(response.active);
                		if (total == 0) {
                			Chart.defaults.global.tooltips.enabled = false;
                		}
                    
                    $('#active_inactive_licenses_graph_quantity').text(total);
                    $('#active_inactive_licenses_graph_quantity').parent().find('.fa-square').css('color','#ECEFF1');
                    
                    $('#active_inactive_licenses_graph_active').text(response.active);
                    $('#active_inactive_licenses_graph_active').parent().find('.fa-square').css('color','#64B5F6');
                    
                    $('#active_inactive_licenses_graph_inactive').text(response.inactive);
                    $('#active_inactive_licenses_graph_inactive').parent().find('.fa-square').css('color','#EF5350');

                    // must have 4 elements
                    self._defaults.data.datasets[0].data = [response.active, response.inactive, total==0?1:0, 0];

                    new Chart($el.find('.canvasChart'), self._defaults);
                }
            });
        }
    };
	activeInactiveLicensesGraph.init($('#active_inactive_licenses_graph'));
	// ----------------------------------------------------------------------------------- //
	
	
	
    // Log Chart ------------------------------------------------------------------------- //
    var logActivity = {
        options: {
            date: {
                startDate: moment().subtract(6, 'days'),
                endDate: moment(),
                minDate: moment().subtract(60, 'days'),
                maxDate: moment(),
                dateLimit: {
                    days: 60
                },
                showDropdowns: true,
                showWeekNumbers: true,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                opens: 'left',
                buttonClasses: ['btn btn-default'],
                applyClass: 'btn-small btn-primary',
                cancelClass: 'btn-small',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                locale: {
                    applyLabel: 'Submit',
                    cancelLabel: 'Clear',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom',
                    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                    monthNames: [
                        'January',
                        'February',
                        'March',
                        'April',
                        'May',
                        'June',
                        'July',
                        'August',
                        'September',
                        'October',
                        'November',
                        'December'
                    ],
                    firstDay: 1
                }
            },
            chart: {
                series: {
                    lines: {
                        show: false,
                        fill: true
                    },
                    splines: {
                        show: true,
                        tension: 0.4,
                        lineWidth: 1,
                        fill: 0.4
                    },
                    points: {
                        radius: 0,
                        show: true
                    },
                    shadowSize: 2
                },
                grid: {
                    verticalLines: true,
                    hoverable: true,
                    clickable: true,
                    tickColor: "#d5d5d5",
                    borderWidth: 1,
                    color: '#fff'
                },
                colors: ["#B71C1C", "#D32F2F", '#F44336', '#FF5722', '#FF9100', '#4CAF50', '#1976D2', '#90CAF9'],
                xaxis: {
                    tickColor: "rgba(51, 51, 51, 0.06)",
                    mode: "time",
                    tickSize: [1, "day"],
                    //tickLength: 10,
                    axisLabel: "Date",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Verdana, Arial',
                    axisLabelPadding: 10
                },
                yaxis: {
                    ticks: 8,
                    tickColor: "rgba(51, 51, 51, 0.06)",
                },
                tooltip: true
            }
        },
        gteChartData: function ($el, start, end) {
            var self = this;

            $.ajax({
                url: 'admin/dashboard/log-chart',
                data: {start: start, end: end},
                success: function (response) {
                		
                		var data = {};
                		var progress = {all: 0};

                    $.each(response, function (k, v) {
                        data[k] = [];
                        progress[k] = 0;
                        //alert(k + ":" + v)
                        $.each(v, function (date, value) {
                            data[k].push([new Date(date).getTime(), value]);
                            progress.all += value;
                            progress[k] += value;
                        });
                    });

                    $.plot($el,
                        [data.emergency, data.alert, data.critical, data.error, data.warning, data.notice, data.info, data.debug],
                        self.options.chart);


                    $.each(progress, function (k, v) {
                        var $progress = $('.progress-bar.log-' + k);
                        if ($progress.length) {
                            $progress.attr('data-transitiongoal', 100 / progress.all * v).progressbar();
                        }
                    });
                }
            });
        },
        init: function ($el) {
            var self = this;

            $el = $($el);

            var $dateEl = $el.find('.date_piker');
            var $chartEl = $el.find('.chart');

            $dateEl.daterangepicker(this.options.date, function (start, end) {
                $dateEl.find('.date_piker_label').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            });

            $dateEl.on('apply.daterangepicker', function (ev, picker) {
                self.gteChartData($chartEl, picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
            });

            self.gteChartData($chartEl, this.options.date.startDate.format('YYYY-MM-DD'), this.options.date.endDate.format('YYYY-MM-DD'));
        }
    };

    logActivity.init($('#log_activity'));
    // ----------------------------------------------------------------------------------- //


})(jQuery);
