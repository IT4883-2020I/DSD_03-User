<table class="table table-bordered table-hover" id="kt_datatable">
    <thead>
        <tr>
            <th>#</th>
            <th>Area ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Created at</th>
            <th style="width: 15%;"></th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="item in areas">
            <td>@{{ $index + 1 }}</td>
            <td>@{{ item.id}}</td>
            <td>@{{ item.name }}</td>
            <td>@{{ item.description }}</td>
            <td>@{{ summarizeDateTime(item.created_at, true) }}</td>
            <td>
                <div class="symbol symbol-40 symbol-light-warning mr-5" style="cursor: pointer;" ng-click="openFormEdit(item, 'update')">
                    <span class="symbol-label">
                        {{ Metronic::getSVG("media/svg/icons/Communication/Write.svg", "svg-icon-lg svg-icon-warning") }}
                    </span>
                </div>
                <div class="symbol symbol-40 symbol-light-warning mr-5" style="cursor: pointer;" ng-click="delete(item)">
                    <span class="symbol-label">
                        {{ Metronic::getSVG("media/svg/icons/Navigation/Close.svg", "svg-icon-lg svg-icon-danger") }}
                    </span>
                </div>
            </td>
        </tr>
    </tbody>
</table>