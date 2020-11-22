<form class="form-horizontal" style="margin-top: 10px">
    <div class="form-group row">
        <label class="col-sm-1 col-form-label">Type</label>
        <div class="col-sm-2">
            <select class="form-control" ng-model="filter.type" ng-change="find();">
                <option value="user" selected>User</option>
                <option value="drone">Drone</option>
            </select>
        </div>

        <label class="col-sm-1 col-form-label">From</label>
        <div class="col-sm-2">
            <input class="form-control" id="from" ng-model="filter.from" ng-change="find();" value=""/>
        </div>

        <label class="col-sm-1 col-form-label">To</label>
        <div class="col-sm-2">
            <input class="form-control" id="to" ng-model="filter.to" ng-change="find();" value=""/>
        </div>
    </div>
</form>