<?php Craigslist\WebUI::header(); ?>
<section class="content" ng-controller="clContent">
    <section ng-repeat="job in jobs" class="post" ng-class="{'saved': job.saved}" ng-class-odd="'odd'" ng-class-even="'even'">
        <div class="container" >
            <div class="row">
                <article class="col-md-12">
                    <h2>
                        <a href="{{ job.link }}" target="_blank">{{ job.title }}</a>
                    </h2>
                    <p>
                        <time datetime="{{ job.date }}">{{ job.date | parseDate }}</time>
                        <span class="icons">
                            <a href class="badge badge-default" ng-click="clickSave(job.id)"><i class="fa fa-heart" aria-hidden="true"></i></a>
                            <a href="api/hide/{{ job.id }}" class="badge badge-default"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </span>
                    </p>
                    <p class="description" ng-bind-html="job.description |trustAsHtml"></p>
                </article>
            </div>
        </div>
    </section>
</section>


<?php Craigslist\WebUI::footer(); ?>