system.controller("PathController", PathController);
/**
 *
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function PathController($scope, $http, $rootScope, $timeout, $interval) {
    this.__proto__ = BaseController($scope, $http, $rootScope);
    $scope.controllerName = "PathController";
    $scope.filter = {
        pageId: 0,
        pageSize: 20
    };
    $scope.pageCount = 0;
    $scope.paths = [];
    $scope.path = {};
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
        $scope.getArea();
        $scope.getPath();
    }

    $scope.getPath = () => {
        var url = '/api/path?sorts=-created_at&embeds=area&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;
        params = buildFilter();
        if (params.length > 0) {
            url += '&filters=' + params;
        }
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.paths = response.data.result;
                $scope.meta = response.data.meta;
                $scope.pageCount = response.data.meta.page_count
            }
        })
    }

    buildFilter = () => {
        let retVal = [];
        if ($scope.filter.search) {
            retVal += 'name~' + $scope.filter.search;
        }

        if ($scope.filter.area && $scope.filter.area != 'All') {
            retVal += 'area_id=' + $scope.filter.area.id;
        }

        if ($scope.filter.from) {
            retVal += 'created_at>=' + $scope.filter.from;
        }

        if ($scope.filter.to) {
            retVal += '&created_at<=' + $scope.filter.to;
        }
        return retVal;
    }

    $scope.reset = () => {
        $scope.filter = {
            pageId: 0,
            pageSize: 20
        };
        $scope.pageCount = 0;
        $scope.getPath();
    }

    $scope.openFormEdit = function(item, type = 'create') {
        $scope.mode = type;
        $scope.path = angular.copy(item);
        if (type != 'create') {
            $scope.path.area = $scope.getByField($scope.areas, 'id', $scope.path.area_id)
        }
        $('#path-modal').modal('show');
    }

    $scope.closeFormEdit = function() {
        $('#path-modal').modal('hide');
    }

    $scope.create = () => {
        var isError = validatePath();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.path.name,
            start: $scope.path.start,
            end: $scope.path.end,
            area_id: $scope.path.area.id
        }

        $http.post('/api/path', data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Tạo path thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    $scope.update = () => {
        var isError = validatePath();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.path.name,
            start: $scope.path.start,
            end: $scope.path.end,
            area_id: $scope.path.area.id
        }

        $http.patch('/api/path/' + $scope.path.id, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Cập nhật path thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    function validatePath() {
        var result = false;
        if (!$scope.path.area) {
            $scope.showMessage('Thất bại', 'Vui lòng chọn khu vực', 'error');
            result = true;
        }
        if (typeof $scope.path.name === 'undefined') {
            $scope.showMessage('Thất bại', 'Vui lòng nhập tên', 'error');
            result = true;
        }
        if (!$scope.path.start) {
            $scope.showMessage('Thất bại', 'Vui lòng nhập điểm bắt đầu', 'error');
            result = true;
        }
        if (!$scope.path.end) {
            $scope.showMessage('Thất bại', 'Vui lòng nhập điểm kết thúc', 'error');
            result = true;
        }

        return result;
    }

    $scope.getArea = () => {
        $http.get('/api/area?page_size=-1').then(response => {
            if (response.data.status == 'successful') {
                $scope.areas = response.data.result;
            }
        })
    }

    $scope.delete = (item) => {
        if (!confirm('Bạn có chắc chắn muốn xóa?')) {
            return;
        }
        $http.delete('/api/path/' + item.id).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Xóa path thành công", 'success');
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }
        })
    }
    init();
}