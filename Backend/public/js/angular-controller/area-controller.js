system.controller("AreaController", AreaController);
/**
 *
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function AreaController($scope, $http, $rootScope, $timeout, $interval) {
    this.__proto__ = BaseController($scope, $http, $rootScope);
    $scope.controllerName = "AreaController";
    $scope.filter = {
        pageId: 0,
        pageSize: 1
    };
    $scope.pageCount = 0;
    $scope.areas = [];
    $scope.mode = 'create';
    init = () => {
        $(function() {
            $('#from').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                startDate: moment().subtract(1, 'months').startOf('day'),
                endDate: moment().startOf('day').add(32, 'hour'),
                locale: {
                    format: 'YYYY-MM-DD HH:mm:ss',
                }
            }, function(start, end, label) {
                $scope.filter.from = moment(start).format('YYYY-MM-DD HH:mm:ss');
                $scope.getArea();
            });
            $('#to').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                startDate: moment().startOf('day'),
                endDate: moment().startOf('day').add(32, 'hour'),
                locale: {
                    format: 'YYYY-MM-DD HH:mm:ss',
                }
            }, function(start, end, label) {
                $scope.filter.to = moment(start).format('YYYY-MM-DD HH:mm:ss');;
                $scope.getArea();
            });
        });
        $('#from').on('apply.daterangepicker', function(ev, picker) {
            $scope.$apply(function() {
                $scope.filter.from = moment(picker.startDate).format('YYYY-MM-DD HH:mm:ss');
            });
        });
        $('#to').on('apply.daterangepicker', function(ev, picker) {
            $scope.$apply(function() {
                $scope.filter.to = moment(picker.startDate).format('YYYY-MM-DD HH:mm:ss');
            });
        });
        $scope.getArea();
    }

    $scope.getArea = () => {
        var url = '/api/area?sorts=-created_at&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;

        params = buildFilter();
        if (params.length > 0) {
            url += '&filters=' + params.join(',');
        }
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.areas = response.data.result;
                $scope.meta = response.data.meta;
                $scope.pageCount = response.data.meta.page_count
            }
        })
    }

    buildFilter = () => {
        let retVal = [];
        if ($scope.filter.search) {
            retVal.push('name~' + $scope.filter.search);
        }
        if ($scope.filter.from) {
            retVal.push('created_at>=' + $scope.filter.from);
        }
        if ($scope.filter.to) {
            retVal.push('created_at<=' + $scope.filter.to);
        }
        console.log(retVal);
        return retVal;
    }

    $scope.reset = () => {
        $scope.filter = {};
        $scope.filter = {
            pageId: 0,
            pageSize: 20
        };
        $scope.pageCount = 0;
        $scope.getArea();
    }

    $scope.openFormEdit = function(item = {}, type = 'create') {
        $scope.mode = type;
        $scope.area = angular.copy(item);

        $('#area-modal').modal('show');
    }

    $scope.closeFormEdit = function() {
        $scope.area = {};
        $('#area-modal').modal('hide');
    }

    $scope.create = () => {
        var isError = validateArea();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.area.name,
            description: $scope.area.description,
        }

        $http.post('/api/area', data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Tạo area thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    $scope.update = () => {
        var isError = validateArea();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.area.name,
            description: $scope.area.description,
        }

        $http.patch('/api/area/' + $scope.area.id, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Cập nhật area thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    function validateArea() {
        var result = false;
        if (typeof $scope.area.name === 'undefined') {
            $scope.showMessage('Thất bại', 'Vui lòng nhập tên', 'error');
            result = true;
        }
        if (!$scope.area.description) {
            $scope.showMessage('Thất bại', 'Vui lòng nhập mô tả', 'error');
            result = true;
        }
        return result;
    }

    $scope.getUser = () => {
        $http.get('/api/user?page_size=-1').then(response => {
            if (response.data.status == 'successful') {
                $scope.users = response.data.result;
            }
        })
    }

    $scope.delete = (item) => {
        if (!confirm('Bạn có chắc chắn muốn xóa?')) {
            return;
        }
        $http.delete('/api/area/' + item.id).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Xóa area thành công", 'success');
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }
        })
    }
    init();
}