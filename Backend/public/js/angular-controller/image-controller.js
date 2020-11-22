system.controller("ImageController", ImageController);
// File directive
system.directive('ngFile', ['$parse', function($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            element.bind('change', function() {
                var callback = '';
                var currentId = -1;
                try {
                    var parseParams = JSON.parse(attrs.ngFile);
                    callback = parseParams.callback;
                    if (typeof parseParams.current !== 'undefined') {
                        currentId = parseParams.current;
                    }
                } catch (err) {
                    console.log('ngFile parse error: ', err);
                    callback = attrs.ngFile;
                }
                scope.uploadImages(element[0].files, { callback, currentId });
            });
        }
    }
}]);
/**
 *
 * @param {type} $scope
 * @param {type} $http
 * @param {type} $rootScope
 * @returns {undefined}
 */
function ImageController($scope, $http, $rootScope, $timeout, $interval) {
    this.__proto__ = BaseController($scope, $http, $rootScope);
    $scope.controllerName = "ImageController";
    
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
    $scope.images = [];
    $scope.users = [];
    $scope.drones = [];
    $scope.mode = 'create';
    $scope.type = 'DRONE';
    $scope.videoUrl = app_url + '/media/videos/SauBenh3.mp4';
    init = () => {
        $(function() {
            $('#image-modal').on('shown.bs.modal', function() {
                $(document).off('focusin.modal');
            });
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
                $scope.getImage();
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
                $scope.filter.to = moment(start).format('YYYY-MM-DD HH:mm:ss');
                $scope.getImage();
            });
            $('#video-modal').on('hidden.bs.modal', function () {
                $('#video').attr('src', '');
            });
        });
        $scope.getUser();
        $scope.getDrone();
        $scope.getImage();
    }

    $scope.getImage = () => {
        var url = '';
        if ($scope.filter.type == 'USER') {
            url += '/api/user'
        } else {
            url += '/api/drone'
        }
        url += '?sorts=-created_at&embeds=images&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;
        if (user.role == 'MEMBER') {
            url += '&filters=user_id=' + user.id;
        }
        params = buildFilter();
        if (params.length > 0) {
            url += '&filters=' + params.join(',');
        }
        
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.images = response.data.result;
                $scope.meta = response.data.meta;
                $scope.pageCount = response.data.meta.page_count
            }
        })
    }

    buildFilter = () => {
        let retVal = [];

        if ($scope.filter.type) {
            if ($scope.filter.type.code == 'USER') {
                if ($scope.filter.user && $scope.filter.user.id) {
                    retVal.push('actor_id=' + $scope.filter.user.id);
                }
            } else {
                retVal.push('type=' + $scope.filter.type.code);
            }
        }

        if ($scope.filter.from) {
            retVal.push('images.created_at>=' + $scope.filter.from);
        }

        if ($scope.filter.to) {
            retVal.push('images.created_at<=' + $scope.filter.to);
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
        $scope.getImage();
    }

    $scope.openFormEdit = function(item, type = 'create') {
        $('#image-modal').modal('show');
    }

    $scope.closeFormEdit = function() {
        $('#image-modal').modal('hide');
    }

    $scope.create = () => {
        var isError = validateImage();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.image.name,
            speed: $scope.image.speed,
            status: $scope.image.status.code,
            type: $scope.image.type.code,
            manufacturer: $scope.image.manufacturer,
            user_id: $scope.image.user.id
        }

        $http.post('/api/image', data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Tạo image thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    $scope.update = () => {
        var isError = validateImage();
        if (isError) {
            return;
        }
        let data = {
            name: $scope.image.name,
            speed: $scope.image.speed,
            status: $scope.image.status.code,
            type: $scope.image.type.code,
            manufacturer: $scope.image.manufacturer,
            user_id: $scope.image.user.id
        }

        $http.patch('/api/image/' + $scope.image.id, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Cập nhật image thành công", 'success');
                $scope.closeFormEdit();
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    function validateImage() {
        var result = false;
        if (!$scope.image.user) {
            $scope.showMessage('Thất bại', 'Vui lòng chọn người dùng', 'error');
            result = true;
        }

        return result;
    }

    $scope.changeType = () => {
        $scope.type = $scope.filter.type.code;
        $scope.getImage();
    }

    $scope.getUser = () => {
        var url = '/api/user?embeds=images&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;
        url += '&filters=images.created_at<=2020-11-13 00:00:00';
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.users = response.data.result;
            }
        })
    }
    $scope.getDrone = () => {
        $http.get('/api/drone?page_size=-1').then(response => {
            if (response.data.status == 'successful') {
                $scope.drones = response.data.result;
            }
        })
    }

    $scope.uploadImages = function(files, params) {
        if (!files) {
            return false;
        }
        var formData = new FormData();
        angular.forEach(files, function(file) {
            formData.append('file[]', file);
        });

        $http({
            method: 'POST',
            url: '/api/upload',
            data: formData,
            headers: { 'Content-Type': undefined },
        }).then(res => {
            var response = res.data;
            if (response.status === 'successful' && $scope.functionExists(params.callback)) {
                for (const i in response.result) {
                    response.result[i].name = files[i].name;
                }
                eval(params.callback)(response, params.currentId);
            }
        });
    }
    var pushImages = (response) => {
        var filePath = '/upload/';
        for (var item of response.result) {
            var extractExtension = item.split('.').pop();
            var fileItem = {
                name: item,
                path: `${filePath}${item}`,
            };
            $scope.image.images.push(fileItem);
        }
    }
    init();
}