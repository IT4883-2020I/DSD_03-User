system.controller("DroneScheduleController", DroneScheduleController);
/**
 *
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function DroneScheduleController($scope, $http, $rootScope, $timeout, $interval, Upload) {
    this.__proto__ = BaseController($scope, $http, $rootScope);
    $scope.controllerName = "DroneScheduleController";
    $scope.filter = {
        pageId: 0,
        pageSize: 20
    };
    $scope.pageCount = 0;
    $scope.types = [
        { "value": 'Khô héo', "code": "drought" },
        { "value": 'Sâu bệnh', "code": "pests" },
        { "value": 'Ngập úng', "code": "flooding" },
    ];
    $scope.statuses = [
        { "value": "Đã giải quyết", "code": "SOLVED" },
        { "value": "Đang chờ", "code": "PENDING" },
    ];
    $scope.schedules = [];
    $scope.schedule = [];
    $scope.users = [];
    $scope.mode = 'create';
    $scope.currentImage = {};

    $scope.imageGalleries = [];
    $scope.videoPopup = app_url + "/media/videos/SauBenh3.mp4";
    init = () => {
        $(function() {
            $('#schedule-modal').on('shown.bs.modal', function() {
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
        $scope.getSchedule();
    }

    $scope.getSchedule = () => {
        var url = '/api/drone_schedule?sorts=-created_at&embeds=drone,path&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;
        params = buildFilter();
        if (params.length > 0) {
            url += '&filters=' + params.join(',');
        }
        
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.schedules = response.data.result;
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

        if (user.role == 'MEMBER') {
            retVal.push('user_id=' + user.id);
        }
        return retVal;
    }

    $scope.updateSchedule = (scheduleId) => {
        var data = buildDataSchedule();
        $http.patch('/api/drone_schedule/' + scheduleId, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Cập nhật thành công', 'success');
            } else {
                $scope.showMessage('Có lỗi. Vui lòng thử lại', 'error');
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
        $scope.currentImage = {};
        $scope.getSchedule();
    }

    buildDataSchedule = () => {
        var result = {};

        if ($scope.schedule) {
            
        }
        return result;
    }

    $scope.openFormEdit = function(item, type = 'create') {
        $scope.mode = type;
        $scope.schedule = angular.copy(item);
        if (type != 'create') {
            $scope.schedule.user = $scope.getByField($scope.users, 'id', $scope.schedule.user_id)
            $scope.schedule.status = $scope.getByField($scope.statuses, 'code', $scope.schedule.status)
            $scope.schedule.type = $scope.getByField($scope.types, 'code', $scope.schedule.type)
        }
        $('#schedule-modal').modal('show');
    }

    $scope.closeFormEdit = function() {
        $scope.schedule = {};
        $('#schedule-modal').modal('hide');
    }

    $scope.create = () => {
        var isError = validateSchedule();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.schedule.name,
            speed: $scope.schedule.speed,
            status: $scope.schedule.status.code,
            type: $scope.schedule.type.code,
            manufacturer: $scope.schedule.manufacturer,
            user_id: $scope.schedule.user.id
        }

        $http.post('/api/drone_schedule', data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Tạo schedule thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    $scope.update = () => {
        var isError = validateSchedule();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.schedule.name,
            description: $scope.schedule.description,
            status: $scope.schedule.status.code,
            type: $scope.schedule.type.code,
            manufacturer: $scope.schedule.manufacturer,
            user_id: $scope.schedule.user.id
        }

        $http.patch('/api/drone_schedule/' + $scope.schedule.id, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Cập nhật schedule thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    function validateSchedule() {
        var result = false;
        if (!$scope.schedule.user) {
            $scope.showMessage('Thất bại', 'Vui lòng chọn người dùng', 'error');
            result = true;
        }
        if (typeof $scope.schedule.name === 'undefined') {
            $scope.showMessage('Thất bại', 'Vui lòng nhập tên', 'error');
            result = true;
        }
        if (!$scope.schedule.type || $scope.schedule.type.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn loại schedule', 'error');
            result = true;
        }
        if (!$scope.schedule.status || $scope.schedule.status.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn trạng thái', 'error');
            result = true;
        }
        if (!$scope.schedule.manufacturer) {
            $scope.showMessage('Thất bại', 'Vui lòng nhập hãng sản xuất', 'error');
            result = true;
        }
        if (!$scope.schedule.description) {
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

    $scope.upload = (file, callback) => {
        if (typeof file == 'array') {
            file = file[0];
        }
        return new Promise(function(resolve, reject) {
            Upload.upload({
                url: '/api/upload',
                data: { file: file }
            }).then(function(resp) {
                if (resp.data.status == 'successful') {
                    let upload = resp.data.result;
                    resolve(upload);
                } else {
                    reject(0);
                }
            }, function(resp) {
                reject(0);
            });
        });
    };

    $scope.delete = (item) => {
        if (!confirm('Bạn có chắc chắn muốn xóa?')) {
            return;
        }
        $http.delete('/api/drone_schedule/' + item.id).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Xóa schedule thành công", 'success');
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }
        })
    }

    $scope.openImageGallery = (item, image) => {
        $scope.currentImage = image;
        $scope.schedule = item;
        $scope.imageGalleries = item.images;
        $('#image-gallery').modal('show');
    }

    $scope.openFormVideo = (item) => {
        $scope.videosUrl = app_url + item.videos[0].url;
        $('#video-modal').modal('show');
    }
    init();
}