function executeTestConnection() {
    var data = {
        web_address: $('propoza_general_web_address').getValue(),
        api_key: $('propoza_general_api_key').getValue()
    };
    new Ajax.Request(testConnectionUrl, {
        method: 'post',
        parameters: data,
        onSuccess: function (data) {
            if (data.responseJSON.response == true) {
                alert(successMessage);
            } else {
                alert(failedMessage);
            }
        },
        onFailure: function (data) {
            alert(failedMessage);
        },
        on0: function (data) {
            alert(failedMessage);
        }
    });
}