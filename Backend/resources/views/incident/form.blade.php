<div id="incident-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="background: white; z-index: 9999 !important;">
        <div class="modal-content">
            <form class="form">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Description:</label>
                            <input type="text" class="form-control" placeholder="Enter incident description" ng-model="incident.description">
                            <span class="form-text text-muted">Please enter incident description</span>
                        </div>
                        <div class="col-lg-6">
                            <label>User:</label>
                            <div class="input-group">
                                <select choosen class="form-control" ng-options="item.name for item in users" ng-model="incident.user">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose user</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Type:</label>
                            <div class="input-group">
                                <select class="form-control" ng-options="item.value for item in types" ng-model="incident.type">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose incident type</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Status:</label>
                            <div class="input-group">
                                <select class="form-control" ng-options="item.value for item in statuses" ng-model="incident.status">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose status</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label text-right">Image Incident</label>
                        <div class="col-lg-9">
                            <div class="image-input image-input-outline" id="kt_image_1">
                                <div class="images-file-item">
                                    <div class="image-input-wrapper" ng-repeat="image in images" style="background-image: url(@{{image.path}})">
                                        <span aria-hidden="true" class="remove-image" ng-click="removeImage($index);">&times;</span>
                                    </div>
                                </div>
                                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                                    <i class="fa fa-pen icon-sm text-muted"></i>
                                    <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg" multiple ng-file='{"callback": "pushImages"}'>
                                    <input type="hidden" name="profile_avatar_remove">
                                </label>
                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
                            </div>
                            <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label text-right">Video Incident</label>
                        <div class="col-lg-9">
                            <div class="image-input image-input-outline" id="kt_image_1">
                                <div class="videos-file-item" ng-repeat="item in videos">
                                    <iframe id="video" class="embed-responsive-item iframe-review-video" ng-src="@{{ trustSrc(item.path + '?autoplay=0') }}" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen controls></iframe>
                                    <span aria-hidden="true" class="remove-image" ng-click="removeVideo($index);">&times;</span>
                                </div>
                                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                                    <i class="fa fa-pen icon-sm text-muted"></i>
                                    <input type="file" name="profile_avatar" accept=".mp4, .mp3" multiple ng-file='{"callback": "pushVideos"}'>
                                    <input type="hidden" name="profile_avatar_remove">
                                </label>
                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
                            </div>
                            <span class="form-text text-muted">Allowed file types: mp4, mp3.</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <button class="btn btn-primary mr-2" ng-click="create();" ng-show="mode == 'create'">Save</button>
                            <button class="btn btn-primary mr-2" ng-click="update();" ng-show="mode == 'update'">Save</button>
                            <button class="btn btn-secondary" ng-click="closeFormEdit();">Cancel</button>
                        </div>
                        <div class="col-lg-6 text-lg-right">
                        </div>
                    </div>
                </div>
            </form>
        </div>
       
    </div>
</div>
<div class="modal fade" id="image-gallery" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header" style="justify-content: left;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">@{{ incident.description }}</h4>
            </div>
            <div class="modal-body">
                <!-- Carousel markup goes in the modal body -->
                
                <div id="carouselImage" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item @{{currentImage.id == image.id ? 'active' : ''}}" ng-repeat="image in imageGalleries">
                            <img class="d-block w-100" ng-src="@{{ image.url }}" style="height: 300px;">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselImage" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselImage" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="video-gallery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="justify-content: left;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Video</h4>
            </div>
            <div id="carouselVideo" class="carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item @{{currentVideo.id == video.id ? 'active' : ''}}" ng-repeat="video in videoGalleries">
                        <iframe id="video" class="embed-responsive-item iframe-video" ng-src="@{{ trustSrc(video.url + '?autoplay=0') }}" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen controls></iframe>  
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselVideo" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselVideo" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<style>
    .iframe-video {
        width: 100%;
        height: 200px;
    }
    .images-file-item {
        display: grid;
        grid-template-columns: 120px 120px;
        grid-gap: 10px;
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .videos-file-item {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .remove-image {
        padding: 5px;
        float: right;
        color: red;
        cursor: pointer;
    }
</style>