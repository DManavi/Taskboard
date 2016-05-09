(function (window, app) {

    "use strict";

    app.controller("dashboardCtrl", ["$http", "$scope", "$rootScope",
        function ($http, $scope, $rootScope) {

            $http({
                method: "GET",
                url: "http://a.com"
            })
            .success(function (data, status, headers, config) {

            })
            .error(function (data, status, headers, config) {

            });
        }]);

})(window, window.app);
