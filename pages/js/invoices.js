loadedPages.invoices = {
  table: null,
  initialize: function() {
    $.fn.dataTable.ext.order['numeric_value'] = function  ( settings, col )
    {
      return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
            if ($('realvalue', td).attr("realvalue") !== undefined) {
              return $('realvalue', td).attr("realvalue") * 1;
            } else {
              return 0;
            }
        } );

    };

    var sp = $.parseJSON(localStorage.sp);
      $("body").LoadingOverlay("hide");

    loadedPages.invoices.table = $("#invoicesTable").DataTable({
        ajax: {
            "url": "https://costerbuilding.com/api/index.php?request=allinvoices",
             data : { salePersonId: sp.EmplID, secret:"scddddedff2fg6TH22" },
             type: "POST"
        },
        "order": [[ 0, "desc" ]],
          "paging": false,
           "order": [[ 0, "desc" ]],
           columns: [
                  { "data": "date" ,

                    "render" : function ( data, type, row )  {
                   //   return moment(data).format("dd.mm.yyyy");

                     return data;
                  }},
                  { "data": "invoiceid",
                    "render" : function(data, type, row) {
                      if (row["locked"] == "0") {
                       var cs = "localStorage.invoiceToOpen='" + row["invoiceid"] + "';invoiceLocked=0;loadPage('openInvoice');";
                       return '<a   onclick=' + cs + '>' + "9" + row["invoiceid"].toString().padStart(5, "0") + ((row["version"] == null) ? "" : row["version"]) + '</a>';
                     } else {
                       if (row["due"].indexOf("Completed") > -1) {
                         return '<a style="width:120px;min-width:120px;">' + "9" + row["invoiceid"].toString().padStart(5, "0") + ((row["version"] == null) ? "" : row["version"]) + '&nbsp;<img src="images/locked.png" style="margin-top:-5px;width:15px;" /></a>';
                       } else {
                         var cs = "localStorage.invoiceToOpen='" + row["invoiceid"] + "';invoiceLocked=1;loadPage('openInvoice');";
                         return '<a onclick=' + cs + ' style="width:120px;min-width:120px;">' + "9" + row["invoiceid"].toString().padStart(5, "0") + ((row["version"] == null) ? "" : row["version"]) + '&nbsp;(<img src="images/locked.png" style="margin-top:-5px;width:15px;" />)</a>';
                       }
                     }
                   }},
                   { data: "pdf",
                       "defaultContent": "",
                       "render": function ( data, type, row ) {
                         var cs = "openPDF('" + data + "');";
                         html = '<div style="width:150px;min-width:150px;"><a class="gen-link" onclick=' + cs + ' ><img src="images/pdf.png" style="width:35px;" /></a>';
                         html += '&nbsp;&nbsp;<a unlock class="gen-link"><img src="images/locked.png" style="width:25px;" /></a><a lock class="gen-link"><img src="images/unlocked.png" style="width:25px;" /></a>';
                         html += "</div>"
                         return html;
                       }
                     },
                  { "data": "customer" },
                  { "data": "tourNo" },
                  { "data": "touroperater" },
                   { "data": "showroom" },
                   { "data": "salesPerson" },
                    { "data": "discountApprovedName" },
                   { "data": "startingTotal",
                    "defaultContent": "",
                    "orderDataType": "numeric_value",
                    "render": function ( data, type, row ) {
                      var html = "<realvalue realvalue='" + data + "'>" + parseFloat(data).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</realvalue>";
                       return html;
                   }},
                   { "data": "discount",
                   "defaultContent": "",
                   "orderDataType": "numeric_value",
                   "type": "numeric",
                   "render": function ( data, type, row ) {
                      return data;

                  }},
                  { "data": "due",
                  "orderDataType": "numeric_value",
                  "type": "numeric",
                    "render" : function(data, type, row) {
                    return data;
                  }},

                 { "data": "status"},
                 { "data": "version"},
                { "data": "locked"},
                { "data": "reference"},
                { "data": "currentdue"},
                  { "data": "hasDicount"}

              ],
              "drawCallback": function(settings) {
                $.each($("#invoices").find("tbody").find("tr"), function(ind) {
              //    alert($(this).find("td").eq(11).html())
                  if ($(this).find("td").eq(12).html() == "1") {
              //      alert("here")
                    $(this).find("[ok]").hide();
                    $(this).find("[void]").show();
                    $(this).css({
                      opacity: 1
                    })
                  } else {
                    $(this).find("[ok]").show();
                    $(this).find("[void]").hide();
                    $(this).css({
                      opacity: 0.6
                    })
                  }
                  if ($(this).find("td").eq(14).html() == "1") {
              //      alert("here")
                    $(this).find("[unlock]").hide();
                    $(this).find("[lock]").show();
                  } else {
                    $(this).find("[unlock]").show();
                    $(this).find("[lock]").hide();
                  }
                });
                $("[void]").unbind("click");
                $("[void]").bind("click", function() {
                  var rw = invoiceTable.row($(this).closest("tr"));
                  var dt = rw.data();
                  var nd = rw.node();
                  $(nd).find("[void]").hide();
                  $(nd).find("[ok]").show();
                  $(nd).css({
                    opacity: 0.6
                  })
                  dt.status = "0";

                  api.call("setInvoiceStatus", function(res) {

                  }, {invoiceid: dt.invoiceid, status: "0" }, {},{})
                })
                $("[ok]").unbind("click");
                $("[ok]").bind("click", function() {
                  var rw = invoiceTable.row($(this).closest("tr"));
                  var dt = rw.data();
                  var nd = rw.node();
                  $(nd).find("[void]").show();
                  $(nd).find("[ok]").hide();
                  $(nd).css({
                    opacity: 1
                  })
                  dt.status = "1";
                  api.call("setInvoiceStatus", function(res) {

                  }, {invoiceid: dt.invoiceid, status: "1" }, {},{})
                })
                $("[lock]").unbind("click");
                $("[lock]").bind("click", function() {
                  var rw = invoiceTable.row($(this).closest("tr"));
                  var dt = rw.data();
                  var nd = rw.node();
                  $(nd).find("[unlock]").show();
                  $(nd).find("[lock]").hide();

                  dt.locked = "0";

                  api.call("setInvoiceLocked", function(res) {

                }, {invoiceid: dt.invoiceid, locked: "0" }, {},{})
                })
                $("[unlock]").unbind("click");
                $("[unlock]").bind("click", function() {
                  var rw = invoiceTable.row($(this).closest("tr"));
                  var dt = rw.data();
                  var nd = rw.node();
                  $(nd).find("[lock]").show();
                  $(nd).find("[unlock]").hide();
                  dt.locked = "1";
                  api.call("setInvoiceLocked", function(res) {

              }, {invoiceid: dt.invoiceid, locked: "1" }, {},{})
                })
              },
              dom: 'Bfrtip',
               buttons: [

               ]
     });
    setTimeout(function() {
      yadcf.init(loadedPages.invoices.table, [{
         column_number: 0,
           filter_type: "range_date",
           date_format: 'yyyy-mm-dd',
           moment_date_format: 'YYYY-MM-DD',
           filter_delay: 500,
            filter_container_id: "t_0"
         },
         {
           column_number: 1,
           filter_type: "auto_complete",
           text_data_delimiter: ",",
             filter_container_id: "t_1"
         },
         {
             column_number: 14,
             filter_type: 'custom_func',
             custom_func: loadedPages.invoices.searchLocked,
             data: [{
                 value: '-1',
                 label: 'All'
             }, {
                 value: '0',
                 label: 'Unlocked'
             }, {
                 value: '1',
                 label: 'Locked'
             }],
             filter_default_label: "Locked/unlocked",
              filter_container_id: "t_2"
         },
         {
             column_number: 16,
             filter_type: 'custom_func',
             custom_func: loadedPages.invoices.searchDue,
             data: [{
                 value: '0',
                 label: 'Completed'
             }, {
                 value: '1',
                 label: 'Due'
             }],
             filter_default_label: "Is Due",
              filter_container_id: "t_3"
         },
         {
             column_number: 17,
             filter_type: 'custom_func',
             custom_func: loadedPages.invoices.searchDiscount,
             data: [{
                 value: '0',
                 label: 'No discount'
             }, {
                 value: '1',
                 label: 'Discaunt'
             }],
             filter_default_label: "Discount",
              filter_container_id: "t_discount"
         },
         {
           column_number: 3,
           filter_type: "auto_complete",
           text_data_delimiter: ",",
             filter_container_id: "t_4"
         },
         {
           column_number: 4,
           filter_type: "auto_complete",
           text_data_delimiter: ",",
             filter_container_id: "t_5"
         },
         {
           column_number: 5,
           filter_type: "auto_complete",
           text_data_delimiter: ",",
             filter_container_id: "t_6"
         },
         {
           column_number: 6,
           filter_type: "auto_complete",
           text_data_delimiter: ",",
             filter_container_id: "t_7"
         },
         {
           column_number: 7,
           filter_type: "auto_complete",
           text_data_delimiter: ",",
             filter_container_id: "t_8"
         },
         {
           column_number: 8,
           filter_type: "auto_complete",
           text_data_delimiter: ",",
             filter_container_id: "t_9"
         },
      ]);
    }, 3000);
  /*    yadcf.exFilterColumn(loadedPages.invoices.table, [
        [14, "0"]
      ]);*/
 },
 openPDF: function(data) {

   $.ajax({
     url: "https://costerbuilding.com/api/invoice.php?invoice=" + data,
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
                    });
            });
          });
        //  DownloadToDevice("http://costerbuilding.com:81/api/invoice.php?invoice=" +  nm + "_" + "gb" + ".pdf");
        }

        //  window.open("http://costerbuilding.com:81/api/invoice.php?invoice=" +  data, '_system');
     }
   })

 },
 searchLocked: function(filterVal, columnVal) {
         var found;
         if (filterVal == "-1") {
           return true;
         }
         if (filterVal == "0") {
           if (columnVal == "0") {
             return true;
           }
         }
         if (filterVal == "1") {
           if (columnVal == "1") {
             return true;
           }
         }

         return false;
     },
  searchDue: function(filterVal, columnVal) {
        var found;
        if (filterVal == "0") {
          if (columnVal == 0) {
            return true;
          }
        }
        if (filterVal == "1") {
          if (columnVal > 1) {
            return true;
          }
        }

        return false;
 },
  searchDiscount: function(filterVal, columnVal) {
        var found;
        if (filterVal == "0") {
          if (columnVal == 0) {
            return true;
          }
        }
        if (filterVal == "1") {
          if (columnVal == 1) {
            return true;
          }
        }

        return false;
 }
}
