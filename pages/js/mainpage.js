loadedPages.mainpage = {
  spp: null,
  initialize: function() {
    if (localStorage.salesPerson === undefined) {
      loadPage("homepage");
      return;
    } else {
      localStorage.sp = localStorage.salesPerson;
    }
    try {
    loadedPages.mainpage.spp = $.parseJSON(localStorage.salesPerson);
  } catch(err) {
    delete localStorage.salesPerson;
    loadPage("homepage");
  }
  try {
   if (loadedPages.mainpage.spp.logged === undefined) {
      $(".navbar").hide();
      delete localStorage.salesPerson;
      loadPage("homepage");
      return;
    }
} catch(err) {
    $(".navbar").hide();
}

    setTimeout(function() {
      $("#content").css({
        marginLeft: ($(window).width() - $("#content").width()) / 2,
        paddingTop: 165,
        overflowY: "auto",
        overflowX: "hidden"
      })
         $(".navbar").show();
         $(".navbar").css({
           padding: 20
         });
         $(".navbar").css({
           marginLeft: ($(window).width() - $("#content").width()) / 2,
         })
         $("#content").css({
           padding: 40,
           paddingTop:80,
           background: "transparent",
              overflowY: "auto",
              overflowX: "hidden"
           });
         $.each($(".catalog"), function() {
           $(this).css({
             minHeight: 1.02 * $(this).width(),
             height: 1.02 * $(this).eq(0).width(),
           })
         })
         $(".leftcol").css({
           minHeight: 0.64 * $(".leftcol").width(),
           height: 0.64 * $(".leftcol").width(),
           paddingTop: 0.20 * $(".leftcol").height(),
           paddingLeft: 0.15 * $(".leftcol").width()
         })
         $(".midcol").css({
           minHeight: 0.64 * $(".leftcol").width(),
           height: 0.64 * $(".leftcol").width(),
           paddingTop: 0.20 * $(".leftcol").height(),
           paddingLeft: 0.15 * $(".leftcol").width()
         })
         $(window).resize(function() {
           loadedPages.mainpage.adjustScreen();
         });
         loadedPages.mainpage.adjustScreen();
         $("#greeting").html(loadedPages.mainpage.getHello());
          $("#spname").html(loadedPages.mainpage.spp.Employee);
       }, 1500);
    /*   api.call("myinvoicesnumber", function(res) {
         $("#mynmb").html(res.n);
         $("#mydue").html(res.d);
       }, {emplid: loadedPages.mainpage.spp.EmplID })*/
  },
  adjustScreen: function() {
    $("#employees").css({
      overflow: "auto",
      maxHeight: $(window).height() - $(".container-fluid").eq(0).height(),
      height: $(window).height() - $(".container-fluid").eq(0).height()
    })
    if ($(window).width() > 992) {
      $(".btn-transparent").css({
        top:"70%",
      })
      $("#content").css({
        padding: 40,
        overflowY: "auto",
        overflowX: "hidden"
      })
      $(".leftcol_1").css({
        fontSize: 48,
      })
      $(".leftcol_2").css({
        fontSize: 20,
      })
    } else {
      $("#content").css({
        padding: 15,
        overflowY: "auto",
        overflowX: "hidden"
      })
      $(".leftcol_1").css({
        fontSize: 24,

      })
      $(".leftcol_2").css({
        fontSize: 12,
      })
      $("#arght").css({
        marginTop: -12,
      })
      $(".btn-transparent").css({
        top:"70%",
        height: 35,
        width: 185
      })
      $(".btn-transparent").find("span").css({
        lineHeight: "25px",
        font: 13
      })
      $(".cat").css({
        fontSize: 15,
        marginLedft: 10
      })
    }
    setTimeout(function() {
      $("#main").css({
        visibility: "visible"
      })
      $("#mainNavigation").css({
        visibility: "visible"
      })
    }, 500)
  },
  getHello: function() {
    var today = new Date()
    var curHr = today.getHours()

    if (curHr < 12) {
      return('Good Morning')
    } else if (curHr < 18) {
      return('Good Afternoon')
    } else {
      return('Good Evening')
    }
  }
}
