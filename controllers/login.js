(function (window, app) {

    "use strict";

    app.controller("loginCtrl", ["$location", "$http", "$scope", "$rootScope", "constant", "$cookies",
        function ($location, $http, $scope, $rootScope, constant, $cookies) {

            if ($cookies.get("auth")) {

                $location.path("/dashboard");
            }
            else {

                $scope.model = {};

                $scope.login = function () {

                    $http({
                        method: "POST",
                        url: constant.API.account.login,
                        data: $scope.model
                    })
                        .success(function (data, status, headers, config) {
                            $location.path("/dashboard");
                        })
                        .error(function (data, status, headers, config) {

                            switch (status) {

                                case 403:
                                {

                                    Materialize.toast("نام کاربری/رمز عبور اشتباه است", 5000);

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
