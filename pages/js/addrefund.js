loadedPages.addrefund = {
  initialize: function() {
    $.validator.addMethod("selected", function(value, element) {
      var ds = $("#category").select2("data")[0];
      var sid = ds.id;
      return (sid != "-1");
    }, "This field is mandatory");
    api.call("getMainGroups", function(res) {
      $.each(res, function() {
        $("<option value='" + this.MainGroup + "'>" + this.MainGroup + "</option>").appendTo($("#category"));
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
        name: {
          required: true
        },
        description: {
          required: true
        }
      },
      submitHandler: function(form) {

        var obj = {
            imageURL: "<img style='width:100px;height:auto;' src='/images/" + "crown.png" + "' />",
            img: "<img style='width:250px;height:auto;' src='/images/" + "crown.png" + "' />",
            SerialNo: "99990000",
            CompName: $("#description").val().replace(/(?:\r\n|\r|\n)/g, '<br>'),
            productName:  $("#name").val(),
            SalesPrice: parseFloat($("#price").val()) * -1,
            realPrice: parseFloat($("#price").val()) * -1,
            startRealPrice: parseFloat($("#price").val()) * -1,
            Discount: "0%",
            MainGroup: $("#category").select2("data")[0].MainGroup,
            info: ""
        }
        try {
          $("#addproducticon").hide();
          $( "#nart" )[0].reset();
          window.parent.postMessage("addToInvoice#" + JSON.stringify(obj), "*");
        } catch(err) {
          alert(err);
        }
      }
    });
  }
}
