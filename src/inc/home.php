<?php Craigslist\WebUI::header(); ?>
<section class="content" ng-controller="clContent">
    <section ng-repeat="job in jobs" class="post {{ direction }}" ng-class="{'saved': job.saved, 'hide': job.hide}" ng-class-odd="'odd'" ng-class-even="'even'">
        <div class="container"  ng-swipe-left="clickHide(job.id)" ng-swipe-right="clickSave(job.id, 'right')">
            <div class="row">
                <article class="col-md-12">
                    <h2>
                        <a href="{{ job.link }}" target="_blank">{{ job.title }}</a>
                    </h2>
                    <p>
                        <time datetime="{{ job.date }}">{{ job.date | parseDate:this }}</time>
                        <span class="icons">
                            <a href class="badge badge-default" ng-click="clickSave(job.id)"><i class="fa fa-heart" aria-hidden="true"></i></a>
                            <a href class="badge badge-default" ng-click="clickHide(job.id)"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </span>
                    </p>
                    <p class="description" ng-bind-html="job.description |trustAsHtml"></p>
                </article>
            </div>
        </div>
    </section>
</section>


<?php Craigslist\WebUI::footer(); ?>