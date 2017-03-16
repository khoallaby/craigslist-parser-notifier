var app = angular.module('clApp', []);

//app.constant('moment', require('moment-timezone'));

app.controller('clContent', function ($scope, $http) {
    var baseUrl = 'api/';
    var numJobs = 10;

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

    $http.get(baseUrl + 'get/' + numJobs).then(function (response) {
        $scope.jobs = response.data.jobs;
    }, function (response) {
        error(response);
    });

    //var defer = $q.defer();

    $scope.clickSave = function (jobId) {
        $http.get(baseUrl + 'save/' + jobId).then(function (response) {
            if (!isError(response)) {
                //var index = $scope.jobs.findIndex( x => x.id==jobId ); // ecma6 way
                var index = _.findIndex($scope.jobs, function (job) {
                    return job.id == jobId;
                });

                if (isNaN(index)) {
                    displayError();
                } else {
                    $scope.jobs[index].saved = response.data.job.saved;
                }
            }
            //defer.resolve(response.data);
        }, function (response) {
            error('Error');
        });

        //return defer.promise;
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