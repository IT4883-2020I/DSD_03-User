<div id="schedule-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="background: white; z-index: 9999 !important;">
        <div class="modal-content">
            <form class="form">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Name:</label>
                            <input type="text" class="form-control" placeholder="Enter schedule name" ng-model="schedule.name">
                            <span class="form-text text-muted">Please enter schedule name</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Description:</label>
                            <input type="text" class="form-control" placeholder="Enter schedule speed" ng-model="schedule.description">
                            <span class="form-text text-muted">Please enter schedule description</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>User:</label>
                            <div class="input-group">
                                <select choosen class="form-control" ng-options="item.name for item in users" ng-model="schedule.user">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose user</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Status:</label>
                            <div class="input-group">
                                <select class="form-control" ng-options="item.value for item in statuses" ng-model="schedule.status">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose status</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        
                        <div class="col-lg-6">
                            <label>Type:</label>
                            <div class="input-group">
                                <select class="form-control" ng-options="item.value for item in types" ng-model="schedule.type">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose schedule type</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label text-right">Image Schedule</label>
                        <div class="col-lg-9 col-xl-6">
                            <div class="image-input image-input-outline" id="kt_image_1">
                                <div class="image-input-wrapper" style="background-image: url(/metronic/theme/html/demo1/dist/assets/media/users/100_1.jpg)"></div>
                                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                                    <i class="fa fa-pen icon-sm text-muted"></i>
                                    <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg">
                                    <input type="hidden" name="profile_avatar_remove">
                                </label>
                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
                            </div>
                            <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
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