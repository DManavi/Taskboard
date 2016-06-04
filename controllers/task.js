(function (window, app) {

    "use strict";

    app.controller("taskCtrl", ["$location", "$http", "$scope", "$rootScope", "constant", "$cookies", "$stateParams",
        function ($location, $http, $scope, $rootScope, constant, $cookies, $stateParams) {

            if (!$cookies.get("auth")) {

                $location.path("/login");
            }
            else {

                if($location.path().indexOf('/createTask') == 0) {

                    $scope.actionType = 0;

                    $scope.action = 'ایجاد وظیفه';

                    $scope.model = $scope.model || {};

                    // set category id from URI
                    $scope.model.categoryId = parseInt($stateParams.parentId);
                }
                else if ($location.path().indexOf('/editTask/') == 0) {

                    $scope.action = 'ویرایش وظیفه';

                    $scope.actionType = 1;

                    $http({
                        method: "GET",
                        url: constant.API.task.read,
                        params: { id: $stateParams.id }
                    })
                        .success(function (data, status, headers, config) {

                            // set data
                            $scope.model = data;
                        })
                        .error(function (data, status, headers, config) {
                            Materialize.toast("در هنگام بار گذاری صفحه خطایی رخ داده است", 5000);
                        });
                }

                $scope.showDashboard = function () {
                    $location.path('/dashboard');
                };

                $scope.submit = function (model) {

                    if(!model.assignedTo) { delete model.assignedTo; }

                    if($scope.actionType == 0) {

                        $http({
                            method: "POST",
                            url: constant.API.task.create,
                            data: model
                        })
                            .success(function (data, status, headers, config) {
                                Materialize.toast("وظیفه جدید با موفقیت ایجاد شد", 5000);

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

                                        Materialize.toast("خطا در ایجاد وظیفه، لطفا مجددا تلاش نمایید.", 5000);

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

    app.controller("taskDeleteCtrl", ["$location", "$http", "$scope", "$rootScope", "constant", "$cookies", "$stateParams",
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
                        url: constant.API.task.delete,
                        params: { id: $stateParams.id }
                    })
                        .success(function (data, status, headers, config) {
                            Materialize.toast("وظیفه با موفقیت حذف شد", 5000);

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