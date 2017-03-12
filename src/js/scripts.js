var app = angular.module('clApp', []);


app.controller('clContent', function($scope, $sce, $http) {

    $http.get("/api/get/jobs").then(function(response) {
        $scope.jobs = response.data;
    });


});




app.filter('trustAsHtml', function($sce) {
    return function(html) {
        return $sce.trustAsHtml(html);
    };
});

app.filter('parseDate', function() {
    return function(input) {
        var date = new Date(input);
        return date.toDateString();
    };
});