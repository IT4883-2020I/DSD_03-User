<div class="col-lg-12">
    <div ng-repeat="item in users">
    <h3 class="card-label">@{{ item.name }}</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(calc(50% - 20px), 1fr)); grid-gap: 20px">
            <div ng-repeat="image in item.images">
                <img id="image" ng-src="@{{ image.url }}" />
            </div>
        </div>
    </div>
</div>
<style>
    #image {
        width: 100%;
        height: 400px;
    }
</style>