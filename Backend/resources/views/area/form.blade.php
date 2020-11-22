<div id="area-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="background: white; z-index: 9999 !important;">
        <div class="modal-content">
            <form class="form">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Name:</label>
                            <input type="text" class="form-control" placeholder="Enter area name" ng-model="area.name">
                            <span class="form-text text-muted">Please enter area name</span>
                        </div>
                        <div class="col-lg-6">
                            <label>Description:</label>
                            <input type="text" class="form-control" placeholder="Enter area description" ng-model="area.description">
                            <span class="form-text text-muted">Please enter area description</span>
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