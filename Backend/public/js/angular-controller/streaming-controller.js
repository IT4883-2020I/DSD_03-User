system.controller("StreamingController", StreamingController);
/**
 *
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function StreamingController($scope, $http, $rootScope, $timeout, $interval) {
    this.__proto__ = BaseController($scope, $http, $rootScope);
    $scope.controllerName = "StreamingController";
    
    $scope.pageCount = 0;
    $scope.types = [
        { "value": 'Drone', "code": "DRONE" },
        { "value": 'User', "code": "USER" },
    ];
    $scope.filter = {
        pageId: 0,
        pageSize: 20,
        type: $scope.types[0],
    };
    $scope.videos = [];
    $scope.users = [];
    $scope.drones = [];
    $scope.mode = 'create';
    $scope.type = 'DRONE';
    $scope.videoUrl = app_url + '/media/videos/SauBenh3.mp4';
    init = () => {
        $(function() {
            $('#video-modal').on('shown.bs.modal', function() {
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
            $('#video-modal').on('hidden.bs.modal', function () {
                $('#video').attr('src', '');
            });
            setInterval(function() {
                document.getElementById("video").src = document.getElementById("video").src
            }, 14000);
        });
        $scope.getUser();
        $scope.getDrone();
        $scope.getStreaming();
    }

    $scope.getStreaming = () => {
        var url = '/api/video?sorts=-created_at&embeds=user,dronesOrIncidents&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;
        if (user.role == 'MEMBER') {
            url += '&filters=user_id=' + user.id;
        }
        params = buildFilter();
        if (params.length > 0) {
            url += '&filters=' + params.join(',');
        }
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.videos = response.data.result;
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
            if ($scope.filter.type.code == 'USER') {
                if ($scope.filter.user && $scope.filter.user.id) {
                    retVal.push('actor_id=' + $scope.filter.user.id);
                }
            } else {
                retVal.push('type=' + $scope.filter.type.code);
            }
        }
        return retVal;
    }

    $scope.reset = () => {
        $scope.filter = {};
        $scope.filter = {
            pageId: 0,
            pageSize: 20,
            type: $scope.types[0],
        };
        $scope.pageCount = 0;
        $scope.type = 'DRONE';
        $scope.getStreaming();
    }

    $scope.openFormEdit = function(item, type = 'create') {
        $('#video-modal').modal('show');
    }

    $scope.closeFormEdit = function() {
        $scope.video = {};
        $('#video-modal').modal('hide');
    }

    $scope.create = () => {
        var isError = validateStreaming();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.video.name,
            speed: $scope.video.speed,
            status: $scope.video.status.code,
            type: $scope.video.type.code,
            manufacturer: $scope.video.manufacturer,
            user_id: $scope.video.user.id
        }

        $http.post('/api/video', data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Tạo video thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    $scope.update = () => {
        var isError = validateStreaming();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.video.name,
            speed: $scope.video.speed,
            status: $scope.video.status.code,
            type: $scope.video.type.code,
            manufacturer: $scope.video.manufacturer,
            user_id: $scope.video.user.id
        }

        $http.patch('/api/video/' + $scope.video.id, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Cập nhật video thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    function validateStreaming() {
        var result = false;
        if (!$scope.video.user) {
            $scope.showMessage('Thất bại', 'Vui lòng chọn người dùng', 'error');
            result = true;
        }

        return result;
    }

    $scope.changeType = () => {
        $scope.type = $scope.filter.type.code;
        if ($scope.filter.type.code == 'DRONE') {
            $scope.getStreaming();
        }
    }

    $scope.getUser = () => {
        $http.get('/api/user?embeds=videos&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize).then(response => {
            if (response.data.status == 'successful') {
                $scope.users = response.data.result;
            }
        })
    }
    $scope.getDrone = () => {
        $http.get('/api/drone?embeds=videos&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize).then(response => {
            if (response.data.status == 'successful') {
                $scope.drones = response.data.result;
            }
        })
    }
    init();
}