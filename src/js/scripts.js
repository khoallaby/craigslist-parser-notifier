var app = angular.module('clApp', ['ngTouch']);

//app.constant('moment', require('moment-timezone'));

app.controller('clContent', function ($scope, $http) {
    var baseUrl = 'api/';
    var numJobs = 50;
    $scope.direction = 'left';

    var displayError = function (message) {
        console.log('error: %s', message);
        // do errory stuff here
    };

    var isError = function (response) {
        var defaultMessage = 'Something blew up..';
        if (response.data.status === true) {
            return false;
        } else {
            if (message = response.data.message) {
                displayError(message);
            } else {
                displayError(defaultMessage);
            }
            return true;
        }
    };



    var updateJob = function (response, type, jobId) {
        if (!isError(response)) {
            //var index = $scope.jobs.findIndex( x => x.id==jobId ); // ecma6 way
            var index = _.findIndex($scope.jobs, function (job) {
                return job.id == jobId;
            });

            if (isNaN(index)) {
                displayError();
            } else {
                if (type == 'save') {
                    $scope.jobs[index].saved = response.data.job.saved;
                } else if (type == 'hide') {
                    // hide the job
                    console.log(response.data.job.hide);
                    $scope.jobs[index].hide = response.data.job.hide;
                }
            }
        }
    };

    // Get jobs
    $http.get(baseUrl + 'get/' + numJobs).then(function (response) {
        $scope.jobs = response.data.jobs;
    }, function (response) {
        error(response);
    });


    // Save a job
    $scope.clickSave = function (jobId) {
        $http.get(baseUrl + 'save/' + jobId).then(function (response) {
            updateJob(response, 'save', jobId);
        }, function (reponse) {
            isError(response);
        });
    };


    // Hide function
    $scope.clickHide = function (jobId, direction) {

        direction = typeof direction !== 'undefined' ? direction : '';

        $http.get(baseUrl + 'hide/' + jobId).then(function (response) {
            $scope.direction = direction;
            updateJob(response, 'hide', jobId);
        }, function (reponse) {
            isError(response);
        });
    };


});


// http://stackoverflow.com/questions/19415394/with-ng-bind-html-unsafe-removed-how-do-i-inject-html/25679834#25679834

app.filter('trustAsHtml', function ($sce) {
    return function (html) {
        return $sce.trustAsHtml(html);
    };
});

app.filter('parseDate', function () {
    return function (input) {
        var date = new Date(input);
        return date.toDateString();
    };
});