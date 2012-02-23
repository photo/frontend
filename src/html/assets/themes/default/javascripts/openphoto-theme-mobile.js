var opTheme = (function() {
  var log = function(msg) {
    if(console !== undefined && console.log !== undefined)
      console.log(msg);
  };
  return{
    callback: {
      login: function(el) {
        navigator.id.getVerifiedEmail(function(assertion) {
            if (assertion) {
              opTheme.user.loginSuccess(assertion);
            } else {
              opTheme.user.loginFailure(assertion);
            }
        });
      }
    },
    init: {
      attach: function() {
        OP.Util.on('click:login', opTheme.callback.login);
      }
    },
    user: {
      loginFailure: function(assertion) {
        log('login failed');
        // TODO something here to handle failed login
      },
      loginProcessed: function(response) {
        if(response.code != 200) {
          log('processing of login failed');
          // TODO do something here to handle failed login
          return;
        }

        log('login processing succeeded');
        window.location.reload();
      },
      loginSuccess: function(assertion) {
        var params = {assertion: assertion};
        OP.Util.makeRequest('/user/browserid/login.json', params, opTheme.user.loginProcessed, 'json');
      }
    }
  };
}());
