if(typeof MW=='undefined')
{
    MW = {
        ProductView: {},
        FollowUpEmail : {
            SystemConfig: {
                Caltax: {}
            },
            Report: {
                Dashboard: {}
            }
        }
    };
}else{
    MW.ProductView = {};
    MW.FollowUpEmail = {
        SystemConfig: {
            Caltax: {}
        },
        Report: {
            Dashboard: {}
        }
    }
}
MW.ProductView = Class.create();

MW.ProductView.prototype =
{
    index_price: 0,
    initialize: function()
    {
        var thisView = this;
        this._(false);

        try
        {
            $$('.mw_show_hide_set_point').invoke('observe', 'click', this.showHide.bind(this));

            $$("input[name*=reward_point_product], input[name*=mw_reward_point_sell_product]").each(function(el){
                el.observe('keydown', thisView.pastePoint.bind(this));
            });

            $("mw-lb-set-point").observe('click', function(event){
                var checkbox = $$('input[name=product\\[mw_show_hide_set_point\\]]');

                $$('.mw_show_hide_set_point').each(function(element) {
                    if (element.checked) {
                        element.value = 0;
                        element.checked = false;
                    }else{
                        element.value = 1;
                        element.checked = true;
                    }
                    thisView.showHide(null, element);
                });
            });
        }catch(e){}
    },
    pastePoint: function(ev, element)
    {
        if(ev.keyCode == 67)
        {
            element = $(Event.element(ev));
            var index_price = 0;
            $$(".mw-reward-point-product thead th span").each(function(el, index){
                if(el.innerText == 'Price'){
                    index_price = index;
                    return;
                }
            });
            var price = element.up('tr').select('td:eq('+index_price+')')[0].innerText;
            if(price.indexOf('.'))
            {
                price = price.split('.');
                price = price[0].replace(/[^0-9\s]/gi, '');
            }
            else
            {
                price = price[0].replace(/[^0-9\s]/gi, '');
            }
            element.setValue(price);
            Event.stop(ev);
        }
    },
    _: function(flag)
    {
        /** Show/Hide label Sell Products in Points */
        $$('label[for=mw_reward_point_sell_product]').each(function(s){
            if(flag){$(s).up('tr').show();}
            else{$(s).up('tr').hide();}
        });

        /** Change text label "Reward Points Earned" to "Set Points to Earn/Reward" or otherwise  */
        $$('label[for=reward_point_product]').each(function(s){
            if(flag){$(s).update("Reward Points Earned");}
            else{$(s).update("Set Points to Earn/Sell in Point");}
        });
    },
    showHide: function(ev, element)
    {
        if (element == undefined)
        {
            element = $(Event.element(event));
        }

        this._(element.checked);

        if(element.checked)
        {
            $$(".mw-reward-point-product").each(Element.hide);
            $$(".mw-reward-point-input").each(Element.show);
            element.value = 1;
        }
        else
        {
            $$(".mw-reward-point-product").each(Element.show);
            $$(".mw-reward-point-input").each(Element.hide);
            element.value = 0;
        }
    }
};

MW.FollowUpEmail.SystemConfig.Caltax = Class.create();

MW.FollowUpEmail.SystemConfig.Caltax.prototype =
{
    initialize: function(params)
    {
        this.params = params;
        try
        {
            this._($(params.element).value);
            $(params.element).observe('change', this.onChange.bind(this));
        }catch(e){
            console.log(e);
        }
    },
    onChange: function(ev, element)
    {
        if (element == undefined)
        {
            element = $(Event.element(event));
        }

        this._(element.value);
    },
    _: function(value)
    {
        if(value == this.params.BEFORE_VALUE)
        {
            $("row_FollowUpEmail_config_redeemed_tax").hide();
            $("row_FollowUpEmail_config_redeemed_shipping").hide();
        }
        else if(value == this.params.AFTER_VALUE)
        {
            $("row_FollowUpEmail_config_redeemed_tax").show();
            $("row_FollowUpEmail_config_redeemed_shipping").show();
        }
    }
};

MW.FollowUpEmail.Report.Dashboard = Class.create();
MW.FollowUpEmail.Report.Dashboard.prototype = {
    initialize: function(params){
        var self = this;

        this.params = params;

        Event.observe('report_range', 'change', this.onChangeRange.bind(this));
        Event.observe('report_refresh', 'click', this.onClickRefresh.bind(this));
        Event.observe(window, 'keypress', this.onWindowKeypress.bind(this));

        this.onChangeRange(null, $("report_range"));

        Calendar.setup({
            inputField: "report_from",
            ifFormat: "%m/%e/%Y %H:%M:%S",
            showsTime: true,
            button: "date_select_trig",
            align: "Bl",
            singleClick : true
        });
        Calendar.setup({
            inputField: "report_to",
            ifFormat: "%m/%e/%Y %H:%M:%S",
            showsTime: true,
            button: "date_select_trig",
            align: "Bl",
            singleClick : true
        });
    },
    onWindowKeypress: function(event, element){
        console.log(event.keyCode);
        if(Event.KEY_RETURN == event.keyCode){
            this.onChangeRange(null, $("report_range"));
        }

        if(event.keyCode == 115){
            var output = '';
            for (property in this.statistics) {
                output += property + ': ' + this.statistics[property]+"; <br>\n";
            }
            $("debug").innerHTML = output;
        }
        if(event.keyCode == 99){
            $("debug").innerHTML = '';
        }
    },
    onClickRefresh: function(event, element){
        var self = this;
        new Ajax.Request(this.params.url, {
            method: 'post',
            parameters: {ajax: true, report_range: $("report_range").value, from: $("report_from").value,  to: $("report_to").value, type: 'dashboard'},
            onSuccess: function(transport){
                if(transport.responseText){
                    var data = transport.responseText.evalJSON();
                    self.data = data.report;
                    self.buildChart(parseInt($("report_range").value));
                    self.buildPieChart(data.report_activities);
                    self.fillDataTemplate(data.template_statistics);
                    self.fillDataStats(data.statistics);
                }else{
                    /** Draw empty grah */
                }
            }
        });
    },
    onChangeRange: function(event, element){
        var self = this;

        if(element == undefined){
            element = $(Event.element(event));
        }
        if(parseInt(element.value) == 7){
            $("custom_range").show();

            return false;
        }

        $("custom_range").hide();
        new Ajax.Request(this.params.url, {
            method: 'post',
            parameters: {ajax: true, report_range: element.value, type: 'dashboard'},
            onSuccess: function(transport){
                if(transport.responseText){
                    var data = transport.responseText.evalJSON();

                    self.data = data.report;
                    self.buildChart(parseInt(element.value));
                    self.buildPieChart(data.report_activities);
                    self.fillDataStats(data.statistics);
                    self.fillDataTemplate(data.template_statistics);
//                    console.log(data.template_statistics);
//                    self.statistics = data.statistics;
                }else{
                    /** Draw empty grah */
                }
            }
        });
    },
    fillDataTemplate : function(data){

        data = JSON.parse(data);
        var myTemplate = new Template('<tr class="even pointer">'
            +'<td> #{name} </td>'
            +'<td class="a-right a-right "> #{sent} </td>'
            +'<td class="a-right a-right "> #{readed} </td>'
            +'<td class="a-right a-right last" > #{percent} %</td>'
            +'</tr>');

        var render_html = '';
        for(var key in data){
            var obj = data[key];
            var temp =  myTemplate.evaluate(obj)
            render_html = render_html + temp;
        }

        $("fue-tbody").update("");
        $("fue-tbody").update(render_html);

    },
    runningPieChart: function(){
        var self = this;

        new Ajax.Request(this.params.url, {
            method: 'post',
            parameters: {ajax: true, type: 'circle'},
            onSuccess: function(transport){
                if(transport.responseText){
                    self.buildPieChart(transport.responseText);
                }else{
                    /** Draw empty grah */
                }
            }
        });
    },
    fillDataStats: function(data){
        /* fill data to statics top */
        $("total_email").innerHTML = data.total_email;
        $("total_email_sent").innerHTML = data.total_email_sent;
        $("total_email_read").innerHTML = data.total_email_readed;

    },
    buildPieChart: function(data){
        var data = data.evalJSON();
        var _data = new Array();
        if(Object.keys(data).length == 0){
            $("rwp-container-pie").innerHTML = '<span style="color: #ccc; text-align: center; display: block;">NULL</span>';
            return false;
        }
        for(var i = 0; i < Object.keys(data).length; i++){
            var _data_item = new Array();
            _data_item.push(data[i][0], data[i][1]);
            _data.push(_data_item);
        }

        var chart = new Highcharts.Chart({
            chart: {
                renderTo: 'rwp-container-pie',
                plotBackgroundColor: null,
                plotBorderWidth: 1,//null,
                plotShadow: false
            },
            exporting:{
                enabled: false
            },
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y:.2f}%</b>'
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                x: 0,
                verticalAlign: 'top',
                y: 20,
                floating: true,
                backgroundColor: 'transparent',
                labelFormatter: function () {
                    return this.name+ ": "+this.y + "%";
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        connectorWidth: 2,
                        format: '{point.name}: {point.y:.2f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        },
                    },
                    showInLegend: false
                }
            },
            series: [{
                type: 'pie',
                name: 'Browser share',
                data: _data
            }]
        });
    },
    buildChart: function(type){
        var self = this;
        self.buildOptionChart(type);

        var chart = new Highcharts.Chart({
            chart: {
                renderTo: 'rwp-container',
            },
            exporting:{
                enabled: false
            },
            title: {
                text: self.data.title
            },
            subtitle: {
                text: ''
            },
            xAxis: self.xAxis,
            yAxis: [{ // Secondary yAxis
                title: {
                    text: 'Read Email(s)',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value:.,0f}',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                }
            },
                { // Primary yAxis
                    labels: {
                        format: '{value:.,0f}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    title: {
                        text: 'Sent Email(s)',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    opposite: true
                }],
            tooltip: {
                shared: true,
                crosshairs: true
            },
            plotOptions: {
                series: {
                    fillOpacity: 0.1
                },
                area: {
                    pointStart: 1940,
                    marker: {
                        enabled: true,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                }
            },
            series: this.series
        });
    },
    buildOptionChart: function(type){
        var self = this;
        var data_sent = null;
        var data_readed = null;
        switch(type){
            case 1: // Last 24 hours
                self.xAxis = {
                    type: 'datetime',
                    labels: {
                        format: '{value:%H:%M}',
                        //rotation: 45,
                        align: 'left'
                    }
                };
                var pointStart = Date.UTC(self.data.date_start.y, self.data.date_start.m - 1, self.data.date_start.d, self.data.date_start.h);
                var pointInterval = 1 * 3600 * 1000;

                data_sent = self.data.sent;
                data_readed = self.data.readed;
                break;
            case 2: // Last week
                self.xAxis = {
                    type: 'datetime',
                    tickInterval: 24 * 3600 * 1000,
                    labels: {
                        format: '{value:%b %d}',
                        align: 'left'
                    }
                };
                var pointStart = Date.UTC(self.data.date_start.y, self.data.date_start.m - 1, self.data.date_start.d);
                var pointInterval = 24 * 3600 * 1000;

                data_sent = self.data.sent;
                data_readed = self.data.readed;
                break;
            case 3: // Last month
                self.xAxis = {
                    type: 'datetime',
                    tickInterval: 7 * 24 * 3600 * 1000,
                    labels: {
                        format: '{value:%b %d}',
                        align: 'left'
                    }
                };
                var pointStart = Date.UTC(self.data.date_start.y, self.data.date_start.m - 1, self.data.date_start.d);
                var pointInterval = 24 * 3600 * 1000;

                data_sent = self.data.sent;
                data_readed = self.data.readed;
                break;
            case 4: // Last 7 days
            case 5: // Last 30 days
            case 7: // Custom range
                self.xAxis = {
                    type: 'datetime',
                    dateTimeLabelFormats: {
                        month: '%e. %b',
                        year: '%b'
                    }
                };

                var redeemed = new Array();
                self.data.sent.each(function(value, k){
                    redeemed.push([Date.UTC(value[0],  value[1] - 1,  value[2]), value[3]]);
                });

                var rewarded = new Array();
                self.data.readed.each(function(value, k){
                    rewarded.push([Date.UTC(value[0],  value[1] - 1,  value[2]), value[3]]);
                });

                data_sent = redeemed;
                data_readed = rewarded;

                var pointStart = null;
                var pointInterval = 24 * 3600 * 1000;
                break;
        }

        this.series = [{
            name: 'Read Email(s)',
            type: 'area',
            color: '#C74204',
            data: data_readed,
            tooltip: {
                valueSuffix: ''
            },
            pointStart: pointStart,
            pointInterval: pointInterval
        },
            {
                name: 'Sent Email(s)',
                type: 'area',
                color: '#0481C7',
                data: data_sent,
                yAxis: 1,
                tooltip: {
                    valueSuffix: ''
                },
                pointStart: pointStart,
                pointInterval: pointInterval
            }];
    },
    print_r: function(printthis, returnoutput){
        var output = '';
        var self = this;
        for(var i in printthis) {
            output += i + ' : ' + self.print_r(printthis[i], true) + '\n';
        }
        if(returnoutput && returnoutput == true) {
            return output;
        }else {
            alert(output);
        }
    }
}