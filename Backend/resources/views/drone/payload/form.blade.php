<div id="payload-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="background: white; z-index: 9999 !important;">
        <div class="modal-content">
            <form class="form">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Name:</label>
                            <input type="text" class="form-control" placeholder="Enter payload name" ng-model="payload.name">
                            <span class="form-text text-muted">Please enter payload name</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Speed:</label>
                            <input type="text" class="form-control" placeholder="Enter payload speed" ng-model="payload.speed">
                            <span class="form-text text-muted">Please enter payload speed</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Manufacturer:</label>
                            <input type="text" class="form-control" placeholder="Enter payload manufacturer" ng-model="payload.manufacturer">
                            <span class="form-text text-muted">Please enter payload manufacturer</span>
                        </div>
                        <div class="col-lg-6">
                            <label>User:</label>
                            <div class="input-group">
                                <select choosen class="form-control" ng-options="item.name for item in users" ng-model="payload.user">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose user</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Status:</label>
                            <div class="input-group">
                                <select class="form-control" ng-options="item.value for item in statuses" ng-model="payload.status">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose status</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Type:</label>
                            <div class="input-group">
                                <select class="form-control" ng-options="item.value for item in types" ng-model="payload.type">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose payload type</span>
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