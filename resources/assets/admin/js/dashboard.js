(function ($) {
		
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
                startDate: moment().subtract(11, 'months').startOf('month'),
                endDate: moment().endOf('month'),
                minDate: moment().subtract(23, 'months'),
                maxDate: moment().endOf('month'),
                dateLimit: {
                    months: 24
                },
                showDropdowns: true,
                showWeekNumbers: true,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                    // 'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    // 'This Month': [moment().startOf('month'), moment().endOf('month')],
                    // 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    'Last 6 months': [moment().subtract(5, 'month').startOf('month'), moment().endOf('month')],
                    'Last 12 months': [moment().subtract(11, 'month').startOf('month'), moment().endOf('month')],
                    'Last 24 months': [moment().subtract(23, 'month').startOf('month'), moment().endOf('month')],
                },
                opens: 'left',
                buttonClasses: ['btn btn-default'],
                applyClass: 'btn-small btn-primary',
                cancelClass: 'btn-small',
                format: 'MM/YYYY',
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
                    bars: {
                        show: true,
                        fill: true,
                        align: "center",
                        barWidth: 15*24*60*60*1000
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
                colors: ["#1976D2"],
                xaxis: {
                    tickColor: "rgba(51, 51, 51, 0.06)",
                    mode: "time",
                    timeformat: "%b<BR>%Y",
                    tickSize: [1, "month"],
                    axisLabel: "Month"
                },
                yaxis: {
                    ticks: 8,
                    tickDecimals: 0,
                    tickColor: "rgba(51, 51, 51, 0.06)",
                    format: "#",
                    axisLabel: 'Orders',
                    min: 0
                },
                tooltip: true,
                axisLabels: {
                    show: true
                }
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

                    $.plot($el, [data.data], self.options.chart);

                    $.each(progress, function (k, v) {
                        var $progress = $('.progress-bar.log-' + k);
                        if ($progress.length) {
                            $progress.attr('data-transitiongoal', 100 / progress.all * v).progressbar();
                        }
                    });

                    $el.UseTooltip();
                }
            });
        },
        init: function ($el) {
            var self = this;

            $el = $($el);

            var $dateEl = $el.find('.date_piker');
            var $chartEl = $el.find('.chart');

            $dateEl.daterangepicker(this.options.date, function (start, end) {
                $dateEl.find('.date_piker_label').html(start.format('MMMM YYYY') + ' - ' + end.format('MMMM YYYY'));
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