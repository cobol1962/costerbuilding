loadedPages.homepage = {
  sps: null,
  initialize: function() {
    $("#content").css({
      marginLeft: ($(window).width() - $("#content").width()) / 2,
      paddingTop: 165
    })
    if (localStorage.salesPerson !== undefined) {
      loadPage("mainpage");
      return;
    }

     $(".navbar").hide();
     loadedPages.homepage.adjustScreen();
     $(window).resize(function() {
       loadedPages.homepage.adjustScreen();
     });
     loadedPages.homepage.fillEmployees();
     $("#findSP").bind("keyup", function() {
        loadedPages.homepage.fillEmployees();
     })

     $("#main").css({
       visibility: "visible"
     })
   },
   fillEmployees: function() {
     if (loadedPages.homepage.sps == null) {
         api.call("getSalespersons", function(res) {
           $("#employees").html("");
           var sorted = _.sortBy(res.data, 'Employee');
           loadedPages.homepage.sps = sorted;
           $.each(sorted, function() {
             if (this.status == "2") {
               var dv = $("#master").clone();
               dv.find("span").html(this.Employee);
               dv.find(".salesperson").attr("salesperson", this.Employee);
               dv.find(".salesperson").attr("emplid", this.EmplID);
               dv.find(".salesperson").attr("sp", JSON.stringify(this));
               dv.find(".salesperson").bind("click", function(e) {
                 $(".salesperson").removeClass("active");
                 localStorage.salesPerson = btoa($(this).attr("sp"));
                 $(this).addClass("active");
                 loadPage("login");
               })
               dv.removeClass("master");
               dv.removeAttr("id");
               dv.appendTo($("#employees"));
               dv.show();
             }
           })

         }, {}, {})
       } else {
          $("#employees").html("");
          $.each(loadedPages.homepage.sps, function() {
                var dv = $("#master").clone();
                dv.find("span").html(this.Employee);
                dv.find(".salesperson").attr("salesperson", this.Employee);
                dv.find(".salesperson").bind("click", function(e) {
                  $(".salesperson").removeClass("active");
                  localStorage.salesPerson = btoa($(this).attr("sp"));
                  $(this).addClass("active");
                  loadPage("login");
                })
                dv.removeClass("master");
                dv.removeAttr("id");
                if ($("#findSP").val() == "") {
                  dv.appendTo($("#employees"));
                  dv.show();
                } else {
                  if (this.Employee.toLowerCase().indexOf($("#findSP").val().toLowerCase()) > -1) {
                    dv.find(".salesperson").attr("sp", JSON.stringify(this));
                    dv.appendTo($("#employees"));
                    dv.find(".salesperson").bind("click", function(e) {
                    
                      $(".salesperson").removeClass("active");
                      localStorage.salesPerson = btoa($(this).attr("sp"));
                      $(this).addClass("active");
                      loadPage("login");
                    })
                    dv.show();
                  } else {
                    dv.remove();
                  }
                }

          })
       }
       if (localStorage.salesPerson !== undefined) {
         var sp = $.parseJSON(localStorage.salesPerson);
         $.each($("#employees").find(".salesperson"), function() {
           console.log($(this).attr("emplid") + " == " + sp.EmplID)
           if ($(this).attr("emplid") == sp.EmplID) {
             $("#employees").scrollTo($(this).parent());
             $(this).addClass("active");
           }
         })
       }
       $("#master").hide();
   },
   adjustScreen: function() {
     $("#employees").css({
       overflow: "auto",
       maxHeight: $(window).height() - $(".container-fluid").eq(0).height(),
       height: $(window).height() - $(".container-fluid").eq(0).height()
     })
     if ($(window).width() > 740) {
       $("#content").css({
         paddingTop: 20
       })
       $("#commands").css({
         float: "right",
         width: 353
       })
       $("#sRow").css({
         height: 115
       })
       $(".salesperson").css({
         maxWidth: "unset"
       })
     } else {

       $(".salesperson").css({
         maxWidth: "92%",

       })
       $("#dfs").css({
         maxWidth: "92%",
         width: "92%",
         margin: "0px auto"
       })
       $("#select_acc").css({
         fontSize: 50,
         lineHeight: "55px",
         marginLeft: 10,
         width: "100%",
         textAlign: "center"

       })
       $("#findSP").closest("div").css({
         marginTop: -38,
         marginBottom: 5
       })
        $("#sRow").css({
          paddingRight: 30
        })
       $("#content").css({
         padding:0,
         marginTop: 0
       })
       $("#commands").css({
         float: "left",
         marginTop:5
       })
     }
   }
 }
