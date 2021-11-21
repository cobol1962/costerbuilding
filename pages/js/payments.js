loadedPages.payments =  {
  items: [],
  invoice: null,
  invoiceid: "",
  customerid: "",
  initialize: function() {
    $("[lock]").hide();
    loadedPages.payments.invoiceid = localStorage.invoiceToOpen;;
    var obj = {
      invoiceid: localStorage.invoiceToOpen
    }

    api.call("oneinvoice", function(res) {
      loadedPages.payments.invoice = res;
      localStorage.payments = JSON.stringify(res);
      var v = "";
      if (loadedPages.payments.invoice.version != null) {
        v = loadedPages.payments.invoice.version;
      }
      loadedPages.payments.customerid = loadedPages.payments.invoice.customerid;
      var html = "INVOICE No. 9" + localStorage.invoiceToOpen.toString().padStart(5, "0") + v + " issued " + moment(res.date).format("DD/MM/YYYY HH:mm");
      html += "<br />By " + res.customer;
      console.log(res)
      localStorage.saledate = res.saledate;
      localStorage.reference = res.reference;
      localStorage.isproform = res.isproform;
      localStorage.remark = res.remark;
      $("#invoiceNumber").html(html);
      if (loadedPages.payments.invoice.version != null) {
        $("<h4>Check previous invoices<h4>").appendTo($("#history"));
        var pdf = loadedPages.payments.invoice.pdf;
        var ppp = pdf.split("_");
        var pp = pdf.split("_")[2];
        var start = pp.substring(0, pp.length - 1);
        var cs = "loadedPages.payments.openPDF('" + ppp[0] + "_" + ppp[1] + "_" + start + "_" + "gb" + ".pdf');";
        var cs1 = ppp[0] + "_" + ppp[1] + "_" + start + "_" + "gb" + ".pdf";
        $("<p onclick=" + cs + ">" + cs1 + "</p>").appendTo($("#history"));
        var pos = versions.indexOf(loadedPages.payments.invoice.version);
        for (var i=0;i<pos;i++) {
          var cs = "loadedPages.payments.openPDF('" + ppp[0] + "_" + ppp[1] +  "_" + start + versions[i] + "_" + "gb" + ".pdf');";
          var cs1 = ppp[0] + "_" + ppp[1] + "_" + start + versions[i] + "_" + "gb" + ".pdf";
          $("<p onclick=" + cs + ">" + cs1 + "</p>").appendTo($("#history"));
        }
      }
      loadedPages.payments.getCustomer();

    }, obj, {}, {});
  },
  openPDF: function(data) {

       $.ajax({
         url: "https://costercatalog.com/api/invoice.php?invoice=" + data,
         type: "GET",
         success: function(res) {
           var app = document.URL.indexOf( 'http://' ) === -1 && document.URL.indexOf( 'https://' ) === -1;
            var blob = b64toBlob(res, "application/pdf");
            var blobUrl = URL.createObjectURL(blob);
            if (!app) {
             window.open(blobUrl, "_system","location=yes");

            } else {
              var storageLocation = "";
               storageLocation = 'file:///storage/emulated/0/';
               var folderpath = storageLocation + "Download";
               var filename = "invoice.pdf";
               var DataBlob = b64toBlob(res, "application/pdf");

              window.resolveLocalFileSystemURL(folderpath, function(dir) {
                dir.getFile(filename, {create:true}, function(file) {
                        file.createWriter(function(fileWriter) {
                            fileWriter.write(DataBlob);
                            setTimeout(function() {

                        cordova.plugins.fileOpener2.open(
                            "file:///storage/emulated/0/Download/invoice.pdf",
                            "application/pdf",
                            {
                                error : function(){ },
                                success : function(){ }
                            }
                        );
                      }, 500);
                        }, function(err){
                          // failed
                      alert(JSON.stringify(err));
                        });
                });
              });
            //  DownloadToDevice("http://costercatalog.com:81/api/invoice.php?invoice=" +  nm + "_" + "gb" + ".pdf");
            }

            //  window.open("http://costercatalog.com:81/api/invoice.php?invoice=" +  data, '_system');
         }
       })

  },
  getCustomer: function() {

    var tr = {
      tourid: loadedPages.payments.invoice.tourNo
    }
    api.call("getInvoiceTour", function(re) {
      var res = re.data[0];
      try {
        res["DT_RowId"] =  loadedPages.payments.invoice.tourNo;
        localStorage.tour = JSON.stringify(res);
      } catch(err) {
        localStorage.tour = JSON.stringify({
            ProjId: loadedPages.payments.invoice.tourNo,
            custom: "1"
        });
      }

    }, tr, {}, {})
    var obj = {
      query: loadedPages.payments.invoice.customerid
    }
    api.call("getCustomerById", function(res) {
      localStorage.customerInfo = JSON.stringify(res[0]);
      customerInfoData = JSON.stringify(res[0]);
      loadedPages.payments.setShoppingCart();
    }, obj, {}, {})
  },
  setShoppingCart: function() {
    var bb = {
      invoiceid: loadedPages.payments.invoiceid
    }
    payments = [];
    api.call("getInvoicePayments", function(res) {
      $.each(res.data, function(ind) {
        var ths = this;

        payments[ind]  = {
          paymentID: ths.paymentID,
          paymentMethod: ths.paymentMethods,
          currency: ths.currency,
          amount: ths.amount,
          date: ths.date,
          isOld: "1",
          version: ths.version
        }
      })

    }, bb, {}, {});
    var obj = {
      invoiceid: loadedPages.payments.invoiceid
    }
    api.call("getInvoiceItems", function(res) {
      $.each(res.data, function() {

        var data = this;
        var o = {
          SerialNo: data.SerialNo
        }
        api.call("getItemImage", function(r) {
          if (data.Discount == "") {
            data.Discount = "0"
          }
          try {
              var iimg = r.data[0].ImageName;
            } catch(err) {
              var iimg = "";
            }
                var obj = {
                    imageURL: "<img style='width:100px;height:auto;' src='/images/" + ((iimg == '') ? "crown.png" :  r.data[0].ImageName) + "' />",
                    img: "<img style='width:250px;height:auto;' src='/images/" + ((iimg == '') ? "crown.png" :  r.data[0].ImageName) + "' />",
                    SerialNo: data.SerialNo,
                    CompName: data.CompName,
                    productName:  data.productName,
                    SalesPrice: parseFloat(data["SalesPrice"]),
                    Discount: (data["Discount"]),
                    MainGroup: data.MainGroup,
                    quantity: data.quantity,
                    startRealPrice: parseFloat(data["SalesPrice"]),
                    info: ""
                }

            window.parent.postMessage("addToInvoiceFromSaved#" + JSON.stringify(obj), "*");

          }, o, {},{});
      })
    }, obj, {}, {})
  }
}
