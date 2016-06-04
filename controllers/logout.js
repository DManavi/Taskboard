(function (window, app) {

    "use strict";

    app.controller("logoutCtrl", ["$location", "$http", "$scope", "$rootScope", "constant", "$window",
        function ($location, $http, $scope, $rootScope, constant, $window) {

            $scope.showDashboard = function () {
                $window.history.back();
            };

            $scope.logout = function () {
                $http({
                    method: "POST",
                    url: constant.API.account.logout
                })
                    .success(function (data, status, headers, config) {

                        $location.path("/login");
                    })
                    .error(function (data, status, headers, config) {
                        Materialize.toast("خطا در خروج از سایت", 5000);
                    });
            };
        }]);

})(window, window.app);
