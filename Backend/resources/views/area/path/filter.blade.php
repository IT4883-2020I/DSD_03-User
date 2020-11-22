<!--begin::Search Form-->
<div class="mt-2 mb-5 mt-lg-5 mb-lg-10">
    <div class="row align-items-center">
        <div class="col-lg-12 col-xl-12">
            <div class="row align-items-center">
                <div class="col-md-3 my-2 my-md-0 pt-5">
                    <div class="input-icon">
                        <input type="text" class="form-control" placeholder="Search..." id="kt_datatable_search_query" ng-model="filter.search" ng-change="getPath();" />
                        <span><i class="flaticon2-search-1 text-muted"></i></span>
                    </div>
                </div>

                <div class="col-md-3 my-2 my-md-0 pt-5">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">Area:</label>
                        <select class="form-control"
                            id="kt_datatable_search_status"
                            ng-model="filter.area"
                            ng-options="area.name for area in areas"
                            ng-change="getPath();"
                        >
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row align-items-center" style="margin-top: 20px">
                <div class="col-md-3 my-2 my-md-0 pt-5">
                </div>

                <div class="col-md-3 my-2 my-md-0 pt-5">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">From:</label>
                            <input class="form-control" id="from" ng-model="filter.from" ng-change="getPath();" value=""/>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 my-2 my-md-0 pt-5">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">To:</label>
                        <input class="form-control" id="to" ng-model="filter.to" ng-change="getPath();" value=""/>
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