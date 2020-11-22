const STATUS_SUCCESSFUL = 'successful';
const STATUS_FAIL = 'fail';

function BaseController($scope, $http, $rootScope, $sce) {

    $scope.appUrl = app_url;

    $scope.isJsonString = function (str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    $scope.htmlDecodeEntities = function (input){
        var e = document.createElement('div');
        e.innerHTML = input;
        return e.childNodes[0].nodeValue;
    }

    $scope.isValidLink = function (link) {
        var regex = /(^|\s)((https?:\/\/)[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/gi;
        return regex.test(link);
    }

    $scope.getByCode = function (list, code) {
        var retVal = null;
        list.forEach(function (item) {
            if (item.code == code) {
                retVal = item;
            }
        });
        return retVal;
    };

    $scope.getByField = function (list, fieldName, value) {
        var retVal = null;
        list.forEach(function (item) {
            if (item[fieldName] == value) {
                retVal = item;
            }
        });
        return retVal;
    };

    $scope.summarizeDateTime = function (dateTime, withYear) {
        if (dateTime != null) {
            var outputFormat = "$3/$2";
            if (withYear) {
                outputFormat += "/$1";
            }
            outputFormat += " $4:$5";
            return dateTime.replace(/(\d{4})-(\d{2})-(\d{2})\s+(\d{1,2}):(\d{1,2}):.*/, outputFormat);
        }
    };

    $scope.summarizeDate = function (dateTime, withYear) {
        if (dateTime != null) {
            var outputFormat = "$3-$2";
            if (withYear) {
                outputFormat += "-$1";
            }
            return dateTime.replace(/(\d{4})-(\d{2})-(\d{2})\s+(\d{1,2}):(\d{1,2}):.*/, outputFormat);
        }
    };

    $scope.vietnameseTimeToSQLTime = function (dateString) {
        var retVal = null;
        return dateString.replace(/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{1,2}):(\d{1,2}):(\d{1,2}).*/, "$3-$2-$1 $4:$5:$6");
    };

    $scope.isValidEmail = function (email) {
        var regex = /^[\w\.-]+@[\w\.-]+\.\w{2,5}$/;
        var retVal = email != null && email.match(regex) != null;
        return retVal;
    };

    $scope.standardizePhone = function (phone) {
        if (phone == null) {
            return phone;
        }
        if (!isNaN(phone)) {
            phone = phone.toString();
        }
        //ELSE:
        return phone.replace(/[^0-9]/g, "");
    };

    $scope.formatPhone = function (phone) {
        if (phone == null) {
            return phone;
        }
        var stdPhone = $scope.standardizePhone(phone);
        return stdPhone.replace(/^(\d+)(\d{3})(\d{3})$/, "$1-$2-$3");
    };

    $scope.isValidPhone = function (phone) {
        if (phone == null) {
            return false;
        }
        //ELSE:
        var stdPhone = $scope.standardizePhone(phone);
        var regex = /^0(9\d{8}|1\d{9}|[2345678]\d{7,14})$/;
        return stdPhone.match(regex) != null;
    };

    $scope.randomString = function (length) {
        var retval = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        if (length == null) {
            length = 6;
        }
        for (var i = 0; i < length; i++) {
            retval += possible.charAt(Math.floor(Math.random() * possible.length));
        }
        return retval;
    };

    $scope.trustAsHtml = function(string) {
        return $sce.trustAsHtml(string);
    };

    $scope.getUserByProjectIds = function(ids){
        let retVal = [];
        if (ids) {
            $scope.users.forEach(function(user){
                if (ids.some(projectId => user.projectIds.includes(parseInt(projectId)))) {
                    retVal.push(user);
                }
            });
        }
        return retVal;
    }

    $scope.dateDiffMysqlTime = function(dateTime, compareTime = null) {
        let t = dateTime.split(/[- :]/);
        let jsDate = new Date(t[2], t[1]-1, t[0]);
        let jsComparetime = new Date();
        
        if(compareTime) {
            t = compareTime.split(/[- :]/);
            jsComparetime = new Date(t[2], t[1]-1, t[0]);
        }

        return Math.ceil((jsDate - jsComparetime) / 1000);
    }

    $scope.getFirstLetter = function(str, limit = 2) {
        str = str.toUpperCase();
        let matches = str.split(' ').map(function(item){return item[0]});
        return matches.join('').slice(0, limit);
    }

    $scope.showMessage = (title, text, type, icon) => {
        var notice = new PNotify({
            title: title,
            text: text,
            type: type,
            icon: 'glyphicon ' + icon,
            addclass: 'snotify',
            closer: true,
            delay: 2000
        });
    }

    $scope.trustSrc = function(src) {
        return $sce.trustAsResourceUrl(src);
    }

    $scope.functionExists = (functionName) => {
        var retval = false;
        try {
            if (typeof eval(functionName) === 'function') {
                retval = true;
            }
        } catch (err) {
            retval = false;
        }
        return retval;
    }
}