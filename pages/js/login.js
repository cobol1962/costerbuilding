loadedPages.login = {
  salesPerson: null,
  initialize: function() {
     $("#content").css({
       marginTop: 0
     })
     if (localStorage.salesPerson === undefined) {
          $(".navbar").hide();
       loadPage("homepage");
       return;
     }
    loadedPages.login.salesPerson = $.parseJSON(atob(localStorage.salesPerson));
    $("#spersons").val(loadedPages.login.salesPerson.Email);
    $("#spn").html(loadedPages.login.salesPerson.Employee);
    $("#password").attr("type") == "password";
    $("#password").hide();
    $(".toggle-password").click(function() {
      $(this).toggleClass("fa-eye fa-eye-slash");
      var input = $($(this).attr("toggle"));
      if (input.attr("type") == "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });
    $(window).resize(function() {
      loadedPages.login.adjustScreen();
    });
    loadedPages.login.adjustScreen();
  },
  adjustScreen: function() {
    $("#employees").css({
      overflow: "auto",
      maxHeight: $(window).height() - $(".container-fluid").eq(0).height(),
      height: $(window).height() - $(".container-fluid").eq(0).height()
    })
    if ($(window).width() > 740) {
      $(".row").css({
        marginLeft: 0,
        marginRight: 0
      })
      $("#password1").css({
        width: 450
      })

      $("#password1").closest("div").css({
        width: "unset"
      })
      $("#spn").closest("td").css({
        paddingTop: 20
      })
      $("#ttt").css({
        paddingTop: 70
      })
      $("#www").css({
        paddingTop: 70
      })
      $(".btn-grey").css({
        marginTop: 0,
        width: 250
      })
      $("#ddd").css({
        paddingRight: 200,
        bottom: 20
      })
      $(".btn-blue").css({
        marginTop: 50,

      })
    } else {
      $(".btn-grey").css({
        marginTop: 10,
        width:"100%",
      })
      $(".btn-blue").css({
        marginTop: 10,

      })

      $("#ddd").css({
        paddingRight: 0,
        bottom: 2
      })
      $("#www").css({
        paddingTop: 10
      })
      $("#ttt").css({
        paddingTop: 10
      })
      $("#spn").closest("td").css({
        paddingTop: 10
      })
      $(".row").css({
        marginLeft: 15,
        marginRight: 15
      })
      $("#password1").css({
        width: "90%"
      })
      $("#password1").closest("div").css({
        width: "100%"
      })
    }
    setTimeout(function() {
      $(".container").css({
        visibility: "visible"
      }, 500)
    })
  },
  login: function() {
    var obj = {
        username: loadedPages.login.salesPerson.Email,
        password: $("#password1").val()
      }
      api.call("loginSalesApp", function(res) {
        if (res.status == "error") {

          showModal({
              type: "error",
              title: res.error,
              showCancelButton: false,
              showClose: false,
              allowBackdrop: false,
              confirmButtonText: "TRY AGAIN",
              confirmCallback: function() {

              }
          })
          return;
       } else {
         loadedPages.login.salesPerson.logged = true;
          localStorage.salesPerson = JSON.stringify(loadedPages.login.salesPerson);
         loadPage("mainpage");
       }
    }, obj, {}, {});
  },
  rPass: function() {
    api.call("resetUserPassword", function(res) {
    if (res.status == "ok") {
      showModal({
          type: "ok",
          title: res.message,
          allowBackdrop: false,
          showCancelButton: false,
          showClose: false,
          confirmCallback: function() {

          },
          confirmButtonText: "CONTINUE"
      })

    } else {
      showModal({
          type: "error",
          title: res.message,
          allowBackdrop: false,
          showCancelButton: false,
          showClose: false,
          confirmButtonText: "TRY AGAIN"
      })
    }
    }, { email: $("#spersons").val() }, {}, {});
  }
}
