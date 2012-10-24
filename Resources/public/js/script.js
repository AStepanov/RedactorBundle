$(document).ready(function() {

    var getFunctionByName = function(functionName, context /*, args */) {
        var namespaces = functionName.split(".");
        var func = namespaces.pop();
        for(var i = 0; i < namespaces.length; i++) {
            context = context[namespaces[i]];
        }
        return context[func];
    };

    var prepareConfig = function(config)
    {
        var callbackList = ['fileUploadErrorCallback', 'imageUploadErrorCallback', 'fileUploadCallback', 'imageUploadCallback'];
        for (var i = 0; i < callbackList.length; i++) {
            var callbackName = callbackList[i];
            if (config[callbackName]) {
                config[callbackName] = getFunctionByName(config[callbackName], window);
            }
        }
    };

    for (var redactorId in configRedactor) {
        var config = configRedactor[redactorId];
        prepareConfig(config);
        $("#" + redactorId).redactor(config);
    }

});

window.redactorErrorUploadFile = function(obj, json)
{
    alert(json.message);
};

window.redactorUploadFile = function(obj, json)
{

};