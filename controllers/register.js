(function (window, app) {

    "use strict";

    app.controller("registerCtrl", ["$location", "$http", "$scope", "$rootScope", "constant", "$cookies",
        function ($location, $http, $scope, $rootScope, constant, $cookies) {

            if ($cookies.get("auth")) {

                $location.path("/dashboard");
            }
            else {

                $scope.model = {};

                $scope.register = function () {

                    $http({
                        method: "POST",
                        url: constant.API.account.register,
                        data: $scope.model
                    })
                        .success(function (data, status, headers, config) {

                            Materialize.toast("ثبت نام با موفقیت انجام شد", 5000);

                            $location.path("/login");
                        })
                        .error(function (data, status, headers, config) {

                            switch (status) {
                                case 409:
                                {

                                    Materialize.toast("این آدرس ایمیل قبلا ثبت شده است", 5000);

                                    break;
                                }

                                default:
                                {

                                    Materialize.toast("در هنگام ثبت نام خطایی رخ داده است", 5000);

                                    break;
                                }
                            }
                        });
                };
            }
        }]);

})(window, window.app);
