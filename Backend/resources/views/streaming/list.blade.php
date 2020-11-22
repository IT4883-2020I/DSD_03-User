<div class="col-lg-12">
    <div ng-repeat="item in drones" ng-if="item.videos.length > 0">
        <div style="display: flex;">
            <h3 class="card-label">Drone: @{{ item.name }}</h3>
            <span style="margin-left: 5px;">
                <img src="/media/giphy.gif" alt="" style="width: 20px;">
            </span>
        </div>
    
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
        width: 100%;
    }
</style>