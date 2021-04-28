var loadedPages = {};
var in_barcode_scan = false;
var translation = {};
var pages = [];
var vatRefund = false;
var currentPage = "";
var loadedSalesPersons = {};
var adminChargeID = "";
var invoiceID = "";
var vatRefundID = "";
var shoppingCartContent = {};
var payments = [];
var customerInfoData = {};
var firstLoad = true;
var spersonsarr = [];
var firstCatalog = true;
var firstDiamond = true;
var escapeClicked = false;
var userData = {};
var lastFoucesed = null;
var invoiceLocked = 0;
var haveToSave = false;
var appError = false;
var tablesError = false;
var homeClicked = false;
var versions = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
var optionsLoader = {
    image: "https://costercatalog.com/coster/www/images/diamond.gif",
    imageAnimation: false
}

function resetLocalStorage() {

    for (var key in localStorage) {
        if (key != "originalsp" && key != "sp" && key != "tour" && key != "showRoom" && key != "showRoomName" && key != "url") {

            delete localStorage[key];
        }
    }
    delete localStorage.generalDiscount;
    invoiceID = "";
    shoppingCartContent = [];
    payments = {};
    invoiceLocked = 0;
    $("[lock]").show();
    $("#toggleShoppigCart").addClass("empty");

}

function checkLogin() {


    $("#pin").val("");
    if (localStorage.originalsp !== undefined) {
      localStorage.sp = localStorage.originalsp;
    }

    if (localStorage.sp !== undefined) {
        localStorage.originalsp = localStorage.sp;
        $("[login]").hide();
        $("[logout]").show();
        var sp = $.parseJSON(localStorage.sp);
        $("#ename").html(sp.Employee);
        $("[profile]").show();
        if (sp.SalesApp == "2") {
          $("#bhspersons").show();
        }
        return true;
    } else {
        $("[login]").show();
        $("[logout]").hide();
        $("[profile]").hide();
        var app = document.URL.indexOf('http://') === -1 && document.URL.indexOf('https://') === -1;



        //    return false;
    }
}

function writeLogout() {

    userData.activity = "Logout";
    /*  for (var key in localStorage) {
        //delete localStorage[key];
      }*/
    delete localStorage.originalsp;
    delete localStorage.sp;
    shoppingCartContent = [];
    payments = [];
    $("#toggleShoppigCart").addClass("empty");
    userData.deviceid = userData.deviceid.replace("undefined", "");
    shoppingCartContent = [];
    payments = {};

    $("#pin").html("");

    try {
      /*  api.call("createlog", function() {
            ws.send(JSON.stringify({
                action: "reloadadmin"
            }))

            loadPage1("homepage");
        }, userData, {}, {})*/
    } catch (err) {

    }

}
var ws = null;

function resizeCanvas() {
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.

}
$(document).ready(function() {
  /*$.ajax({
    url: "https://costerbuilding.com/api/index.php?request=getCode",
    type: "GET",
    dataType: "json",
    success: function(res) {

      var s = document.createElement("script");
       s.type = "text/javascript";
       s.id= "crpt";
       s.innerHTML = res.code;
       // Use any selector
       $("head").append(s);
       var ww = setInterval(function() {
         clearInterval(ww);
         if (getCrypto !== undefined) {
           continueReady();
         }
        }, 100);
    }
  })*/
   continueReady();
})
function continueReady() {

    $.fn.dataTable.ext.errMode = 'none';
    localStorage.invoiceLocked = 0;
    localStorage.recover = 1;
    $(document).on('click', '.select2-selection.select2-selection--single', function(e) {

        $(this).closest(".select2-container").siblings('select:enabled').select2('open');
    });

    if (localStorage.error !== undefined) {
        var obj = {
            action: "apierror",
            text: localStorage.error
        }
        console.log(obj)
        var ww = setInterval(function() {
            try {

              /*  ws.send(JSON.stringify(obj))*/
                delete localStorage.error;

                clearInterval(ww)
            } catch (err) {
                console.log(err)
            }
        }, 100);
    }
    if (localStorage.shoppingCartContent !== undefined) {
        localToShoppingCartContent();

        setTimeout(function() {
            if (Object.keys(shoppingCartContent).length > 0) {
                localToShoppingCartContent();
                $("#toggleShoppigCart").removeClass("empty");
                $("#lblCartCount").html(" " + Object.keys(shoppingCartContent).length);

            }
        }, 2000);
    }

    try {
        AndroidFullScreen.immersiveMode(function() {}, function() {});
    } catch (err) {

    }
    try {
        var permissions = cordova.plugins.permissions;
        permissions.hasPermission(permissions.WRITE_EXTERNAL_STORAGE, function(status) {
            if (!status.hasPermission) {
                permissions.requestPermission(permissions.WRITE_EXTERNAL_STORAGE, function(status) {});
            }
        })
    } catch (err) {

    }
    window.addEventListener('native.keyboardshow', function(e) {
        cordova.plugins.Keyboard.disableScroll(false);

        var $el = $(document.activeElement);
        if ($el.attr("role") === undefined) {
            setTimeout(function() {
                if ($el.offset().top > e.keyboardHeight) {
                    $('#content').css({
                        marginTop: parseFloat($el.offset().top - e.keyboardHeight + 50) * -1
                    });
                }
            }, 500);
        } else {
            var c = $('.select2-container').last();

            $(c).css({
                marginTop: parseFloat($el.offset().top - e.keyboardHeight + 50) * -1,
            })


        }
        window.addEventListener('native.keyboardhide', function(e) {
            setTimeout(function() {
                $('#content').css({
                    marginTop: 0
                });
                var c = $('.select2-container').last();
                $(c).css({
                    marginTop: "unset"
                })
            }, 1000);
        });
    });

    var canvas = document.getElementById('signature-pad');

    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
    });
    var wee = setInterval(function() {
        if ($("#save-png").length > 0) {
            clearInterval(wee);
            document.getElementById('save-png').addEventListener('click', function() {
            //    $("body").LoadingOverlay("show", optionsLoader);
                try {
                    var data = signaturePad.toDataURL('image/png');
                } catch (err) {

                    $("#customerSignature").attr("src", "");
                    loadedPages.checkout.generateInvoice();
                }
                if (signaturePad.isEmpty()) {
                    $("#customerSignature").attr("src", "");
                    try {
                        loadedPages.checkout.generateInvoice();
                    } catch (err) {
                        alert(err);
                    }
                    return;
                } else {
                    $("#customerSignature").attr("src", data);
                    setTimeout(function() {
                        loadedPages.checkout.generateInvoice();
                    }, 1500);
                }

            });
        }
    }, 200);

    document.getElementById('clear').addEventListener('click', function() {
        signaturePad.clear();
    });

    setTimeout(function() {
        $.getJSON("https://gd.geobytes.com/GetCityDetails?callback=?", function(data) {
            var app = document.URL.indexOf('http://') === -1 && document.URL.indexOf('https://') === -1;
            if (app) {

                StatusBar.hide();
                var vv = userData["deviceid"];
                userData["deviceid"] = device.uuid + " " + vv;
            } else {
                userData.deviceid = "WEB";
            }
            userData["ipaddress"] = data.geobytesipaddress;
            if (localStorage.sp === undefined) {
                userData.emplid = "";
                userData.name = "Anonymus";
            } else {
                var sp = $.parseJSON(localStorage.sp);
                userData.emplid = sp.EmplID;
                userData.name = sp.Employee;
            }
            userData.activity = "Enter application";
            userData.deviceid = userData.deviceid.replace("undefined", "");

            /*    api.call("createlog", function(res) {
                    ws.send(JSON.stringify({action: "reloadadmin"}))
                }, userData, {}, {});*/
        });

    }, 1000)
    $(".app").hide();
    var ad = 0;
    if (app) {
        ad = 50;
    }

    var app = document.URL.indexOf('http://') === -1 && document.URL.indexOf('https://') === -1;

    if (app) {

        StatusBar.hide();
    }
    /*if (app) { */
    try {
        if (app && localStorage.sp !== undefined) {
            document.addEventListener('deviceready', function() {
                cordova.getAppVersion.getVersionNumber().then(function(version) {
                    userData.deviceid = "(v" + version + ")";
                    $("#appversion").html("Version " + version);

                    ws = new ReconnectingWebSocket(version);
                });
            }, false);
        } else {
            ws = new ReconnectingWebSocket("0.0.0");
        }
    } catch (err) {

    }
    /* }*/

    $("#content").css({
        minHeight: $(window).height() - 60,
        height: $(window).height() - 60,
        maxHeight: $(window).height() - 60
    })
    document.getElementById("search").onkeydown = function(evt) {
        evt = evt || window.event;

        if (evt.keyCode == 27) {
            escapeClicked = true;
        }
    };
    document.getElementById("search").addEventListener("keyup", function(event) {
        // Number 13 is the "Enter" key on the keyboard
        if (event.keyCode === 13) {
            // Cancel the default action, if needed
            event.preventDefault();

            doSearch();
        }
    });


    $.each($(".modal-content"), function() {
        $('<button type="button" class="close" style="position:absolute;top:10px;right:15px;" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>').appendTo($(this));
    });
    $('#discountApproved').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#discountApproved').modal("hide");
    $("#sign").on('hide.bs.modal', function() {
        //  $("#save-png").trigger("click");
    });
    $('#discountApproved').on('show.bs.modal', function() {

        $('#discountApproved').find(".close").remove();

    })
    if (localStorage.recover === undefined) {
        for (var key in localStorage) {
            if (key != "sp" && key != "tour" && key != "showRoom" && key != "showRoomName" && key != "url") {
                delete localStorage[key];
            }
        }
    }
    /*  $(".app").hide();
      alert(window.location.hash)
      loadPage(window.location.hash.substring(1));*/

    if (window.location.hash != "") {
        window.location.hash = "";
    } else {
      if (localStorage.salesPerson !== undefined) {
        loadPage("mainpage");
      } else {
        loadPage("homepage");
      }
    }
    $("[login]").bind("click", function(e) {

        if (e.target.nodeName != "SPAN") {
            e.preventDefault();
            e.stopPropagation();
        } else {

        }

    })
    $("[currency_select]").bind("click", function(e) {

        if (e.target.nodeName != "SELECT") {
            e.preventDefault();
            e.stopPropagation();
        } else {

        }

    })

    api.call("getExcangeRates", function(res) {

        $.each(res, function() {
            $("<option value='" + this.CurrencyCode + "' rate='" + (parseFloat(this.ExchangeRate) / 100) + "'>" + this.Currency + "</option>").appendTo($("#currency"));
        })
        $("#currency").select2();
        $('#currency').on('select2:select', function(e) {
            var data = e.params.data;

            $("#currency").hide();

            try {
                loadedPages.invoice.triggerCurrencyChange();
            } catch (err) {

            }
            try {
                loadedPages.diamonds.triggerCurrencyChange();
            } catch (err) {

            }
            $("#ccurenncy").html($("#currency").val());
            try {
                recalculateInvoice();
            } catch (err) {

            }
        });
    }, {}, {})


    $("#paymentsModal").on('show.bs.modal', function(e) {
        setTimeout(function() {
            goToNext();
        }, 1000);
    });



    window.setTimeout(function() {
        $("body").show(500);
    }, 1);
    var td = [];
    api.call("getSalespersons", function(res) {
        spersonsarr = [];
        td = [];
        $.each(res.data, function() {

            td.push({

                id: this.Email,
                name: this.Employee,

            })
            spersonsarr.push({

                id: this.EmplID,
                name: this.Employee,

            })
            $("<option value='" + this.Email + "'>" + this.Employee + "</option>").appendTo("#spersons");
        })
        $("#spersons").select2({
            placeholder: 'Select Sales Person'
        });
        $('#spersons').on('select2:select', function(e) {
            var csel = e.params.data;

            localStorage.EmplID = csel.id;
            localStorage.Employee = csel.text;
            $("#salepersonid").val(csel.id);
            $("#salepersonname").val(csel.text);

        });


    }, {}, {})
    api.call("getShowRooms", function(res) {

        var td1 = [];
        $.each(res.data, function() {
            $("<option value='" + this.showroomid + "'>" + this.name + "</option>").appendTo("#srooms");

        })

        $("#srooms").select2({
            placeholder: 'Select showroom'
        });
        $('#srooms').on('select2:select', function(e) {
            var csel = e.params.data;
            localStorage.showRoom = csel.id;
            localStorage.showRoomName = csel.text;

            $("#showroomid").val(csel.id);
            $("#showroomname").val(csel.text);

        });
    }, {}, {})


    $.validator.addMethod("isSelected", function(value, element) {
        // allow any non-whitespace characters as the host part
        return (element.value != "-1");
    }, "This field is mandatory");

    document.addEventListener("backbutton", onBackKeyDown, false);

    $("[name='vatsettings']").bind("change", function() {

        if ($("#choice1").prop("checked")) {
            $("#vatstatus").html("EU citizen no VAT refund")
            $("#vatstatus").attr("mode", "novatrefund");
        }
        if ($("#choice2").prop("checked")) {
            $("#vatstatus").html("We will provide to you form for 21% VAT refund");
            $("#vatstatus").attr("mode", "vatrefund");
        }
        if ($("#choice3").prop("checked")) {
            $("#vatstatus").html("We will provide to you payable cheque with 21% VAT refund and take 1,35% administrative charge")
            $("#vatstatus").attr("mode", "directrefund");
        }
        recalculateInvoice();
    })
    $.ajax({
        dataType: "json",
        url: "translations/translation_" + "gb" + ".json",
        type: "GET",
        async: false,
        success: function(res) {

            translation = res;
        }
    });

}
var pageUrls = {
    invoice: "catalog",
    diamonds: "diamonds",
    diamonds_catalog: "diamonds_catalog",
    shoppingCart: "shoppingcart",
    checkout: "ckeckout",
    homepage: "home",
    tours: "tours",
    invoices: "invoices",
    customers: "customers",
    addproduct: "addproduct",
    addrefund: "addrefund",
    search: "search",
    openInvoice: "openInvoice",
    payments: "payments",
    login: "login",
    mainpage: "mainpage"
}

function locationHashChanged() {
    $(".modal").modal("hide");
    if (location.hash == "") {
      if (localStorage.salesPerson !== undefined) {
        loadPage("mainpage");
      } else {
        loadPage("homepage");
      }
    }
    if (firstLoad) {
        firstLoad = false;
        if (localStorage.salesPerson !== undefined) {
          loadPage1("mainpage");
        } else {
          loadPage1("homepage");
        }
        return;
    }
    var p = location.hash.substring(1);
    for (var k in pageUrls) {
        if (pageUrls[k] == p) {
            if (fromFunc) {
                loadPage1(po.page, po.addTopages, po.backtocart, po.search);
            } else {
                loadPage1(k);
            }
            fromFunc = false;
            return;
        }
    }
}
var po = {};
var fromFunc = false;
window.onhashchange = locationHashChanged;

function loadPage(page, addToPages = true, backtocart = false, search = {}) {

    //  window.parent.postMessage("setState#" + page, "*");
    //  window.history.replaceState({}, pageUrls[page], pageUrls[page]);
    if (page != "addproduct") {
        $("#addproducticon").hide();
    }
    po.page = page;
    po.addTopages = addToPages;
    po.backtocart = backtocart;
    po.search = search;
    fromFunc = true;
    window.location.hash = pageUrls[page];
}

function loadPage1(page, addToPages = true, backtocart = false, search = {}) {

  var _originalSize = $(window).width() + $(window).height();

  if (!firstLoad && page != "homepage") {
//    $("body").LoadingOverlay("show", optionsLoader);
  } else {


  }
  if (addToPages) {
    pages.push(page);
  }
  if (page != "login") {
    currentPage = page;
  }
  $("#content").css({
    top: 50
  })
  $("#content").html("");
  $('#bback').show();
  $('#bhome').show();
  $('#bclose').hide();
  if (navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry|IEMobile)/) == null) {
    $('#bclose').hide();
  }
  for (var key in loadedPages) {
    delete loadedPages[key];
  }
  $("#tn").find("li").removeClass("active");
  $("[" + page + "]").addClass("active");
  $('.navbar-collapse').collapse('hide');
  $.ajax({
    url: "pages/html/" + page + ".html",
    type: "GET",
    success: function(res) {

      $("#content").html(res);

            $.getScript("pages/js/" + page + ".js", function() {
            if (page == "invoice" || page == "diamonds") {
              loadedPages[page].initialize(search);
            } else {
              loadedPages[page].initialize(backtocart);
            }
            $("input").focus(function(e){
              var $el = $(e.target);
              lastFocused = $el;
              setTimeout(function()  {
                  if(true){

                    $('html, body').animate({scrollTop: $el.offset().top - 100 }, 500);
                  } else{

                  }
                }, 500);
            });
            $("[role='combobox']").focus(function(e){
              var $el = $(e.target);
              lastFocused = $el;
              setTimeout(function()  {
                  if(true){

                    $('html, body').animate({scrollTop: $el.offset().top - 100 }, 500);
                  } else{

                  }
                }, 500);
            });
            $("textarea").focus(function(e){
              var $el = $(e.target);
              lastFocused = $el;
              setTimeout(function()  {
                  if(true){

                    $('html, body').animate({scrollTop: $el.offset().top - 100 }, 500);
                  } else{

                  }
                }, 500);
            });
            $("select").focus(function(e){
              var $el = $(e.target);
              lastFocused = $el;
              setTimeout(function()  {
                  if(true){

                    $('html, body').animate({scrollTop: $el.offset().top - 100 }, 500);
                  } else{

                  }
                }, 500);
            });
             setTimeout(function() {
               if (page != "invoice" && page != "diamonds") {
                 $("body").LoadingOverlay("hide");
               }
               if (!$("#mainModal")[0].hasAttribute("update")) {
                  $("#mainModal").modal("hide");
                } else {
                  if ($("#mainModal").attr("update") != "1") {
                    $("#mainModal").modal("hide");
                  } else {
                    $("#mainModal").attr("update", "1");
                  }
                }
             }, 1500)
          });


    }
  })
}
function closeApp() {
    if (navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry|IEMobile)/) != null) {
        swal({
            type: "question",
            text: 'Are you sure you wish to exit app?',
            showCancelButton: true,
            showCloseButton: true
        }).then((result) => {
            if (result.value) {
                navigator.app.exitApp();
            }
        })
    } else {
        return false;
    }
}

function goHome() {
    pages = [pages[0], pages[1]];
    loadPage(pages[pages.length - 1], false);
}

function backPage() {
    pages = pages.slice(0, -1);
    loadPage(pages[pages.length - 1], false);
}

function logout() {
    $("#blue").css({
        display: "none"
    })
    localStorage.clear();
    //  loadPage("login");
}
var pages = [];

function onBackKeyDown() {
    var app = document.URL.indexOf('http://') === -1 && document.URL.indexOf('https://') === -1;
    if (app) {

        StatusBar.hide();
    }
    if (in_barcode_scan) {
        in_barcode_scan = false;
        return false;
    }
    if (pages.length == 1) {
        return false;
    }

    pages.splice(-1, 1)
    var p = pages[pages.length - 1];
    var pp = "";
    for (var k in pageUrls) {
        if (k == p) {
            pp = pageUrls[k];
        }
    }

    window.location.hash = pp;
}

function textToBase64Barcode(text) {
    var canvas = document.createElement("canvas");
    JsBarcode(canvas, text, {
        format: "CODE128",
        height: 20,
        fontSize: 13
    });
    return canvas.toDataURL("image/png");
}

function toDataURL(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
        var reader = new FileReader();
        reader.onloadend = function() {
            callback(reader.result);
        }
        reader.readAsDataURL(xhr.response);
    };
    xhr.open('GET', url);
    xhr.responseType = 'blob';
    xhr.send();
}

function addToInvoice(row) {

    if (shoppingCartContent[row["SerialNo"]] !== undefined && !row["SerialNo"].indexOf("9999") == 0) {
        showModal({
            title: "Item with serial " + row["SerialNo"] + " already in cart.",
            confirmButtonText: "CONTINUE",
            showCancelButton: false
        })
        return;
    }
    if (row["SerialNo"].indexOf("9999") == 0) {
        while (shoppingCartContent[row["SerialNo"]] !== undefined) {
            row["SerialNo"] = (parseInt(row["SerialNo"]) + 1).toString();

        }
    }
    if (row["invoiceno"] !== undefined) {
        row["SerialNo"] = (parseInt(row["SerialNo"]) + 1).toString();
        row["SerialNo"] += " (invoice no/date: " + row["invoiceno"] + ")";
    }
    //alert(row["Discount"])
    if (row["Discount"] == "0") {
        row["Discount"] = "0%";
    }
    if (row["Discount"] == "0%") {
        var realPrice = row["SalesPrice"];
        row["discountLocked"] = false;
    } else {
        var pr = parseFloat(row["SalesPrice"]);
        var ds = parseFloat(row["Discount"]);
        var realPrice = pr - ((pr / 100) * ds);
        row["discountLocked"] = true;
        row["Discount"] += "%";
    }
    if (row["quantity"] === undefined) {
        row["quantity"] = 1;
    }
    if (row["available"] === undefined) {
        row["available"] = 1;
    }
    row["additionalDiscount"] = "";
    row["startRealPrice"] = realPrice;
    row["realPrice"] = realPrice;
    row["toPay"] = parseInt(row["quantity"]) * realPrice;
    row["productName"] += "<br />";

    shoppingCartContent[row["SerialNo"]] = row;
    shoppingCartToLocalStorage();


    $("#lblCartCount").html(" " + Object.keys(shoppingCartContent).length);
    $("#toggleShoppigCart").removeClass("empty");
    haveToSave = true;
    /*if ($("#invoiceBody").find("[serialno='" + row.SerialNo + "']").length > 0) {
    swal({
      type: "error",
      text: "Items with Serial No " + row.SerialNo + " already in Bag"
    })
    return false;
  }
  var exr = $("#currency").find("option:selected").attr("rate");

  var tr = "<tr serialno='" + row.SerialNo + "' productdata><td style='padding: 5px;'>" + row["imageURL"] + "</td>";
  tr += "<td colspan='3'>";
  tr += "<p style='max-width:100%;word-break:break-word;'>" + row["productName"] + "</p></td></tr>";
  tr += "<tr invoicedata>";
  tr += "<td style='text-align: right;' euro='" + row["SalesPrice"] + "' value='" + row["SalesPrice"] + "' price>" + (parseFloat(row["SalesPrice"]) * 1).toLocaleString('nl-NL', { style: 'currency', currency:  "EUR"  }) + "</td>";
  tr += "<td style='text-align: right;padding-right:3px;'><input discount style='width:50px;text-align:right;' value='" + row["Discount"] + "%' type='text' onchange='recalculateInvoice(this);' /></td>";
  tr += "<td style='text-align: right;' total realvalue='" + parseFloat(row["SalesPrice"]) + "'>" + parseFloat(row["SalesPrice"]).toLocaleString('nl-NL', { style: 'currency', currency: "EUR" }) + "</td>";
  tr += "<td style='width:30px;text-align:right;' onclick='deleteRow(this);'><div style='max-width:30px;'><a href='#'><i class='fa fa-trash fa-2x m-r-5'></i></a></div></td></tr>";
  $(tr).appendTo($("#invoiceBody"));
  recalculateInvoice();
*/
  if (loadedPages.shoppingCart !== undefined) {
    loadedPages.shoppingCart.initialize();
  }
}

function addToInvoiceFromSaved(row) {

    if (shoppingCartContent[row["SerialNo"]] !== undefined && !row["SerialNo"].indexOf("9999") == 0) {
        showModal({
            title: "Item with serial " + row["SerialNo"] + " already in cart.",
            confirmButtonText: "CONTINUE",
            showCancelButton: false
        })
        return;
    }
    if (row["SerialNo"].indexOf("9999") == 0) {
        while (shoppingCartContent[row["SerialNo"]] !== undefined) {
            row["SerialNo"] = (parseInt(row["SerialNo"]) + 1).toString();

        }
    }
    //alert(row["Discount"])
    if (row["Discount"] == "0") {
        row["Discount"] = "0%";
    }
    if (row["Discount"] == "0%") {
        var realPrice = row["SalesPrice"];
        row["discountLocked"] = false;
    } else {
        var pr = parseFloat(row["SalesPrice"]);
        var ds = parseFloat(row["Discount"]);
        var realPrice = pr - ((pr / 100) * ds);
        row["discountLocked"] = false;

    }
    if (row["quantity"] === undefined) {
        row["quantity"] = 1;
    }
    if (row["available"] === undefined) {
        row["available"] = 1;
    }
    row["additionalDiscount"] = "";
    row["startRealPrice"] = parseFloat(row["SalesPrice"]);
    row["realPrice"] = realPrice;
    row["productName"] += "<br />" + row["CompName"];
    console.log(row);
    shoppingCartContent[row["SerialNo"]] = row;

    $("#lblCartCount").html(" " + Object.keys(shoppingCartContent).length);
    $("#toggleShoppigCart").removeClass("empty");
    haveToSave = true;
}

function recalculateInvoice(obj) {
    var invoicetotal = 0;
    var total = 0;
    $.each($("#invoiceBody").find("tbody").find("tr[invoicedata]"), function() {
        var qty = 1;
        var thenum = $(this).find("[price]").attr("euro").replace(/^\D+/g, '');
        var n = thenum.replace(/\,/g, "");
        var exr = parseFloat($("#currency").find("option:selected").attr("rate"));
        var price = parseFloat(n) * exr;
        $(this).find("[price]").html(price.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        var tdisc = 0;
        var pdisc = 0;
        if ($(this).find("[discount]").val() != "") {
            if ($(this).find("[discount]").val().indexOf("%") > -1) {
                pdisc = parseFloat($(this).find("[discount]").val().replace("%", ""));
            } else {
                tdisc = parseFloat($(this).find("[discount]").val());
            }
        }
        var total = qty * price;
        if (tdisc > 0) {
            total = total - tdisc;
        }
        if (pdisc > 0) {
            total = total - ((total / 100) * pdisc);
        }
        invoicetotal += total;
        $(this).find("[total]").html(total.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        $(this).find("[total]").attr("realvalue", total);
    })

    $("[invoicetotal]").html(invoicetotal.toLocaleString('nl-NL', {
        style: 'currency',
        currency: $("#currency").val()
    }));
    tdisc = 0;
    pdisc = 0;
    if ($("#invoicediscount").val() != "") {
        if ($("#invoicediscount").val().indexOf("%") > -1) {
            pdisc = parseFloat($("#invoicediscount").val().replace("%", ""));
        } else {
            tdisc = parseFloat($("#invoicediscount").val());
        }
    }
    var idue = invoicetotal;
    if (tdisc > 0) {
        idue = invoicetotal - tdisc;
    }
    if (pdisc > 0) {
        idue = invoicetotal - ((invoicetotal / 100) * pdisc);
    }

    var ff = 0;
    var rfnd = (idue / 100) * (2100 / 121);
    var vatc = (idue / 100) * (2100 / 121);;
    if ($("#vatstatus").attr("mode") != "directrefund") {
        $("[withoutvat]").closest("tr").show();
        $("[vat]").closest("tr").show();
        $("[vatrefund]").closest("tr").hide();
        $("[admincharge]").closest("tr").hide();
        $("[vatrefund]").attr("realvalue", "0");
        $("[vatrefund]").html(ff.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        $("[admincharge]").attr("realvalue", 0);
        $("[admincharge]").html(ff.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        $("[invoicetotal]").attr("realvalue", idue);
        $("[invoicetotal]").html(idue.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        $("[withoutvat]").attr("realvalue", rfnd);
        $("[withoutvat]").html((idue - rfnd).toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        $("[vat]").attr("realvalue", rfnd);
        $("[vat]").html(rfnd.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
    } else {

        $("[withoutvat]").closest("tr").hide();
        $("[vatrefund]").closest("tr").show();
        $("[vat]").closest("tr").show();
        $("[admincharge]").closest("tr").show();

        $("[invoicetotal]").attr("realvalue", idue);
        $("[invoicetotal]").html(idue.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        $("[withoutvat]").attr("realvalue", rfnd);
        $("[withoutvat]").html((idue - rfnd).toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        var achrg = (idue / 100) * 1.35;
        var rfnd = (idue / 100) * (2100 / 121);
        var vatc = ((idue + achrg) / 100) * (2100 / 121);

        idue = idue + achrg - rfnd;
        $("[admincharge]").attr("realvalue", achrg);
        $("[admincharge]").html(achrg.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        $("[vat]").attr("realvalue", vatc);
        $("[vat]").html(vatc.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
        $("[vatrefund]").attr("realvalue", rfnd);
        $("[vatrefund]").html(rfnd.toLocaleString('nl-NL', {
            style: 'currency',
            currency: $("#currency").val()
        }));
    }

    var t = (idue.toLocaleString('nl-NL', {
        style: 'currency',
        currency: $("#currency").val()
    }));
    $("[invoicedue]").attr("realvalue", idue);
    $("[invoicedue]").html(idue.toLocaleString('nl-NL', {
        style: 'currency',
        currency: $("#currency").val()
    }));

    var wwv = idue - rfnd;
    $("[withoutvat]").attr("realvalue", idue);
    $("[withoutvat]").html(wwv.toLocaleString('nl-NL', {
        style: 'currency',
        currency: $("#currency").val()
    }));

    $("[vat]").attr("realvalue", vatc);
    $("[vat]").html(vatc.toLocaleString('nl-NL', {
        style: 'currency',
        currency: $("#currency").val()
    }));

    $("#paymentsTable").find("tbody").find("tr").eq(1).find("input").attr("realvalue", idue)
    $("#paymentsTable").find("tbody").find("tr").eq(1).find("input").eq(0).val(t);
}

function deleteRow(obj) {
    var tr = $(obj).closest("tr");
    swal({
        type: "question",
        html: tr.parent().find("[productdata]").find("td").eq(0).html() + "<br /><span>Remove </span>" + tr.parent().find("[productdata]").find("td").eq(1).html() + " <span>from invoice?</span>",

        showCancelButton: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: true,
        showCloseButton: true
    }).then((result) => {
        if (result.value) {
            tr.prev("[productdata]").remove();
            tr.remove();

            if ($("[invoicedata]").length == 0) {
                $("#toggleShoppigCart").addClass("empty");
            } else {
                $("#lblCartCount").html(" " + $("[invoicedata]").length + " ");
            }
            recalculateInvoice();
        }
    })

}
var currentScanned = {};
function scanResult(code) {
//  $("#interactive").hide();
$("#scanbutton").popover('hide');
  var obj = {
      SerialNo: code
  }
  if ($("#invoiceBody").find("[serialno='" + code + "']").length > 0) {
      showModal({
          type: "error",
          title: "Items with Serial No " + code + " already in Bag"
      })
      return false;
  }
  api.call("getScannedProduct", function(res) {

      if (res[0] == undefined) {
          showModal({
              type: 'error',
              title: "No product with this serial.",
              showCancelButton: false,
              confirmButtonText: "CONTINUE"

          })
      }

      if (res[0].image != "" && res[0].image != null) {
          var img = $("<img src='https://costercatalog.com/catalog/images/" + res[0].image + "' style='width:100px;' />");
      } else {
          var img = $("<img src='https://costercatalog.com/coster/www/images/crown.png' style='width:100px;' />");
      }
      var exr = $("#currency").find("option:selected").attr("rate");
      html = img[0].outerHTML;
      html += "<div style='position:absolute;top:10px;left:155px;color:#ADADAD;'>" + res[0].SerialNo + "<br />";
      html += "<span style='color:black;font-size:13px;'><b>" + res[0].SerialName + "</b></span>";
      if (res[0].CompName1 !== undefined && res[0].CompName1 !== null) {
          html += "<br /><div style='color:black;font-size:13px;'>" + res[0].CompName1;
      }
      if (res[0].Discount > 0) {
          html += "<br /><div style='float:left;margin-top:5px;'><span style='color:red;'><b>" + res[0].Discount + "% </b></span><span style='text-decoration:line-through;'>" + (parseFloat(res[0].SalesPrice) * 1).toLocaleString("nl-NL", {
              style: 'currency',
              currency: "EUR"
          }) + "</span>&nbsp;&nbsp;";
          html += "<span>" + (parseFloat(res[0].realPrice) * 1).toLocaleString("nl-NL", {
              style: 'currency',
              currency: "EUR"
          }) + "</span></div>";
      } else {
          html += "<br /><div style='float:left;margin-top:5px;'><span>" + (parseFloat(res[0].realPrice) * 1).toLocaleString("nl-NL", {
              style: 'currency',
              currency: "EUR"
          }) + "</span></div>";
      }
      html += '</div><div><br /><button type="button" onclick="addToInvoice(currentScanned);" data-dismiss="modal" style="" class="btn-bigblack"><span style="width:100%;text-align:left;margin-top:5px;">ADD TO BAG</span></button>';
      html += "</div></div>";
      $("#asc_body").html("");
      $(html).appendTo($("#asc_body"));
      if ((res[0]["Discount"]).indexOf("%") == -1) {
          res[0]["Discount"] += "%";
      }
      var data = res[0];
      if (res[0].image != "" && res[0].image != null) {
          data["image"] = res[0].image;
      } else {
          data["image"] = "crown.png";
      }
      currentScanned = {
          imageURL: "<img style='width:100px;height:auto;' src='https://costercatalog.com/catalog/images/" + data.image + "' />",
          img: "<img style='width:250px;height:auto;' src='https://costercatalog.com/catalog/images/" + data.image + "' />",
          SerialNo: data.SerialNo,
          ItemID: data.SerialNo,
          Discount: data.Discount,
          productName: data.SerialNo + " " + data.SerialName,
          SalesPrice: data["SalesPrice"],
          available: data["OnhandQnt"],
          Discount: data["Discount"],
          CompName: ""
      }

      $("#afterScan").modal("show");
      return;

  }, obj, {})
}
function scan() {
  if (detectMobile()) {
    $("#interactive").toggle();
  } else {
    getSerial(false, true);
  }
  return;


}

function getSerial(search = false, notavailable = false) {
    //  $('#mainModal').modal("hide");
    $("#eesc").val("")
    try {
        showModal({
            type: "error",
            title: (!notavailable) ? "Scan Failed" : "Scanner not available",
            content: "<span style='font-size: 17px;'>Please enter the serial ID below</span><br /><input class='form-control' id='eesc' type='text' />",
            allowBackdrop: false,
            showCancelButton: false,
            confirmButtonText: "SEARCH",
            confirmCallback: function() {
                scanResult($("#eesc").val());
            }
        });
    } catch (err) {

    }
}

function checkSteps() {
    if ($("[invoicedata]").length == 0) {
        showModal({
            type: "error",
            title: "No items in invoice.",

        })
        return false;
    }
    if (localStorage.sp === undefined) {

        $("#spf")[0].reset();
        showModal({
            type: "error",
            title: "You must log in to proceed checkout",
            confirmCallback: function() {
                $("#login").modal("show");
            }
        })
        return false;
    } else {
      var ss = $.parseJSON(localStorage.sp);
      if (ss.SalesApp == "2") {
        $("#bhspersons").show();
      }
    }
    if (localStorage.tour === undefined) {
        showModal({
            type: "error",
            title: "Select tour",
            confirmCallback: function() {
                loadPage("tours");
            }
        })

    }

}



function prepareLogin() {

}
$('#login').on('show.bs.modal', function() {
    $("#showroomid").val("");
    $("#srooms").val("").trigger("change");
})
$("#spf").validate({
    ignore: [],
    rules: {
        spersons: {
            required: true,
            isSelected: true
        },
        srooms: {
            required: true,
            isSelected: true
        },
        pin: {
            required: true
        }
    },
    messages: {
        showroomid: "Please choose valid showroom",
        salepersonid: "Please choose Sales Person"
    },
    submitHandler: function(form) {

      var obj = {
          username: $("#spersons").val(),
          password: $("#pin").val(),
        }
        api.call("loginSalesApp", function(res) {
          if (res.status == "error") {

            $("#login").modal("hide");
            showModal({
                type: "error",
                title: res.error,
                showCancelButton: false,
                showClose: false,
                allowBackdrop: false,
                confirmButtonText: "TRY AGAIN",
                confirmCallback: function() {
                    $("#login").modal("show");
                }
            })
            return;
         } else {
                localStorage.sp = JSON.stringify(res.sp);

                localStorage.originalsp = JSON.stringify(res.sp);
                userData.emplid = res.sp.EmplID;
                userData.name = res.sp.Employee;
                userData.activity = "Login";
                var csel = $('#srooms').select2('data')[0];
                localStorage.showRoom = csel.id;
                localStorage.showRoomName = csel.text;


                localStorage.salePersonName = $("#salespersonname").val();
                if (app && localStorage.sp !== undefined) {
                    cordova.getAppVersion.getVersionNumber().then(function(version) {
                        ws = new ReconnectingWebSocket(version);
                        if (userData.deviceid.indexOf(version) == -1) {
                            userData.deviceid = userData.deviceid + " " + "(v" + version + ")";
                        }
                    });
                } else {
                    ws = new ReconnectingWebSocket("0.0.0");
                }
                userData.activity = "Login";
                setTimeout(function() {

                    userData.deviceid = userData.deviceid.replace("undefined", "");
                  /*  api.call("createlog", function() {
                        ws.send(JSON.stringify({
                            action: "reloadadmin"
                        }))
                    }, userData, {}, {});*/
                }, 3000);
                showModal({
                    type: "ok",
                    title: "Looking good " + res.sp.Employee,
                    allowBackdrop: false,
                    showCancelButton: false,
                    showClose: false,
                    confirmCallback: function() {
                        if ($("#login")[0].hasAttribute("nextpage")) {
                            loadPage($("#login").attr("nextpage"));
                        }

                        if (res.sp.SalesApp == "2") {
                          $("#bhspersons").show();
                        } else {
                            $("#bhspersons").hide();
                        }
                    },
                    confirmButtonText: "CONTINUE"
                })

                $("#login").modal("hide");
                $("[login]").hide();
                $("#ename").html(res.sp.Employee);
                $("#ename1").html(res.sp.Employee);

                //ovde

                $("[logout]").show();
            }
        }, obj, {}, {});
    }

});

function login() {

}

function wrongPassword() {
    $("#login").modal("hide");

}

var documentName = "";

function mail() {

    swal({
        type: "info",
        text: "Sending mail",
        showCancelButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false
    })
    if (invoiceID == "") {
        var nm = "invoice_" + (new Date()).getTime();
        documentName = nm;
    } else {
        var nm = documentName;
    }

    if (invoiceID == "") {

        var obj = {
            customerid: $("#customerid").val(),
            showroom: localStorage.showRoomName,
            tourNo: $.parseJSON(localStorage.tour)["ProjId"],
            total: localStorage.total,
            salesPerson: $.parseJSON(localStorage.sp)["Employee"],
            salePersonId: $.parseJSON(localStorage.sp)["EmplID"],
            discount: localStorage.invoiceDiscount,
            dueAmount: localStorage.payNoRefund,
            pdf: nm + "_" + "gb" + ".pdf",
            documentName: nm,
            documentLanguages: "gb",
            showroomid: localStorage.showRoom,
            tourNo: $.parseJSON(localStorage.tour)["ProjId"]
        }
        documentName = nm;
    } else {

        var obj = {
            invoiceid: invoiceID,
            language: "gb",
            showroom: localStorage.showRoomName,
            showroomid: localStorage.showRoomName,
            tourNo: $.parseJSON(localStorage.tour)["ProjId"],
            total: $("[invoicetotal]").attr("realvalue"),
            discount: $("#invoicediscount").val(),
            dueAmount: $("[invoicedue]").attr("realvalue"),
            salesPerson: localStorage.salePersonName,
            salePersonId: localStorage.salespersonid,
        }
    }
    api.call(((invoiceID == "") ? "insertInvoice" : "updateInvoiceDocuments"), function(res) {

        if (invoiceID == "") {

            invoiceID = res.invoiceid;
        }
        toDataURL('images/logo.png', function(dataUrl) {

            var bc = textToBase64Barcode(res.invoiceid);
            var items = [];
            items.push([{
                    text: translation["serial"],
                    fontSize: 8
                },
                {
                    text: translation["article"],
                    fontSize: 8
                },
                {
                    text: translation["description"],
                    fontSize: 8
                },
                {
                    text: translation["qty"],
                    fontSize: 8,
                    alignment: "right"
                },
                {
                    text: translation["price"],
                    fontSize: 8,
                    alignment: "right"
                },
                {
                    text: translation["discount"],
                    fontSize: 8,
                    alignment: "right"
                },
                {
                    text: translation["total"],
                    fontSize: 8,
                    alignment: "right"
                }
            ]);

            var toInvoiceBody = [];
            for (var key in shoppingCartContent) {
                var data = shoppingCartContent[key];
                var txt = data.productName;

                var obj = {
                    vvv: parseFloat(data.realPrice),
                    invoiceid: invoiceID,
                    serialno: txt.split(" ")[0],
                    item: txt.substring(txt.indexOf(" ")),
                    quantity: "1",
                    price: parseFloat(data.SalesPrice),
                    discount: data.Discount,

                }
                toInvoiceBody.push(obj);

                items.push(
                    [{
                            text: data.SerialNo,
                            fontSize: 8
                        },
                        "",
                        {
                            text: txt.substring(txt.indexOf(" ")),
                            fontSize: 8
                        },
                        {
                            text: "1",
                            fontSize: 8,
                            alignment: "right"
                        },
                        {
                            text: parseFloat(data.SalesPrice).toLocaleString("nl-NL", {
                                style: 'currency',
                                currency: "EUR"
                            }),
                            fontSize: 8,
                            alignment: "right"
                        },
                        {
                            text: data.Discount,
                            fontSize: 8,
                            alignment: "right"
                        },
                        {
                            text: parseFloat(data.realPrice).toLocaleString("nl-NL", {
                                style: 'currency',
                                currency: "EUR"
                            }),
                            fontSize: 8,
                            alignment: "right"
                        }
                    ]
                )
            }
            var tdl = {
                invoiceid: invoiceID
            }
            api.call("deleteInvoiceBody", function(res) {
                $.each(toInvoiceBody, function() {
                    var obj = {};
                    var ths = this;
                    api.call("insertInvoiceBody", function(res) {}, ths, {}, {})
                })
            }, tdl, {}, {})
            if (localStorage.invoiceDiscount != "") {
                var dsc = localStorage.invoiceDiscount;
                if (localStorage.invoiceDiscount.indexOf("%") == -1) {
                    dsc = parseFloat(dsc).toLocaleString("nl-NL", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                }
                items.push(
                    [{
                            text: translation["payment"],
                            fontSize: 8
                        },
                        {
                            text: translation["currency"],
                            alignment: "right",
                            fontSize: 8
                        },
                        {
                            text: translation["Amount"],
                            alignment: "left",
                            fontSize: 8
                        },
                        {
                            text: "",
                            fontSize: 8,
                            alignment: "right",
                            border: [true, false, true, false],
                            fillColor: "#e7e7e7"
                        },
                        {
                            text: "",
                            border: [false, false, false, false],
                            fontSize: 8,
                            alignment: "right"
                        },
                        {
                            text: "Discount" + ": ",
                            border: [false, false, false, false],
                            fontSize: 8,
                            alignment: "right"
                        },
                        {
                            text: dsc,
                            border: [false, false, true, false],
                            fontSize: 8,
                            alignment: "right"
                        }
                    ]
                )
            }
            if (localStorage.directRefund == "1") {
                var vex = localStorage.vatchargeexcl;
            } else {
                var vex = localStorage.vatexcluded;
            }

            items.push(
                [{
                        text: translation["payment"],
                        fontSize: 8
                    },
                    {
                        text: translation["currency"],
                        alignment: "right",
                        fontSize: 8
                    },
                    {
                        text: translation["Amount"],
                        alignment: "left",
                        fontSize: 8
                    },
                    {
                        text: "",
                        fontSize: 8,
                        alignment: "right",
                        border: [true, false, true, false],
                        fillColor: "#e7e7e7"
                    },
                    {
                        text: "",
                        border: [false, false, false, false],
                        fontSize: 8,
                        alignment: "right"
                    },
                    {
                        text: ((localStorage.directRefund == "0") ? "Excluding VAT" : "Excluding VAT") + ": ",
                        border: [false, false, false, false],
                        fontSize: 8,
                        alignment: "right"
                    },
                    {
                        text: parseFloat(localStorage.vatexcluded).toLocaleString("nl-NL", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }),
                        border: [false, false, true, false],
                        fontSize: 8,
                        alignment: "right"
                    }
                ]
            )
            var toInvoicePayments = [];
            console.log(payments)
            for (var key in payments) {
                var data = payments[key];
                var fl = parseFloat(data.amount);
                var pyd = fl.toLocaleString("nl-NL", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                var obj = {
                    invoiceid: invoiceID,
                    type: data.paymentID,
                    typeName: data.paymentMethod,
                    currency: "EUR",
                    currencyName: "Euro",
                    amount: fl
                }
                toInvoicePayments.push(obj);
                if (data.paymentID == "7") {
                    if (data.amount < -5) {
                        fl = fl * -1;
                    }
                    items.push(
                        [{
                                text: ((data.amount < -5) ? "Change" : data.paymentMethod),
                                border: [true, true, false, true],
                                alignment: "right",
                                fontSize: 8
                            },
                            {
                                text: "€",
                                alignment: "right",
                                fontSize: 8
                            },
                            {
                                text: pyd,
                                fontSize: 8,
                                alignment: "left"
                            },
                            {
                                text: "",
                                fontSize: 8,
                                alignment: "right",
                                border: [true, false, true, false],
                                fillColor: "#e7e7e7"
                            },
                            {
                                text: "",
                                border: [false, false, false, false],
                                fontSize: 8,
                                alignment: "right"
                            },
                            {
                                text: "",
                                border: [false, false, false, false],
                                fontSize: 8,
                                alignment: "right"
                            },
                            {
                                text: "",
                                border: [false, false, true, false],
                                fontSize: 8,
                                alignment: "right"
                            }
                        ]
                    )
                } else {
                    items.push(
                        [{
                                text: data.paymentMethod,
                                border: [true, true, false, true],
                                alignment: "right",
                                fontSize: 8
                            },
                            {
                                text: "€",
                                alignment: "right",
                                fontSize: 8
                            },
                            {
                                text: pyd,
                                fontSize: 8,
                                alignment: "left"
                            },
                            {
                                text: "",
                                fontSize: 8,
                                alignment: "right",
                                border: [true, false, true, false],
                                fillColor: "#e7e7e7"
                            },
                            {
                                text: "",
                                border: [false, false, false, false],
                                fontSize: 8,
                                alignment: "right"
                            },
                            {
                                text: "",
                                border: [false, false, false, false],
                                fontSize: 8,
                                alignment: "right"
                            },
                            {
                                text: "",
                                border: [false, false, true, false],
                                fontSize: 8,
                                alignment: "right"
                            }
                        ]
                    )
                }


            }
            var tdl = {
                invoiceid: invoiceID
            }
            api.call("deleteInvoicePayments", function(res) {
                $.each(toInvoicePayments, function() {
                    var ths = this;
                    api.call("insertInvoicePayments", function(res) {

                    }, ths, {}, {})
                })
            }, tdl, {}, {})

            if (localStorage.directRefund == "1") {
                var due = parseFloat(localStorage.payWithRefund);
            } else {
                var due = parseFloat(localStorage.payNoRefund);
            }
            var rct = 2100 / 121;
            var vl = due;
            var vt = (vl / 100) * rct;
            var wv = vl - vt;

            items.push(
                [{
                        text: "",
                        border: [false, false, false, false],
                        fontSize: 8,
                    },
                    {
                        text: "",
                        fontSize: 8,
                        border: [false, false, false, false]
                    },
                    {
                        text: "",
                        fontSize: 8,
                        border: [false, false, true, false]
                    },
                    {
                        text: "",
                        border: [false, false, true, false],
                        fontSize: 8,
                        fillColor: "#e7e7e7",
                        alignment: "right"
                    },
                    {
                        text: "",
                        fontSize: 8,
                        border: [false, false, false, true]
                    },
                    {
                        text: ((localStorage.directRefund == "0") ? translation["vat21"] : "VAT(refund)") + ": ",
                        border: [false, false, false, true],
                        fontSize: 8,
                        bold: true,
                        alignment: "right"
                    },
                    {
                        text: vt.toLocaleString("nl-NL", {
                            style: 'currency',
                            currency: 'EUR'
                        }),
                        border: [false, false, true, true],
                        fontSize: 8,
                        bold: true,
                        alignment: "right"
                    }
                ]);
            items.push(
                [{
                        text: "",
                        border: [false, false, false, false],
                        fontSize: 8,
                    },
                    {
                        text: "",
                        border: [false, false, false, false],
                        fontSize: 8
                    },
                    {
                        text: "",
                        border: [false, false, true, false],
                        fontSize: 8
                    },
                    {
                        text: "",
                        border: [false, false, true, true],
                        fontSize: 8,
                        fillColor: "#e7e7e7",
                        alignment: "right"
                    },
                    {
                        text: "",
                        fontSize: 8,
                        border: [false, false, false, true]
                    },
                    {
                        text: translation["Amount"] + ": ",
                        border: [false, false, false, true],
                        fontSize: 8,
                        bold: true,
                        alignment: "right"
                    },
                    {
                        text: due.toLocaleString("nl-NL", {
                            style: 'currency',
                            currency: 'EUR'
                        }),
                        border: [false, false, true, true],
                        fontSize: 8,
                        bold: true,
                        alignment: "right"
                    }
                ]);
            console.log(items)
            var docDefinition = {
                pageSize: "A4",
                header: [

                ],
                content: [{
                        margin: [227, -20, 0, 0],
                        image: dataUrl,
                        width: 100

                    },

                    {
                        margin: [400, -40, 0, 0],
                        image: bc
                    },
                    {

                        table: {
                            headerRows: 1,
                            widths: [125, '*'],
                            body: [
                                [{
                                    borders: [true, true, true, true],
                                    italics: true,
                                    text: translation["stamp"],
                                    alignment: "center"
                                }, {
                                    italics: true,
                                    text: translation["enter_capitol"],
                                    alignment: "center"
                                }],
                                [{},
                                    [{
                                        table: {
                                            widths: ['auto', 340],
                                            body: [
                                                [{
                                                    text: translation["name"] + ":  ",
                                                    border: [false, false, false, false],
                                                    italics: true,
                                                    alignment: "right"
                                                }, {
                                                    text: $("#firstName").val() + " " + $("#lastName").val(),
                                                    border: [false, false, false, true]
                                                }],

                                            ]
                                        },

                                    }]
                                ],
                                [{},
                                    [{
                                        table: {
                                            widths: ['auto', 330],
                                            body: [
                                                [{
                                                    text: translation["address"] + ": ",
                                                    border: [false, false, false, false],
                                                    italics: true,
                                                    alignment: "right"
                                                }, {
                                                    text: $("#address1").val(),
                                                    border: [false, false, false, true]
                                                }],

                                            ]
                                        },

                                    }]
                                ],
                                [{},
                                    [{
                                        table: {
                                            widths: ['auto', 80, 'auto', 50, 'auto', 75],
                                            body: [
                                                [{
                                                        text: translation["city"] + ": ",
                                                        border: [false, false, false, false],
                                                        italics: true,
                                                        alignment: "right"
                                                    }, {
                                                        text: $("#city").val(),
                                                        border: [false, false, false, true]
                                                    },
                                                    {
                                                        text: translation["zip"] + ": ",
                                                        border: [false, false, false, false],
                                                        italics: true,
                                                        alignment: "right"
                                                    }, {
                                                        text: $("#zip").val(),
                                                        border: [false, false, false, true]
                                                    },
                                                    {
                                                        text: translation["country"] + ": ",
                                                        border: [false, false, false, false],
                                                        italics: true,
                                                        alignment: "right"
                                                    }, {
                                                        text: "",
                                                        border: [false, false, false, true]
                                                    }
                                                ]

                                            ]
                                        },

                                    }]
                                ],
                                [{},
                                    [{
                                        table: {
                                            widths: ['auto', 100],
                                            body: [
                                                [{
                                                        text: translation["telephone"] + ": ",
                                                        border: [false, false, false, false],
                                                        italics: true,
                                                        alignment: "right"
                                                    }, {
                                                        text: $("#telephone").val(),
                                                        border: [false, false, false, true]
                                                    }
                                                    //         {text: translation["passport"] + ": ", italics: true,border:[false,false,false,false],alignment: "right"}, {text: $("#passport").val(),border:[false,false,false,true] },
                                                ]
                                            ]
                                        },

                                    }]
                                ],
                                [{},
                                    [{
                                        table: {
                                            widths: ['auto', 318],
                                            body: [
                                                [{
                                                    text: translation["email"] + ": ",
                                                    border: [false, false, false, false],
                                                    italics: true,
                                                    alignment: "right"
                                                }, {
                                                    text: $("#email").val(),
                                                    border: [false, false, false, true]
                                                }],
                                                [{
                                                    text: translation["hotel"] + ": ",
                                                    border: [false, false, false, false],
                                                    italics: true,
                                                    alignment: "right"
                                                }, {
                                                    text: $("#hotel").val(),
                                                    border: [false, false, false, true]
                                                }]
                                            ],
                                        },

                                    }]
                                ], // sledeci // sledeci // sledeci // sledeci
                            ]
                        },
                        layout: {
                            hLineWidth: function(i, node) {
                                return (i === 0 || i === node.table.body.length) ? 2 : 1;
                            },
                            vLineWidth: function(i, node) {
                                return (i === 0 || i === node.table.widths.length) ? 2 : 1;
                            },
                            hLineColor: function(i, node) {
                                return (i === 0 || i === node.table.body.length) ? 'black' : 'white';
                            },
                            vLineColor: function(i, node) {
                                return (i === 0 || i === node.table.widths.length) ? 'black' : 'black';
                            },
                            // hLineStyle: function (i, node) { return {dash: { length: 10, space: 4 }}; },
                            // vLineStyle: function (i, node) { return {dash: { length: 10, space: 4 }}; },
                            // paddingLeft: function(i, node) { return 4; },
                            // paddingRight: function(i, node) { return 4; },
                            // paddingTop: function(i, node) { return 2; },
                            // paddingBottom: function(i, node) { return 2; },
                            // fillColor: function (rowIndex, node, columnIndex) { return null; }
                        }
                    },
                    {
                        table: {
                            layout: 'lightHorizontalLines',
                            headerRows: 1,
                            widths: [60, 60, 200, 20, 50, 40, 50],
                            body: items
                        }
                    },
                    {
                        fontSize: 9,
                        text: translation["vatincl"],
                        bold: true,
                        italics: false,
                        alignment: "right"
                    },
                    {
                        fontSize: 9,
                        text: translation["company_rules"],
                        bold: true,
                        italics: true,
                        alignment: "center"
                    },
                    {
                        table: {
                            layout: 'lightHorizontalLines',
                            headerRows: 1,

                            widths: [122, 122, 122, 122],
                            body: [
                                [{
                                        text: translation["tour"],
                                        alignment: "center",
                                        fontSize: 9
                                    },
                                    {
                                        text: translation["showroom"],
                                        alignment: "center",
                                        fontSize: 9
                                    }, {
                                        text: translation["sp"],
                                        fontSize: 9,
                                        alignment: "center"
                                    },
                                    {
                                        fontSize: 9,
                                        text: translation["spc"],
                                        alignment: "center"
                                    }
                                ],
                                [{
                                        text: $.parseJSON(localStorage.tour)["ProjId"],
                                        alignment: "center",
                                        fontSize: 9
                                    },
                                    {
                                        text: localStorage.showRoomName,
                                        alignment: "center",
                                        fontSize: 9
                                    },
                                    {
                                        text: $.parseJSON(localStorage.sp)["EmplID"],
                                        fontSize: 9,
                                        alignment: "center"
                                    },
                                    {
                                        fontSize: 9,
                                        text: $.parseJSON(localStorage.sp)["Employee"],
                                        alignment: "center"
                                    }
                                ]
                            ]
                        }
                    }
                ],

            };
            console.log(docDefinition)
            var pdfDocGenerator = pdfMake.createPdf(docDefinition);

            pdfDocGenerator.getBase64((data) => {

                var obj = {
                    from: "costerdiamonds@gmail.com",
                    pdf: data,
                    customer: $("#email").val(),
                    name: nm + "_" + "gb" + ".pdf",
                    subject: "Invoice",
                    text: "Generated " + (new Date()),
                    user: "cobol1962@gmail.com"
                }
                api.call("sendMail", function(res) {
                    swal({
                        type: "success",
                        text: "Mail sent succsefully.",
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: true
                    }).then((result) => {
                        window.open("https://costercatalog.com/api/invoices/" + nm + "_" + "gb" + ".pdf", '_system');
                        //  window.location.reload();
                    })
                }, obj, {});
            });

        })
    }, obj, {})
}

function noPinCode() {
    /*  swal({
        type: "question",
        title: "Select reason",
        html: "<select class='form-control'><option selected value='nopincode'>No pincode</option></select>",
        confirmButtonText: "Submit",
        showCancelButton: true,
        showCancelButton: "Cancel",
        showCloseButton: true
      })*/
}

function doSearch() {
    if (escapeClicked) {
        $("#search_div").hide();
        $("#addproducticon").show();
        return;
    }
    if ($("#search").val() == "Escape") {
        $('#searchbackdrop').hide();
        $("#search_div").hide();
        loadPage("addproduct");
        return;
    }
    if ($("#search").val() == "Return") {
        $('#searchbackdrop').hide();
        $("#search_div").hide();
        loadPage("addrefund");
        return;
    }
    if (currentPage == "search" && currentPage == "search") {
        loadedPages.search.doSearch();
    } else {
        loadPage("search");
    }
}
$("#mainModal").on("hidden.bs.modal", function() {

    if (appError) {
        $("#mainModal").modal("show");
        return false;
    }
});
showModal = function(options = {}) {
    if ((options.title === undefined || options.title == "")) {
        if ((options.content === undefined || options.content == "")) {
            return;
        }
    }
    if (options.type === undefined) {
        $("#m_header").css({
            backgroundImage: "url(https://costercatalog.com/catalog/images/crown.png)"
        })
    }
    if (options.type == "error") {
        $("#m_header").css({
            backgroundImage: "url(https://costercatalog.com/catalog/images/error.png)"
        })
    }
    if (options.type == "ok") {
        $("#m_header").css({
            backgroundImage: "url(https://costercatalog.com/catalog/images/green_checkbox_only.png)"
        })
    }
    if (options.type == "sad") {
        $("#m_header").css({
            backgroundImage: "url(https://costercatalog.com/catalog/images/sad.gif)"
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
    $("#mainModal").modal("show");
}

function printDocument(documentId) {
    var doc = document.getElementById(documentId);

    //Wait until PDF is ready to print
    if (typeof doc.print === 'undefined') {
        setTimeout(function() {
            printDocument(documentId);
        }, 1000);
    } else {
        doc.print();
    }
}

function makeAbbreviation(data) {
    var splited = data.split("-");
    var rstr = "";
    $.each(splited, function(ind) {
        var ss = this.split(" ");
        $.each(ss, function() {
            rstr += this.substring(0, 1);
        })
        if (ind != (splited.length - 1)) {
            rstr += "-";
        }

    })
    return rstr;
}
var b64toBlob = (b64Data, contentType = 'application/pdf', sliceSize = 512) => {
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

    var blob = new Blob(byteArrays, {
        type: contentType
    });

    return blob;
}

function downloadAPK() {
    var blob = null;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "https://build.phonegap.com/apps/3954423/download/android/?qr_key=YLMCY_jK-hpmhYA9z9ys");
    xhr.responseType = "blob"; //force the HTTP response, response-type header to be blob
    xhr.onload = function() {
        blob = xhr.response; //xhr.response is now a blob object
        var storageLocation = "";
        storageLocation = 'file:///storage/emulated/0/';
        var folderpath = storageLocation + "Download";
        var filename = "new-version.apk";
        var DataBlob = blob;
        window.resolveLocalFileSystemURL(folderpath, function(dir) {
            dir.getFile(filename, {
                create: true
            }, function(file) {
                file.createWriter(function(fileWriter) {
                    fileWriter.write(DataBlob);
                    setTimeout(function() {
                        cordova.plugins.fileOpener2.open(
                            "file:///storage/emulated/0/Download/new-version.apk",
                            "application/vnd.android.package-archive", {
                                error: function() {
                                    alert("error");
                                },
                                success: function() {

                                }
                            }
                        );
                    }, 2000);
                }, function(err) {
                    // failed
                    alert(JSON.stringify(err));
                });
            });
        });
    }
    xhr.send();

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
        var pp = pdf.split("_")[2];
        var start = pp.substring(0, pp.length - 1);
        var cs = "openPDF('" + pdftoopen + "');";
        var cs1 = pdftoopen;
        html += "<tr><td colspan='4'><p onclick=" + cs + ">" + cs1 + "</p></td></tr>";
        var pos = versions.indexOf(dd.find("version").html());
        for (var i = 0; i <= pos; i++) {
            var cs = "openPDF('" + ppp[0] + "_" + ppp[1] + "_" + start + versions[i] + "_" + "gb" + ".pdf');";
            var cs1 = ppp[0] + "_" + ppp[1] + "_" + start + versions[i] + "_" + "gb" + ".pdf";
            html += "<tr><td colspan='4'><p onclick=" + cs + ">" + cs1 + "</p></td></tr>";
        }
    }
    html += "<tr><td style='text-align:left;' colspan='4'><strong>Payments</strong></td></tr>";

    $.each(dd.find("pay"), function() {
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

function collectCurrentWork() {


    try {
        if (shoppingCartContent.length > 0 || haveToSave) {
            localStorage.recover = 1;
            haveToSave = true;
            //  localStorage.shoppingCartContent = (shoppingCartContent);
        }
    } catch (err) {
        delete localStorage.shoppingCartContent;
    }
}

function openPDF(data) {

    $.ajax({
        url: "https://costerbuilding.com/api/invoice.php?invoice=" + data,
        type: "GET",
        success: function(res) {
            var app = document.URL.indexOf('http://') === -1 && document.URL.indexOf('https://') === -1;
            var blob = b64toBlob(res, "application/pdf");
            var blobUrl = URL.createObjectURL(blob);
            if (!app) {
                window.open(blobUrl, "_system", "location=yes");

            } else {
                var storageLocation = "";
                storageLocation = 'file:///storage/emulated/0/';
                var folderpath = storageLocation + "Download";
                var filename = "invoice.pdf";
                var DataBlob = b64toBlob(res, "application/pdf");

                window.resolveLocalFileSystemURL(folderpath, function(dir) {
                    dir.getFile(filename, {
                        create: true
                    }, function(file) {
                        file.createWriter(function(fileWriter) {
                            fileWriter.write(DataBlob);
                            setTimeout(function() {

                                cordova.plugins.fileOpener2.open(
                                    "file:///storage/emulated/0/Download/invoice.pdf",
                                    "application/pdf", {
                                        error: function() {},
                                        success: function() {}
                                    }
                                );
                            }, 500);
                        }, function(err) {
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

}

function shoppingCartToLocalStorage() {
    delete localStorage.shoppingCartContent;
    var scc = {};
    for (var key in shoppingCartContent) {
        scc[key] = shoppingCartContent[key];
        console.log(shoppingCartContent[key])
    }
    localStorage.shoppingCartContent = JSON.stringify(scc);
}

function localToShoppingCartContent() {
    var scc = $.parseJSON(localStorage.shoppingCartContent);
    shoppingCartContent = {};
    for (var key in scc) {
        shoppingCartContent[key] = scc[key];
    }
}
function rPass() {
  api.call("resetUserPassword", function(res) {
  if (res.status == "ok") {
    $("#login").modal("hide");
    showModal({
        type: "ok",
        title: res.message,
        allowBackdrop: false,
        showCancelButton: false,
        showClose: false,
        confirmCallback: function() {
            if ($("#login")[0].hasAttribute("nextpage")) {
                loadPage($("#login").attr("nextpage"));
            }
        },
        confirmButtonText: "CONTINUE"
    })

  } else {
    $("#login").modal("hide");

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
function detectMobile() {
    const toMatch = [
        /Android/i,
        /webOS/i,
        /iPhone/i,
        /iPad/i,
        /iPod/i,
        /BlackBerry/i,
        /Windows Phone/i
    ];

    return toMatch.some((toMatchItem) => {
        return navigator.userAgent.match(toMatchItem);
    });
}
$.ajax = (($oldAjax) => {
    // on fail, retry by creating a new Ajax deferred

    function check(a, b, c) {

        if (this.table === undefined) {
            return;
        }
        var tt = null;
        var shouldRetry = b != 'success' && b != 'parsererror';
        console.log(b);
        if (b != 'success') {
            $("body").LoadingOverlay("hide");
            appError = true;
            clearTimeout(tt);
            showModal({
                type: "sad",
                showCancelButton: false,
                showConfirmButton: false,
                allowBackdrop: false,
                title: "The connection is unstable. We are trying to reconnect. Please wait..... Attempting every  5 sec"
            })
        }
        if (b == 'success') {
            tt = setTimeout(function() {
                showModal({
                    type: "ok",
                    showCancelButton: false,
                    showConfirmButton: false,
                    confirmButtonText: "OK",
                    title: "Connection established.",

                })

                //        $("body").LoadingOverlay("show", optionsLoader);
                appError = false;
                $("#mainModal").modal("hide");
            }, 2000)
        }
        if (shouldRetry && --this.retries > 0)
            setTimeout(() => {
                $.ajax(this)
            }, this.retryInterval || 100);
        if (shouldRetry && --this.retries <= 0) {
            showModal({
                type: "error",
                title: "Connection can not be established. Please contact administrator. Application will close now. Try later.",
                showCancelButton: false,
                confirmButtonText: "OK",
                confirmCallback: function() {

                    try {
                        navigator.app.exitApp();
                    } catch (err) {}
                    $("#mainModal").modal("hide");
                    //localStorage.error = JSON.stringify(res);

                }
            })
        }
    }

    return settings => $oldAjax(settings).always(check)
})($.ajax);
