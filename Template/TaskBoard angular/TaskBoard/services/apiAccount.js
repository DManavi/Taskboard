(function (window, app, undefined) {

    "use strict";

    app.factory("apiAccount", ["$http", "$q",
        function ($http, $q) {
            return {
                login: function (url, username, password, rememberMe) {

                    var deferred = $q.defer();

                    $http({
                        method:'POST',
                        url: url,
                        data: {
                            username: username,
                            password: password,
                            rememberMe: rememberMe,
                            grant_type: "password"
                        },
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        transformRequest: function (obj) {

                            var str = [];

                            for (var p in obj) {
                                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                            }

                            return str.join("&");
                        }
                    })
                    .success(function (data, status, headers, config) {
                        deferred.resolve({
                            data: data,
                            status: status,
                            headers: headers(),
                            config: config
                        });
                    })
                    .error(function (data, status, headers, config) {
                        deferred.resolve({
                            data: data,
                            status: status,
                            headers: headers(),
                            config: config
                        });
                    });

                    return deferred.promise;
                }
            }
        }
    ])

})(window, window.app);

