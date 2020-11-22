<?php
//validate parameters
if (!isset($accessPageId)) {
    echo "<span class='text-bold text-red'>Render paginator: missing parameter accessPageId to access pageId value</span>";
    return;
}
if (!isset($accessPagesCount)) {
    echo "<span class='text-bold text-red'>Render paginator: missing parameter accessPagesCount to access pagesCount value</span>";
    return;
}

if (!isset($accessFind)) {
    echo "<span class='text-bold text-red'>Render paginator: missing parameter accessFind to access find() function</span>";
    return;
}
?>
<style>
    .pagination li {
        cursor: pointer;
    }
</style>
<div class="datatable datatable-default datatable-primary datatable-loaded">
    <div ng-show="<?= $accessPagesCount ?> > 0" ng-init="steps = [ - 5, - 4, - 3, - 2, - 1, 0, 1, 2, 3, 4, 5 ]" class="datatable-pager datatable-paging-loaded">
        <ul class="datatable-pager-nav mb-5 mb-sm-0">
            <li>
                <a title="First"
                    class="datatable-pager-link datatable-pager-link-first {{$parent.<?= $accessPageId ?> == 0 ? 'datatable-pager-link-disabled' : ''}}"
                    disabled="disabled"
                    ng-click="$parent.<?= $accessPageId ?> = 0; $parent.<?= $accessFind ?>"
                    ng-disabled="$parent.<?= $accessPageId ?> == 0"
                >
                    <i class="flaticon2-fast-back"></i>
                </a>
            </li>
            <li>
                <a title="Previous"
                    class="datatable-pager-link datatable-pager-link-prev {{$parent.<?= $accessPageId ?> == 0 ? 'datatable-pager-link-disabled' : ''}}"
                    disabled="disabled"
                    ng-click="$parent.<?= $accessPageId ?> = $parent.<?= $accessPageId ?> - 1; $parent.<?= $accessFind ?>"
                    ng-disabled="$parent.<?= $accessPageId ?> == 0"
                >
                    <i class="flaticon2-back"></i>
                </a>
            </li>
            <li class=""
                ng-repeat="step in steps"
                ng-show="$parent.<?= $accessPageId ?> + step >= 0 && $parent.<?= $accessPageId ?> + step < $parent.<?= $accessPagesCount ?>">
                <a ng-click="$parent.<?= $accessPageId ?> = $parent.<?= $accessPageId ?> + step;$parent.<?= $accessFind ?>"
                    class="datatable-pager-link datatable-pager-link-number @{{step == 0 ? 'datatable-pager-link-active' : ''}}"
                >
                    {{$parent.<?= $accessPageId ?> + step + 1}}
                </a>
            </li>
            <li>
                <a title="Next"
                    class="datatable-pager-link datatable-pager-link-next {{$parent.<?= $accessPageId ?> == <?= $accessPagesCount ?> - 1 ? 'datatable-pager-link-disabled' : ''}}"
                    ng-click="$parent.<?= $accessPageId ?> = $parent.<?= $accessPageId ?> + 1; $parent.<?= $accessFind ?>"
                    ng-disabled="$parent.<?= $accessPageId ?> == <?= $accessPagesCount ?> - 1"
                >
                    <i class="flaticon2-next"></i>
                </a>
            </li>
            <li>
                <a title="Last"
                    class="datatable-pager-link datatable-pager-link-last {{$parent.<?= $accessPageId ?> == <?= $accessPagesCount ?> - 1 ? 'datatable-pager-link-disabled' : ''}}"
                    ng-click="$parent.<?= $accessPageId ?> = <?= $accessPagesCount ?> - 1; $parent.<?= $accessFind ?>"
                    ng-disabled="$parent.<?= $accessPageId ?> == <?= $accessPagesCount ?> - 1"
                >
                    <i class="flaticon2-fast-next"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
