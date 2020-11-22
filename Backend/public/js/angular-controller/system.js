var system = angular.module('Distributed', ['ngSanitize', 'ngFileUpload']);

system.factory('httpRequestInterceptor', function () {
    return {
        request: function (config) {
            config.headers['api-token'] = api_token;
            return config;
        }
    };
});

system.config(function ($httpProvider) {
    $httpProvider.interceptors.push('httpRequestInterceptor');
});