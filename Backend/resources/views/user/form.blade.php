<div id="user-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="background: white; z-index: 9999 !important;">
        <div class="modal-content">
            <form class="form">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Name:</label>
                            <input type="text" class="form-control" placeholder="Enter user name" ng-model="user.name">
                            <span class="form-text text-muted">Please enter user name</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Phone:</label>
                            <input type="phone" class="form-control" placeholder="Enter user phone" ng-model="user.phone">
                            <span class="form-text text-muted">Please enter user phone</span>
                        </div>
                    </div>
                    <div class="form-group row" ng-show="mode == 'create'">
                        <div class="col-lg-6">
                            <label>Password:</label>
                            <input type="password" class="form-control" placeholder="Enter user password" ng-model="user.password">
                            <span class="form-text text-muted">Please enter user password</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Confirm Password:</label>
                            <input type="password" class="form-control" placeholder="Enter confirm password" ng-model="user.confirm_password">
                            <span class="form-text text-muted">Please enter confirm password</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Email:</label>
                            <input type="text" class="form-control" placeholder="Enter user email" ng-model="user.email">
                            <span class="form-text text-muted">Please enter user email</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Address:</label>
                            <input type="text" class="form-control" placeholder="Enter user address" ng-model="user.address">
                            <span class="form-text text-muted">Please enter user address</span>
                        </div>
                    </div>
                    @if (Auth::user()->role == 'ADMIN')
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Status:</label>
                            <div class="input-group">
                                <select class="form-control" ng-options="item.value for item in statuses" ng-model="user.status">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose status</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Role:</label>
                            <div class="input-group">
                                <select class="form-control" ng-options="item.value for item in roles" ng-model="user.role">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose user roles</span>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label text-right">Avatar</label>
                        <div class="col-lg-9 col-xl-6">
                            <div class="image-input image-input-outline" id="kt_image_1">
                                <div class="image-input-wrapper" style="background-image: url(/media/users/default.jpg)"></div>
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