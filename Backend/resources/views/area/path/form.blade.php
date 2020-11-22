<div id="path-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="background: white; z-index: 9999 !important;">
        <div class="modal-content">
            <form class="form">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Name:</label>
                            <input type="text" class="form-control" placeholder="Enter path name" ng-model="path.name">
                            <span class="form-text text-muted">Please enter path name</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Areas:</label>
                            <div class="input-group">
                                <select choosen class="form-control" ng-options="item.name for item in areas" ng-model="path.area">
                                    <option value="">Chưa xác định</option>
                                </select>
                            </div>
                            <span class="form-text text-muted">Please choose area</span>
                        </div>
                        
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Start:</label>
                            <input type="text" class="form-control" placeholder="Enter path start" ng-model="path.start">
                            <span class="form-text text-muted">Please enter path start</span>
                        </div>
                        <div class="col-lg-6">
                            <label>End:</label>
                            <input type="text" class="form-control" placeholder="Enter path end" ng-model="path.end">
                            <span class="form-text text-muted">Please enter path end</span>
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