<div class="col-lg-12">
    <div ng-repeat="item in users">
    <h3 class="card-label">User: @{{ item.name }}</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(calc(50% - 20px), 1fr)); grid-gap: 20px">
        <div ng-repeat="video in item.videos">
            <iframe id="video" class="embed-responsive-item iframe-video" ng-src="@{{ video.url }}" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        </div>
    </div>
</div>
<style>
    #video {
        height: 200px;
    }
</style>