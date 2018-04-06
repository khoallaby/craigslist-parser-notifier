var app = angular.module('clApp', ['ngTouch']);

//app.constant('moment', require('moment-timezone'));

app.controller('clContent', function ($scope, $http) {
    var baseUrl = 'api/';
    var numJobs = 100;
    var jobsUrl = 'get/';
    if( $scope.type == 'favorites' ) {
        jobsUrl = 'favorites/';
    }
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



    $scope.updateJob = function (response, type, jobId) {
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
                    $scope.jobs[index].hide = response.data.job.hide;
                }
            }
        }
    };

    // Get jobs
    $http.get(baseUrl + jobsUrl + numJobs).then(function (response) {
        $scope.jobs = response.data.jobs;
    }, function (response) {
        isError(response);
    });


    // Search for jobs
    $scope.search = function (searchValue) {
        $scope.jobs = [];
        $http.get(baseUrl + 'search/' + searchValue).then(function (response) {
            $scope.jobs = response.data.jobs;
        }, function (reponse) {
            isError(response);
        });
    };


    // Save a job
    $scope.clickSave = function (jobId) {
        $http.get(baseUrl + 'save/' + jobId).then(function (response) {
            $scope.updateJob(response, 'save', jobId);
        }, function (reponse) {
            isError(response);
        });
    };


    // Hide function
    $scope.clickHide = function (jobId, direction) {

        direction = typeof direction !== 'undefined' ? direction : '';

        $http.get(baseUrl + 'hide/' + jobId).then(function (response) {
            $scope.direction = direction;
            $scope.updateJob(response, 'hide', jobId);
        }, function (reponse) {
            isError(response);
        });
    };





    /**
     * Time functions
     */


    $scope.timeSince = function (date) {

        var seconds = Math.floor((new Date() - date) / 1000);
        var interval = Math.floor(seconds / 31536000);

        if (interval > 1) {
            return interval + " years";
        }
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) {
            return interval + " months";
        }
        interval = Math.floor(seconds / 86400);
        if (interval > 1) {
            return interval + " days";
        }
        interval = Math.floor(seconds / 3600);
        if (interval > 1) {
            return interval + " hours";
        }
        interval = Math.floor(seconds / 60);
        if (interval > 1) {
            return interval + " minutes";
        }
        return Math.floor(seconds) + " seconds";
    };


    $scope.formatTime = function (date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }


});


// http://stackoverflow.com/questions/19415394/with-ng-bind-html-unsafe-removed-how-do-i-inject-html/25679834#25679834

app.filter('trustAsHtml', function ($sce) {
    return function (html) {
        return $sce.trustAsHtml(html);
    };
});

app.filter('parseDate', function () {
    return function (input, scope) {
        var date = new Date(input),
            locale = "en-us",
            month = date.toLocaleString(locale, { month: "long" });
        var dateFormat = month + ' ' + date.getDay();
        var timeFormat = scope.formatTime(date);
        return scope.timeSince(date) + ' ago (' + dateFormat + ' - ' + timeFormat + ')';
    };
});

