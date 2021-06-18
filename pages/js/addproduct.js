loadedPages.addproduct = {
  initialize: function() {
    $("#btns").hide();
    $.validator.addMethod("selected", function(value, element) {
      var ds = $("#category").select2("data")[0];
      var sid = ds.id;
      return (sid != "-1");
    }, "This field is mandatory");
    api.call("getMainGroups", function(res) {
        $("<option value='upgrade'>Trade in items</option>").appendTo($("#category"));
      $.each(res, function() {

        if (this.MainGroup != "null" && this.MainGroup != null) {
          $("<option value='" + this.MainGroup + "'>" + this.MainGroup + "</option>").appendTo($("#category"));
        }
      });
    }, {},{},{})
    $("#category").select2();
    $( "#nart" ).validate({
      rules: {
        category: {
          selected: true
        },
        price: {
          required: true
        },
        productname: {
          required: true
        },
        description: {
          required: true
        }
      },
      submitHandler: function(form) {
        var m = 1;
        if ($("#category").select2("data")[0].id.trim() == "upgrade") {
          m = -1;
        }

        var obj = {
            imageURL: "<img style='width:100px;height:auto;' src='https://costercatalog.com/catalog/images/" + "crown.png" + "' />",
            img: "<img style='width:250px;height:auto;' src='https://costercatalog.com/catalog/images/" + "crown.png" + "' />",
            SerialNo: "99990000",
            CompName: $("#description").val().replace(/(?:\r\n|\r|\n)/g, '<br>'),
            productName:  $("#category").select2("data")[0].text + "<br />" + $("#name").val(),
            SalesPrice: $("#price").val() * m,
            realPrice: $("#price").val(),
            startRealPrice: $("#price").val(),
            Discount: "0%",
            MainGroup: $("#category").select2("data")[0].id,
            info: ""
        }
        try {
          console.log(obj);
          showModal({
            title: "ADD ITEM " + obj.CompName + " TO BAG?",
            allowBackdrop: false,
            cancelButtonText: "CANCEL",
            confirmButtonText: "CONFIRM",
            confirmCallback: function() {
              $("#addproducticon").hide();
              var dt = $("#category").select2("data");
              var vl = $("#category").val();
              $( "#nart" )[0].reset();
              $("#category").val(vl).trigger('change');
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
