(function (window, app) {

    "use strict";

    app.controller("dashboardCtrl", ["$http", "$scope", "$rootScope", "constant", "$location", "$cookies",
        function ($http, $scope, $rootScope, constant, $location, $cookies) {

            if (!$cookies.get("auth")) {

                $location.path("/login");
            }
            else {

                $scope.model = {
                    categories: [],

                    profile: {
                        edit: {}
                    }
                };

                // $filter('filter')(array, {id: 12})[0];

                $scope.category = {

                    list: function (model) {
                        $http({
                            method: "GET",
                            url: constant.API.category.list,
                        })
                            .success(function (data, status, headers, config) {

                                var parents = data.filter(function(obj) {

                                    return !obj.parentId;
                                });

                                for(var i = 0; i < parents.length; i++) {

                                    var children = data.filter(function(obj){
                                        return obj.parentId == parents[i].id;
                                    });

                                    if(children) {
                                        parents[i].subCategories = children;

                                        parents[i].tasks = [];
                                    }
                                    else {

                                        parents[i].subCategories = [];



                                        parents[i].tasks = [];
                                    }
                                }

                                $scope.model.categories = parents;
                            })
                            .error(function (data, status, headers, config) {
                                Materialize.toast("در هنگام بار گذاری صفحه خطایی رخ داده است", 5000);
                            });
                    }};

                $scope.task = {
                    create: function (model) {
                        $http({
                            method: "POST",
                            url: constant.API.task.create,
                            data: model
                        })
                            .success(function (data, status, headers, config) {

                            })
                            .error(function (data, status, headers, config) {
                                Materialize.toast("در ایجاد وظیفه جدید خطایی رخ داده است", 5000);
                            });
                    },
                    update: function (model) {
                        $http({
                            method: "PUT",
                            url: constant.API.task.upddate,
                            data: model
                        })
                            .success(function (data, status, headers, config) {

                            })
                            .error(function (data, status, headers, config) {
                                Materialize.toast("در بروزرسانی وظیفه خطایی رخ داده است", 5000);
                            });
                    },
                    delete: function (id) {
                        $http({
                            method: "DELETE",
                            url: constant.API.task.delete + id
                        })
                            .success(function (data, status, headers, config) {

                            })
                            .error(function (data, status, headers, config) {
                                Materialize.toast("در حذف وظیفه خطایی رخ داده است", 5000);
                            });
                    }
                };

                $scope.category.list();

                //$(document).ready(function () {
                //    // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
                //    $('.modal-trigger').leanModal();
                //
                //    $('.datepicker').pickadate({
                //        selectMonths: true, // Creates a dropdown to control month
                //        selectYears: 15 // Creates a dropdown of 15 years to control year
                //    });
                //
                //});
            }
        }]);

})(window, window.app);
