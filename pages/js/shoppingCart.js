loadedPages.shoppingCart = {
  firstDraw: true,
  firstDR: true,
  firstDC: true,
  showDiscount: false,
  total: 0,
  fullprice: 0,
  vatexcluded: 0,
  vat: 0,
  administrative: 0,
  vatrefunt: 0,
  topay: 0,
  masterdiscount: 0,
  discountClicked: false,
  dApproved: {},
  locked: [],
  currentInvoice: null,
  approvedRequested: false,
  countryEu: {},
  initialize: function() {

    var ool = Object.keys(shoppingCartContent).length;
    if (ool == 0 || ool > 1) {
      $("#nits").html( "&nbsp;items");
    } else {
      $("#nits").html( "&nbsp;item");
    }
    $('#scanbutton').popover({
      sanitize: false,
      content: function () {
          return $("#scanContent").html();
      }
    }).on('hide.bs.popover', function () {
      $('#scanvalue').val("");
    });

    $("#nit").html( Object.keys(shoppingCartContent).length);
    $("#content").css({
      marginTop: 0,
      paddingTop: 80
    })
    if (invoiceLocked == 1) {
      $("[lock]").hide();
    }
    $(".menutoggle").unbind("click");
    $(".menutoggle").bind("click", function() {
      $(".menutoggle").toggle();
      $(".menuitems").toggle();
    })
    var cntrs = [];

    localStorage.goingback = "0";
    if (localStorage.openInvoice !== undefined) {
      loadedPages.shoppingCart.currentInvoice = $.parseJSON(localStorage.openInvoice);
    }
    $("#masterdiscount").bind("change", function() {
      if ($("#masterdiscount").val() == "") {
        delete localStorage.invoiceDiscount;
      }
      if ($("#masterdiscount").val() == "") {
        $("[spdiscount1]").trigger("click");
      }
    })

    $("#dapproved").typeahead({
      items: "all",
      scrollHeight: 100,
      source: spersonsarr,
      autoSelect: false,
      maxLength: 5,
      afterSelect: function(obj) {

        $("#showDiscount").prop("disabled", false);
        loadedPages.shoppingCart.dApproved = obj;

         $("#dpersonid").val(obj.id);
         $("#dpersonname").val(obj.name);

         $("#dap").html(obj.name)
         localStorage.dapproved = obj.id;
         localStorage.dapprovedname = obj.name;
         if (window.StatusBar){

           try {
               window.StatusBar.show();
               setTimeout(function(){
                   window.StatusBar.hide();
               },5);
             } catch(err) {

             }
         }
      }
    });
    $("#dapproved").bind("change", function() {
      $("#dap").html($("#dapproved").val())
    })
    $("#dapproved").bind("blur", function() {
      try {

       if (window.StatusBar) window.StatusBar.hide();
     } catch(err) {

     }
    })
    api.call("getCountries", function(res) {
      $.each(res, function() {
        var ths = this;
        var obj = {
          id: ths.CountryID,
          text: ths.Country,
          eu: ths.EUMember,
          nationality: ths.Nationality
        }
        loadedPages.shoppingCart.countryEu[ths.CountryID] = ths;
        cntrs.push(obj);
      })

      $("#countries").select2({
        data: cntrs,
        placeholder: "Select a customer country origin",
        allowClear: true,
        width: 200
      });
      setTimeout(function() {
          if (localStorage.customerCountry !== undefined) {
            var o = $.parseJSON(localStorage.customerCountry);
            if (o.CountryID !== undefined) {
              $('#countries').val(o.CountryID).trigger("change");
            } else {
              $('#countries').val(o.id).trigger("change");

            }
          }
      }, 2000)
      $('#countries').on('select2:clear', function (e) {
        $("#refund")[0].checked = false;
        $("[refundcontainer]").hide();
        delete localStorage.customerCountry;
        loadedPages.shoppingCart.calculateRefund();
      });
      $('#countries').on('select2:select', function (e) {

          var csel = e.params.data;
         localStorage.isEU = loadedPages.shoppingCart.countryEu[csel.id].EUMember;
          csel.eu = loadedPages.shoppingCart.countryEu[csel.id].EUMember;
          localStorage.customerCountry = JSON.stringify(csel);
          customerInfoData["countryCode"] = csel.id;
          if (localStorage.isEU == "0") {
          //  $("[refundcontainer]").show();
          } else {
            $("#dRefund").removeClass("refund");
            $("#dRefund").html("VAT refund");
            $("[refundcontainer]").hide();
          }
          loadedPages.shoppingCart.calculateRefund();

      });
      if (localStorage.customerInfo !== undefined) {
        customerInfoData = $.parseJSON(localStorage.customerInfo);
        if (customerInfoData["countryCode"] !== undefined) {
        try {
            $("#countries").val(customerInfoData["countryCode"]).trigger('change');
      //      localStorage.customerCountry = JSON.stringify(loadedPages.shoppingCart.countryEu[customerInfoData["countryCode"]]);
            localStorage.isEu = $.parseJSON(localStorage.customerCountry).eu;
          } catch(err) {
            localStorage.customerCountry = JSON.stringify(loadedPages.shoppingCart.countryEu[customerInfoData["CountryID"]]);
            $("#countries").val(customerInfoData["CountryID"]).trigger('change');
            customerInfoData["countryCode"] = customerInfoData["CountryID"];
            customerInfoData["id"] = customerInfoData["EUMember"];
            if (customerInfoData["CountryID"] !== undefined) {
        //      localStorage.customerCountry = JSON.stringify(loadedPages.shoppingCart.countryEu[customerInfoData["CountryID"]]);
              localStorage.isEu = $.parseJSON(localStorage.customerCountry).EUMember;
            }
          }
        }
      }

    },{},{});
    setTimeout(function() {
      loadedPages.shoppingCart.drawCart();
      $("#main").css({
        visibility: "visible"
      })
      $("#mainNavigation").css({
        visibility: "visible"
      })
    }, 1500);
  //  $('#dapproved').typeahead('val', myVal);
      $("[nrfnd]").bind("change", function() {
        loadedPages.shoppingCart.setToPay(this);
      //  $("#cartToPay").val(parseFloat($("#cartToPay").val()).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }));
      })
      $("[rfnd]").bind("change", function() {
        loadedPages.shoppingCart.setToPay(this);
      //  $("#cartToPay").val(parseFloat($("#cartToPay").val()).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }));
      })
      $("#cartToPay").bind("blur", function() {

        $(this).css({
          background: "transparent"
        })
      })
  },
  drawCart: function() {
  ///  delete localStorage.generalDiscount;
    var cntrs = [];
    var hasDiscount = false;
    if (localStorage.generalDiscount !== undefined && localStorage.generalDiscount != "") {
       $("#masterdiscount").val(localStorage.generalDiscount);
    }
    localStorage.generalDiscount = $("#masterdiscount").val();
    var totalDiscount = 0;
      if (localStorage.openInvoice !== undefined) {
        var cc = $.parseJSON(localStorage.customerInfo);
        if (cc.countryCode != "") {
          $('#countries').val(cc.countryCode).trigger("change");
          var dtt = $("#countries").select2('data');
          localStorage.customerCountry = JSON.stringify(dtt[0]);
        } else {

        }
      }

      if (localStorage.customerCountry !== undefined) {
          var data = $.parseJSON(localStorage.customerCountry);
          if (data.CountryID !== undefined) {
            data.id = data.CountryID;
          }
          $('#countries').val(data.id);
          $('#countries').select2().trigger('change');
          localStorage.isEU = data.EUMember;
          $("[refundcontainer]").hide();
          if (data.eu == "0") {

            $("[refundcontainer]").hide();
      //      $("#directRefund")[0].checked = true;
          //    loadedPages.shoppingCart.checkCode();
          } else {
            $("#dRefund").removeClass("refund");
            $("#dRefund").html("VAT refund");
            $("[refundcontainer]").hide();
          }
          loadedPages.shoppingCart.checkCode();
      //    $("#directRefund")[0].checked = true;
          loadedPages.shoppingCart.calculateRefund();
        }

      $("#refund").bind("change", function() {
       loadedPages.shoppingCart.calculateRefund();
      })

  //  $("#items").hide();
    $("#items").html("");
    $("#lblCartCount").html(" " + Object.keys(shoppingCartContent).length);
  console.log(shoppingCartContent);
    if (Object.keys(shoppingCartContent).length == 0) {
      $("#toggleShoppigCart").addClass("empty");
      $("#fullshoppingcart").hide();
      $("#emptyshoppingcart").show();
    } else {
      $("#toggleShoppigCart").removeClass("empty");
    }
     loadedPages.shoppingCart.total = 0;
     var ii = 0;
     var ttl1 = 0;
    shoppingCartToLocalStorage();

    for (var key in shoppingCartContent) {
      var obj = shoppingCartContent[key];
    //  console.log(obj)
      if (loadedPages.shoppingCart.firstDraw) {
          obj.Discount = ((obj.Discount == "0%") ? "" : (obj.Discount));

      }
      if (obj.Discount != "") {
        var oo = parseInt(obj.Discount.replace("%", ""));
        if (oo <= 0) {
          obj.Discount = "";
          obj.realPrice = obj.startRealPrice;
        }
      }
      shoppingCartContent[key] = obj;
    //  console.log(shoppingCartContent)
    //  console.log(localStorage)
  //  shoppingCartToLocalStorage();
      obj.Discount = obj.Discount.replace("%%", "%");
      if (obj.Discount != "" && !obj.discountLocked) {
        if (obj.Discount != "" && obj.Discount != "%") {
           hasDiscount = true;
            var sm = parseFloat(obj.SalesPrice);
            if (obj.Discount.indexOf("%") > -1) {
              var prc = parseFloat(obj.Discount.replace("%", ""));
              totalDiscount += prc;
              obj.realPrice = sm - (Math.ceil((sm / 100) * prc));
            } else {
              var prc = parseFloat(obj.Discount);
              totalDiscount += prc;
              obj.realPrice = sm - prc;
            }
          } else {
            obj.realPrice = obj.SalesPrice;
          }
          if ((obj.realPrice - parseInt(obj.realPrice)) > 0) {
            obj.realPrice = parseInt(obj.realPrice) + 1;
          } else {
            obj.realPrice = parseInt(obj.realPrice);
          }

      }
      if (obj.additionalDiscount != "" && obj.discountLocked) {
        if (obj.additionalDiscount != "") {

            var sm = parseFloat(obj.startRealPrice);
            if (obj.additionalDiscount.indexOf("%") > -1) {
              var prc = parseFloat(obj.additionalDiscount.replace("%", ""));
              totalDiscount += prc;
              obj.realPrice = parseInt(sm - (Math.ceil((sm / 100) * prc)));
            } else {
              var prc = parseFloat(obj.additionalDiscount);
              totalDiscount += parseInt(prc);
              obj.realPrice = parseInt(sm - prc);
            }

          } else {

            obj.realPrice = obj.startRealPrice;
          }
          obj.realPrice = parseInt(obj.realPrice);
          if ((obj.realPrice - parseInt(obj.realPrice)) > 0) {
            obj.realPrice = parseInt(obj.realPrice) + 1;
          } else {
            obj.realPrice = parseInt(obj.realPrice);
          }
      }
      if ((obj.realPrice - parseInt(obj.realPrice)) > 0) {
        obj.realPrice = parseInt(obj.realPrice) + 1;
      } else {
        obj.realPrice = parseInt(obj.realPrice);
      }
      obj.toPay = parseInt(obj.quantity) * parseFloat(obj.realPrice);

      loadedPages.shoppingCart.total +=  parseFloat(obj.toPay);
      loadedPages.shoppingCart.fullprice += parseFloat(obj.SalesPrice);
      obj.imageURL = obj.imageURL.replace("50px", "100px");
      var html = "<div root style='display: block;font-size:12px;border-bottom:1px solid rgba(0, 0, 0, 0.1);'>";
      html += "<div id='" + obj.SerialNo + "' serial='" + obj.SerialNo + "' style='font-size: 18px;padding:10px;padding-bottom:20px;'>";
      html += "<table id='ttt' style='width:100%;'><tr>";
      html += "<td style='max-width:120px;width:120px;'>" + ((obj.imageURL != "") ? obj.imageURL : "<img style='width:100px;' src='https://costercatalog.com/coster/www/images/crown.png' /></td>");
      html += "<td style='text-align: left;width:50%;'><div pdata style='position: relative;top:10px;right:0px;color:#ADADAD;display:inline-block;padding-bottom: 10px;'>" + obj.SerialNo + "<br />";
      if (obj.productName !== undefined && obj.productName != "undefined") {
        html += "<span productname style='font-size:24px;font-weight: bold;color:black;max-width:300px;min-width:300px;'>" + obj.productName.replace("undefined", "") + "</span>";
        html += "<span productname style='font-size:18px;color:black;max-width:300px;min-width:300px;'>" + obj.CompName.replace("undefined", "") + "</span>";

       }
       html += "<br /><table class='dsct'  spdiscount style='display:none;float:left;'><tr>";
       html += "<td><input spdiscount a onchange='loadedPages.shoppingCart.applyDiscount(this);' value='" + obj.Discount + "' type='text' class='form-control' style='width:50px;clear:both;width:95px;display:none;' placeholder='Discount' /></td>";
       html += "<td><select spdiscount id='percenttype' value='" + ((obj.discountType == "") ? "" : "euro") + "' class='form-control' serial='" +obj.SerialNo + "' onchange='loadedPages.shoppingCart.switchPercent(this);' spdiscount style='display:none;'>";
       html += "<option value='' " + ((obj.discountType == "") ? "selected" : "") + ">%</option>";
       html += "<option value='euro'" + ((obj.discountType == "euro") ? "selected" : "") + ">€</option></select></td>";
  //     html += "<td><div serial='" +obj.SerialNo + "' onclick='loadedPages.shoppingCart.switchPercent(this);' spdiscount style='display:none;' class='discounttype " + ((obj.discountType === undefined) ? "" : obj.discountType) + "'></div></td>";

       html += "</tr></table>";
     // html += "<div style='position:absolute;right:0px;color:black;font-size:13px;'>";
     html += "<td>";
     if (  true ) {
       html += "<div style='" + ((obj.available == "11111111") ? "display:none;" : "") + "'><table><tr><td style='vertical-align: bottom;'>";
       html += "<img onclick='loadedPages.shoppingCart.calcInput(this, -1);' src='/images/minus.svg' /></td>";
       html += "<td style='vertical-align: top;'><input class='form-control' readonly serialno='" + obj.SerialNo + "' max='" + obj.available + "' onfocus='lastFocused=$(this);' onchange='loadedPages.shoppingCart.recalculate(this);' style='color:black;text-align:right;width:70px;' quantity type='number' itemid='" + obj.ItemID + "' value='" + obj.quantity + "' /></td>";
       html += "<td style='vertical-align: bottom;'><img onclick='loadedPages.shoppingCart.calcInput(this, +1);' src='/images/plus.svg' /></div></td></tr></table>";
     } else {
       html += "<div style=''><label style='color:black;font-size:15px;'>Quantity:&nbsp;</label><input  serialno='" + obj.SerialNo + "' max='" + obj.available + "' onfocus='lastFocused=$(this);'  onchange='loadedPages.shoppingCart.recalculate(this);' style='color:black;font-size:13px;text-align:right;width:70px;' quantity type='number' itemid='" + obj.ItemID + "' value='" + obj.quantity + "' /></div></td>";
     }
   html += "</td>";
   html += "<td style='position:relative;vertical-align: top;text-align:right;'>";
    if (obj.Discount != "" && obj.Discount != "%") {

         html += "<div style='float:right;color:black;width:100%;padding-top:0px;margin-top: 10px;'>";

         if (true) {

         }

         if (obj.discountLocked) {
           html += "<div style='position: relative;padding:0px;'><span style='color:red;'>";
         } else {

           html += "<div style='position: relative;padding:0px;margin-top: -15px;'><span style='color:red;'>";
         }
         html += "<b>" + obj.Discount + "</b>&nbsp;</span><span style='text-decoration:line-through;'>" + (parseFloat(obj.SalesPrice) * 1).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</span>";
         html += "<br /><span realvalue='" + parseInt(obj.realPrice) + "'>" + (parseInt(obj.realPrice) * 1).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</span>";
        // html += "<br /><label style='color:black;font-size:15px;'>Total:&nbsp;</label>" + parseInt(obj.toPay).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</div>";
         html += "</div><img onclick='loadedPages.shoppingCart.removeItem(this);' style='position:absolute; bottom: 10px;right: 0px;' src='/images/deleteitem.svg' /></td>";

       //  html += '</div>';
       } else {
         obj.toPay = parseInt(obj.quantity) * parseFloat(obj.realPrice);
         ttl1 += obj.toPay;

         if (true) {

        /*   html += "<br /><table class='dsct' spdiscount style='display:none;float:left;'><tr>";
           html += "<td onclick='loadedPages.shoppingCart.applyDiscount(this);'>Apply</td>";
           html += "<td><div serial='" +obj.SerialNo + "' onclick='loadedPages.shoppingCart.switchPercent(this);' spdiscount style='display:none;' class='discounttype " + ((obj.discountType === undefined) ? "" : obj.discountType) + "'></div></td>";
           html += "<td><input spdiscount a  value='" + obj.Discount + "' type='text' class='form-control' style='display:none;width:50px;clear:both;text-align:right;float:right;width:65px;' placeholder='Discount' />";
           html += "<br /><div style='color:black;font-size:13px;'><label style='color:black;font-size:15px;'>Total:&nbsp;</label>" + parseInt(obj.toPay).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</div></td>";

           html += "</tr></table><br />";*/
         }
       }

       if (obj.Discount == "" || obj.Discount == "%") {
         html += "<td style='position:relative;vertical-align: top;text-align: right;'><div style='float:right;color:black;'><span realvalue='" + parseInt(obj.realPrice) + "'>" + (parseInt(obj.realPrice) * 1).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</span></div>";
        // html += "<br /><div style='color:black;font-size:13px;'><label style='color:black;font-size:15px;'>Total:&nbsp;</label>" + parseInt(obj.toPay).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</div>";
         html += "<img style='position:absolute;bottom:10px;right:0px;' onclick='loadedPages.shoppingCart.removeItem(this);'  src='/images/deleteitem.svg' />";

       }

       html += "</td></tr>";
       html += "</table>";

       $(html).appendTo($("#items"));


     }
     $("#subtotal").parent().next("td").html(parseInt(loadedPages.shoppingCart.total).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }));
      $("#subtotal").attr("realvalue", parseInt(loadedPages.shoppingCart.total));
     var ttl = 0;
     var grandTotal = parseFloat(loadedPages.shoppingCart.total);
     if (localStorage.generalDiscount !== undefined) {
       $("#masterdiscount").val(localStorage.generalDiscount);
     }
     if (loadedPages.shoppingCart.currentInvoice != null && loadedPages.shoppingCart.firstDC) {
       var dd = loadedPages.shoppingCart.currentInvoice.discount;
       if (dd.indexOf("%") == -1) {
         $("#mdt").val("euro");
       }
       $("#masterdiscount").val(loadedPages.shoppingCart.currentInvoice.discount);
       loadedPages.shoppingCart.firstDC = false;
     }
     localStorage.discountAmount = 0;

     if (localStorage.generalDiscount !== undefined) {
       localStorage.discountAmount = localStorage.generalDiscount;
       $("#masterdiscount").val(localStorage.discountAmount);
       if ($("#masterdiscount").val().indexOf("%") == -1 && $("#masterdiscount").val() != "") {
         $("#mdt").val("euro");
       }
     } else {
       localStorage.discountAmount = 0;
     }
     if ($("#masterdiscount").val() != "") {
       hasDiscount = true;
       totalDiscount = 1;
       if ($("#masterdiscount").val() != "") {
          if ($("#mdt").val() == "euro") {
            $("#masterdiscount").val($("#masterdiscount").val().replace("%", ""));
          } else {
            $("#masterdiscount").val($("#masterdiscount").val().replace("%", "") + "%");
          }
        }
             var sm = loadedPages.shoppingCart.total;
             if ($("#masterdiscount").val().indexOf("%") > -1) {
               var prc = parseInt($("#masterdiscount").val().replace("%", ""));
               totalDiscount += prc;
               localStorage.discountAmount = Math.ceil(((sm / 100) * prc));

              ttl = sm - Math.ceil(((sm / 100) * prc));
             } else {
               var prc = parseInt($("#masterdiscount").val());
               totalDiscount += prc;
               localStorage.discountAmount =  prc;
               ttl = parseInt(sm - prc);
             }

     } else {
       ttl = parseInt(loadedPages.shoppingCart.total);
     }
     if ((ttl - parseInt(ttl)) > 0) {
       ttl = parseInt(ttl) + 1;
     } else {
       ttl = parseInt(ttl);
     }
     loadedPages.shoppingCart.showDiscount = hasDiscount;
  /*   if (!loadedPages.shoppingCart.showDiscount) {
       $('[spdiscount]').hide();
       $('[spdiscount1]').hide();
     } else {
       $('[spdiscount]').show();
       $('[spdiscount1]').show();
     }*/

     var vex = parseFloat(ttl / 1.21);
     var vat = parseFloat(ttl - (ttl / 1.21));
     var achg = parseFloat((ttl / 100) * 1.35);
     var torefund = vat - achg;
     torefund = Math.floor(torefund);
     var withcharge = ttl + achg;
     var vatchargeexcl =  withcharge / 1.21;
     var vatcharge = withcharge - vatchargeexcl;
     var bb = parseInt(vat - achg);
     achg =  vat - torefund;

//     admin charge = VAT21% - VAT REFUND AMOUNT = 30.815,85 - 28.417,00 = 2398,85
     $("#vatexcluded").parent().next("td").html((parseFloat(vex).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" })));
     $("#vat").parent().next("td").html((parseFloat(ttl - vex).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" })));
     $("#admincharge").parent().next("td").html((parseFloat(achg).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" })));
     $("#withcharge").parent().next("td").html((parseFloat(withcharge).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" })));
     $("#vatchargeexcl").parent().next("td").html((parseFloat(vex).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" })));
     $("#vatcharge").parent().next("td").html((parseFloat(vatcharge).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" })));
     $("#torefund").parent().next("td").html(torefund.toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }));
     $("#total").parent().next("td").html((parseFloat(ttl).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" })));

     localStorage.vatexcluded = parseFloat(vex);
     localStorage.vat = parseFloat(ttl - vex);
     localStorage.admincharge = parseFloat(achg);
     localStorage.withcharge = parseFloat(withcharge);
     localStorage.vatchargeexcl = parseFloat(vatchargeexcl);
     localStorage.vatcharge = parseFloat(vatcharge);
     localStorage.torefund = torefund;
     localStorage.total = parseFloat(ttl);
     localStorage.grandTotal = parseFloat(grandTotal);
     localStorage.invoiceDiscount = $("#masterdiscount").val();
    // localStorage.directRefund = (($("#directRefund")[0].checked) ? "1" : "0");
    var bb = parseInt((parseFloat(localStorage.torefund) - parseFloat(localStorage.admincharge)));

     localStorage.payNoRefund = parseFloat(vex + vat);
     localStorage.payWithRefund = parseInt((localStorage.total -  vat) + achg);
     localStorage.isEU = $('#countries').find(':selected').data('eu');

     $(".norefund").val((Math.ceil(vex + vat).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" })));
     $(".refund").val((Math.ceil((vatchargeexcl + vatcharge) - vat).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" })));
     if (localStorage.directRefund === undefined) {
       localStorage.directRefund = "0";
     }
     if (localStorage.directRefund == "0") {
       $(".norefund").show();
       $(".refund").hide();
     } else {
       $(".norefund").hide();
       $(".refund").show();
     }

     if (!loadedPages.shoppingCart.approvedRequested && parseFloat(totalDiscount) > 0) {
       if (!loadedPages.shoppingCart.firstDraw) {
         $("#discountApproved").modal("show");
         $("[spdiscount2]").show();
        }
       loadedPages.shoppingCart.approvedRequested = true;
      // loadedPages.shoppingCart.firstDC = true;
     } else {
        //loadedPages.shoppingCart.approvedRequested = true;
     }
     loadedPages.shoppingCart.firstDraw = false;
     if (loadedPages.shoppingCart.approvedRequested) {
       $("[spdiscount]").show();
       $("[spdiscount1]").show();
     }

     $("#items").show();
/*     if (!loadedPages.shoppingCart.showDiscount) {
       $('[spdiscount]').hide();
       $('[spdiscount1]').hide();
     } else {
       $('[spdiscount]').show();
       $('[spdiscount1]').show();
     }*/

     $("#content").find("#main").find("input").unbind("keyup");
     $("#content").find("#main").find("input").bind("keyup", function (event) {
          $(event.target).val($(event.target).val().toString().replace(/[^0-9]/g, ''));
      });
      $("#content").find("input").unbind("focusout");
      $("#content").find("input").bind("focusout", function (event) {
        if (window.StatusBar){
          try {
              window.StatusBar.show();
              setTimeout(function(){
                  window.StatusBar.hide();
              },5);
            } catch(err) {

            }
        }
       });

       for (var key in shoppingCartContent) {
         var obj = shoppingCartContent[key];
         obj.discountLocked = false;
       }

      if (invoiceLocked == 1) {
        loadedPages.shoppingCart.checkIsLogged();
      }
  },
  checkCode: function() {
    return;
    if (!$("#directRefund")[0].checked) {
      loadedPages.shoppingCart.calculateRefund();
      return;
    }
    showModal({
        title: "Please confirm choice. Enter code.",
        content: "<input id='ccode' type='number' class='form-control' /><span style='color:red;display:none;' id='cer'>Wrong code.</span>",
        allowBackdrop: false,
        showClose: false,
        noclose: true,
        cancelCallback: function() {
            $('#mainModal').modal("hide");
        },
        confirmCallback: function() {

          if ($("#ccode").val() == "1071") {
              $("#dRefund").addClass("refund");
              //$("[refundcontainer]").show();
              $("#dfw").html("Direct Refund");
              localStorage.directRefund = "1";
              loadedPages.shoppingCart.calculateRefund(true);
              $('#mainModal').modal("hide");
          } else {
            $("#cer").show();
          }

        }
    })

  },
  recalculate: function(obj, vl) {
    var el = $(obj);
    var typed = el.val();
    var mx = 0;
    api.call("getQuantityBySerial", function(res) {
      var available = res[0].OnhandQnt;

      if (available == 0) {
        el.attr("max", typed);
        $(obj).attr("max", typed);
        mx = typed;
      } else {
        el.attr("max", available);
        $(obj).attr("max", available);
        mx = available;
      }

      loadedPages.shoppingCart.recalculate1(obj, mx, vl);
    }, {serialno: el.attr("serialno")}, {}, {})
  },
  recalculate1: function(obj, mx, vl) {

    lastFocused = $(obj);
  /*  if ($(obj).val() > parseInt(mx)) {
      $(obj).val(vl);
      showModal({
        type: "error",
        title: "Available quantity is " + mx
      })
      return false;
    }*/
    var id = $(obj).attr("serialno");

    if (shoppingCartContent[id].quantity === undefined) {
      shoppingCartContent[id].quantity = 1;
    }
    shoppingCartContent[id].quantity = $(obj).val();
    shoppingCartContent[id].toPay = parseInt($(obj).val()) * parseFloat(shoppingCartContent[id].realPrice);

    shoppingCartContent[id]["toPay"] = parseInt($(obj).val()) * parseFloat(shoppingCartContent[id].realPrice);
    shoppingCartToLocalStorage();
  //  loadedPages.shoppingCart.drawCart();
  },
  recalculateDiscount: function() {
    $('#masterdiscount').show();
    var dsc = parseFloat($("#subtotal").attr("realvalue")) - $("#cartToPay").val();
    dsc = dsc.toFixed(2);
    $("#masterdiscount").val(dsc);
    $("#masterdiscount").trigger("change");
  },
  removeItem: function(obj) {
    showModal({
      title: "Are you sure to remove item " + $(obj).closest("[serial]").attr("serial") + " " + $(obj).closest("[root]").find("[productname]").html() + " from cart?",
      cancelButtonText: "Cancel",
      confirmButtonText: "Delete",
      confirmCallback: function() {
        $("#mainModal").modal("hide");
        delete shoppingCartContent[$(obj).closest("[serial]").attr("serial")];
        shoppingCartToLocalStorage();
        loadedPages.shoppingCart.drawCart();

      }
    })

  },
  discounts: function(obj) {
    var id = $(obj).closest("[serial]").attr("serial");
    if (!shoppingCartContent[id].discountLocked) {
      shoppingCartContent[id].Discount = obj.value;
    } else {
      shoppingCartContent[id].additionalDiscount = obj.value;
    }
    if (shoppingCartContent[id].Discount == "") {
      return;
    }
    shoppingCartToLocalStorage();
  //  loadedPages.shoppingCart.firstDC = false;
  //  loadedPages.shoppingCart.drawCart();
  },
  calculateRefund: function(showpay = false) {
      localStorage.directRefundChecked = (($("#dfw").html().trim() == "VAT Refund") ? "0" : "1")
      if (localStorage.customerCountry !== undefined) {
        var data = $.parseJSON(localStorage.customerCountry);
        if (data.id == "") {
          return;
        }
        if (data.CountryID !== undefined) {
          data.id = data.CountryID;
        }
        localStorage.isEU = loadedPages.shoppingCart.countryEu[data.id].EUMember;
      }

      if (loadedPages.shoppingCart.currentInvoice != null && loadedPages.shoppingCart.firstDR) {

        if (loadedPages.shoppingCart.currentInvoice.directRefund == "1") {
            localStorage.directRefundChecked = "1";
          $("#dfw").html("Direct Refund");
        } else {
          $("#dfw").html("VAT Refund");
            localStorage.directRefundChecked = "0";
        }
        loadedPages.shoppingCart.firstDR = false;
      }
      if (localStorage.isEU == "0" && $("#directRefund")[0].checked == true) {
          if (localStorage.directRefundChecked == "1") {
            setTimeout(function() {
                  $("[refund]").show();
                  $(".refund").show();
                  $(".norefund").hide();
                //  $("[refundcontainer]").show();
                  $("[norefund]").hide();
                  if (showpay) {
                    $("#dfw").html("Direct Refund");
                    $(".refund").show();
                    $(".norefund").hide();

                  }
              }, 50);
            } else {
              $("#dfw").html("VAT Refund");
            //  $("[refundcontainer]").show();
              $(".refund").hide();
              $(".norefund").show();
              $("[refund]").show();
              $("[norefund]").hide();
            }
      } else {

            $("#dfw").html("VAT Refund");
            $(".refund").hide();
            $(".norefund").show();
            $("[refund]").hide();
            $("[norefund]").show();

      }
    //  loadedPages.shoppingCart.drawCart();
  },
  checkIsLogged: function() {

    if (localStorage.customerCountry === undefined) {
      showModal({
        type: "error",
        showCancelButton: false,
        confirmButtonText: "CONTINUE",
        title: "Select country of customer origin. Is mandatory."
      })
      return false;
    }

    if (localStorage.sp === undefined) {
      $("#login").modal("show");
    } else {

      if (loadedPages.shoppingCart.approvedRequested && loadedPages.shoppingCart.currentInvoice == null) {

        if ($("#dapproved").val() == "") {
           $("#discountApproved").modal("show");
             return false;
        }

      }
      localStorage.invoiceDiscount = $("#masterdiscount").val();

      localStorage.directRefund = (($("#dfw").html() == "Direct Refund") ? "1" : "0");
      localStorage.total_div = $("#total_div")[0].outerHTML;

      $("[spdiscount1]").val($("#masterdiscount").val());

      if (localStorage.directRefund == "1") {

        localStorage.toBePaid =   $("[rfnd]").val();
      } else {
        localStorage.toBePaid =   $("[nrfnd]").val();
      }
      localStorage.generalDiscount = $("#masterdiscount").val();
      try {
        AndroidFullScreen.immersiveMode(function() {}, function() {});
      } catch (err) {

      }
      if (Object.keys(shoppingCartContent).length == 0) {
        showModal({
          type: "error",
          showCancelButton: false,
          confirmButtonText: "CONTINUE",
          title: "Shopping cart is empty."
        })
        return false;
      }
      loadPage("checkout");
    }
  },
  discountClickedFired: function() {
    loadedPages.shoppingCart.discountClicked = !loadedPages.shoppingCart.discountClicked;
    loadedPages.shoppingCart.showDiscount = loadedPages.shoppingCart.discountClicked;

      $('#masterdiscount').show();
      $('[spdiscount]').show();
      $('[spdiscount1]').show();
  //  }

  //  loadedPages.shoppingCart.drawCart();
},
switchMasterDiscountType: function(obj) {

  if ($(obj).val() == "euro") {
    $("#masterdiscount").val($("#masterdiscount").val().replace("%", ""));
  } else {
    $("#masterdiscount").val($("#masterdiscount").val().replace("%", "") + "%");
  }
  localStorage.generalDiscount = $("#masterdiscount").val();
},
switchPercent: function(obj) {
    var dfield = $(obj).closest("table").find("td").eq(0).find("input");
    var sno = $(obj).attr("serial");

    var o = $(obj);
    shoppingCartContent[sno]["discountType"] = o.val();

//    o[0].className = "discounttype " + shoppingCartContent[sno]["discountType"];
    if (o.val() == "") {
      dfield.val(dfield.val() + "%");
    } else {
      dfield.val(dfield.val().replace("%", ""));
    }
    shoppingCartContent[sno]["Discount"] = dfield.val();
    shoppingCartToLocalStorage();
  //  loadedPages.shoppingCart.discounts(dfield[0]);
},
checkDApproved: function() {
  $('#discountApproved').modal('hide');
  $('[spdiscount1]').show();
  $('#masterdiscount').show();
},
applyDiscount: function(obj) {
     var dtype = $(obj).closest("table").find("#percenttype").val();

     var dfield = $(obj).closest("table").find("td").eq(0).find("[spdiscount]");
     if (dfield.val() == "" || dfield.val() == "0" || dfield.val() == "%" || dfield.val() == "0%") {

       dfield.val("");
       shoppingCartContent[$(obj).closest("[serial]").attr("serial")].Discount = "";
       shoppingCartContent[$(obj).closest("[serial]").attr("serial")].realPrice = shoppingCartContent[$(obj).closest("[serial]").attr("serial")].SalesPrice;
       shoppingCartContent[$(obj).closest("[serial]").attr("serial")].toPay = shoppingCartContent[$(obj).closest("[serial]").attr("serial")].quantity * shoppingCartContent[$(obj).closest("[serial]").attr("serial")].realPrice;
      shoppingCartToLocalStorage();
//       loadedPages.shoppingCart.drawCart();
       return;
     }

     if (dtype != "euro") {
       dfield.val(dfield.val().replace("%", ""));
       dfield.val(dfield.val() + "%");
     } else {
       dfield.val(dfield.val().replace("%", ""));
     }

     loadedPages.shoppingCart.discounts(dfield[0]);
  },
  setToPay: function(obj) {
    var $el = $(obj);
  //  loadedPages.shoppingCart.discountClickedFired();
    var vv = parseFloat($el.val());

    if (!$el.hasClass("refund")) {
      var dsc = parseInt(loadedPages.shoppingCart.total) - $el.val();
      loadedPages.shoppingCart.discountClickedFired();
      $("select[spdiscount1]").val("euro");
    //  loadedPages.shoppingCart.switchMasterDiscountType($("#mdt")[0]);
      $("#masterdiscount").val(dsc);
      delete localStorage.generalDiscount;

      loadedPages.shoppingCart.drawCart();
    } else {
      var vl = vv;
      var vt = (vv / 100) * 19.054;
      vl = vl + vt;
      var dsc = parseInt(loadedPages.shoppingCart.total) - vl;
    //  loadedPages.shoppingCart.switchMasterDiscountType($("#mdt")[0]);
      $("#masterdiscount").val(dsc);
    //  delete localStorage.generalDiscount;
//      loadedPages.shoppingCart.drawCart();
    }
  },
  loadSection: function(section) {
    if (section != "main") {
      $("[checkoutenabled]").hide();
      $("[checkoutdisabled]").show();
      $.getScript("/pages/js/" + section + ".js", function() {
          loadedPages[section].initialize();
      });

    } else {
      $("[checkoutenabled]").show();
      $("[checkoutdisabled]").hide();
      delete loadedPages.addproduct;
    }
    $("[section]").hide();
    $("[section='" + section + "']").show();
  },
  calcInput: function(obj, add) {
    var el = $(obj);
    var tgt = el.closest("tr").find("td").eq(1).find("input");
    if (add < 0 && tgt.val() <= 1) {
      return;
    }
    var oldvalue = tgt.val();
    tgt.val(parseInt(tgt.val()) + add);
    loadedPages.shoppingCart.recalculate(tgt[0], oldvalue);
  },
  generalDisc: function() {
    if (isNaN(parseFloat($("#masterdiscount").val()))) {
      $("#masterdiscount").val("");
    }
    if ($("#masterdiscount").val() == "") {
      localStorage.generalDiscount = "";
      return;
    }
     if ($("#mdt").val() == "") {
      $("#masterdiscount").val($("#masterdiscount").val().replace(/%/g,"") + "%");
    } else {
      $("#masterdiscount").val($("#masterdiscount").val().replace(/%/g,""));
    }
    localStorage.generalDiscount = $("#masterdiscount").val();
  }
}
