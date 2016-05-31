(function (window, app) {

    "use strict";

    app.controller("profileCtrl", ["$location", "$http", "$scope", "$rootScope", "constant",
        function ($location, $http, $scope, $rootScope, constant) {

            $scope.showDashboard = function () {
                $location.path('/dashboard');
            };

            $scope.readImage = function (input) {
                if (input.files && input.files[0]) {
                    var FR = new FileReader();
                    FR.onload = function (e) {
                        $scope.model.image = angular.copy(e.target.result);
                        $scope.$apply(function (scope) {
                        });
                    };
                    FR.readAsDataURL(input.files[0]);
                }
            };

            $scope.update = function (model) {

                if(model.image) {
                    model.hasImage = 1;
                }

                //model.hasImage = (model.image != null) || (model.image != null);
                //
                //model.hasImage = model.hasImage ? 1 : 0;

                $http({
                    method: "PUT",
                    url: constant.API.profile.update,
                    data: model
                })
                    .success(function (data, status, headers, config) {

                        // broadcast profile updated
                        $rootScope.$broadcast('profileUpdated', data);

                        Materialize.toast("اطلاعات پروفایل با موفقیت به روز رسانی شد", 5000);

                        $location.path("/dashboard");
                    })
                    .error(function (data, status, headers, config) {
                        Materialize.toast("خطا در به روز رسانی پروفایل، لطفا مجددا تلاش نمایید", 5000);
                    });
            };

            $http({
                method: "GET",
                url: constant.API.profile.read
            })
                .success(function (data, status, headers, config) {

                    $scope.model = data;
                })
                .error(function (data, status, headers, config) {
                    Materialize.toast("خطا در دریافت اطلاعات پروفایل", 5000);
                });
        }]);

})(window, window.app);
