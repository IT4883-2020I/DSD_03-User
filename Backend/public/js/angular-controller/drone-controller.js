system.controller("DroneController", DroneController);
/**
 *
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function DroneController($scope, $http, $rootScope, $timeout, $interval) {
    this.__proto__ = BaseController($scope, $http, $rootScope);
    $scope.controllerName = "DroneController";
    $scope.filter = {
        pageId: 0,
        pageSize: 20
    };
    $scope.pageCount = 0;
    $scope.types = [
        { "value": 'GPS Drones', "code": "GPS" },
        { "value": 'DJI Tello', "code": "DJI" },
    ];
    $scope.statuses = [
        { "value": "Hoạt động", "code": "FLYING" },
        { "value": "Đang chờ", "code": "WAITING" },
    ];
    $scope.drones = [];
    $scope.users = [];
    $scope.mode = 'create';
    init = () => {
        $(function() {
            $('#drone-modal').on('shown.bs.modal', function() {
                $(document).off('focusin.modal');
            });
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
        $scope.getUser();
        $scope.getDrone();
    }

    $scope.getDrone = () => {
        var url = '/api/drone?sorts=-created_at&embeds=user&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;
        if (user.role == 'MEMBER') {
            url += '&filters=user_id=' + user.id;
        }
        params = buildFilter();
        if (params.length > 0) {
            url += '&filters=' + params.join(',');
        }
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.drones = response.data.result;
                $scope.meta = response.data.meta;
                $scope.pageCount = response.data.meta.page_count
            }
        })
    }

    buildFilter = () => {
        let retVal = [];
        if ($scope.filter.search) {
            retVal.push('name~' + $scope.filter.search)
        }

        if ($scope.filter.status) {
            retVal.push('status=' + $scope.filter.status.code)
        }

        if ($scope.filter.type) {
            retVal.push('type=' + $scope.filter.type.code);
        }
        return retVal;
    }

    $scope.reset = () => {
        $scope.filter = {};
        $scope.filter = {
            pageId: 0,
            pageSize: 20
        };
        $scope.pageCount = 0;
        $scope.getDrone();
    }

    $scope.openFormEdit = function(item, type = 'create') {
        $scope.mode = type;
        $scope.drone = angular.copy(item);
        if (type != 'create') {
            $scope.drone.user = $scope.getByField($scope.users, 'id', $scope.drone.user_id)
            $scope.drone.status = $scope.getByField($scope.statuses, 'code', $scope.drone.status)
            $scope.drone.type = $scope.getByField($scope.types, 'code', $scope.drone.type)
        }
        $('#drone-modal').modal('show');
    }

    $scope.closeFormEdit = function() {
        $scope.drone = {};
        $('#drone-modal').modal('hide');
    }

    $scope.create = () => {
        var isError = validateDrone();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.drone.name,
            speed: $scope.drone.speed,
            status: $scope.drone.status.code,
            type: $scope.drone.type.code,
            manufacturer: $scope.drone.manufacturer,
            user_id: $scope.drone.user.id
        }

        $http.post('/api/drone', data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Tạo drone thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    $scope.update = () => {
        var isError = validateDrone();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.drone.name,
            speed: $scope.drone.speed,
            status: $scope.drone.status.code,
            type: $scope.drone.type.code,
            manufacturer: $scope.drone.manufacturer,
            user_id: $scope.drone.user.id
        }

        $http.patch('/api/drone/' + $scope.drone.id, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Cập nhật drone thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    function validateDrone() {
        var result = false;
        if (!$scope.drone.user) {
            $scope.showMessage('Thất bại', 'Vui lòng chọn người dùng', 'error');
            result = true;
        }
        if (typeof $scope.drone.name === 'undefined') {
            $scope.showMessage('Thất bại', 'Vui lòng nhập tên', 'error');
            result = true;
        }
        if (!$scope.drone.type || $scope.drone.type.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn loại drone', 'error');
            result = true;
        }
        if (!$scope.drone.status || $scope.drone.status.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn trạng thái', 'error');
            result = true;
        }
        if (!$scope.drone.manufacturer) {
            $scope.showMessage('Thất bại', 'Vui lòng nhập hãng sản xuất', 'error');
            result = true;
        }
        if (!$scope.drone.speed) {
            $scope.showMessage('Thất bại', 'Vui lòng nhập tốc độ', 'error');
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
        $http.delete('/api/drone/' + item.id).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Xóa drone thành công", 'success');
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }
        })
    }
    init();
}