system.controller("UserController", UserController);
/**
 *
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function UserController($scope, $http, $rootScope, $timeout, $interval, Upload) {
    this.__proto__ = BaseController($scope, $http, $rootScope);
    $scope.controllerName = "UserController";
    $scope.filter = {
        pageId: 0,
        pageSize: 20
    };
    $scope.pageCount = 0;
    $scope.roles = [
        { "value": 'Quản trị viên', "code": "ADMIN" },
        { "value": 'Nhân viên', "code": "MEMBER" },
    ];
    $scope.statuses = [
        { "value": "Hoạt động", "code": "ACTIVE" },
        { "value": "Chờ xác nhận", "code": "PENDING" },
    ];
    $scope.users = [];
    $scope.user = {};
    init = () => {
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
        $scope.getUser();
    }

    $scope.getUser = () => {
        var url = '/api/user?page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;
        params = buildFilter();
        if (params.length > 0) {
            url += '&filters=' + params.join(',');
        }
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.users = response.data.result;
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

        if ($scope.filter.role) {
            retVal.push('role=' + $scope.filter.role.code);
        }
        return retVal;
    }

    $scope.updateUser = (userId) => {
        var data = buildDataUser();
        $http.patch('/api/user/' + userId, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showAlert('Cập nhật thành công', 'success');
            } else {
                $scope.showAlert('Có lỗi. Vui lòng thử lại', 'error');
            }
        })
    }

    $scope.reset = () => {
        $scope.filter = {};
        $scope.filter = {
            pageId: 0,
            pageSize: 20
        };
        $scope.pageCount = 0;
        $scope.getUser();
    }

    buildDataUser = () => {
        var result = {};

        if ($scope.user) {
            
        }
        return result;
    }
    $scope.openFormEdit = function(item, type = 'create') {
        $scope.mode = type;
        $scope.user = angular.copy(item);
        if (type != 'create') {
            $scope.user.status = $scope.getByField($scope.statuses, 'code', $scope.user.status)
            $scope.user.role = $scope.getByField($scope.roles, 'code', $scope.user.role)
        }
        $('#user-modal').modal('show');
    }

    $scope.closeFormEdit = function() {
        $scope.user = {};
        $('#user-modal').modal('hide');
    }

    $scope.delete = (item) => {
        $http.delete('/api/user/' + item.id).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Xóa user thành công", 'success');
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }
        })
    }

    $scope.create = () => {
        var isError = validateUser();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.user.name,
            phone: $scope.user.phone,
            email: $scope.user.email,
            address: $scope.user.address,
            status: $scope.user.status.code,
            role: $scope.user.role.code,
            password: $scope.user.password,
            avatar: '/media/users/default.jpg',
        }

        $http.post('/api/user', data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Tạo user thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    $scope.update = () => {
        var isError = validateUser();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.user.name,
            phone: $scope.user.phone,
            email: $scope.user.email,
            address: $scope.user.address,
            status: $scope.user.status.code,
            role: $scope.user.role.code,
            avatar: '/media/users/default.jpg',
        }

        $http.patch('/api/user/' + $scope.user.id, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Cập nhật user thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    function validateUser() {
        var result = false;

        if (typeof $scope.user.name === 'undefined') {
            $scope.showMessage('Thất bại', 'Vui lòng nhập tên', 'error');
            result = true;
        }
        if (!$scope.user.role || $scope.user.role.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn quyền', 'error');
            result = true;
        }
        if (!$scope.user.status || $scope.user.status.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn trạng thái', 'error');
            result = true;
        }
        if (!$scope.user.phone) {
            $scope.showMessage('Thất bại', 'Vui lòng nhập số điện thoại', 'error');
            result = true;
        }
        if (!$scope.user.email) {
            $scope.showMessage('Thất bại', 'Vui lòng nhập email', 'error');
            result = true;
        }
        if ($scope.mode == 'create') {
            if (!$scope.user.password) {
                $scope.showMessage('Thất bại', 'Vui lòng nhập mật khẩu', 'error');
                result = true;
            }
            if (!$scope.user.confirm_password && $scope.user.confirm_password != $scope.user.password) {
                $scope.showMessage('Thất bại', 'Vui lòng xác nhận mật khẩu', 'error');
                result = true;
            }
        }

        return result;
    }

    init();
}