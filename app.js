(function (window, document, undefined) {

    "use strict";

    window.app = angular.module("app", [
        "ngAnimate",
        "ngCookies",
        "ngMessages",
        "ngResource",
        "ngSanitize",
        "ui.router",
        "ui.materialize"
    ])

    .config(["$locationProvider", "$urlRouterProvider", "$stateProvider",
        function ($locationProvider, $urlRouterProvider, $stateProvider) {

            // set default url
            $urlRouterProvider.otherwise("/dashboard");

            // route config
            $stateProvider

                // splash
                .state('splash', {
                    url: '/splash',
                    title: "Splash",
                    templateUrl: "/templates/splash.html",
                    controller: 'splashCtrl'
                })

                // inherit layout routing
                .state("root", {
                    abstract: true,
                    url: "",
                    templateUrl: "/templates/layout.html"
                })

                // dashboard
                .state('root.dashboard', {
                    url: '/dashboard',
                    title: "Dashboard",
                    templateUrl: "/templates/dashboard.html",
                    controller: 'dashboardCtrl'
                })


            // $locationProvider.html5Mode(true);
        }
    ]);

})(window, document);
