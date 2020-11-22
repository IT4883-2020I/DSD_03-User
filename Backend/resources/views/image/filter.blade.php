<!--begin::Search Form-->
<div class="mt-2 mb-5 mt-lg-5 mb-lg-10">
    <div class="row align-items-center">
        <div class="col-lg-12 col-xl-12">
            <div class="row align-items-center">
                <div class="col-md-3 my-2 my-md-0 pt-5">
                    <div class="input-icon">
                        <input type="text" class="form-control" placeholder="Search..." id="kt_datatable_search_query" ng-model="filter.search" ng-change="getUser();" />
                        <span><i class="flaticon2-search-1 text-muted"></i></span>
                    </div>
                </div>
                <div class="col-md-3 my-2 my-md-0 pt-5">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">Type:</label>
                        <select class="form-control"
                            id="kt_datatable_search_type"
                            ng-model="filter.type"
                            ng-options="type as type.value for type in types"
                            ng-change="changeType();"
                        >
                        </select>
                    </div>
                </div>
                <div class="col-md-3 my-2 my-md-0 pt-5" ng-show="type == 'USER'">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">User:</label>
                        <select class="form-control"
                            id="kt_datatable_search_type"
                            ng-model="filter.user"
                            ng-options="item.name for item in users"
                            ng-change="getImage();"
                        >
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 my-2 my-md-0 pt-5" ng-show="type == 'DRONE'">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">Drone:</label>
                        <select class="form-control"
                            id="kt_datatable_search_type"
                            ng-model="filter.user"
                            ng-options="item.name for item in drones"
                            ng-change="getImage();"
                        >
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 my-2 my-md-0 pt-5">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">Create from:</label>
                        <input class="form-control" id="from" ng-model="filter.from" ng-change="getArea();"/>
                    </div>
                </div>
                <div class="col-md-3 my-2 my-md-0 pt-5">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">Create to:</label>
                        <input class="form-control" id="to" ng-model="filter.to" ng-change="getArea();"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 row align-items-center" style="justify-content: center; margin-top: 10px">
        <a href="#" class="btn btn-light-primary px-6 font-weight-bold" ng-click="reset();">
            Reset
        </a>
    </div>
</div>
<!--end::Search Form-->