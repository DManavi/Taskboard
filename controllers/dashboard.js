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

                            if(children.length > 0) {

                                parents[i].subCategories = parents[i].subCategories || [];

                                for(var j = 0; j < children.length; j++) {

                                    children[j].tasks = [];

                                    parents[i].subCategories.push(children[j]);
                                }

                                parents[i].tasks = [];
                            }
                            else {
                                parents[i].subCategories = [];

                                parents[i].tasks = [];
                            }
                        }

                        $scope.model.categories = parents;

                        for(var i = 0; i < $scope.model.categories.length; i++) {

                            // assign short name
                            var parentCategory = $scope.model.categories[i];

                            // if category has sub category
                            if(parentCategory.subCategories.length > 0) {

                                // iterate all sub-categories
                                for (var j = 0; j < parentCategory.subCategories.length; j++) {

                                    // load sub-category tasks
                                    $scope.loadTasks(parentCategory.subCategories[j]);
                                }
                            }
                            else {

                                // load parent category tasks
                                $scope.loadTasks(parentCategory);
                            }
                        }
                    })
                    .error(function (data, status, headers, config) {
                        Materialize.toast("در هنگام بار گذاری صفحه خطایی رخ داده است", 5000);
                    });

                $scope.changeState = function(task) {

                    alert(task.title);
                };

                $scope.loadTasks = function(category, pageIndex, pageSize) {

                    if(!pageSize) { pageSize = 5; }

                    if(!pageIndex) { pageIndex = 0; }

                    console.log(category);

                    if(category.tasks.currentPage && (category.tasks.currentPage == pageIndex)) { return; }

                    $http({
                        method: "GET",
                        url: constant.API.task.list,
                        params: {
                            id: category.id,
                            pageSize: pageSize,
                            pageIndex: pageIndex
                        }
                    })
                        .success(function (data, status, headers, config) {

                            for(var i = 0; i < data.items.length; i++) {

                                data.items[i].dueDate = new Date(data.items[i].dueDate);
                            }

                            category.tasks = data.items;

                            category.tasks.total = data.total;

                            category.tasks.maxPage = parseInt(data.total / pageSize);

                            if((data.total / pageSize) > category.tasks.maxPage) {

                                category.tasks.maxPage++;
                            }

                            category.tasks.pages = [];

                            for(var i = 0; i < category.tasks.maxPage; i++) {
                                category.tasks.pages.push(i);
                            }

                            category.tasks.pages.reverse();

                            console.log(category.tasks.pages);

                            category.tasks.currentPage = pageIndex;
                        })
                        .error(function (data, status, headers, config) {
                            Materialize.toast("در هنگام بار گذاری صفحه خطایی رخ داده است", 5000);
                        });
                };
            }
        }]);

})(window, window.app);
