system.directive('chartProject', function () {
    return {
        restrict: 'E',
        template: '<div></div>',
        scope: {
            categories: '=',
            series: '=',
            title: '=',
        },
        link: function (scope, element) {
            var width = $('.highcharts-figure').width();
            var process = function() {
                Highcharts.chart(element[0], {
                    chart: {
                        type: 'column',
                        width: width,
                    },
                    title: {
                        text: 'Statistics by ' + scope.title,
                        style: {}
                    },
                    subtitle: {
                        text: 'Source: Group 3'
                    },
                    xAxis: {
                        categories: scope.categories.map(i => i.name),
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Incident'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y} incident(s)</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        },
                    },
                    series: scope.series
                });
                
            }
            process();
            scope.$watch("series", function (newValue, oldValue) {
                process();
            });
        }
    };
})
system.controller('StatisticsController', function ($scope, $http, $rootScope) {

    this.prototype = new BaseController($scope, $http, $rootScope);
    $scope.filter = {};
    $scope.filter.type = 'user';
    $scope.dataSeries = [];
    $scope.categories = [];
    $scope.title = 'user';

    $(function() {
        $('#from').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            startDate: moment().subtract(1, 'months').endOf('day'),
            endDate: moment().endOf('day').add(32, 'hour'),
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss',
            }
        });
        $('#to').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            startDate: moment().endOf('day'),
            endDate: moment().endOf('day').add(32, 'hour'),
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss',
            }
        });
    });

    function init() {
        $scope.reset();
        $scope.find();
    }

    $scope.getUsers = function() {
        $http({
            method: 'GET',
            url: '/api/user'
        }).then(function successCallback(response) {
            if (response.data.status == 'successful' && response.data.result.length > 0) {
                $scope.categories = response.data.result;
            }
        });
    }

    $scope.getDrones = function() {
        $http({
            method: 'GET',
            url: '/api/drone'
        }).then(function successCallback(response) {
            if (response.data.status == 'successful' && response.data.result.length > 0) {
                $scope.categories = response.data.result;
            }
        });
    }

    $scope.find = async function() {
        $scope.dataSeries = [];
        if (validateTime()) {
            $scope.filter.from = moment().endOf('day').subtract(1, 'months').format('YYYY-MM-DD HH:mm:ss');
            $scope.filter.to = moment().endOf('day').format('YYYY-MM-DD HH:mm:ss');
            toastr.error('Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc');
            $scope.find();
        };
        var url = '/api/incident?embeds=user';
        var params = buildFilter();
        params += '&page_size=-1'
        await $scope.getUsers();
        $scope.title = 'user';
        await $http({
            method: 'GET',
            url: url + params,
        }).then(function successCallback(response) {
            if (response.data.status == 'successful' && response.data.result.length > 0) {
                var retVal = response.data.result;

                if ($scope.categories.length > 0) {
                    $scope.categories = $scope.categories.map( i => {
                        return {...i, solved: 0, pending: 0};
                    })
                    $scope.categories.forEach(element => {
                        retVal.forEach(e => {
                            var type = 0;
                            if ($scope.filter.type == 'user') {
                                type = e.user_id;
                                if (type == element.id) {
                                    element[e.status.toLowerCase()]++;
                                }
                            }
                        });
                    });
                }
                $scope.dataSeries = [{
                        name: 'pending',
                        data: $scope.categories.map(i => i.pending),
                        color: "#ffc34d"
                    },
                    {
                        name: 'solve',
                        data: $scope.categories.map(i => i.solved),
                        color: "#7cb5ec"
                    },
                ];
                console.log($scope.dataSeries)
            }
        });
    }

    function buildFilter() {
        var retVal = '&filters=';
        if ($scope.filter.from) {
            retVal += 'created_at>=' + $scope.filter.from;
        }
        if ($scope.filter.to) {
            retVal += '&created_at<=' + $scope.filter.to;
        }
        return retVal;
    }

    function validateTime() {
        var from = moment($scope.filter.from, 'YYYY-MM-DD HH:mm:ss');
        var to = moment($scope.filter.to, 'YYYY-MM-DD HH:mm:ss');
        return from > to;
    }


    $scope.reset = function () {
        $scope.filter = {};
        $scope.title = 'User';
        $scope.filter.type = 'user';
    }

    init();
});