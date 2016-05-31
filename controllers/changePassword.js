(function (window, app) {

    "use strict";

    app.controller("changePasswordCtrl", ["$location", "$http", "$scope", "$rootScope", "constant", "$cookies",
        function ($location, $http, $scope, $rootScope, constant, $cookies) {

            if (!$cookies.get("auth")) {

                $location.path("/login");
            }
            else {

                $scope.showDashboard = function () {
                    $location.path('/dashboard');
                };

                $scope.changePassword = function () {
                    $http({
                        method: "POST",
                        url: constant.API.account.changePassword,
                        data: $scope.model
                    })
                        .success(function (data, status, headers, config) {
                            Materialize.toast("رمز عبور با موفقیت تغییر یافت", 5000);

                            $location.path("/dashboard");
                        })
                        .error(function (data, status, headers, config) {

                            switch (status) {

                                case 403:
                                {

                                    Materialize.toast("رمز عبور فعلی اشتباه است", 5000);

                                    break;
                                }

                                default:
                                {

                                    Materialize.toast("در هنگام ورود خطایی رخ داده است", 5000);

                                    break;
                                }
                            }
                        });
                };
            }
        }]);

})(window, window.app);
