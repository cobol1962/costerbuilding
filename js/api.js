api = {
        neterror: false,
        call: function(endpoint, cb, params, ajaxExtend, type = "POST") {
      //      $.LoadingOverlay("show");
            cb = cb || function(res) {};
            params = params || {};
            ajaxExtend = ajaxExtend || {};

            // Extend the data object load sent with API Ajax request.
            var data = {};
      //      data.access_token = (localStorage.access_token !== undefined) ? localStorage.access_token : "";
             for (var prop in params) {
                if (params.hasOwnProperty(prop)) {
                 data[prop] = params[prop];
                }
              }
              var dataEncrypted = {};

              dataEncrypted["d"] = getCrypto(JSON.stringify(data));
                dataEncrypted["encrypted"] = "1";

           var apiAjax = {
             type: "POST",
             dataType: "json",
             async: false,
             data: data,
            success: function(r) {
              if (r != null) {
                if (r.d !== undefined) {

                  var res = $.parseJSON(getEcrypted(r.d));
                } else {
                    var res = r;
                }
              } else {
                var res = r;
              }
              if (res.status == "fail") {
                var sp = $.parseJSON(localStorage.sp);
                res.name = sp.Employee;
                res.endpoint = endpoint;
                res.time = moment(new Date()).format("DD/MM/YYYY HH:mm");
                showModal({
                  type: "error",
                  title: "The connection to our stock database is broken. Please close and reopen the sales app. The data you entered is not lost. An admin will be contacted with details of this error immediately."

                })
                ws.send(JSON.stringify({
                    action: "apierror",
                    text: JSON.stringify(res)

                }))
              }

              if (api.neterror) {
                api.neterror = false;
                showModal({
                  type: "ok",
                  showCancelButton: false,
                  showConfirmButton: false,
                  confirmButtonText: "OK",
                  title: "Connection established.",

                })
                setTimeout(function() {
                  //  $("body").LoadingOverlay("show", optionsLoader);
                    cb(res);
                    appError = false;
                    $("#mainModal").modal("hide");
                }, 2000)
              } else {
                appError = false;
                api.neterror = false;
                cb(res);
              }
             }, 
            error: function(e) {

            }
          };
            //  apiAjax.url = "https://costercatalog.comapi/index.php?request=" + endpoint;
            apiAjax.url = "https://costercatalog.com/api/index.php?request=" + endpoint;

              for (var prop in ajaxExtend) {
                if (ajaxExtend.hasOwnProperty(prop)) {
                  apiAjax[prop] = ajaxExtend[prop];
                }
              }
              api.callAjax(apiAjax, endpoint, 0);
        },
        callAjax: function(apiAjax, endpoint, attempt) {
      //    api.neterror = true;
          attempt += 1;
          $.ajax(apiAjax).fail(function(jqXHR, textStatus, errorThrown) {
            api.neterror = true;
            appError = true;
            $("body").LoadingOverlay("hide");
            var sp = $.parseJSON(localStorage.sp);
        //    alert("fail " + alert(apiAjax.url));
            var res = {

              name: sp.Employee,
              time: moment(new Date()).format("DD/MM/YYYY HH:mm"),
              status: "fail",
              type: "Connection error",
              endpoint: endpoint,
              xr: JSON.stringify(jqXHR),
              td: JSON.stringify(textStatus),
              err: JSON.stringify(errorThrown)
            }
            showModal({
              type: "sad",
              showCancelButton: false,
              showConfirmButton: false,
              allowBackdrop: false,
              title: "The connection is unstable. We are trying to reconnect. Please wait..... Attempting every  5 sec"
            })
            setTimeout(function() {
              if (attempt < 5) {
                api.callAjax(apiAjax, endpoint, attempt);
              } else {
                showModal({
                  type: "error",
                  title: "Connection can not be established. Please contact administrator. Application will close now. Try later.",
                  showCancelButton: false,
                  confirmButtonText: "OK",
                  confirmCallback: function() {
                    localStorage.error = JSON.stringify(res);
                    $("#mainModal").modal("hide");

                    try {
                        navigator.app.exitApp();
                    } catch(err) {

                    }
                  }
                })
              }
            }, 5000)
          //  localStorage.error = JSON.stringify(res);
          //  cb(res);
          });
        },
        isEmpty: function(obj) {
          for(var key in obj) {
            if(obj.hasOwnProperty(key))
              return false;
          }
          return true;
    }
}
