<?php
/**
 * This is the view.
 *
 * The only PHP code in this file is this comment block, and the $bookmarkConfig output at the bottom
 *
 * @author Michael C. Butler
 */

?><!doctype html>
<html ng-app>
<head>
    <title>Bookmark Manager</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='http://fonts.googleapis.com/css?family=Hammersmith+One' rel='stylesheet' type='text/css'>
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="/static/css/custom.css" rel="stylesheet">

</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h1>Bookmark Manager</h1>

            <p>Begin by entering your favorite websites and press ENTER or click &quot;Add&quot;.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="bookmark-list-wrapper" ng-controller="BookmarkListCtrl">
                <form method="get" ng-submit="addBookmark()" id="bookmark-form">
                    <fieldset class="row">
                        <div class="col-md-12">
                            <p>
                                <ng-pluralize count="bookmarks.length"
                                              when="{'0': 'You have no bookmarks',
                                               'one': 'You have one bookmark',
                                               'other': 'You have {} bookmarks'}">
                                </ng-pluralize>.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <div class="title-group">
                                <input type="text" class="form-control" id="title-input" ng-model="bookmarkTitle"
                                       autocomplete="off" required
                                       placeholder="Enter a title" title="The name of the bookmark"/>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" id="url-input" ng-model="bookmarkUrl"
                                       autocomplete="off" required
                                       placeholder="www.mysite..." title="The URL address to the bookmark"/>
                                <input type="hidden" ng-model="bookmarkDisplayId" value="" />
                                <span class="input-group-btn">
                                    <button ng-click="cancelEdit()" class="btn btn-default" ng-show="editing" type="button">
                                        <span class="glyphicon glyphicon-ban-circle"></span>
                                        <span>Cancel</span>
                                    </button>
                                    <button class="btn btn-default" type="submit">
                                        <span class="glyphicon glyphicon-plus"></span>
                                        <span ng-hide="editing">Add</span>
                                        <span ng-show="editing">Edit</span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </fieldset>
                </form>
                <ul class="bookmarks">
                    <li ng-repeat="bookmark in bookmarks" class="{{ bookmark.className }}">
                        <a ng-click="deleteBookmark($index)" class="glyphicon glyphicon-trash" title="Delete bookmark"></a>
                        <a ng-click="editBookmark($index)" class="glyphicon glyphicon-pencil" title="Edit bookmark"></a>
                        <div>
                            <a href="{{ bookmark.url }}" title="{{ bookmark.url }}" target="_blank">{{ bookmark.title }} ({{ bookmark.url }})</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="fun-image"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <p>Powered by:
                <a href="http://getbootstrap.com">Twitter Bootstrap</a> &hybull;
                <a href="http://angularjs.org">AngularJS</a> &hybull;
                <a href="http://php.net">PHP</a> &hybull;
                <a href="http://mysql.com">MySQL</a>
            </p>
        </div>
    </div>

</div>


<script src="/static/js/angular.min.js"></script>
<script src="/static/js/bookmarks.js"></script>
<script>
    var bookmarks = {};
    bookmarks.config = <?php echo $bookmarkConfig; ?>;
</script>
</body>
</html>
