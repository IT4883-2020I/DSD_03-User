<table class="table table-bordered table-hover" id="kt_datatable">
    <thead>
        <tr>
            <th>#</th>
            <th>Incident ID</th>
            <th>Mô tả</th>
            <th>Status</th>
            <th>Type</th>
            <th style="width: 180px;">Image</th>
            <th>Video</th>
            <th>User</th>
            <th>Created at</th>
            <th style="width: 15%;"></th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="item in incidents">
            <td>@{{ $index + 1 }}</td>
            <td>@{{ item.id}}</td>
            <td>@{{ item.description }}</td>
            <td>@{{ getByField(statuses, 'code', item.status).value }}</td>
            <td>@{{ getByField(types, 'code', item.type).value }}</td>
            <td>
                <div class="list-images">
                    <div class="box-image" ng-repeat="image in item.images">
                        <img ng-src="@{{ image.url }}" alt="" ng-click="openImageGallery(item, image)">
                    </div>

                </div>
            </td>
            <td>
                <div class="symbol symbol-40 symbol-light-warning mr-5" style="cursor: pointer;" ng-click="openFormVideo(item)" ng-show="item.videos.length > 0">
                    <span class="symbol-label">
                        {{ Metronic::getSVG("plugins/global/fonts/line-awesome/eye-solid.svg", "svg-icon-lg svg-icon-warning") }}
                    </span>
                </div>
            </td>
            <td>@{{ item.user.name }}</td>
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
<style>
    .list-images {
        display: flex;
    }
    .box-image {
        margin-right: 10px;
    }
    .box-image img {
        width: 40px;
        height: 30px;
        background-size: cover;
    }
</style>