(function (window, app, undefined) {

    "use strict";

    app.directive('validSubmit', ["$parse",
      function ($parse) {
          return {

              // we need a form controller to be on the same element as this directive
              // in other words: this directive can only be used on a FORM TAG;
              require: 'form',

              // one time action per form
              link: function (scope, element, iAttrs, form) {

                  form.$submitted = false;

                  // get a hold of the function that handles submission when form is valid
                  var fn = $parse(iAttrs.validSubmit);

                  // register DOM event handler and wire into Angular's lifecycle with scope.$apply
                  element.on('submit', function (event) {

                      event.preventDefault();

                      scope.$apply(function () {

                          // on submit event, set submitted to true (like the previous trick)
                          form.$submitted = true;

                          // if form is valid, execute the submission handler function and reset form submission state
                          if (form.$valid) {
                              fn(scope, { $event: event });
                              form.$submitted = false;
                          }
                          else {
                              Object.keys(form.$error).forEach(function (key) {
                                  form.$error[key].forEach(function (control) {

                                      control.$setDirty(); // no $setDirty function
                                  });
                              });
                          }
                      });
                  });
              }
          }
      }
    ]);

    // validation element
    app.directive("validation", ["$compile", 
        function ($compile) {
            return {
                restrict: 'E',
                link: function (scope, ele, attrs) {

                    // find parent form
                    var form = ele.parents("form")[0];

                    // main element
                    var element = angular.element(form[attrs.name]);

                    // property string name
                    var property = form.name + "." + attrs.name;

                    // result 
                    var messages = {
                        'pattern': 'orginal pattern',
                        'required': 'مقدار دهی به این فیلد اجباری است',
                        'number': 'orginal number',
                        'email': 'ایمیل وارد شده معتبر نیست',
                        'url': 'آدرس وارد شده معتبر نیست',
                        'date': 'orginal date',
                        'time': 'orginal time',
                        'datetimelocal': 'orginal datetimelocal',
                        'week': 'orginal week',
                        'month': 'orginal month',
                        'match': 'original match'
                    };

                    for (var i in attrs) {
                        for (var j in messages) {
                            if (i == j) {
                                messages[j] = attrs[i];
                            }
                        }
                    }

                    var template = '<div ng-messages="' + property + '.$error" ng-show="' + property + '.$dirty">';

                    for (var i in messages) {
                        template += '<div ng-message="' + i + '">' + messages[i] + '</div>';
                    }

                    template += '</div>';

                    ele.html(template);

                    $compile(ele.contents())(scope);
                }
            }
        }
    ]);

    // match validation
    app.directive("ngMatch", ["$parse",
        function ($parse) {
            return {
                restrict: 'A',
                require: '?ngModel',
                link: function (scope, elem, attrs, ctrl) {
                    // if ngModel is not defined, we don't need to do anything
                    if (!ctrl) return;
                    if (!attrs["ngMatch"]) return;

                    var firstPassword = $parse(attrs["ngMatch"]);

                    var validator = function (value) {
                        var temp = firstPassword(scope),
                        v = value === temp;
                        ctrl.$setValidity('match', v);
                        return value;
                    }

                    ctrl.$parsers.unshift(validator);
                    ctrl.$formatters.push(validator);
                    attrs.$observe("ngMatch", function () {
                        validator(ctrl.$viewValue);
                    });

                }
            }
        }
    ]);

})(window, window.app);