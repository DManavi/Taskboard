(function (window, app) {

    "use strict";

    app.controller("layoutCtrl", ["$http", "$scope", "$rootScope", "constant", "$location", "$cookies",
        function ($http, $scope, $rootScope, constant, $location, $cookies) {

            $http({
                method: "GET",
                url: constant.API.profile.read
            })
                .success(function (data, status, headers, config) {

                    $scope.profile = {
                        firstName: data.firstName,
                        lastName: data.lastName,
                        image: data.hasImage == 1 ? "./content/img/" + data.id + ".jpg" : './content/img/no-avatar.png'
                    };
                })
                .error(function (data, status, headers, config) {
                    Materialize.toast("خطا در دریافت اطلاعات پروفایل", 5000);
                });

            $rootScope.$on('profileUpdated', function(event, data) {

                console.log(data);

                $scope.profile = {
                    firstName: data.firstName,
                    lastName: data.lastName,
                    image: data.hasImage == 1 ? "./content/img/" + data.id + ".jpg" : './content/img/no-avatar.png'
                };
            });

            $(document).ready(function () {
                // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
                $('.modal-trigger').leanModal();

                $('.datepicker').pickadate({
                    selectMonths: true, // Creates a dropdown to control month
                    selectYears: 15 // Creates a dropdown of 15 years to control year
                });
            });
        }]);

})(window, window.app);
