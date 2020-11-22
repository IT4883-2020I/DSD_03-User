system.controller("IncidentController", IncidentController);

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
function IncidentController($scope, $http, $rootScope, $timeout, $interval, Upload, $sce) {
    this.__proto__ = BaseController($scope, $http, $rootScope, $sce);
    $scope.controllerName = "IncidentController";
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
    $scope.incidents = [];
    $scope.incident = {};
    $scope.users = [];
    $scope.mode = 'create';
    $scope.currentImage = {};
    $scope.currentVideo = {};
    $scope.images = [];
    $scope.videos = [];
    $scope.imageGalleries = [];
    $scope.videoGalleries = [];
    init = () => {
        $(function() {
            $('#incident-modal').on('hidden.bs.modal', function () {
                $scope.$apply(function() {
                    $scope.incident = {};
                    $scope.videos = [];
                    $scope.images = [];
                })
            });
            $('#video-modal').on('shown.bs.modal', function() {
                $("#video-modal iframe").attr("src", $("#video-modal iframe").attr("src"));
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
                $scope.getIncident();
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
                $scope.getIncident();
            });
        });
        $scope.getUser();
        $scope.getIncident();
    }

    $scope.getIncident = () => {
        var url = '/api/incident?sorts=-created_at&embeds=user,images,videos&page_id=' + $scope.filter.pageId + '&page_size=' + $scope.filter.pageSize;
        params = buildFilter();
        if (params.length > 0) {
            url += '&filters=' + params.join(',');
        }

        if ($scope.filter.search) {
            url += '&q=' + $scope.filter.search;
        }
    
        $http.get(url).then(response => {
            if (response.data.status == 'successful') {
                $scope.incidents = response.data.result;
                $scope.meta = response.data.meta;
                $scope.pageCount = response.data.meta.page_count
            }
        })
    }

    buildFilter = () => {
        let retVal = [];

        if ($scope.filter.status) {
            retVal.push('status=' + $scope.filter.status.code)
        }

        if ($scope.filter.type) {
            retVal.push('type=' + $scope.filter.type.code);
        }

        if (user.role == 'MEMBER') {
            retVal.push('user_id=' + user.id);
        }

        if ($scope.filter.from) {
            retVal.push('created_at>=' + $scope.filter.from);
        }

        if ($scope.filter.to) {
            retVal.push('created_at<=' + $scope.filter.to);
        }

        if ($scope.filter.user && $scope.filter.user.id) {
            retVal.push('user_id=' + $scope.filter.user.id);
        }

        return retVal;
    }

    $scope.updateIncident = (incidentId) => {
        var data = buildDataIncident();
        $http.patch('/api/incident/' + incidentId, data).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Cập nhật thành công', 'success');
            } else {
                $scope.showMessage('Có lỗi. Vui lòng thử lại', 'error');
            }
        })
    }

    $scope.reset = () => {
        $scope.filter = {
            pageId: 0,
            pageSize: 20
        };
        $scope.pageCount = 0;
        $scope.currentImage = {};
        $scope.currentVideo = {};
        $scope.images = [];
        $scope.videos = [];
        $scope.getIncident();
    }

    buildDataIncident = () => {
        var result = {};

        if ($scope.incident) {
            
        }
        return result;
    }

    $scope.openFormEdit = function(item, type = 'create') {
        $scope.mode = type;
        $scope.incident = angular.copy(item);
        if (type != 'create') {
            $scope.incident.user = $scope.getByField($scope.users, 'id', $scope.incident.user_id)
            $scope.incident.status = $scope.getByField($scope.statuses, 'code', $scope.incident.status)
            $scope.incident.type = $scope.getByField($scope.types, 'code', $scope.incident.type)
            if ($scope.incident.images.length > 0) {
                $scope.incident.images.forEach(element => {
                    $scope.images.push({
                        id: element.id,
                        name: element.name,
                        path: element.url
                    })
                });
            }
            if ($scope.incident.videos.length > 0) {
                $scope.incident.videos.forEach(element => {
                    $scope.videos.push({
                        id: element.id,
                        name: element.name,
                        path: element.url
                    })
                });
            }
        }
        $('#incident-modal').modal('show');
    }

    $scope.closeFormEdit = function() {
        $scope.incident = {};
        $scope.videos = [];
        $scope.images = [];
        $('#incident-modal').modal('hide');
    }

    $scope.create = () => {
        var isError = validateIncident();
        if (isError) {
            return;
        }
        let data = {
            status: $scope.incident.status.code,
            type: $scope.incident.type.code,
            description: $scope.incident.description,
            user_id: $scope.incident.user.id
        }

        $http.post('/api/incident', data).then(response => {
            if (response.data.status == 'successful') {
                if ($scope.images.length > 0) {
                    var attachmentParams = [];
                    var incidentResult = response.data.result;
                    for (var item of $scope.images) {
                        var itemParam = {
                            actor_id: user.id,
                            url: item.path,
                            created_at: moment().format('YYYY-MM-DD HH:mm:ss'),
                            updated_at: moment().format('YYYY-MM-DD HH:mm:ss'),
                            target_id: incidentResult.id,
                            name: item.name,
                            type: 'INCIDENT'
                        };
                        attachmentParams.push(itemParam);
                    }
                    $http({
                        url: '/api/image-incident',
                        method: 'POST',
                        data: JSON.stringify(attachmentParams)
                    }).then(res => {
                        if (res.data.status === 'successful') {
                            $scope.showMessage('Thành công', "Tạo incident thành công", 'success');
                            $scope.closeFormEdit();
                            $scope.reset();
                        }
                    })
                }
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    $scope.update = () => {
        var isError = validateIncident();
        if (isError) {
            return;
        }
        let data = {
            description: $scope.incident.description,
            status: $scope.incident.status.code,
            type: $scope.incident.type.code,
            user_id: $scope.incident.user.id
        }

        $http.patch('/api/incident/' + $scope.incident.id, data).then(async response => {
            if (response.data.status == 'successful') {
                var success = false;
                var listId = [];
                if ($scope.images.length > 0) {
                    var attachmentParams = [];
                    for (var item of $scope.images) {
                        if (!item.id) {
                            var itemParam = {
                                actor_id: user.id,
                                url: item.path,
                                created_at: moment().format('YYYY-MM-DD HH:mm:ss'),
                                updated_at: moment().format('YYYY-MM-DD HH:mm:ss'),
                                target_id: $scope.incident.id,
                                name: item.name,
                                type: 'INCIDENT'
                            };
                            attachmentParams.push(itemParam);
                        } else {
                            listId.push(item.id)
                        }
                    }
                    var listRemove = $scope.incident.images.filter(i => !listId.includes(i.id) && i.id);
                    console.log(listRemove);
                    await $http({
                        url: '/api/image-incident',
                        method: 'POST',
                        data: JSON.stringify(attachmentParams)
                    }).then(res => {
                        if (res.data.status === 'successful') {
                            success = true;
                        } else {
                            $scope.showMessage('Thất bại', 'Có lỗi khi lưu ảnh! Vui lòng thử lại.', 'error');
                        }
                    })
                }
                if ($scope.videos.length > 0) {
                    var attachmentParams = [];
                    for (var item of $scope.videos) {
                        if (!item.id) {
                            var itemParam = {
                                actor_id: user.id,
                                url: item.path,
                                created_at: moment().format('YYYY-MM-DD HH:mm:ss'),
                                updated_at: moment().format('YYYY-MM-DD HH:mm:ss'),
                                target_id: $scope.incident.id,
                                name: item.name,
                                type: 'INCIDENT'
                            };
                            attachmentParams.push(itemParam);
                        }
                    }
                    await $http({
                        url: '/api/video-incident',
                        method: 'POST',
                        data: JSON.stringify(attachmentParams)
                    }).then(res => {
                        if (res.data.status === 'successful') {
                            success = true;
                        } else {
                            $scope.showMessage('Thất bại', 'Có lỗi khi lưu video! Vui lòng thử lại.', 'error');
                        }
                    })
                }
                if (success) {
                    $scope.closeFormEdit();
                    $scope.showMessage('Thành công', "Cập nhật incident thành công", 'success');
                    $scope.reset();
                }
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }

        })
    }

    function validateIncident() {
        var result = false;
        if (!$scope.incident.user) {
            $scope.showMessage('Thất bại', 'Vui lòng chọn người dùng', 'error');
            result = true;
        }
        if (!$scope.incident.type || $scope.incident.type.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn loại incident', 'error');
            result = true;
        }
        if (!$scope.incident.status || $scope.incident.status.value == 'Chưa xác định') {
            $scope.showMessage('Thất bại', 'Vui lòng chọn trạng thái', 'error');
            result = true;
        }
        if (!$scope.incident.description) {
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
            $scope.images.push(fileItem);
        }
    }
    var pushVideos = (response) => {
        var filePath = '/upload/';
        for (var item of response.result) {
            var extractExtension = item.split('.').pop();
            var fileItem = {
                name: item,
                path: `${filePath}${item}`,
            };
            $scope.videos.push(fileItem);
        }
    }

    $scope.delete = (item) => {
        if (!confirm('Bạn có chắc chắn muốn xóa?')) {
            return;
        }
        $http.delete('/api/incident/' + item.id).then(response => {
            if (response.data.status == 'successful') {
                $scope.showMessage('Thành công', "Xóa incident thành công", 'success');
                $scope.reset();
            } else {
                $scope.showMessage('Thất bại', 'Có lỗi! Vui lòng thử lại.', 'error');
            }
        })
    }

    $scope.openImageGallery = (item, image) => {
        $scope.currentImage = image;
        $scope.incident = item;
        $scope.imageGalleries = item.images;
        $('#image-gallery').modal('show');
    }

    $scope.openFormVideo = (item) => {
        $scope.currentVideo = item.videos[0];
        $scope.incident = item;
        $scope.videoGalleries = item.videos;
        $('#video-gallery').modal('show');
    }

    $scope.removeImage = (index) => {
        $scope.images.forEach((element, i) => {
            if (i == index) {
                $scope.images.splice(i, 1);
            }
        });
    }

    $scope.removeVideo = (index) => {
        $scope.videos.forEach((element, i) => {
            if (i == index) {
                $scope.videos.splice(i, 1);
            }
        });
    }

    init();
}