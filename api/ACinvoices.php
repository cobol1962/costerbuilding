<?php
header('Access-Control-Allow-Origin: *');
?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8" />
      <meta name="format-detection" content="telephone=no" />
      <meta name="msapplication-tap-highlight" content="no" />
      <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width" />
      <script type="text/javascript" src="https://costercatalog.com/admin/js/jquery.js"></script>
      <link href="https://fonts.googleapis.com/css?family=Merriweather&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/bootstrap.css" />
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/sweetalert.css" />
      <link rel="apple-touch-icon" href="https://costercatalog.com/admin_modern/app-assets/images/ico/apple-icon-120.png">
      <link rel="shortcut icon" type="image/x-icon" href="https://costercatalog.com/admin_modern/app-assets/images/ico/favicon.ico">
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
      <!-- BEGIN: Vendor CSS-->
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/vendors/css/vendors.min.css">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/vendors/css/forms/icheck/icheck.css">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/vendors/css/forms/icheck/custom.css">
      <!-- END: Vendor CSS-->
      <!-- BEGIN: Theme CSS-->
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/css/bootstrap.css">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/css/bootstrap-extended.css">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/css/colors.css">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/css/components.css">
      <!-- END: Theme CSS-->
      <!-- BEGIN: Page CSS-->
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/css/core/menu/menu-types/vertical-menu-modern.css">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/css/core/colors/palette-gradient.css">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin_modern/app-assets/css/pages/invoice.css">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/font-awesome.min.css" />
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/countrySelect.css" />
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/DataTables/datatables.min.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha256-rByPlHULObEjJ6XQxW/flG2r+22R5dKiAoef+aXWfik=" crossorigin="anonymous" />
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/croppie.css" />
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/chosen.css" />
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/select2.css" />
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/style.css" />
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/sweetalert.css" />
      <link rel="stylesheet" type="text/css" href="https://costercatalog.com/admin/css/pretty-checkbox.css" />
      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  </head>

  <body>
    <div id="regularGB" style="display:none;padding:10px;">
      <button id="gbs" class="btn btn-primary" style="margin-top:5px;margin-left:20px;" onclick="submitGB();">Open Tax refund form</button>
    </div>
    <div id="hasGB" style="display:none;padding:10px;">
      <button id="gbs1" class="btn btn-primary" style="margin-top:5px;margin-left:20px;" onclick="submitGB();">Reprint TRF</button>
      <button  class="btn btn-primary" style="margin-top:5px;margin-left:20px;" onclick="voidTFF();">Void TRF</button>

    </div>
    <div>
    <table id="invoicesTable" class="display" style="min-width:100%;max-width:100%;width:100%;">
       <thead>
          <tr>
             <th>Date</th>
             <th>No</th>
             <th action>Action</th>
             <th>Customer</th>
             <th>Tour</th>
             <th>Touroperater</th>
             <th>Showroom</th>
             <th>Sales Persom</th>
             <th>Discount Approved</th>
             <th style="text-align:right;" id="total">Total</th>
             <th>Discount</th>
             <th>Due</th>
             <th style="display:none;">Status</th>
             <th style="display:none;">Version</th>
             <th style="display:none;">Locked</th>
             <th style="display:none;">Reference</th>
             <th style="display:none;">Reference</th>
             <th style="display:none;">Reference</th>
             <th style="display:none;">Is Due</th>
             <th style="display:none;">Is Due</th>
            <th style="display:none;">GB</th>
          </tr>
       </thead>
       <tbody>
       </tbody>
    </table>
  </div>
  <div>
  <iframe id="inv" src="" style="width:100%;height:2000px;margin-top:10px;"></iframe>
  <div id="gbdiv" style="display:none;">
       <form id="gbform" method="post" action="" onsubmit="return target_popup(this);" >
         <input type="text" id="sessiontoken" name="sessiontoken" />
         <input type="text" id="language" name="language" value="gb-en" />
         <input type="text" id="action" name="action" value="issue" />
         <input type="text" id="application" name="application" value="integra" />
         <input type="text" id="issuemodel" name="issuemodel" />
       </form>


     </div>
</div>
<div class="modal" id="mainModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header" id="m_header" style="border:none;padding:0;height:60px;background-size:50px 50px;background-repeat: no-repeat;background-position: center 5px;">
         </div>
         <p id="m_title" style="margin-top:10px;margin-bottom:10px;font-size:20px;text-align:center;">LOGIN TO YOUR <b>ROYAL COSTER</b> SALES ACCOUNT</p>
         <div id="m_content" style="padding-left:60px;padding-right:60px;">
         </div>
         <div class="modal-footer" id="m_footer">
            <div style="width: 100%;display: flex;align-items:flex-end;">
               <button id="m_cancel" class="btn-red" >CANCEL</button>
               <button id="m_confirm" class="btn-black">CONFIRM</button>
            </div>
         </div>
      </div>
   </div>
</div>
<div id="loginModal" class="modal" style="width:500px;padding:10px;margin:50px auto;">

    <div class="modal-content">

      <div class="modal-body">

		<div class="row">
			<div class="col-md-offset-3 col-xs-12 col-sm-6">
			    <form class="omb_loginForm" action="" id="login_form" autocomplete="off" method="POST">
            <div class="input-group">
  						<span class="input-group-addon"><i class="fa fa-user"></i></span>
  						<input  type="text" class="form-control" name="username" id="username" placeholder="Username">
          	</div>

					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-lock"></i></span>
						<input  type="password" class="form-control" name="password" id="password" placeholder="Password">
        	</div>
          	<div class="input-group">
              <span id="wrongpassword" style="color:red;display:none;">Wrong password or username</span>

            </div>
				</form>

        <button class="btn btn-lg btn-black btn-block" id="login" onclick="loginAdmin();" style="margin-top:20px;">Login</button>

			</div>
    	</div>

      </div>

    </div>

  </body>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
  <script src="https://unpkg.com/@popperjs/core@2" type="text/javascript"></script>
  <script type="text/javascript" src="https://costercatalog.com/admin/js/bootstrap.js"></script>
  <script type="text/javascript" src="https://costercatalog.com/admin/js/chosen.jquery.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/javascript" src="https://costercatalog.com/admin/DataTables/datatables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
  <script type="text/javascript" src="https://costercatalog.com/admin/js/api_reports.js"></script>
  <script type="text/javascript" src="https://costercatalog.com/admin/js/underscore.js"></script>
  <script type="text/javascript" src="https://costercatalog.com/admin/js/sweetalert2.js"></script>
  <script type="text/javascript" src="https://costercatalog.com/admin/js/jquery.validate.js"></script>
  <script type="text/javascript" src="https://costercatalog.com/admin/js/api.js"></script>
    <script type="text/javascript" src="https://costercatalog.com/admin/js/moment.js"></script>
  <script>
  var invoiceTable = null;
  var _0x5326=['584592PjNiKk','5unoHag','22pVNLOk','toString','AES','97264ntENMN','decrypt','309tVOkiZ','4fzKuJH','949BWtnQw','408716mHWKSS','160299QcciTR','52195gEjKsa','enc','3711wCWEim','encrypt','47588LYyrMT'];var _0x1f4c=function(_0x3499f8,_0x21b88a){_0x3499f8=_0x3499f8-0x124;var _0x5326f0=_0x5326[_0x3499f8];return _0x5326f0;};var _0x1e9e75=_0x1f4c;(function(_0x569d03,_0x33c626){var _0x43bed1=_0x1f4c;while(!![]){try{var _0x95801d=parseInt(_0x43bed1(0x127))*-parseInt(_0x43bed1(0x132))+-parseInt(_0x43bed1(0x12e))*parseInt(_0x43bed1(0x125))+parseInt(_0x43bed1(0x12b))+-parseInt(_0x43bed1(0x128))*-parseInt(_0x43bed1(0x134))+parseInt(_0x43bed1(0x12d))*-parseInt(_0x43bed1(0x12f))+parseInt(_0x43bed1(0x131))+parseInt(_0x43bed1(0x126));if(_0x95801d===_0x33c626)break;else _0x569d03['push'](_0x569d03['shift']());}catch(_0x17b0ab){_0x569d03['push'](_0x569d03['shift']());}}}(_0x5326,0x2bc1d));var wwhtto=_0x1e9e75(0x12a),desccnnfgt=CryptoJS,kkfftt=_0x1e9e75(0x130);function getCrypto(_0xbd202d){var _0x4ea003=_0x1e9e75;return CryptoJS[wwhtto][_0x4ea003(0x124)](_0xbd202d,kkfftt)[_0x4ea003(0x129)]();}function getEcrypted(_0x2a6027){var _0x2d6acc=_0x1e9e75,_0x125fb1=CryptoJS[wwhtto][_0x2d6acc(0x12c)](_0x2a6027,kkfftt),_0xd081cd=_0x125fb1[_0x2d6acc(0x129)](CryptoJS[_0x2d6acc(0x133)]['Utf8']);return _0xd081cd;}
    $(document).ready(function() {
      continueReady();
    })
    function continueReady() {

      var ics = "<?=$_GET['invoices']?>";

      invoiceTable = $("#invoicesTable").DataTable({
          ajax: {
              "url": "https://costercatalog.com/api/index.php?request=showInvoices&invoices=" + ics
          },
           "order": [[ 0, "desc" ]],
             "paging": false,
             "pageLength": 20,
             columns: [
                    { "data": "date" ,

                      "render" : function ( data, type, row )  {
                     //   return moment(data).format("dd.mm.yyyy");

                       return data;
                    }},
                    { "data": "invoiceid",   "render": function ( data, type, row ) {

                         var str = "9" + data.toString().padStart(5, "0") + ((row["version"] != null) ? row["version"] : "");
                         if (row["reference"] != "") {
                            str += "<br />Ref. No." + row["reference"];
                         }
                         return str;
                     }},
                     { data: "pdf",
                         "defaultContent": "",
                         "render": function ( data, type, row ) {
                           var cs = "openPDF('" + data + "');";
                           html = '<div style="width:150px;min-width:150px;"><a class="gen-link" onclick=' + cs + ' ><i class="fa fa-file-pdf-o fa-2x m-r-5"></i></a>';
                      //     html += '&nbsp;&nbsp;<a unlock class="gen-link"><i class="fa fa-unlock fa-2x m-r-5"></i></a><a lock class="gen-link"><i class="fa fa-lock fa-2x m-r-5"></i></a>';
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
                     "type" : "numeric",
                        "defaultContent": "",
                  /*    "orderDataType": "numeric_value",*/
                      "render": function ( data, type, row ) {
                        var html = "<realvalue realvalue='" + data + "'>" + parseFloat(data).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</realvalue>";
                         return html;
                     }},
                     { "data": "discount",
                     orderable: false,
                     "defaultContent": "",
                     "orderDataType": "numeric_value",
                     "type": "numeric",
                     "render": function ( data, type, row ) {

                        return (data);

                    }},
                    { "data": "due",
                       orderable: false,
                    "orderDataType": "numeric_value",
                    "type": "numeric",
                      "render" : function(data, type, row) {
                      return (data);
                    }},

                   { "data": "status"},
                   { "data": "version"},
                  { "data": "locked"},
                  { "data": "reference"},
                  { "data": "currentdue"},
                  { "data": "isDiscounted"},
                  { "data": "isDue"},
                  { "data": "dueAmount",
                    "type": "numeric"
                  },
                  { "data": "GBid"},

                ],
                "initComplete": function(settings, json) {
                  setTimeout(function() {
                    $.each($("#invoicesTable").find("tbody").find("tr").eq(0), function(ind) {
                      var dt = new Date($(this).find("td").eq(0).html().trim());
                      dt.setHours(0,0,0,0);
                      var gb = $(this).find("td").eq(20).html().trim();
                      var dtn = new Date();
                      dtn.setHours(0,0,0);

                      if (dtn > dt && gb != "") {
                        $("#regularGB").hide();
                        $("#hasGB").show();
                      }
                      if (dtn > dt && gb == "") {
                        $("#regularGB").hide();
                        $("#hasGB").hide();
                      }
                      if ((dtn.toString().trim() == dt.toString().trim()) && gb == "") {
                        $("#regularGB").show();
                        $("#hasGB").hide();
                      }
                    });
                  }, 1000);


                },
                dom: 'Bfrtip',
                buttons: [

                ]

       });
       setTimeout(function() {
            invoiceTable.columns.adjust().draw();
            $("#invoicesTable").find("tr").eq(1).find("td").eq(2).find("a").eq(0).trigger("click");
       }, 2000)
  }
  function reprintTFF(b64) {
    var blob = b64toBlob(b64, "application/pdf");
    var blobUrl = URL.createObjectURL(blob);
     $("#inv").attr("src", blobUrl);

  }
    function openPDF(data) {
      alert("https://costerbuilding.com/api/invoice.php?invoice=" + data)
      $.ajax({
       url: "https://costerbuilding.com/api/invoice.php?invoice=" + data,
       type: "GET",
       success: function(res) {

         var app = document.URL.indexOf( 'mobile' ) > -1;
          var blob = b64toBlob(res, "application/pdf");
          var blobUrl = URL.createObjectURL(blob);
      //    alert(app)
          if (!app) {
           $("#inv").attr("src", blobUrl);

          } else {
          //  $("#gbs").trigger("click");
          /*  var reader = new FileReader();
            var out = new Blob([res], { type: 'application/pdf' });
            reader.onload = function(e) {
                window.location.href = reader.result;
            }
            reader.readAsDataURL(out);

            // var blob = new Blob([response.data], { type: "application/pdf" });
            var fileURL = URL.createObjectURL(out);
            var a = document.createElement('a');
            a.href = fileURL;
            a.target = '_blank';
            a.download = 'lkn_' + id + '.pdf';
            document.body.appendChild(a);
            a.click();
          /*  var storageLocation = "";
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
            });*/
          //  DownloadToDevice("http://costercatalog.com:81/api/invoice.php?invoice=" +  nm + "_" + "gb" + ".pdf");
          }

          //  window.open("http://costercatalog.com:81/api/invoice.php?invoice=" +  data, '_system');
       }
     })
     }
     var b64toBlob = (b64Data, contentType='application/pdf', sliceSize=512) => {
       var byteCharacters = atob(b64Data);
       var byteArrays = [];

       for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
         var slice = byteCharacters.slice(offset, offset + sliceSize);

         var byteNumbers = new Array(slice.length);
         for (var i = 0; i < slice.length; i++) {
           byteNumbers[i] = slice.charCodeAt(i);
         }

         var byteArray = new Uint8Array(byteNumbers);
         byteArrays.push(byteArray);
       }

       var blob = new Blob(byteArrays, {type: contentType});

       return blob;
     }
     function showPayments(obj) {

       var o = $(obj);
       var dd = o.parent().find("[payments]").clone();
       var html = "<table>";
       html += "<tr><td style='text-align:left;' colspan='4'><strong>Invoice number " + "9" + dd.find("invoice").html().padStart(5, "0") + dd.find("version").html() + "</strong></td></tr>";
       var pdftoopen = dd.find("pdf").html();

               if (true) {

                 var pdf = pdftoopen;
                 var ppp = pdf.split("_");
                 var pp = pdf.split("_")[2].substring(0,6);
                 var start = pp;

                 api.call("listInvoicesByNumber", function(res) {
                   $.each(res, function() {
                       var cs = "openPDF('" + this + "');";
                       html += "<tr><td colspan='4'><p onclick=" + cs + ">" + this + "</p></td></tr>";
                    });
                },{number: pp}, {},{});
              }
               html += "<tr><td style='text-align:left;' colspan='4'><strong>Payments</strong></td></tr>";

               $.each(dd.find("pay"), function(ind) {
                 var rr = $(this).html().split("|");
                 html += "<tr><td style='padding:5px;'>" + rr[0] + "</td><td style='padding:5px;'>" + rr[1] + "</td><td style='padding:5px;'>" + rr[2] + "</td><td style='padding:5px;text-align:right;'>" + rr[3] + "</td></tr>";
               });
               html += "</div>";
               showModal({
                 title: "Payments",
                 showCancelButton: false,
                 content: html,
                 confirmButtonText: "CLOSE"
             })
     }
     function showPaymentsById(id) {


        var inm = parseInt(id.toString().substring(1));
        var pp = id;
        var start = pp;
       api.call("listInvoicesByNumber", function(res) {
            openPDF(res[res.length - 1]);
         },{number: pp}, {},{});

     }
     showModal = function(options = {}) {

       if (options.type === undefined) {
         $("#m_header").css({
           backgroundImage: "url(images/crown.png)"
         })
       }
       if (options.type == "error") {
         $("#m_header").css({
           backgroundImage: "url(images/error.png)"
         })
       }
       if (options.type == "ok") {
         $("#m_header").css({
           backgroundImage: "url(images/green_checkbox_only.png)"
         })
       }
       if (options.title !== undefined) {
         $("#m_title").html(options.title);
       }
       if (options.content !== undefined) {

         $("#m_content").html(options.content);
       } else {
           $("#m_content").html("");
       }
       if (options.addContent !== undefined) {
         $("#" + options.addContent).appendTo($("#m_content"))
     } else {
         if (options.content === undefined) {
           $("#m_content").html("");
         }
       }
       if (options.showCancelButton !== undefined) {
         $("#m_cancel").hide();
       } else {
         $("#m_cancel").show();
       }
       if (options.showConfirmButton !== undefined) {
         $("#m_confirm").hide();
       } else {
         $("#m_confirm").show();
       }
       if (options.confirmButtonText !== undefined) {
         $("#m_confirm").html(options.confirmButtonText);
       } else {
         $("#m_confirm").html("CONFIRM");
       }
       if (options.cancelButtonText !== undefined) {
         $("#m_cancel").html(options.cancelButtonText);
       } else {
         $("#m_cancel").html("CANCEL");
       }
       $("#m_confirm").unbind("click");

       if (options.confirmCallback !== undefined) {
         $("#m_confirm").bind("click", function() {
           options.confirmCallback();
           if (options.noclose === undefined) {
             $('#mainModal').modal("hide");
           }
         });
       } else {
         $("#m_confirm").bind("click", function() {
           if (options.noclose === undefined) {
             $('#mainModal').modal("hide");
           }
         });
       }
       if (options.cancelCallback === undefined) {
         $("#m_cancel").bind("click", function() {
           if (options.noclose === undefined) {
             $('#mainModal').modal("hide");
           }
         });
       } else {
         $("#m_cancel").bind("click", function() {
           options.cancelCallback();
           if (options.noclose === undefined) {
             $('#mainModal').modal("hide");
           }
         });
       }
       setTimeout(function() {
           if (options.showClose !== undefined) {
              $('#mainModal').find(".close").hide();
           } else {
             $('#mainModal').find(".close").show();
           }
       }, 1000);
       if (options.allowBackdrop !== undefined) {
         $('#mainModal').modal({
           backdrop: 'static',
           keyboard: false
         })
       } else {
         $('#mainModal').modal({
           backdrop: true,
           keyboard: true
         })
       }
       if (options.addToContent !== undefined) {
         var dv = $(options.addToContent).clone().appendTo($("#m_content"));
         $("#mainModal").find(options.addToContent).show();
         $("#mainModal").find(options.addToContent).attr("id",options.addToContent.substring(1));
       }
       if (options.onOpen !== undefined) {
         options.onOpen();
       }
       $("#mainModal").modal("show");
     }
   function loginAdmin() {
     api.call("checkACLogin", function(res) {
      if (res.login == "1") {
        $("#loginModal").modal("hide");
        $("#wrongpassword").hide();
        continueReady();
      } else {
        $("#wrongpassword").show();
      }

    }, { password: $("#password").val(), username: $("#username").val() }, {}, {});
   }
   var getUrlParameter = function getUrlParameter(sParam) {
      var sPageURL = window.location.search.substring(1),
          sURLVariables = sPageURL.split('&'),
          sParameterName,
          i;

      for (i = 0; i < sURLVariables.length; i++) {
          sParameterName = sURLVariables[i].split('=');

          if (sParameterName[0] === sParam) {
              return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
          }
      }
      return false;
  };
  function target_popup(form) {
   // get stringified IssueModel object
      //  alert($("#sessiontoken").val())
        // open new window for IC2 Integra UI
        window.open('', 'formpopup', "width=" + screen.availWidth + ",height=" + screen.availHeight + ', resizeable,scrollbars,toolbar=no,location=no,status=no,menubar=no');
        form.target = 'formpopup';
        // IC2 Integra UI URL
        var issueposturl = "https://ic2integra-web.mspe.globalblue.com/ui/integra";
        form.action = issueposturl;
        return true;
    }
    window.addEventListener("message", receiveMessage, false);

    function receiveMessage(event) {

    // ensure event.data received is valid
      if (event != null && event.data != null && event.data != "") {

        var ic2response = event.data;

      // ensure received message originates from IC2 Integra UI
        if (ic2response.originator === "IC2") {

          if (ic2response.success) {

            inmb = parseInt(inm.substring(1));

            api.call("setInvoiceGB", function(r) {
              $("#regularGB").hide();
                $("#hasGB").show();
            }, {invoiceid: inmb,amount: ic2response.actionResponse.totalGrossAmount, dif: ic2response.actionResponse.documentIdentifier},{},{})

          }
        }
      }
    }
    var inm = "";
    function submitGB() {
      inm = "<?=$_GET["invoices"]?>";
      var ii = parseInt(inm.substring(1))
      var rcpts = [];

      api.call("getGBdata", function(res) {
        console.log(res)
        var im = {};
        im.shop = {
          "shopId": "303782",
          'deskId': "367217"
        };
        im.purchase = {
          'receipts': [
            {
              "receiptDate": res.invoice.date,
              'purchaseItems':[
                {
                  'vatRate': 21.00,
                  'amount': {
                    'grossAmount': res.invoice.due
                  },
                  'goodDescription': "Coster Diamonds invoice No. " + inm
                }
              ]
            }
          ]
       }
       console.log(res.customer)
       im.traveller =  {
        "address": {
          "street": res.customer.address1,
          "state": "",
          "city": res.customer.city,
          "zip": res.customer.zip
        },
        "firstName": res.customer.lastName,
        "lastName": res.customer.firstName,
        "mobileNumber": res.customer.telephone,
        "email": res.customer.email,
        "countryOfResidence":
            {
              "alpha3Code": res.customer.countryCode
            },
        }
       var ss = "<?=$_GET['sessiontoken']?>";
      if  (ss == "") {
        var obj1 = {
           country: res.customer.countryCode,
           amount: parseFloat(res.invoice.due)
        }
        if (res.GBid == "") {
          api.call("checkCustomerGB", function(resp) {
            console.log(resp);
            if (resp.response.status == "ok") {
              $("#sessiontoken").val(resp.response.GBToken);
              $("#issuemodel").val(JSON.stringify(im));
              $("#gbform").submit();
            } else {

                showModal({
                  type: "error",
                  title: "Invoice not eligible for Tax refund. Amount less then € 50",
                  showConfirmButton: false,
                  cancelButtonText: "CLOSE",
                  confirmCallback: function() {
                    avoidAndRecreateTFF(ts,resp.response.GBToken,res.GBinitial);
                  }
                });

            }
          },  obj1,{},{})
        } else {

          var ts = {
            "TotalGrossAmount": res.invoice.due,
            "DocIdentifier": res.GBid,
            "Shop": {
              "ShopID": 303782,
              "DeskID": 367217
            },
            "SenderId": "303782",
          };
          api.call("checkCustomerGB", function(resp) {
            if (resp.response.status == "ok") {
              $.ajax({
                url: "https://ic2integra-api.mspe.globalblue.com/api/TfsIssuingService/ReprintRenderedCheque",
                type: "POST",
                dataType: "json",
                headers: {
                  "GB-SessionToken" : resp.response.GBToken,
                  "Content-Type": "application/json"
                },
                data: JSON.stringify(ts),
                success: function(res) {
                  if (res.Content !== undefined) {
                    reprintTFF(res.Content);
                  }
                },
                error: function() {
                  showModal({
                    type: "error",
                    title: "TFF can't be reprinted due differencies in data between old TFF and form required to be reprinted. Do you wish to avoid old document and create new one?",
                    confirmCallback: function() {
                      avoidAndRecreateTFF(ts,resp.response.GBToken,res.GBinitial);
                    }
                  });
                }

              })
            }
        },  obj1,{},{})
      }
      } else {
          if (res.GBid == "") {
             $("#sessiontoken").val(ss);
             $("#issuemodel").val(JSON.stringify(im));
             $("#gbform").submit();
          } else {
            var ts = {
              "TotalGrossAmount": res.invoice.due,
              "DocIdentifier": res.GBid,
              "Shop": {
                "ShopID": 303782,
                "DeskID": 367217
              },
              "SenderId": "303782",
            };
            $.ajax({
              url: "https://ic2integra-api.mspe.globalblue.com/api/TfsIssuingService/ReprintRenderedCheque",
              type: "POST",
              dataType: "json",
              headers: {
                "GB-SessionToken" : ss,
                "Content-Type": "application/json"
              },
              data: JSON.stringify(ts),
              success: function(res) {
                if (res.Content !== undefined) {
                  reprintTFF(res.Content);
                }
                },
              error: function() {
                showModal({
                  type: "error",
                  title: "TFF can't be reprinted due differencies in data between old TFF and form required to be reprinted. Do you wish to avoid old document and create new one?",
                  confirmCallback: function() {
                    avoidAndRecreateTFF(ts, ss, res.GBinitial);
                  }
                });
              }


            })
          }
      }
    }, {invoiceid: ii}, {}, {})


    }
function avoidAndRecreateTFF(obj, token, initial) {
  obj.TotalGrossAmount = initial;
  var inb = "<?=$_GET['invoices']?>";
  var inmb = parseInt(inb.toString().substring(1));

  $.ajax({
    url: "https://ic2integra-api.mspe.globalblue.com/api/TfsIssuingService/VoidCheque",
    type: "POST",
    dataType: "json",
    headers: {
      "GB-SessionToken" : token,
      "Content-Type": "application/json"
    },
    data: JSON.stringify(obj),
    success: function(res) {
      api.call("deleteInvoiceGB", function(r) {
          submitGB();
      }, {invoiceid: inmb,amount: "", dif: ""},{},{})

    }
  })
}
function voidTFF(obj, token, initial) {
  $.ajax({
    url: "https://costercatalog.com:9000/?gbtoken",
    type: "GET",
    dataType: "json",
    success: function(rrr) {
      var inb = "<?=$_GET['invoices']?>";
      var inmb = parseInt(inb.toString().substring(1));
        api.call("getGBdata", function(res) {
            var ts = {
              "TotalGrossAmount": res.invoice.due,
              "DocIdentifier": res.GBid,
              "Shop": {
                "ShopID": 303782,
                "DeskID": 367217
              },
              "SenderId": "303782",
            };
            ts.TotalGrossAmount = res.invoice.due;
            $.ajax({
              url: "https://ic2integra-api.mspe.globalblue.com/api/TfsIssuingService/VoidCheque",
              type: "POST",
              dataType: "json",
              headers: {
                "GB-SessionToken" : rrr.gbt,
                "Content-Type": "application/json"
              },
              data: JSON.stringify(ts),
              success: function(res) {

                api.call("deleteInvoiceGB", function(r) {
                  showModal({
                    type: "success",
                    title: "Tax refund request voided successfully",
                    showCancelButton: false,
                    confirmButtonText: "CONTINUE",
                    confirmCallback: function() {
                      window.location.reload();
                    }
                  });
                }, {invoiceid: inmb,amount: "", dif: ""},{},{})

              }
            })
    }, {invoiceid: inmb },{},{});
    }
  })

}
function reprintGB() {

}
</script>
  <style>
  .dataTables_filter {
  display:none;
  }
  #invoicesTable td:nth-of-type(13n), #invoicesTable td:nth-of-type(14n),
  #invoicesTable td:nth-of-type(15n),#invoicesTable td:nth-of-type(16n), #invoicesTable td:nth-of-type(17n),
  #invoicesTable td:nth-of-type(18n),  #invoicesTable td:nth-of-type(19n), #invoicesTable td:nth-of-type(20n)   {
  display:none;
  }
  #invoicesTable td:nth-of-type(19n),   #invoicesTable td:nth-of-type(20n)   {
  display:none;
  }
  #invoicesTable td:nth-of-type(10n)  {
  text-align: right;
  }
  #invoicesTable td {
  vertical-align:bottom;
  }
  #invoicesTable td:nth-of-type(1st),  #invoicesTable th:nth-of-type(1st)  {
  width:50px;
  }
  </style>
</html>
