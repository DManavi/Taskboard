(function (window, document, undefined) {

    "use strict";

    window.app = angular.module("app", [
        "ngAnimate",
        "ngCookies",
        "ngMessages",
        "ngResource",
        "ngSanitize",
        "ui.router",
        "ui.materialize",
        "ngCookies"
    ])

    .constant("constant", {
        API: {
            base: "http://localhost:8080/taskboard/api",
            account: {
                get login() {
                    return "http://localhost:8080/taskboard/api/account.php?action=login";
                },
                get register() {
                    return "http://localhost:8080/taskboard/api/account.php?action=register";
                },

                get logout() {
                    return "http://localhost:8080/taskboard/api/account.php?action=logout";
                },

                get changePassword() {
                    return "http://localhost:8080/taskboard/api/account.php?action=changePassword";
                }
            },
            dashboard: {
                get list() {
                    return "http://localhost:8080/taskboard/api/dashboard.php?action=read";
                }
            },
            category: {
                get list() {
                    return "http://localhost:8080/taskboard/api/category.php?action=read";
                },
                get create() {
                    return "http://localhost:8080/taskboard/api/category.php?action=create";
                },
                get delete () {
                    return "http://localhost:8080/taskboard/api/category.php?action=delete&id=";
                },
                get update() {
                    return "http://localhost:8080/taskboard/api/category.php?action=update";
                }
            },
            task: {
                get list() {
                    return "http://localhost:8080/taskboard/api/task.php?action=read";
                },
                get create() {
                    return "http://localhost:8080/taskboard/api/task.php?action=create";
                },
                get delete () {
                    return "http://localhost:8080/taskboard/api/task.php?action=delete&id=";
                },
                get update() {
                    return "http://localhost:8080/taskboard/api/task.php?action=update";
                }
            },
            profile: {
                get read() {
                    return "http://localhost:8080/taskboard/api/profile.php?action=read";
                },
                get update() {
                    return "http://localhost:8080/taskboard/api/profile.php?action=update";
                }
            }
        }
    })

    .config(["$locationProvider", "$urlRouterProvider", "$stateProvider","$httpProvider",
        function ($locationProvider, $urlRouterProvider, $stateProvider, $httpProvider) {

            //$httpProvider.defaults.withCredentials = true;

            // set default url
            $urlRouterProvider.otherwise("/dashboard");

            // route config
            $stateProvider

                // register
                .state('register', {
                    key: "register",
                    url: '/register',
                    title: "ثبت نام",
                    templateUrl: "./templates/register.html",
                    controller: 'registerCtrl'
                })

                // login
                .state('login', {
                    key: "login",
                    url: '/login',
                    title: "ورود",
                    templateUrl: "./templates/login.html",
                    controller: 'loginCtrl'
                })

                // inherit layout routing
                .state("root", {
                    abstract: true,
                    url: "",
                    templateUrl: "./templates/layout.html",
                    controller: 'layoutCtrl'
                })

                // dashboard
                .state('root.dashboard', {
                    key: "index",
                    url: '/dashboard',
                    title: "Dashboard",
                    templateUrl: "./templates/dashboard.html",
                    controller: 'dashboardCtrl'
                })

                .state('root.logout', {
                    key: "logout",
                    url: '/logout',
                    title: "Logout",
                    templateUrl: "./templates/logout.html",
                    controller: 'logoutCtrl'
                })

                .state('root.changePassword', {
                    key: "changePassword",
                    url: '/changePassword',
                    title: "Change password",
                    templateUrl: "./templates/changePassword.html",
                    controller: 'changePasswordCtrl'
                })

                .state('root.profile', {
                    key: "profile",
                    url: '/profile',
                    title: "Profile",
                    templateUrl: "./templates/profile.html",
                    controller: 'profileCtrl'
                })

                .state('root.createSubCategory', {
                    key: "category",
                    url: '/createSubCategory/:parentId',
                    title: "Create Sub-Category",
                    templateUrl: "./templates/category.html",
                    controller: 'categoryCtrl'
                })

                .state('root.createCategory', {
                    key: "category",
                    url: '/createCategory',
                    title: "Create Category",
                    templateUrl: "./templates/category.html",
                    controller: 'categoryCtrl'
                })

                .state('root.editCategory', {
                    key: "category",
                    url: '/editCategory/:id',
                    title: "Edit Category",
                    templateUrl: "./templates/category.html",
                    controller: 'categoryCtrl'
                })

                .state('root.deleteCategory', {
                    key: "category",
                    url: '/deleteCategory/:id',
                    title: "Delete Category",
                    templateUrl: "./templates/category_delete.html",
                    controller: 'categoryDeleteCtrl'
                })

                .state('root.createTask', {
                    key: "task",
                    url: '/createTask/:parentId',
                    title: "Create task",
                    templateUrl: "./templates/task.html",
                    controller: 'taskCtrl'
                })

                .state('root.editTask', {
                    key: "task",
                    url: '/editTask/:parentId/:id',
                    title: "Edit task",
                    templateUrl: "./templates/task.html",
                    controller: 'editCtrl'
                })

                .state('root.deleteTask', {
                    key: "task",
                    url: '/deleteTask/:id',
                    title: "Delete Task",
                    templateUrl: "./templates/task_delete.html",
                    controller: 'taskDeleteCtrl'
                })

            // $locationProvider.html5Mode(true);
        }
    ])

    .run(function ($rootScope) {
        $rootScope.$on("$stateChangeStart", function (event, toState, toParams, fromState, fromParams) {
            $rootScope.key = toState.key;
        })
    })

})(window, document);
