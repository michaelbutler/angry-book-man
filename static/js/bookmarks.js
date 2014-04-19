/**
 * AngularJS Controller: Bookmarks
 *
 * @author Michael C. Butler
 */

window.addEventListener('DOMContentLoaded', function () {
    document.getElementById('title-input').focus();
});

function BookmarkListCtrl ($scope, $http) {
    'use strict';

    $scope.editing = false;

    function getRandomUUID () {
        var chars = 'abcdefghijk1mnopqrstuvwxyz234567890-_',
            uuid = '',
            max = chars.length;
        for (var i = 0; i < 8; i++) {
            uuid += chars.charAt(Math.floor(Math.random() * (max + 1)));
        }
        return (new Date().getTime()) + uuid;
    }

    $scope.addBookmark = function () {
        var urlString = $scope.bookmarkUrl,
            newBookmark;

        if ($scope.bookmarkUrl === '' || $scope.bookmarkTitle === '') {
            return;
        }

        if (urlString.substring(0, 4) !== 'http') {
            urlString = 'http://' + urlString;
        }
        newBookmark = {
            url: urlString,
            title: $scope.bookmarkTitle,
            display_id: $scope.bookmarkDisplayId || getRandomUUID()
        };

        if ($scope.bookmarkDisplayId) {
            // Find and update the internal data model with the changes
            var editingIndex = 0;
            angular.forEach($scope.bookmarks, function (value, key) {
                if (value.display_id === $scope.bookmarkDisplayId) {
                    editingIndex = key;
                }
            });
            $scope.bookmarks[editingIndex] = newBookmark;
        }
        else {
            $scope.bookmarks.push(newBookmark);
        }

        // Central ajax call handles updating and inserting
        $http.post('/bookmarks/', newBookmark).success(function () {
            $scope.bookmarkUrl = '';
            $scope.bookmarkTitle = '';
            $scope.bookmarkDisplayId = '';
            $scope.editing = false;
            document.getElementById('title-input').focus();
        });
    };

    $scope.deleteBookmark = function (index) {
        var bookmark = $scope.bookmarks[index];

        // Remove first from the server
        $http.delete('/bookmarks/' + bookmark.display_id).success(function () {
            // Animate removal
            bookmark.className = 'deleted';
            setTimeout(function () {
                $scope.$apply(function () {
                    // Finally remove from data model
                    $scope.bookmarks.splice(index, 1);
                });
            }, 200);
        });
    };

    $scope.editBookmark = function (index) {
        var bookmark = $scope.bookmarks[index];
        $scope.bookmarkUrl = bookmark.url;
        $scope.bookmarkTitle = bookmark.title;
        $scope.bookmarkDisplayId = bookmark.display_id;
        $scope.editing = true;
        setTimeout(function () {
            document.getElementById('title-input').select();
        }, 50);
    };

    $scope.cancelEdit = function () {
        $scope.bookmarkUrl =
            $scope.bookmarkTitle =
            $scope.bookmarkDisplayId = '';
        $scope.editing = false;
    };

    /**
     * Load initial bookmarks from (in order of priority):
     * 1. the DOM (PHP preloaded)
     * 2. Empty array
     */
    if (window.bookmarks && window.bookmarks.config) {
        $scope.bookmarks = window.bookmarks.config || [];
    }
    else {
        $scope.bookmarks = [];
    }

}