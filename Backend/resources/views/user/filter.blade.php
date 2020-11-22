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
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">Status:</label>
                        <select class="form-control"
                            id="kt_datatable_search_status"
                            ng-model="filter.status"
                            ng-options="status.value for status in statuses"
                            ng-change="getUser();"
                        >
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 my-2 my-md-0 pt-5">
                    <div class="d-flex align-items-center">
                        <label class="mr-3 mb-0 d-none d-md-block w-60px">Role:</label>
                        <select class="form-control"
                            id="kt_datatable_search_type"
                            ng-model="filter.role"
                            ng-options="role.value for role in roles"
                            ng-change="getUser();"
                        >
                            <option value="">All</option>
                        </select>
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