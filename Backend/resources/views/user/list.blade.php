<table class="table table-bordered table-hover" id="kt_datatable">
    <thead>
        <tr>
            <th>#</th>
            <th>User ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Status</th>
            <th>Role</th>
            <th>Created at</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="item in users">
            <td>@{{ $index + 1 }}</td>
            <td>@{{ item.id}}</td>
            <td>@{{ item.name }}</td>
            <td>@{{ formatPhone(item.phone) }}</td>
            <td>@{{ item.email }}</td>
            <td>@{{ getByField(statuses, 'code', item.status).value }}</td>
            <td>@{{ getByField(roles, 'code', item.role).value }}</td>
            <td>@{{ summarizeDateTime(item.created_at, true) }}</td>
            <td>
                <div class="symbol symbol-40 symbol-light-warning mr-5" style="cursor: pointer;" ng-click="openFormEdit(item, 'update')">
                    <span class="symbol-label">
                        {{ Metronic::getSVG("media/svg/icons/Communication/Write.svg", "svg-icon-lg svg-icon-warning") }}
                    </span>
                </div>            
            </td>
        </tr>
    </tbody>
</table>