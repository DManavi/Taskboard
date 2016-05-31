(function (window, app) {

    "use strict";

    app.controller("categoryCtrl", ["$location", "$http", "$scope", "$rootScope", "constant", "$cookies", "$stateParams",
        function ($location, $http, $scope, $rootScope, constant, $cookies, $stateParams) {

            if (!$cookies.get("auth")) {

                $location.path("/login");
            }
            else {

                if($stateParams.parentId) {

                    $scope.model = $scope.model || {};

                    // set state param
                    $scope.model.parentId = $stateParams.parentId;
                }

                if($location.path() == '/createCategory') {

                    $scope.actionType = 0;

                    $scope.action = 'ایجاد دسته بندی';
                }
                else if ($location.path().indexOf('/editCategory/') == 0) {

                    $scope.action = 'ویرایش دسته بندی';

                    $scope.actionType = 1;

                    $http({
                        method: "GET",
                        url: constant.API.category.list,
                    })
                        .success(function (data, status, headers, config) {

                            $scope.model = data.filter(function(obj){
                                return obj.id == $stateParams.id;
                            })[0];
                        })
                        .error(function (data, status, headers, config) {
                            Materialize.toast("در هنگام بار گذاری صفحه خطایی رخ داده است", 5000);
                        });
                }
                else if ($location.path().indexOf('/createSubCategory') == 0) {
                    $scope.actionType = 0;

                    $scope.action = 'ایجاد زیر دسته';
                }

                $scope.showDashboard = function () {
                    $location.path('/dashboard');
                };

                $scope.submit = function (model) {

                    if($scope.actionType == 0) {

                        $http({
                            method: "POST",
                            url: constant.API.category.create,
                            data: $scope.model
                        })
                            .success(function (data, status, headers, config) {
                                Materialize.toast("دسته بندی با موفقیت ایجاد شد", 5000);

                                $location.path("/dashboard");
                            })
                            .error(function (data, status, headers, config) {

                                switch (status) {

                                    case 403:
                                    {

                                        Materialize.toast("دسترسی امکان پذیر نیست", 5000);

                                        break;
                                    }

                                    default:
                                    {

                                        Materialize.toast("خطا در به روز رسانی دسته بندی، لطفا مجددا تلاش نمایید.", 5000);

                                        break;
                                    }
                                }
                            });
                    }
                    else if ($scope.actionType == 1) {
                        $http({
                            method: "PUT",
                            url: constant.API.category.update,
                            data: model
                        })
                            .success(function (data, status, headers, config) {

                                Materialize.toast("دسته بندی با موفقیت به روز رسانی شد.", 5000);

                                $location.path("/dashboard");
                            })
                            .error(function (data, status, headers, config) {
                                Materialize.toast("در بروزرسانی دسته بندی خطایی رخ داده است", 5000);
                            });
                    }
                };
            }
        }]);


    app.controller("categoryDeleteCtrl", ["$location", "$http", "$scope", "$rootScope", "constant", "$cookies", "$stateParams",
        function ($location, $http, $scope, $rootScope, constant, $cookies, $stateParams) {

            if (!$cookies.get("auth")) {

                $location.path("/login");
            }
            else {

                $scope.showDashboard = function () {
                    $location.path('/dashboard');
                };

                $scope.submit = function () {

                    $http({
                        method: "DELETE",
                        url: constant.API.category.delete,
                        params: { id: $stateParams.id }
                    })
                        .success(function (data, status, headers, config) {
                            Materialize.toast("دسته بندی با موفقیت حذف شد", 5000);

                            $location.path("/dashboard");
                        })
                        .error(function (data, status, headers, config) {

                            switch (status) {

                                case 403:
                                {

                                    Materialize.toast("دسترسی امکان پذیر نیست", 5000);

                                    break;
                                }

                                default:
                                {

                                    Materialize.toast("خطا در به روز رسانی دسته بندی، لطفا مجددا تلاش نمایید.", 5000);

                                    break;
                                }
                            }
                        });
                };
            }
        }]);

})(window, window.app);
