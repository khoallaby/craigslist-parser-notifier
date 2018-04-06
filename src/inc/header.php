<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">
	<title>Craigslist Posts</title>
	<link href="assets/css/style.min.css" rel="stylesheet" />
    <script type="text/javascript" src="assets/js/angular.min.js"></script>
    <script type="text/javascript" src="assets/js/angular-touch.min.js"></script>
    <script type="text/javascript" src="assets/js/underscore-min.js"></script>
    <script type="text/javascript" src="assets/js/scripts.js"></script>
</head>
<body ng-app="clApp" ng-controller="clContent" ng-init="type='<?php echo Craigslist\WebUI::getCurrentPage(); ?>'">
