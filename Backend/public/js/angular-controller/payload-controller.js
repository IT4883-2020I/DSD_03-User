system.controller("PayloadController", PayloadController);
/**
 *
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function PayloadController($scope, $http, $rootScope, $timeout, $interval) {
    this.__proto__ = BaseController($scope, $http, $rootScope);
    $scope.controllerName = "PayloadController";
    $scope.filter = {
        pageId: 0,
        pageSize: 20
    };
    $scope.pageCount = 0;
    $scope.types = [
        { "value": 'GPS Payloads', "code": "GPS" },
        { "value": 'DJI Tello', "code": "DJI" },
    ];
    $scope.statuses = [
        { "value": "Hoạt động", "code": "FLYING" },
        { "value": "Đang chờ", "code": "WAITING" },
    ];
    $scope.payloads = [];
    $scope.users = [];
    $scope.mode = 'create';
    init = () => {
        $(function() {
            $('#payload-modal').on('shown.bs.modal', function() {
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
        $scope.getPayload();
    }

    $scope.getPayload = () => {
        var url = '/api/payload?sorts=-created_at&embeds=user&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;
        if (user.role == 'MEMBER') {
            url += '&filters=user_id=' + user.id;
        }
        params = buildFilter();
        if (params.length > 0) {
            url += '&filters=' + params.join(',');
        }
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.payloads = response.data.result;
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
        $scope.getPayload();
    }

    $scope.openFormEdit = function(item, type = 'create') {
        $scope.mode = type;
        $scope.payload = angular.copy(item);
        if (type != 'create') {
            $scope.payload.user = $scope.getByField($scope.users, 'id', $scope.payload.user_id)
            $scope.payload.status = $scope.getByField($scope.statuses, 'code', $scope.payload.status)
            $scope.payload.type = $scope.getByField($scope.types, 'code', $scope.payload.type)
        }
        $('#payload-modal').modal('show');
    }

    $scope.closeFormEdit = function() {
        $scope.payload = {};
        $('#payload-modal').modal('hide');
    }

    $scope.create = () => {
        var isError = validatePayload();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.payload.name,
            speed: $scope.payload.speed,
            status: $scope.payload.status.code,
            type: $scope.payload.type.code,
            manufacturer: $scope.payload.manufacturer,
            user_id: $scope.payload.user.id
        }

        $http.post('/api/payload', data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Tạo payload thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    $scope.update = () => {
        var isError = validatePayload();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.payload.name,
            speed: $scope.payload.speed,
            status: $scope.payload.status.code,
            type: $scope.payload.type.code,
            manufacturer: $scope.payload.manufacturer,
            user_id: $scope.payload.user.id
        }

        $http.patch('/api/payload/' + $scope.payload.id, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Cập nhật payload thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    function validatePayload() {
        var result = false;
        if (!$scope.payload.user) {
            $scope.showMessage('Thất bại', 'Vui lòng chọn người dùng', 'error');
            result = true;
        }
        if (typeof $scope.payload.name === 'undefined') {
            $scope.showMessage('Thất bại', 'Vui lòng nhập tên', 'error');
            result = true;
        }
        if (!$scope.payload.type || $scope.payload.type.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn loại payload', 'error');
            result = true;
        }
        if (!$scope.payload.status || $scope.payload.status.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn trạng thái', 'error');
            result = true;
        }
        if (!$scope.payload.manufacturer) {
            $scope.showMessage('Thất bại', 'Vui lòng nhập hãng sản xuất', 'error');
            result = true;
        }
        if (!$scope.payload.speed) {
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
        $http.delete('/api/payload/' + item.id).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Xóa payload thành công", 'success');
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }
        })
    }
    init();
}