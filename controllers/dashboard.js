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

                $http({
                    method: "GET",
                    url: constant.API.category.list,
                })
                    .success(function (data, status, headers, config) {

                        for(var i = 0; i < data.length; i++) {

                            data[i].shared = data[i].shared == "1" ? true : false;
                        }

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

                    // if task is a shared task
                    if(task.isShared) {

                        // go to task details
                        $location.path("/readTask/" + task.id);

                        return;
                    }

                    $http({
                        method: "GET",
                        url: constant.API.task.toggleStatus,
                        params: {
                            id: task.id,
                        }
                    })
                        .success(function (data, status, headers, config) {

                            if(data.doneDate) {

                                task.doneDate = new Date(data.doneDate);
                            }
                            else {

                                task.doneDate = null;
                            }
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
                };

                $scope.loadTasks = function(category, pageIndex, pageSize) {

                    if(!pageSize) { pageSize = 5; }

                    if(!pageIndex) { pageIndex = 0; }

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

                            category.tasks.currentPage = pageIndex;
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

                                    Materialize.toast("خطا در بارگذاری اطلاعات از سرور.", 5000);

                                    break;
                                }
                            }
                        });
                };
            }
        }]);

})(window, window.app);
