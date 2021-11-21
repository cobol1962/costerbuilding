loadedPages.returnproduct = {
  initialize: function() {
    $("#btns").hide();

    $.validator.addMethod("selected", function(value, element) {
      var ds = $("#rcategory").select2("data")[0];
      var sid = ds.id;
      return (sid != "-1");
    }, "This field is mandatory");
    api.call("getMainGroups", function(res) {
        $("<option value='upgrade'>Trade in items</option>").appendTo($("#rcategory"));
      $.each(res, function() {

        if (this.MainGroup != "null" && this.MainGroup != null) {
          $("<option value='" + this.MainGroup + "'>" + this.MainGroup + "</option>").appendTo($("#rcategory"));
        }
      });
    }, {},{},{})
    $("#rcategory").select2();
    $( "#rnart" ).validate({
      rules: {
        rcategory: {
          selected: true
        },
        rprice: {
          required: true
        },
        invoiceno: {
          required: true
        },
        rproductname: {
          required: true
        },
        rdescription: {
          required: true
        }
      },
      submitHandler: function(form) {
        var m = 1;
          m = -1;

        var obj = {
            imageURL: "<img style='width:100px;height:auto;' src='https://costercatalog.com/catalog/images/" + "crown.png" + "' />",
            img: "<img style='width:250px;height:auto;' src='https://costercatalog.com/catalog/images/" + "crown.png" + "' />",
            SerialNo: "99990000",
            CompName: "Invoice/date: " + $("#invoiceno").val(),
            productName:  $("#rproductname").val() +"<br>" + $("#rcategory").select2("data")[0].text + "<br>" + $("#rdescription").val().replace(/(?:\r\n|\r|\n)/g, '<br>'),
            SalesPrice: $("#rprice").val() * m,
            realPrice: $("#rprice").val(),
            startRealPrice: $("#rprice").val(),
            Discount: "0%",
            MainGroup: $("#rcategory").select2("data")[0].id,
            info: "",
            invoiceno: $("#invoiceno").val()
        }
        try {

          showModal({
            title: "ADD ITEM " + obj.CompName + " TO BAG?",
            allowBackdrop: false,
            cancelButtonText: "CANCEL",
            confirmButtonText: "CONFIRM",
            confirmCallback: function() {
              $("#addproducticon").hide();
              var dt = $("#rcategory").select2("data");
              var vl = $("#rcategory").val();
              $( "#rnart" )[0].reset();
              $("#rcategory").val(vl).trigger('change');
              window.parent.postMessage("addToInvoice#" + JSON.stringify(obj), "*");

            }
          });

        } catch(err) {
          alert(err);
        }
      }
    });
  }
}
