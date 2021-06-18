loadedPages.checkout = {
    table: {},
    adminChargeID: "",
    vatRefundID: "",
    directRefund: false,
    cache: 0,
    invoiceID: "",
    documentName: "",
    currentInvoice: "",
    firstAddPayment: true,
    toursTable: null,
    iid: "",
    csid: "",
    initialize: function() {
      $("[parts]").css({
        position: "absolute",
        top: 50,
        width: "100%"
      })
      $("#stour").select2({
          allowClear: true,
      });


      $('#stour').on('select2:select', function(e) {
          var data = e.params.data;

          if (data.id == "-1") {
              $(this).removeClass('selected');
              $("#tours_div").removeClass("checked");
              $("#tnmbup").html("");
              $("#tours_div").addClass("checked");
              delete localStorage.tour;
              return;
          } else {
            if (data.id == "-2") {
              loadedPages.checkout.addTour();
            } else {
              loadedPages.checkout.table.$('tr.selected').removeClass('selected');
              localStorage.tour = JSON.stringify({PrivateID: null,ProjId: data.id, ProjName: data.text});
              $("#searchCustomer").val("");
              var dt = loadedPages.checkout.table.row(this).data();
              $("#tnmbup").html(data.id);
              $("#tours_div").addClass("checked");
              $("#2")[0].checked = true;
            }
          }
      });


        if (localStorage.customerInfo !== undefined) {

            customerInfoData = $.parseJSON(localStorage.customerInfo);
            for (var key in customerInfoData) {
                $("#customerForm").find("[name='" + key + "']").val(customerInfoData[key]);
            }
            $("#customerid").val(customerInfoData.customerid);
            $("#countries").val(customerInfoData["countryCode"]).trigger('change');
            var data = $('#countries').select2('data');
            customerInfoData["countryCode"] = data[0].id;
        }
        api.call("getExcangeRates", function(res) {
            $.each(res, function() {

                $('<option value="' + this.CurrencyCode + '" rate="' + (parseFloat(this.ExchangeRate) / 100) + '">' + this.CurrencyCode + '</option>').appendTo($("#pay_currency"));
            })
            loadedPages.checkout.initializeTours();
        }, {}, {});
        $("#mainModal").modal("hide");
        $("#fullshoppingcart").css({
            minHeight: window.innerHeight - 100,
            maxHeight: window.innerHeight - 100
        })

        $( "#saledate" ).datepicker({
          dateFormat: "dd/mm/yy"
        });

        $("#additionalInfo").find("[f]").bind("change", function() {
          var dt = $("#saledate").datepicker( 'getDate' );
          localStorage.saledate = moment(dt).format("YYYY-MM-DD HH:mm:ss");
          localStorage.reference = $("#reference").val();
          localStorage.isproform = $("#proforma").val();
          localStorage.remark = $("#remark").val().replace(/\n/g, "<br />");
        })
        if (localStorage.saledate !== undefined) {
           var myDate = new Date(localStorage.saledate);
           $('#saledate').datepicker('setDate', myDate);
           $( "#saledate" ).val(moment(new Date(localStorage.saledate)).format("DD/MM/YYYY"));
        } else {
          $( "#saledate" ).datepicker("setDate", new Date());
        }
        if (localStorage.reference !== undefined) {
          $("#reference").val(localStorage.reference);
        }
        if (localStorage.isproform !== undefined) {
          $("#proforma").val(localStorage.isproform);
        }
        if (localStorage.remark !== undefined) {
          $("#remark").val(localStorage.remark.replace(/<br \/>/g, "\n"));
        }

      //  $("#saledate").val(moment(new Date()).format("DD/MM/YYYY"));
        var ii = "";
        ii = ((Object.keys(shoppingCartContent).length > 1) ? " items " : " item ");
        $("#itemsinfo").html(Object.keys(shoppingCartContent).length + ii + parseFloat(localStorage.payNoRefund).toLocaleString("nl-NL", {
            style: 'currency',
            currency: "EUR"
        }))

        if (localStorage.customerCountry === undefined) {
            api.call("getCountries", function(res) {
                var data = [];
                $.each(res, function() {
                    var ths = this;
                    var obj = {
                        id: ths.CountryID,
                        text: ths.Country,
                        eu: ths.EUMember,
                        nationality: ths.Nationality
                    }

                    data.push(obj);
                })

                $("#countries").select2({
                    allowClear: false,
                    data: data,
                    placeholder: "Select a customer country origin",
                    allowClear: true,
                    width: '100%'
                });
                setTimeout(function() {

                    for (var key in customerInfoData) {
                        $("#customerForm").find("[name='" + key + "']").val(customerInfoData[key]);
                    }
                  //  $("#countries").val(customerInfoData["countryCode"]).trigger('change');
                }, 2000);
            }, {}, {});
        } else {
            var obj = $.parseJSON(localStorage.customerCountry);
            if (obj.Country !== undefined) {
              $("#cstc").html("Country: " + obj.Country);
              localStorage.isEU = $.parseJSON(localStorage.customerCountry).EUMember;
            } else {
              $("#cstc").html("Country: " + obj.text);
              localStorage.isEU = obj.eu;
            }
            $("#countries").hide();
            $("#cstc").show();
        }



        //loadedPages.checkout.initializeTours();
        var ww = setInterval(function() {
          if (loadedPages.checkout.toursTable != null) {
            console.log(loadedPages.checkout.toursTable);
            clearInterval(ww);
            yadcf.init(loadedPages.checkout.table, [{
                    style_class: "form-control",
                    filter_container_id: "filter_date",
                    filter_type: "text",
                    column_number: 0,
                },
                {
                    style_class: "form-control",
                    filter_container_id: "filter_nationality",
                    column_number: 3,
                    filter_type: "multi_select",
                    select_type: 'select2'
                },
                {
                    style_class: "form-control",
                    filter_container_id: "filter_operator",
                    column_number: 4,
                    filter_type: "multi_select",
                    select_type: 'select2'
                },
            ]);

            $("#filters_temp").appendTo($("#filters_body"));
            $("#filters_temp").css({
                visibility: "visible"
            })
          }
          if (localStorage.tour !== undefined) {
              var data = $.parseJSON(localStorage.tour);
              if (data.custom === undefined) {
                $("#tnmbup").html(data.ProjId);
              } else {
                $("#tnmbup").html(data.ProjId);

              }
              $('#' + data.DT_RowId).addClass("selected");
              $("#tdiv").animate({
                  scrollTop: $('#' + data.DT_RowId).offset().top - 200
              }, 500);
              $("#tours_div").addClass("checked");
          }
        //    $('#tours tbody').off('click')


        }, 100);
        $('#tours tbody').on('click', 'tr', function() {

            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                $("#tours_div").removeClass("checked");
                delete localStorage.tour;
            } else {

                loadedPages.checkout.table.$('tr.selected').removeClass('selected');
            //    alert(JSON.stringify(loadedPages.checkout.table.row(this).data()))
                localStorage.tour = JSON.stringify(loadedPages.checkout.table.row(this).data());
                $("#searchCustomer").val("");

                var dt = loadedPages.checkout.table.row(this).data();

                $("#tnmbup").html(dt.ProjId);

                $(this).addClass('selected');
                $("#tours_div").addClass("checked");
                $("#2")[0].checked = true;

            }
        });
        $("#customerForm").find("[name]").bind("change", function() {

            $.each($("#customerForm").find("[name]"), function() {
                customerInfoData[this.getAttribute("name")] = this.value;
            })
            localStorage.customerInfo = JSON.stringify(customerInfoData);
        })
        $.validator.addMethod("countrySelected", function(value, element) {
            var data = $('#countries').select2('data');
            return (data[0].id != "");
        }, "This field is mandatory");
        $(".form-group").css({
            marginBottom: 25
        })
        $(".form-control").css({
            minHeight: 40
        })
        $("#customerForm").validate({
            rules: {
                countries: {
                    countrySelected: true
                },
                name: {
                    required: true
                }
            },
            submitHandler: function(form) {
                var obj = {};
                var tour = $.parseJSON(localStorage.tour);
                $("#tourNo").val(tour.ProjId);
                $.each($("#customerForm").find("[name]"), function() {
                    obj[$(this).attr("name")] = $(this).val();
                })
                delete obj["countries"];

                obj["country"] = $("#countries").select2("data")[0].text;
                obj["countryCode"] = $("#countries").select2("data")[0].id;
                if ($("#cstc").is(":visible")) {
                    obj["country"] = $("#cstc").html().split(":")[1].trim();
                    obj["countryCode"] = $("#countries").select2("data")[0].id;
                } else {
                    obj["country"] = $("#countries").select2("data")[0].text;
                    obj["countryCode"] = $("#countries").select2("data")[0].id;
                }

                if (true) {

                    api.call("insertCustomer", function(res) {
                        try {
                            if (res.status == "ok") {
                                $("#customer_div").addClass("checked");
                                /*swal({
                                  type: "success",
                                  text: "Customer succesfully registered."
                                })*/
                                loadedPages.checkout.csid = res.customerid;
                                $("#customerid").val(res.customerid);
                                $("#2").hide();
                                $("#3").show();
                            } else {
                          /*      swal({
                                    type: "error",
                                    text: "Something went wrong " + JSON.stringify(res)
                                })*/
                            }
                        } catch (err) {

                        }
                    }, obj, {})

                } else {
                    obj["customerid"] = $("#customerid").val();

                    api.call("updateCustomer", function(res) {

                        if (res.status == "ok") {
                            $("#2").hide();
                            $("#3").show();

                        } else {
                          /*  swal({
                                type: "Error",
                                text: "Something went wrong"
                            })*/
                        }
                    }, obj, {})
                }
            }
        });
        $('#searchCustomer').typeahead({
            autoSelect: true,
            maxLength: 5,
            afterSelect: function(obj) {
                api.call("getCustomerByid", function(res) {

                    $("#customerid").val(obj.id);
                    var d = res[0];
                    for (var k in d) {
                        $("#customerForm").find("[name='" + k + "']").val(d[k]);
                        customerInfoData[k] = d[k];
                    }
                    customerInfoData["countryCode"] = d["countryCode"];
                    $("#countries").val(d["countryCode"]).trigger('change');


                }, {
                    query: obj.id
                }, {}, {})
            },
            source: function(query, result) {
                $.ajax({
                    url: "https://costercatalog.com/api/index.php?request=searchCustomers",
                    data: { query: query, secret: "scddddedff2fg6TH22" },
                    dataType: "json",
                    type: "POST",

                    success: function(data) {
                        result($.map(data, function(item) {
                            return item;
                        }));

                    }
                });
            }
        });
        setTimeout(function() {
            if (window.StatusBar) {
                try {
                    window.StatusBar.show();
                    setTimeout(function() {
                        window.StatusBar.hide();
                    }, 5);
                } catch (err) {

                }
            }
        }, 1000);
      //  loadedPages.checkout.setPayments();
        setTimeout(function() {

            if (invoiceLocked == 2) {
              $('#1').hide();
              $('#2').hide();
              $('#3').show();
              $("[lock]").hide();
              $("#additionalInfo").hide();
            }

          }, 1500);
    },
    setPayments: function() {

        api.call("getPMethods", function(res) {
            $.each(res, function() {
                if (this.IsAdminCharge == "0" && this.IsVatRefund == "0") {
                    $("<option value='" + this.PaymentID + "' iswwftcheck='" + this.IsWWFTCheck + "'>" + this.Payment + "</option>").appendTo($("#paymentMethods"));
                    $("<option value='" + this.PaymentID + "' iswwftcheck='" + this.IsWWFTCheck + "'>" + this.Payment + "</option>").appendTo($("#paymentsTable").find("tbody").find("tr").eq(1).find("select").eq(0));
                } else {
                    $("<option disabled value='" + this.PaymentID + "' iswwftcheck='" + this.IsWWFTCheck + "'>" + this.Payment + "</option>").appendTo($("#paymentMethods"));
                    $("<option disabled value='" + this.PaymentID + "' iswwftcheck='" + this.IsWWFTCheck + "'>" + this.Payment + "</option>").appendTo($("#paymentsTable").find("tbody").find("tr").eq(1).find("select").eq(0));
                    if (this.IsAdminCharge != "0") {
                        loadedPages.checkout.adminChargeID = this.PaymentID;
                    }
                    if (this.IsVatRefund != "0") {
                        loadedPages.checkout.vatRefundID = this.PaymentID;
                    }
                }
            })
            loadedPages.checkout.toggleVAT();

        }, {}, {});

    },
    toggleVAT: function() {
        vatRefund = (localStorage.directRefund == "1");
        if (true) {
            $.each($("#paymentsTable").find("tbody").find("tr"), function(ind) {
                if (ind > 0) {
                    $(this).remove();
                }
            });

            if (vatRefund) {
                var tpp = parseInt(localStorage.payWithRefund);
                if (Object.keys(payments).length == 0) {
                  loadedPages.checkout.calculatePayments();
                }
            } else {
                var tpp = parseInt(localStorage.payNoRefund);
            }
            if (tpp < 0) {
                tpp = 0;
            }
            if (Object.keys(payments).length == 0) {

                  var tr = $("#master").clone();
                  tr.find("select").eq(0).val("1");
                  //  tr.find("select").eq(1).hide();
                  tr.find("input").attr("realvalue", tpp);
                  tr.find("input").attr("euro", tpp);
                  //    alert(parseFloat(tpp))
                  //      tr.find("input").val(tpp);

                  tr.find("input").val(parseFloat(tpp).toLocaleString("nl-NL", {
                      style: 'currency',
                      currency: 'EUR'
                  }));
                  tr.find("input").prop("disabled", false)
                  tr.find("select").eq(1).prop("disabled", false)
                  tr.find("td").eq(3).html(moment(new Date()).format("DD-MM-YYYY"));
                  tr.find("td").eq(3).attr("realdate",moment(new Date()).format("YYYY-MM-DD HH:mm:ss"));
                  tr.find("td").eq(4).attr("isold", "0");
                  tr.find("td").eq(4).attr("version", "");
                  tr.find("i").show();
                  tr.appendTo($("#paymentsTable").find("tbody"));
            } else {
              $.each(payments, function() {
                if (this.amount > 0) {
                    var ths = this;
                    var tr = $("#master").clone();
                    tr.find("select").eq(0).val(ths.paymentID);
                    //  tr.find("select").eq(1).hide();
                    tr.find("input").attr("realvalue", ths.amount);
                    tr.find("input").attr("euro", ths.amount);
                    if (ths.date !== undefined) {
                      tr.find("td").eq(3).html(moment(new Date(ths.date)).format("DD-MM-YYYY"));
                      tr.find("td").eq(3).attr("realdate",ths.date);
                      tr.find("td").eq(4).attr("isOld",ths.isOld);
                      tr.find("td").eq(4).attr("version", ths.version);
                    } else {
                      tr.find("td").eq(3).html(moment(new Date()).format("DD-MM-YYYY"));
                      tr.find("td").eq(3).attr("realdate",moment(new Date()).format("YYYY-MM-DD HH:mm:ss"));
                      tr.find("td").eq(4).attr("isOld","0");
                      tr.find("td").eq(4).attr("version", "");
                    }
                    tr.find("input").val(parseFloat(ths.amount).toLocaleString("nl-NL", {
                        style: 'currency',
                        currency: ths.currency
                    }));
                    tr.find("input").prop("disabled", true);
                    tr.find("select").eq(1).prop("disabled", true);
                    tr.find("select").eq(0).prop("disabled", true);
                    tr.find("i").show();
                    tr.appendTo($("#paymentsTable").find("tbody"));
                  }
              })
              setTimeout(function() {
              loadedPages.checkout.addPayment();
            }, 1500);
              return;
            }
            var tp = 0;
            $.each($("#paymentsTable").find("tbody").find("tr"), function() {
                if ($(this).find("select").eq(0).val() == "7") {
                    $(this).remove();
                }
            })
            $.each($("#paymentsTable").find("tbody").find("tr").not(":last"), function(ind) {

                if (this.id != "administrative") {
                    if ($(this).find("input").length > 0) {

                        if ($(this).find("select").eq(0).val() != "7") {
                            var thenum = $(this).find("input").val().replace(/^\D+/g, '');
                            var n = thenum.replace(/\./g, "");
                            n = n.replace(/\,/g, ".");
                            tp += parseFloat(n);
                        }
                    }
                }

            })
            if (vatRefund) {
                var tpp = parseFloat(localStorage.payWithRefund);
                loadedPages.checkout.calculatePayments();
            } else {
                var tpp = parseFloat(localStorage.payNoRefund);
                $("#administrative").remove();
                $("#vatrefund").remove();
                loadedPages.checkout.calculatePayments();
            }
        } else {
            $("#administrative").remove();
            $("#vatrefund").remove();
            loadedPages.checkout.calculatePayments();
        }


    },
    calculatePayments: function() {

        var tp = 0;
        payments = {};
        $.each($("#paymentsTable").find("tbody").find("tr"), function() {
            if ($(this).find("select").eq(0).val() == "7") {
                $(this).remove();
            }
        })
        loadedPages.checkout.cache = 0;
        $.each($("#paymentsTable").find("tbody").find("tr"), function(ind) {
            if (this.id != "administrative") {
                var ths = this;

                if ($(this).find("input").length > 0) {
                    var m = 1;
                    if ($(ths).find("input").val().indexOf("-") > -1) {
                        m = -1;
                    }
                    var thenum = $(ths).find("input").val().replace(/^\D+/g, '');
                    if ($(ths).find("select").eq(0).find("option:selected").attr("value") == "1") {
                        loadedPages.checkout.cache += parseFloat($(ths).find("input").attr("euro"));
                    }
                    var n = thenum.replace(/\./g, "");
                    var n = n.replace(/\,/g, ".");
                    //    n = parseFloat(n) / (parseFloat($(ths).closest("tr").find("td").eq(1).find("select").find("option:selected").attr("rate")));
                    $(ths).find("input").val(parseInt(n * m).toLocaleString("nl-NL", {
                        style: 'currency',
                        currency: $(ths).find("td").eq(1).find("select").val()
                    }))

                    var obj = {
                        paymentID: $(ths).find("select").eq(0).val(),
                        paymentMethod: $(ths).find("select").eq(0).find(":selected").text(),
                        currency: $(ths).find("select").eq(1).find(":selected").attr("value"),
                        amount: parseFloat(n),
                        date: $(ths).find("td").eq(3).attr("realdate"),
                        isOld: $(ths).find("td").eq(4).attr("isOld"),
                        version: $(ths).find("td").eq(4).attr("version")
                    }

                    if ($(this).find("input").val() != "") {
                        var thenum = $(this).find("input").val().replace(/^\D+/g, '');
                        var n = thenum.replace(/\./g, "");
                        var n = n.replace(/\,/g, ".");
                        n = parseFloat(n) / (parseFloat($(this).closest("tr").find("td").eq(1).find("select").find("option:selected").attr("rate")));
                        tp += parseFloat(n);
                        obj.original = n;
                    }

                    payments[Object.keys(payments).length] = obj;
                }
            }

        })

        if (vatRefund) {
            var tpp = parseFloat(localStorage.payWithRefund);
        } else {
            var tpp = parseFloat(localStorage.payNoRefund);
        }

        $("#master").clone().appendTo($("#paymentsTable").find("tbody"));
        //  $("#paymentsTable").find("tbody").find("tr:last").find("select").eq(1).hide();
        $("#paymentsTable").find("tbody").find("tr:last").find("i").hide();
        $("#paymentsTable").find("tbody").find("tr:last").find("select").eq(0).val("7");
        $("#paymentsTable").find("tbody").find("tr:last").find("select").prop("disabled", true);
        $("#paymentsTable").find("tbody").find("tr:last").find("input").val(parseInt(tpp - tp).toLocaleString("nl-NL", {
            style: 'currency',
            currency: "EUR"
        }));

        $("#paymentsTable").find("tbody").find("tr:last").find("input").prop("disabled", true);
        $("#paymentsTable").find("tbody").find("input").unbind("change");
        var obj = {
            paymentID: $("#paymentsTable").find("tbody").find("tr:last").find("select").eq(0).val(),
            paymentMethod: $("#paymentsTable").find("tbody").find("tr:last").find("select").eq(0).find(":selected").text(),
            amount: parseInt(tpp - tp),
            date:$("#paymentsTable").find("tbody").find("tr:last").find("td").eq(3).attr("realdate"),
            isOld:$("#paymentsTable").find("tbody").find("tr:last").find("td").eq(4).attr("isOld"),
            version: $("#paymentsTable").find("tbody").find("tr:last").find("td").eq(4).attr("versio")
        }
        if (parseInt(tpp - tp) <= 0) {
            $("#addpayment").prop("disabled", true);
        } else {
            $("#addpayment").prop("disabled", false);
        }
        payments[Object.keys(payments).length] = obj;
        $("#paymentsTable").find("tbody").find("input").bind("change", function() {
            var slct = $(this).closest("tr").find("td").eq(1).find("select");
            var rate = slct.find("option:selected").attr("rate");
            $(this).attr("euro", parseFloat($(this).val() / rate));
            //  $(this).val(parseFloat($(this).val()).toLocaleString("nl-NL",{ style: 'currency', currency: slct.find("option:selected").attr("value")}));
            loadedPages.checkout.calculatePayments();
        });

    },
    deletePayment: function(obj) {
        var tr = $(obj).closest("tr");
        showModal({
            title: "Remove this payment?",
            allowBackdrop: false,
            showClose: false,
            confirmCallback: function() {
                tr.remove();
                loadedPages.checkout.calculatePayments();
            }
        })
    },
    addPayment: function() {

      var tr = $("#master").clone();
      if (localStorage.openInvoice === undefined || !loadedPages.checkout.firstAddPayment) {
        $("#paymentsTable").find("tbody").find("tr:last").remove();
      }
      tr.appendTo($("#paymentsTable").find("tbody"));
      $("#paymentsTable").find("tbody").find("tr:last").find("select").eq(0).val("1");
      $("#paymentsTable").find("tbody").find("tr:last").find("select").eq(0).prop("disabled", false);
      $("#paymentsTable").find("tbody").find("tr:last").find("select").eq(1).prop("disabled", false);
      $("#paymentsTable").find("tbody").find("tr:last").find("input").prop("disabled", false);
      $("#paymentsTable").find("tbody").find("tr:last").find("td").eq(3).html(moment(new Date()).format("DD-MM-YYYY"));
      $("#paymentsTable").find("tbody").find("tr:last").find("td").eq(3).attr("realdate",moment(new Date()).format("YYYY-MM-DD HH:mm:ss"));
      $("#paymentsTable").find("tbody").find("tr:last").find("td").eq(4).attr("isOld","0");
      $("#paymentsTable").find("tbody").find("tr:last").find("td").eq(4).attr("version","");
      $("#paymentsTable").find("tbody").find("tr:last").find("i").show();

       loadedPages.checkout.firstAddPayment = false;
        var tp = 0;

        $.each($("#paymentsTable").find("tbody").find("tr"), function() {
            if ($(this).find("select").eq(0).val() == "7") {
                $(this).remove();
            }
        })
        if (localStorage.openInvoice === undefined || !loadedPages.checkout.firstAddPayment) {

              $.each($("#paymentsTable").find("tbody").find("tr").not(":last"), function(ind) {
                  if (this.id != "administrative") {
                      if ($(this).find("input").length > 0) {
                          if ($(this).find("select").eq(0).val() != "7") {
                                var mi = ($(this).find("input").val().indexOf("-") > -1) ? -1 : 1;
                              var thenum = $(this).find("input").val().replace(/^\D+/g, '');
                              var n = thenum.replace(/\./g, "");
                              n = parseFloat(n * mi) / (parseFloat($(this).closest("tr").find("td").eq(1).find("select").find("option:selected").attr("rate")));
                              tp += parseFloat(n);
                          }
                      }
                  }

              })
            } else {
              $.each($("#paymentsTable").find("tbody").find("tr"), function(ind) {
                  if (this.id != "administrative") {
                      if ($(this).find("input").length > 0) {
                          if ($(this).find("select").eq(0).val() != "7") {
                              var mi = ($(this).find("input").val().indexOf("-") > -1) ? -1 : 1;
                              var thenum = $(this).find("input").val().replace(/^\D+/g, '');
                              var n = thenum.replace(/\./g, "");
                              n = parseFloat(n * mi) / (parseFloat($(this).closest("tr").find("td").eq(1).find("select").find("option:selected").attr("rate")));
                              tp += parseFloat(n);
                          }
                      }
                  }
              })
            }
        if (vatRefund) {
            var tpp = parseFloat(localStorage.payWithRefund);
        } else {
            var tpp = parseFloat(localStorage.payNoRefund);
        }
        $("#paymentsTable").find("tbody").find("tr:last").find("input").attr("euro", tpp - tp);
        $("#paymentsTable").find("tbody").find("tr:last").find("input").attr("realvalue", tpp - tp);
        $("#paymentsTable").find("tbody").find("tr:last").find("input").val(parseFloat(tpp - tp).toLocaleString("nl-NL", {
            style: 'currency',
            currency: "EUR"
        }));
        loadedPages.checkout.calculatePayments();
    },
    changeCurrency: function(obj) {
        var crt = parseFloat($(obj).find("option:selected").attr("rate"));
        var vl = $(obj).closest("tr").find("td").eq(2).find("input").attr("euro");
        $(obj).closest("tr").find("td").eq(2).find("input").val(parseInt(vl * crt).toLocaleString("nl-NL", {
            style: 'currency',
            currency: $(obj).find("option:selected").attr("value")
        }));
        var pid = $(obj).closest("tr")[0].rowIndex - 1;
        var vl = $(obj).closest("tr").find("td").eq(2).find("input").val();
        var thenum = vl.replace(/^\D+/g, '');
        var n = thenum.replace(/\./g, "");
        n = n.replace(/\,/g, ".");

        var obj = {
            paymentID: $(obj).closest("tr").find("select").eq(0).val(),
            paymentMethod: $(obj).closest("tr").find("select").eq(0).find(":selected").text(),
            currency: $(obj).closest("tr").find("select").eq(1).find(":selected").attr("value"),
            amount: parseInt(n),
            original: $(obj).closest("tr").find("td").eq(2).find("input").attr("euro")
        }
        payments[pid] = obj;

    },
    newInvoice: function() {
        shoppingCartContent = {};

        if (Object.keys(shoppingCartContent).length == 0) {
            $("#toggleShoppigCart").addClass("empty");
        } else {
            $("#toggleShoppigCart").removeClass("empty");
        }
        loadPage("invoice");
    },
    invoice: function() {
        var checkd = 0;
        $.each($("[step]"), function() {
            if ($(this).hasClass("checked")) {
                checkd++;
            }
        })
        if (checkd != 3) {
            swal({
                type: "warning",
                text: "Confirm all steps please."
            })
            return;
        } else {

            mail();
        }
    },
    paymentMethodChanged: function(obj) {

        if ($(obj).val() == "1") {
            $(obj).closest("tr").find("td").eq(1).find("select").prop("disabled", false);
        } else {
            $(obj).closest("tr").find("td").eq(1).find("select").val("EUR");
            var euro = $(obj).closest("tr").find("td").eq(2).find("input").attr("euro");
            $(obj).closest("tr").find("td").eq(2).find("input").val((parseFloat(euro)).toLocaleString("nl-NL", {
                style: 'currency',
                currency: "EUR"
            }));
            $(obj).closest("tr").find("td").eq(1).find("select").prop("disabled", true);
        }
        loadedPages.checkout.calculatePayments();
    },
    prepareOverview: function() {

        var sp = $.parseJSON(localStorage.sp);
        console.log(sp)
        $("#served").html("");
        $("<span>You have been served by <b>" + sp.Employee + "</b> in " + localStorage.showRoomName + "</span>").appendTo($("#served"));
        $("#consultant").html(sp.Employee.replace(/\s/g, "").trim() + "@costerdiamonds.com");
        var tour = $.parseJSON(localStorage.tour);
        try {
            var sc = $.parseJSON(localStorage.customerCountry);
            if (sc.eu == "0") {

                if (localStorage.directRefund) {
                    $("<span>We haven processed a Fast Refund.</span>").appendTo($("#notice"));
                } else {
                    $("<span>We have given you a VAT form for refund purposes.</span>").appendTo($("#notice"));
                }
            }
        } catch (err) {

        }

        if (loadedPages.checkout.cache > 1000) {
            $("<span>Due to company regulations we require a copy of the customer's identitification details.</span>").appendTo($("#cache"));
        }
        $("<span>Tour no. " + tour.ProjId, +", " + moment(new Date(tour.AVisitDateTime)).format("DD.MM.YYYY HH:mm") + "</span>").appendTo($("#tour"));

        if ($("#name").val() != "") {
            var nma = $("#name").val().split(" ");
            var pt = ($("#ptitle").val() != "Nvt.") ? $("#ptitle").val() : "";
            var lname = "";
            for (var i=1;i<nma.length;i++) {
              lname += (nma[i] + " ");
            }
            $("[firstname]").html(pt + " " + ((nma[0] !== undefined) ? nma[0].substring(0,1).toUpperCase() : "") + ". " + lname);
            $("[firstname]").parent().show();
        } else {
            $("[firstname]").parent().hide();
        }
        if ($("#hotel").val() != "") {
            $("[hotel]").html($("#hotel").val());
            $("[hotel]").parent().show();
        } else {
            $("[hotel]").parent().hide();
        }
        if ($("#address1").val() != "") {
            $("[address1]").html($("#address1").val());
            $("[address1]").parent().show();
        } else {
            $("[address1]").parent().hide();
        }
        if ($("#ringsize").val() != "") {
            $("[ringsize]").html("Ring size: " + $("#ringsize").val());
            $("[ringsize]").parent().show();
        } else {
            $("[ringsize]").parent().hide();
        }
        if ($("#address2").val() != "") {
            $("[address2]").html($("#address2").val());
            $("[address2]").parent().show();
        } else {
            $("[address2]").parent().hide();
        }
        if ($("#zip").val() != "" || $("#city").val() != "") {
            $("[city]").html($("#zip").val() + " " + $("#city").val());
            $("[city]").parent().show();
        } else {
            $("[city]").parent().hide();
        }

        if ($("#cstc").is(":visible")) {
            $("[country]").html($("#cstc").html());
        } else {
            if ($("#country").val() != "") {
                $("[country]").html($("#countries").select2('data')[0].text);
                $("[country]").parent().show();
            } else {
                $("[country]").parent().hide();
            }
        }
        if ($("#telephone").val() != "") {
            $("[telephone]").html("T " + $("#telephone").val());
            $("[telephone]").parent().show();
        } else {
            $("[telephone]").parent().hide();
        }
        if ($("#email").val() != "") {
            $("[email]").html($("#email").val());
            $("[email]").parent().show();
        } else {
            $("[email]").parent().hide();
        }
        $("#items").html("");
        $("#total_div").html("");
        var ii = 0;
        var total = 0;

        for (var key in shoppingCartContent) {
          var obj = shoppingCartContent[key];
       //   alert(obj.Discount)

          obj.Discount = ((obj.Discount == "0%") ? "" : (obj.Discount));
          obj.Discount = obj.Discount.replace("%%", "%");
          obj.imageURL = obj.imageURL.replace("50px", "100px");
          ii += parseInt(obj.quantity);
          total += parseInt(obj.toPay);
          var html = "<div root style='font-size:14px;'><div serial='" + obj.SerialNo + "' style='border-top:1px solid #e2e2e2;min-height:115px;border-bottom:1px solid #e2e2e2;padding:10px;padding-bottom:20px;width:100%;position:relative;'>";
          html += "<div>" + ((obj.imageURL != "") ? obj.imageURL : "<img style='width:100px;' src='https://costercatalog.com/coster/www/images/crown.png' />");
          html += "<div style='position:absolute;top:10px;left:120px;color:#ADADAD;'>" + obj.SerialNo + "<br />"
          html += "<span productname style='color:black;max-width:300px;font-size:11px;'>" + obj.productName.replace("undefined","") + "</span></div>";

           html += "<div style='position:absolute;top:10px;right:0px;color:black;font-size:13px;'>";
           html += "<div style='float:right;'>" + "<span>" + obj.quantity + "X&nbsp;</span>" + "<span realvalue='" + parseFloat(obj.realPrice) + "'>" + (parseFloat(obj.realPrice) * 1).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</span></div>";
           html += "<br /><span style='float:right;font-weight:bold;'>" + (parseFloat(obj.toPay) * 1).toLocaleString("nl-NL",{ style: 'currency', currency: "EUR" }) + "</span></div>";

           html += "<input spdiscount onchange='loadedPages.shoppingCart.discounts(this);' value='" + obj.Discount + "' type='text' class='form-control' style='clear:both;text-align:right;float:right;width:85px;display:none;' placeholder='Discount' /><br />";
           html += "</div></div></div>";
           $(html).appendTo($("#items"));

        }
        var dv = $(localStorage.total_div);
        var tb = dv.find("table");
        var sgn = (tb.find(".discounttype").hasClass("euro")) ? "€" : "%";

        if (localStorage.invoiceDiscount !== undefined) {
          if (localStorage.invoiceDiscount != "") {
            if (sgn == "€") {
                tb.parent().html(sgn + " " + parseFloat(localStorage.invoiceDiscount).toLocaleString("nl-NL", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            } else {
                tb.parent().html(localStorage.invoiceDiscount);
            }
          } else {
            tb.parent().html("");
          }
        }
        dv.appendTo($("#total_div"));
        if (localStorage.invoiceDiscount == "") {
            $("#total_div").find("#total").hide();
        }
        $("<tr><td style='text-align:left;'><b>To be paid</b></td><td style='padding-left:5px;'><b>" + localStorage.toBePaid + "</b></td></tr>").appendTo($("#total_div").find("table").find("tbody"));
        $.each($("#total_div").find("input"), function() {
            $(this).val(localStorage.invoiceDiscount);
            $(this).prop("disabled", true);
            if ($(this).val() == "") {
                $(this).closest("tr").remove();
            }
        })
        $("#pt").find("tr").remove();
        $.each($("#paymentsTable").find("tr"), function(ind) {
            if (ind > 0) {
                if ($(this).find("select").eq(0).val() != "7") {
                    $("<tr><td style='text-align:left;'>" + $(this).find("select").eq(0).find("option:selected").text() + "</td><td style='padding-left:5px;'>" + $(this).find("input").val() + "</td></tr>").appendTo($("#pt"));
                } else {
                    var thenum = $(this).find("input").val().replace(/^\D+/g, '');
                    var n = thenum.replace(/\./g, "");
                    var n = n.replace(/\,/g, ".");
                    if (Math.abs(n) > (parseFloat(localStorage.toBePaid) / 100)) {
                        $("<tr><td style='text-align:left;'>Change</td><td style='padding-left:5px;'>" + $(this).find("input").val() + "</td></tr>").appendTo($("#pt"));
                    }
                }
            }
        })
        $("#3").hide();
        $("#4").show();
    },
    shareOverview: function() {

        var node = document.getElementById('share');
        $.LoadingOverlay("show", optionsLoader);
        domtoimage.toPng(node)
            .then(function(dataUrl) {
                $("body").append("<img id='hiddenImage' src='" + dataUrl + "' />");
                var width = $('#hiddenImage').width();
                var height = $('#hiddenImage').height();
                $('#hiddenImage').remove();
                var doc = new jsPDF("p", "mm", "a5");
                var width = doc.internal.pageSize.getWidth();
                var height = doc.internal.pageSize.getHeight();
                var imgProps = doc.getImageProperties(dataUrl);
                var pdfHeight = (imgProps.height * width) / imgProps.width;
                doc.addImage(dataUrl, 'PNG', 0, 0, width, pdfHeight);
                var b64 = btoa(doc.output());

                $.LoadingOverlay("hide");
                window.plugins.socialsharing.shareViaEmail(
                    "", // can contain HTML tags, but support on Android is rather limited:  http://stackoverflow.com/questions/15136480/how-to-send-html-content-with-image-through-android-default-email-client
                    'Overview of your order',
                    null, // TO: must be null or an array
                    null, // CC: must be null or an array
                    null, // BCC: must be null or an array
                    "data:application/pdf;base64," + b64, // FILES: can be null, a string, or an array
                    onSuccess, // called when sharing worked, but also when the user cancelled sharing via email. On iOS, the callbacks' boolean result parameter is true when sharing worked, false if cancelled. On Android, this parameter is always true so it can't be used). See section "Notes about the successCallback" below.
                    onError // called when sh*t hits the fan
                );

            })
            .catch(function(error) {
                console.error('oops, something went wrong!', error);
            });


    },
    printOverview: function() {

        try {
            var type = "text/html";
            var title = "overview.html";
            var fileContent = "<html>Phonegap Print Plugin</html>";
            window.plugins.PrintPlugin.print(fileContent, function() {
                console.log('success')
            }, function() {
                console.log('fail')
            }, "", type, title);
        } catch (err) {
            alert("11111 " + err);
        }

    },
    writeInvoice: function() {

    },
    generateInvoice: function(mode) {
        try {
          if (localStorage.goingback == "1") {
            return;
          }
        } catch(err) {

        }
      //  var mode = $("#sign").attr("mode");
        $("#sign").modal("hide");
        $("[parts]").hide();
        $("#invoice").show();
        var tour = $.parseJSON(localStorage.tour);
        $("#cinf").html("");
        $("#customerInfo").clone().appendTo($("#cinf"));
        $("#tnmbr").html(tour.ProjId);
        var sp = $.parseJSON(localStorage.sp);
        var bc = textToBase64Barcode(15);
        $("#bar_image").attr("src", bc);
        $("#discountApproved").html(localStorage.dapproved);
        $("#mTableBody").html("");
        $("#pTable").html("");
        $("#summary").html("");
        $("#servedby").html("You have been served by <b>" + sp.Employee + "</b> in " + localStorage.showRoomName);
        $("#invoiceDate").html(moment(new Date()).format("DD-MM-YYYY HH:mm"));
        //  $("#invoiceDate").html("18-06-2020 16:33");
        var version = "";
        if (localStorage.openInvoice !== undefined) {
          var inc = $.parseJSON(localStorage.openInvoice);
          invoiceID = inc.invoiceid;
          if (inc.version == "null" || inc.version == null) {
            version = "A";
          } else {
            version = versions[versions.indexOf(inc.version) + 1];
          }
        }
        var h = "";
        var rclass = "even";
        for (var key in shoppingCartContent) {
            var obj = shoppingCartContent[key];
            if (obj.discountLocked && obj.Discount != "" && obj.Discount != "0" && obj.Discount != "0%") {
              if (obj.Discount.indexOf("%") == -1) {
                obj.Discount += "%";

              }
            }
              obj.Discount =   obj.Discount.replace("%%", "%");
            if (obj.additionalDiscount != "" && obj.discountLocked) {
                if (obj.additionalDiscount != "") {

                    var sm = parseFloat(obj.realPrice);
                    if (obj.additionalDiscount.indexOf("%") > -1) {
                        var prc = parseFloat(obj.additionalDiscount.replace("%", ""));
                        obj.realPrice = sm - ((sm / 100) * prc);
                    } else {
                        var prc = parseFloat(obj.additionalDiscount);
                        obj.realPrice = sm - prc;
                    }

                } else {

                    obj.realPrice = obj.startRealPrice;
                }

            }
            rclass = (rclass == "even") ? "" : "even";
            h += "<tr class='" + rclass + "'>";
            h += "<td style=''>" + obj.SerialNo + "</td>";
            h += "<td style='width:50%;max-width:50%;min-width:50%;'>" + obj.productName.replace("undefined", "") + "</td>";
            h += "<td style='padding-left:3px;text-align:right;'>€&nbsp;</td>";
            h += "<td style='text-align:right;'>" + obj.quantity + " X " + parseFloat(obj.SalesPrice).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td>";
            var aDisc = (obj.SalesPrice - obj.realPrice);
            var aDiscPercent = ((obj.SalesPrice - obj.realPrice) / obj.SalesPrice) * 100;
            var dd = "";
            if (obj.Discount != "") {
              obj.Discount = obj.Discount.replace("%%", "%");
                if (obj.Discount.indexOf("%") > -1) {
                    dd += "€&nbsp;-" + parseFloat(aDisc).toLocaleString("nl-NL", {minimumFractionDigits: 2, maximumFractionDigits: 2}) + "&nbsp;(" + obj.Discount + ")";
                } else {
                    dd += "€&nbsp;-" + parseFloat(obj.Discount).toLocaleString("nl-NL", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + "&nbsp(" + Math.floor(parseFloat(aDiscPercent)).toLocaleString("nl-NL", {minimumFractionDigits: 0, maximumFractionDigits: 2}) + "%)";
                }
            }
            var add = "";
            if (obj.additionalDiscount != "") {
                if (obj.additionalDiscount.indexOf("%") > -1) {
                    add += " " + obj.additionalDiscount;
                } else {
                    add += "&nbsp;€&nbsp;" + parseFloat(obj.additionalDiscount).toLocaleString("nl-NL", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
            obj["showPrice"] = "€&nbsp;" + parseFloat(obj.toPay).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            shoppingCartContent[key] = obj;
            h += "<td style='text-align:right;color:red;'>" + dd + add + "</td>";
            h += "<td style='padding-left:7px;text-align:right;'>€&nbsp;</td>";
            h += "<td style='text-align:right;'>" + parseFloat(obj.toPay).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td>";
            h += "</tr>";
        }
        if (localStorage.invoiceDiscount != "") {
            var ttd = "";
            var dd = "";
            if (localStorage.invoiceDiscount != "") {
                if (localStorage.invoiceDiscount.indexOf("%") > -1) {
                    ttd += "&nbsp;" + localStorage.invoiceDiscount + "";
                    dd = localStorage.invoiceDiscount;
                } else {
                    ttd += "€&nbsp;" + parseFloat(localStorage.discountAmount).toLocaleString("nl-NL", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    var prcnt = Math.floor((parseFloat(localStorage.discountAmount) / parseFloat(localStorage.grandTotal)) * 100);
                    dd = prcnt + "%";
                }
            }
            h += "<tr><td></td><td></td><td style='padding-top:1px;padding-bottom:1px;text-align:right;' colspan='3'>Subtotal:</td><td style='padding-top:1px;padding-bottom:1px;text-align:right;'>€&nbsp;</td><td style='padding-top:1px;padding-bottom:1px;text-align:right;'>" + parseFloat(localStorage.grandTotal).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td></tr>";

            localStorage.discountAmount = Math.floor(parseFloat(localStorage.discountAmount));

        //    h += "<tr><td></td><td></td><td colspan='3'  style='font-size: 5pt;padding-top:1px;padding-bottom:1px;'>Subtotal: </td><td style='padding-top:1px;padding-bottom:1px;text-align:right;'>€&nbsp;</td><td style='padding-top:1px;padding-bottom:1px;text-align:right;'>" + parseFloat(localStorage.grandTotal).toLocaleString("nl-NL",{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "</td></tr>";
            h += "<tr><td></td><td></td><td style='padding-top:1px;padding-bottom:1px;text-align:right;' colspan='3'>Discount (" + dd + "):&nbsp;</td><td style='padding-top:1px;padding-bottom:1px;text-align:right;'>€&nbsp;</td><td style='padding-top:1px;padding-bottom:1px;text-align:right;'>-" + parseFloat(localStorage.discountAmount).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td></tr>";
            //    h += "<tr><td></td><td></td><td colspan='3' style='padding-top:1px;padding-bottom:1px;'>Total: </td><td style='padding-top:1px;padding-bottom:1px;text-align:right;'>€&nbsp;</td><td style='padding-top:1px;padding-bottom:1px;text-align:right;font-size: 5pt;'>" + parseFloat(localStorage.total).toLocaleString("nl-NL",{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "</td></tr>";
        }
        if (true) {

            if (localStorage.discountAmount === undefined || localStorage.discountAmount == "") {
              localStorage.discountAmount = 0;
            }
            h += "<tr><td></td><td></td><td colspan='3' style='font-size:6pt;padding-left:10px;vartical-align:bottom;text-align:right;'>Total: </td><td style='border-top:1px solid #e2e2e2;text-align:right;font-size: 5pt;'>€&nbsp;</td><td style='border-top:1px solid #e2e2e2;text-align:right;font-size: 5pt;'>" + (parseFloat(localStorage.grandTotal) - parseFloat(localStorage.discountAmount)).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td></tr>";
        }

        if (localStorage.directRefund == "1") {
            var bb = parseInt(localStorage.torefund);

            h += "<tr><td></td><td></td><td colspan='3' style='font-size:5pt;padding-left:10px;text-align:right;'>VAT refund: </td><td style='text-align:right;font-size: 5pt;'>€&nbsp;</td><td style='text-align:right;font-size: 5pt;'>" + bb.toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td></tr>";
            h += "<tr><td></td><td></td><td colspan='3' style='font-size:5pt;padding-left:10px;text-align:right;'>To be paid: </td><td style='border-top:1px solid #e2e2e2;text-align:right;font-size: 5pt;'>€&nbsp;</td><td style='border-top:1px solid #e2e2e2;text-align:right;font-size: 5pt;'>" + parseInt(localStorage.payWithRefund).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td></tr>";

        }

        var tobepaid = 0;

        if (localStorage.directRefund == "1") {
            $("#dfund").show();
            $("#dfamount").html("€ " + (parseFloat(localStorage.torefund) - 0).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            tobepaid = parseInt(localStorage.total) - parseFloat(localStorage.torefund);
        } else {
            $("#dfund").hide();
            tobepaid = parseInt(localStorage.total);
        }
        $(h).appendTo($("#mTableBody"));
        /*  h = "<tr><td style='width:100%;font-size: 5pt;'>Total amount to be paid:</td>";
          h += "<td style='width:100px;text-align:left;font-size: 5pt;border-bottom:1px solid #e2e2e2;'>€</td>";
          h += "<td style='width:100px;text-align:right;font-size: 5pt;border-bottom:1px solid #e2e2e2;'>" + parseFloat(tobepaid).toLocaleString("nl-NL",{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "</td>";
          h += "</tr>";
          $(h).appendTo($("#pTable"));*/


        h = "";
        h += "<tr><td style='width:100%;text-align: left;font-size: 5pt;'>Total amount incl. VAT:</td>";
        h += "<td style='width:100px;text-align:left;padding-right:10px;font-size: 5pt;'>€</td>";
        h += "<td style='width:100px;text-align:right;font-size: 5pt;'>" + parseFloat(localStorage.total).toLocaleString("nl-NL", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + "</td>";
        h += "</tr>";

        h += "<tr><td style='width:100%;text-align: left;font-size: 5pt;'>Total amount excl. VAT:</td>";
        h += "<td style='width:100px;text-align:left;padding-right:10px;font-size: 5pt;'>€</td>";
        h += "<td style='width:100px;text-align:right;font-size: 5pt;'>" + parseFloat(localStorage.vatexcluded).toLocaleString("nl-NL", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + "</td>";
        h += "</tr>";

        h += "<tr><td style='width:100%;text-align: left;font-size: 5pt;'>VAT 21%:</td>";
        h += "<td style='width:100px;text-align:left;padding-right:10px;font-size: 5pt;'>€</td>";
        h += "<td style='width:100px;text-align:right;font-size: 5pt;'>" + parseFloat(localStorage.vat).toLocaleString("nl-NL", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + "</td>";
        h += "</tr>";
        var dt = $("#countries").select2('data');

        if (localStorage.isEU == "0") {
            var bb = parseFloat(localStorage.torefund);
            h += "<tr><td style='width:100%;text-align: left;font-size: 5pt;'>Admin Charge:</td>";
            h += "<td style='width:100px;text-align:left;padding-right:10px;font-size: 5pt;'>€</td>";
            h += "<td style='width:100px;text-align:right;font-size: 5pt;'>" + parseFloat(parseFloat(localStorage.admincharge) - 0).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td>";
            h += "</tr>";

            h += "<tr><td style='width:100%;text-align: left;font-size: 5pt;'>Vat Refund amount:</td>";
            h += "<td style='width:100px;text-align:left;padding-right:10px;font-size: 5pt;'>€</td>";
            h += "<td style='width:100px;text-align:right;font-size: 5pt;'>" + bb.toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td>";
            h += "</tr>";
            if (localStorage.directRefund == "0") {
                $("[norefund]").show();
            }
            if (localStorage.directRefund == "1") {
                $("[refund]").show();
            }
        }
        $(h).appendTo($("#summary"));
        var tpaid = 0;
        var chpaid = 0;
        var fp = true;
        for (var key in payments) {

            var pay = payments[key];
            if (pay.version == "") {
              pay["date"] = moment($("#saledate").datepicker( 'getDate' )).format("YYYY-MM-DD HH:mm:ss");

            }
            if (fp) {
                var pad = "padding-top:10px;";
                fp = false;
            } else {
                var pad = "";
            }
            if (pay.amount == "NaN") {
              pay.amount = 0;
            }
            if (isNaN(pay.amount)) {
              pay.amount = 0;
            }

            var dte = "";
            if (pay.date === undefined) {
              pay.date = moment(new Date()).format("YYYY-MM-DD HH:mm:ss");
              dte = moment(new Date()).format("DD-MM-YYYY");
            } else {
              dte = moment(new Date(pay.date)).format("DD-MM-YYYY");
            }
            if (pay.paymentID != "7" && pay.paymentID != "2" && parseFloat(pay.original) > 0) {
                h = "<tr><td></td><td></td><td colspan='3' style='font-size:5pt;padding-left:10px;text-align:right;'>";
                h +=  dte + "&nbsp;" + pay.paymentMethod + ": </td><td style='text-align:right;font-size: 5pt;'>€&nbsp;</td>";
                h += "<td style='text-align:right;font-size: 5pt;'>" + parseFloat(pay.original).toLocaleString("nl-NL", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + "</td></tr>";

                /*    h = "<tr><td style='" + pad + "width:100%;font-size: 5pt;text-align:right;'>" + pay.paymentMethod + "</td>";
                    h += "<td style='" + pad + "text-align:left;font-size: 5pt;'>"+ "€&nbsp;" + "</td>";
                    h += "<td style='" + pad + "width:100px;text-align:right;font-size: 5pt;'>" + parseFloat(pay.original).toLocaleString("nl-NL",{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "</td>";
                    h += "</tr>";*/

                if (pay.currency != "EUR") {
                    h = "<tr><td></td><td></td><td colspan='3' style='font-size:5pt;padding-left:10px;text-align:right;'>" + pay.paymentMethod + ": </td><td style='text-align:right;font-size: 5pt;'>€&nbsp;</td><td style='text-align:right;font-size: 5pt;'>" + parseFloat(pay.original).toLocaleString("nl-NL", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + "</td></tr>";
                    h += "<tr><td></td><td></td><td colspan='3' style='font-size:5pt;padding-left:10px;text-align:right;'></td><td style='text-align:right;font-size: 5pt;'></td><td style='text-align:right;font-size: 4pt;'>(" + pay.paymentMethod + " " + pay.currency + " " + parseFloat(pay.amount).toLocaleString("nl-NL", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ")</td></tr>";
                    /*      h = "<tr><td></td><td></td><td  style='font-size:5pt;padding-left:10px;text-align:right;'></td><td style='text-align:right;font-size: 5pt;'>€&nbsp;</td><td style='text-align:right;font-size: 5pt;'>(" + pay.paymentMethod + " " + pay.currency + " " + parseFloat(pay.amount).toLocaleString("nl-NL",{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ")</td></tr>";
                          h += "<tr><td></td><td></td><td></td>";
                          h += "<td style='width:100px;text-align:left;font-size: 5pt;'></td>";
                          h += "<td style='width:100px;text-align:right;font-size: 4pt;color: #e5e5e;'>(" + pay.paymentMethod + " " + pay.currency + " " + parseFloat(pay.amount).toLocaleString("nl-NL",{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ")</td>";
                          h += "</tr>";*/
                }
                if (pay.paymentID != "2") {
                    tpaid += parseFloat(pay.original);
                }
                if (pay.paymentID == "1") {
                    chpaid += parseFloat(pay.original);
                }
                $(h).appendTo($("#mTableBody"));
            }
        }
        var pdiff = (parseFloat(localStorage.total) / 100) * 1;
        if ((tpaid > tobepaid)) {
            h = "<tr><td></td><td><div style='min-height:10px;'></div></td><td colspan='3' style='font-size:5pt;padding-left:10px;text-align:right;'></td><td style='text-align:right;font-size: 5pt;'></td><td style='text-align:right;font-size: 5pt;'></td></tr>";

            /*  h = "<tr><td style='width:100%;font-size: 5pt;'><div style='min-height:10px;'></div></td>";
              h += "<td style='width:100px;text-align:left;font-size: 5pt;'></td>";
              h += "<td style='width:100px;text-align:right;font-size: 5pt;'></td>";
              h += "</tr>";*/
            $(h).appendTo($("#mTableBody"));
            h = "<tr><td></td><td></td><td colspan='3' style='font-size:5pt;padding-left:10px;text-align:right;color:black;'>Change: </td><td style='text-align:right;font-size: 5pt;'>€</td><td style='text-align:right;font-size: 5pt;'>" + parseInt(tpaid - tobepaid).toLocaleString("nl-NL", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "</td></tr>";

            /*  h = "<tr><td style='width:100%;font-size: 5pt;color:black;text-align:right;'>Change</td>";
              h += "<td style='color:black;text-align:left;font-size: 5pt;'>"+ "€" + "</td>";
              h += "<td style='color:black;text-align:right;font-size: 5pt;'>" + parseInt(tpaid - tobepaid).toLocaleString("nl-NL",{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "</td>";
              h += "</tr>";*/
            $(h).appendTo($("#mTableBody"));
        }
        var tp = tobepaid - tpaid;
        if (tp < 0) {
            var tt = 0;
        } else {
            var tt = tp;
        }
        h = "<tr><td></td><td><div style='min-height:10px;'></div></td><td colspan='3' style='font-size:5pt;padding-left:10px;text-align:right;'><b>Total amount due: </b></td><td style='border-top: 2px solid #e5e5e5;color:black;text-align:right;font-size: 5pt;'>&nbsp;€</td><td style='border-top: 2px solid #e5e5e5;color:black;text-align:right;font-size: 5pt;'><b>" + parseInt(tt).toLocaleString("nl-NL", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + "</b></td></tr>";

        /*    h = "<tr><td style='width:100%;font-size: 5pt;color:black;text-align:right;'><b>Total amount due:</b></td>";
            h += "<td style='border-top: 2px solid #e5e5e5;color:black;text-align:left;font-size: 5pt;'>"+ "<b>€</b>" + "</td>";
            h += "<td style='border-top: 2px solid #e5e5e5;color:black;text-align:right;font-size: 5pt;'><b>" + parseInt(tt).toLocaleString("nl-NL",{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "</b></td>";
            h += "</tr>";*/
        $(h).appendTo($("#mTableBody"));

        if (chpaid > 10000) {
            $("#cacheover").show();
        }
        localStorage.isEU = $.parseJSON(localStorage.customerCountry).eu;
        if (localStorage.isEU == "0" && localStorage.directRefund == "0") {
            $("#vatform").show();
        }
        if (localStorage.isEU == "0" && localStorage.directRefund == "1") {
            $("#fastrefund").show();
        }
        var node = document.getElementById('invoice');
        $.LoadingOverlay("show", optionsLoader);
        //  var invoiceID = ((  localStorage.invoiceID === undefined) ? "" : localStorage.invoiceID);

        if (localStorage.tour !== undefined) {
          var tour = $.parseJSON(localStorage.tour);
        }
        $("#sld").html($("#saledate").val());
        if ($("#reference").val() != "") {
          $("#rnm").html($("#reference").val());
          $("[reference]").show();
        }
        $("#rmrk").html(localStorage.remark);
        $("#inm").html(invoiceID + version);

        if (invoiceID == "") {
            if (localStorage.discountAmount == "") {
                localStorage.discountAmount = 0;
            }
            if (isNaN(parseFloat(localStorage.discountAmount))) {
                localStorage.discountAmount = 0;
            }
            var dt = $("#saledate").datepicker( 'getDate' );

            localStorage.saledate = moment(dt).format("YYYY-MM-DD HH:mm:ss");
            localStorage.reference = $("#reference").val();
            localStorage.isproform = $("#proforma").val();
            localStorage.remark = $("#remark").val().replace(/\n/g, "<br />");
            var obj = {

                customerid: $("#customerid").val(),
                showroom: localStorage.showRoomName,
                salesPerson: $.parseJSON(localStorage.sp)["Employee"],
                tourNo: tour.ProjId,
                total: parseFloat(localStorage.grandTotal),
                discount: localStorage.invoiceDiscount,
                discountAmount: parseFloat(localStorage.discountAmount),
                discountApproved: localStorage.dapproved,
                discountApprovedName: localStorage.dapprovedname,
                salePersonId: $.parseJSON(localStorage.sp)["EmplID"],
                dueAmount: tobepaid,
                //   pdf:  nm + "_" + "gb" + ".pdf",
                //   documentName :  nm,
                documentLanguages: "gb",
                showroomid: localStorage.showRoomName,
                salePersonId: $.parseJSON(localStorage.sp)["EmplID"],
                status: "1",
                vatExcluded: parseFloat(localStorage.vatexcluded),
                vat: parseFloat(localStorage.vat),
                vatRefund: parseFloat(localStorage.torefund) - parseFloat(localStorage.admincharge),
                directRefund: localStorage.directRefund,
                adminCharge: parseFloat(localStorage.admincharge),
                saledate: moment(dt).format("YYYY-MM-DD HH:mm:ss"),
                reference: $("#reference").val(),
                isproform: $("#proforma").val(),
                remark: $("#remark").val().replace(/\n/g, "<br />")
            }

        } else {
            if (isNaN(parseFloat(localStorage.discountAmount))) {
                localStorage.discountAmount = 0;
            }
              var dt = $("#saledate").datepicker( 'getDate' );
            var obj = {
                invoiceid: invoiceID,
                version: version,
                customerid: $("#customerid").val(),
                showroom: localStorage.showRoomName,
                salesPerson: $.parseJSON(localStorage.sp)["Employee"],
                tourNo: tour.ProjId,
                total: parseFloat(localStorage.grandTotal),
                discount: localStorage.invoiceDiscount,
                discountAmount: parseFloat(localStorage.discountAmount),
                salePersonId: $.parseJSON(localStorage.sp)["EmplID"],
                dueAmount: tobepaid,
                //   pdf:  nm + "_" + "gb" + ".pdf",
                //   documentName :  nm,
                documentLanguages: "gb",
                showroomid: localStorage.showRoomName,
                salePersonId: $.parseJSON(localStorage.sp)["EmplID"],
                status: "1",
                vatExcluded: parseFloat(localStorage.vatexcluded),
                vat: parseFloat(localStorage.vat),
                vatRefund: parseFloat(localStorage.torefund) - parseFloat(localStorage.admincharge),
                directRefund: localStorage.directRefund,
                adminCharge: parseFloat(localStorage.admincharge),
                saledate: moment(dt).format("YYYY-MM-DD HH:mm:ss"),
                reference: $("#reference").val(),
                isproform: $("#proforma").val(),
                remark: $("#remark").val().replace(/\n/g, "<br />")

            }

        }
        if (obj.vatRefund == "") {
            obj.vatRefund == "0";
        }
        if (obj.adminCharge == "") {
            obj.adminCharge == "0";
        }

        api.call(((invoiceID == "") ? "insertInvoice" : "updateInvoiceDocuments"), function(res) {
            if (res.status == "ok") {
                loadedPages.checkout.currentInvoice = invoiceID.toString().padStart(5, "0");
                invoiceID = res.invoiceid;
                loadedPages.checkout.iid = "9" + invoiceID.toString().padStart(5, "0");

                localStorage.invoiceID = res.invoiceid;
                var nm = "SalesInvoice_" + moment(new Date()).format("YYYYMMDD") + "_" + "9" + invoiceID.toString().padStart(5, "0") + version;
                localStorage.documentName = nm;
                documentName = nm;
                var obj1 = {
                    invoiceid: invoiceID,
                    pdf: nm + "_" + "gb" + ".pdf",
                    documentName: nm,
                }
                api.call("updateInvoicepdf", function(res) {

                }, obj1, {}, {});
                var ivoiceID = "9" + invoiceID.toString().padStart(5, "0") + version;
                //          var ivoiceID = "9" + 19.toString().padStart(5, "0");
                $("#inm").html(ivoiceID);
                api.call("deleteInvoiceBody", function(r) {}, {
                    invoiceid: invoiceID
                }, {}, {})
                api.call("deleteInvoicePayments", function(r) {}, {
                    invoiceid: invoiceID
                }, {}, {})
                for (var key in shoppingCartContent) {
                    var data = shoppingCartContent[key];
                    var obj = {};
                    var img = $(data["imageURL"]);
                    for (var k in data) {
                        obj[k] = data[k];
                    }
                    obj["name"] = obj["productName"].split("<br />")[0];
                    obj["imageURL"] = img.attr("src");
                    obj["invoiceid"] = invoiceID;

                    api.call("insertInvoiceBody", function(r) {
                    }, obj, {}, {});
                }

                for (var key in payments) {
                    var data = payments[key];

                    var obj = {};
                    for (var k in data) {
                        obj[k] = data[k];
                    }

                    obj["invoiceid"] = invoiceID;
                    if (obj["isOld"] == "0") {
                      obj["version"] = version;
                    }
                    delete obj["isOld"];
                    if (obj.version == "") {
                      obj["date"] = moment($("#saledate").datepicker( 'getDate' )).format("YYYY-MM-DD HH:mm:ss");
                    }
                    if (obj.version === undefined) {
                      obj["date"] = moment($("#saledate").datepicker( 'getDate' )).format("YYYY-MM-DD HH:mm:ss");
                    }
                    if (obj.paymentID != "7") {
                        api.call("insertInvoicePayments", function(r) {

                        }, obj, {}, {});
                    }
                    payments[key] = obj;
                }
                setTimeout(function() {
                  api.call("updateInvoiceFinance", function(res) {

                  }, {invoiceid: invoiceID }, {}, {});
                }, 3500);
                var ivoiceID = "9" + invoiceID.toString().padStart(5, "0") + version;

                var bc = textToBase64Barcode(ivoiceID);
                // $.LoadingOverlay("hide");
                $("#bar_image").attr("src", bc);
                var html = $("#invoice")[0].outerHTML;
                $.ajax({
                    url: "https://costercatalog.com:5100",
                    type: 'POST',
                    dataType: "json",
                    data: {
                        createPDF: "1",
                        html: html,
                        name: nm
                    },
                    success: function(res) {
                        var mail = {
                            from: "costerdiamonds@gmail.com",
                            customer: $("#email").val(),
                            customerName: $("#name").val(),
                            name: nm + "_" + "gb" + ".pdf",
                            subject: "Invoice",
                            text: "Generated " + (new Date()),
                            user: "cobol1962@gmail.com",
                            mode: mode,
                            invoiceid: invoiceID,
                            date: moment(new Date()).format("DD-MM-YYYY"),
                            invoiceNumber: ivoiceID
                        }
                        api.call("sendMail", function(res) {
                          var data = "";
                        //  $("body").LoadingOverlay("hide");
                            var txt = "";
                            var token = "";
                            var GB = false;
                            data = $('#countries').select2('data');
                            localStorage.gb_token = "";
                            api.call("checkCustomerGB", function(resp) {
                              if (resp.response.status == "ok") {
                                token = resp.response.GBToken

                                GB = true;
                                if (mode == 1) {
                                    txt = "Mail sent succesfully. Tax refund form opened in next window."
                                }
                                if (mode == 2) {
                                    txt = "Invoice created succesfully. Tax refund form opened in next window."
                                }
                                if (mode == 3) {
                                    txt = "Invoice created and sent succesfully. Tax refund form opened in next window."
                                }
                              } else {
                                GB = false;
                                if (mode == 1) {
                                    txt = "Mail sent succesfully"
                                }
                                if (mode == 2) {
                                    txt = "Invoice created succesfully"
                                }
                                if (mode == 3) {
                                    txt = "Invoice created and sent succesfully"
                                }
                              }
                              setTimeout(function() {
                                  $.LoadingOverlay("hide");
                                    resetLocalStorage();
                                    delete localStorage.shoppingCartContent;
                                  showModal({
                                      title: txt,
                                      allowBackdrop: false,
                                      showCancelButton: false,
                                      confirmCallback: function() {

                                    /*    $("#issuemodel").val(JSON.stringify(im));
                                        $("#sessiontoken").val(token);
                                          alert($('form#gbform').serialize());
                                          $('form#gbform').submit();*/
                                    /*    $.post( 'https://ic2integra-web.mspe.globalblue.com/ui/integra', $('form#gbform').serialize(), function(data) {
                                            var code = data.replace("<script>","").replace("</script>","");
                                              code = code.replace("ui/handleintegraredirect","https://ic2integra-web.mspe.globalblue.com/ui/handleintegraredirect");
                                              eval(code);
                                            },

                                         );*/

                                          if (mode != 1) {
                                             var app = detectMobile();
                                             alert(app)
                                              var blob = b64toBlob(res.base64, "application/pdf");
                                              var blobUrl = URL.createObjectURL(blob);
                                              alert(blobUrl)
                                              if (true) {
                                               window.open("https://costercatalog.com/api/ACinvoices.php?invoices=" + loadedPages.checkout.iid + "&print=1&customer=" + loadedPages.checkout.csid + "&sessiontoken=" + token, "_system","location=yes");

                                              } else {


                                                var storageLocation = "";
                                                 storageLocation = 'file:///storage/emulated/0/';
                                                 var folderpath = storageLocation + "Download";
                                                 var filename = "invoice.pdf";
                                                 var DataBlob = b64toBlob(res.base64, "application/pdf");
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
                                                                  success : function(){

                                                                  }
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
                                              $.LoadingOverlay("hide");

                                          } else {

                                            if (GB) {
                                              window.open("https://costercatalog.com/api/ACinvoices.php?invoices=" + loadedPages.checkout.iid + "&print=1&customer=" + loadedPages.checkout.csid + "&sessiontoken=" + token, "_system","location=yes");
                                            }
                                          }
                                          var t = $.parseJSON(localStorage.tour);
                                          if (t.custom !== undefined) {
                                              delete localStorage.tour;
                                          }


                                          userData.activity = "Created invoice 9" + loadedPages.checkout.currentInvoice;
                                      /*    api.call("createlog", function() {
                                              ws.send(JSON.stringify({
                                                  action: "reloadadmin"
                                              }))
                                          }, userData, {}, {});*/

                                          localStorage.sp = localStorage.originalsp;
                                          var spp = $.parseJSON(localStorage.originalsp);
                                          localStorage.EmplID = spp.EmplID;
                                          localStorage.Employee = spp.Employee;
                                          $("#salepersonid").val(spp.EmplID);
                                          $("#salepersonname").val(spp.Employee);
                                          if (!app) {
                                            loadPage('homepage')
                                          } else {
                                            setTimeout(function() {
                                              loadPage('homepage')
                                            }, 3000)
                                            window.open("https://costercatalog.com/api/ACinvoices.php?mobile=1&invoices=" + loadedPages.checkout.iid + "&print=1&customer=" + loadedPages.checkout.csid + "&sessiontoken=" + token, "_blank","location=yes");

                                          }
                                      }
                                  })
                              }, 4000);
                            }, {country: data[0].id, amount: localStorage.total})


                        }, mail, {});
                    }
                })

            } else {
              /*  showModal({
                    title: "Something went wrong",
                    showCancelButton: false
                })*/

                $.LoadingOverlay("hide");
                return false;
            }
        }, obj, {}, {});
    },
    checkEmail: function() {

        /*   api.call("checkCustomerEmail", function(res) {
             if (res.length > 0) {
               var html = "<table style='width:100%;'>";
               $.each(res, function() {
                 html += "<tr id='" + this.customerid + "' onclick='loadedPages.checkout.getCustomer(this);'><td>" + this.customer + "</td></tr>";
               })
               html += "</table>";
               showModal({
                 title: "Customer(s) bellow found with same email. Click one to get data or confirm new customer.",
                 content: html,
                 showCancelButton: false,
                 confirmButtonText: "CONFIRM NEW CUSTOMER"
               })
             }

           }, { email: $("#email").val() }, {}, {})*/
    },
    getCustomer: function(obj) {

        api.call("getCustomerByid", function(res) {
            $("#customerid").val(obj.id);
            var d = res[0];
            for (var k in d) {
                $("#customerForm").find("[name='" + k + "']").val(d[k]);
            }
            customerInfoData["countryCode"] = d["countryCode"];
            setTimeout(function() {
                $("#countries").val(d["countryCode"]).trigger('change');
            }, 2000);
            $('#mainModal').modal("hide");
        }, {
            query: obj.id
        }, {}, {})
    },
    addTour: function() {
      delete localStorage.tour;
        showModal({
            title: "Enter tour number",
            content: "<input id='tnum' type='number' class='form-control' />",
            allowBackdrop: false,
            showClose: false,
            noclose: true,
            showCancelButton: false,
            cancelCallback: function() {
                $('#mainModal').modal("hide");
            },
            confirmCallback: function() {

              if ($("#tnum").val() == "") {
                showModal({
                    type: "error",
                    title: "Please enter valid number",
                    showClose: false,
                      showCancelButton: false,
                    confirmCallback: function() {
                      loadedPages.checkout.addTour();
                    }
                  });
                  return false;
              }
                localStorage.tour = JSON.stringify({
                    ProjId: $("#tnum").val(),
                    custom: "1"
                });
                $("#tnmbup").html($("#tnum").val());

                $('#tours tbody tr').removeClass('selected');
                $("#mainModal").modal("hide");
                loadedPages.checkout.setCName();
            }
        })
    },
    setCName: function() {

        if (localStorage.tour !== undefined) {
            var data = $.parseJSON(localStorage.tour);
            if (data.custom !== undefined) {
              if (data.ProjId === undefined) {
                showModal({
                    title: "Choose tour or add new tour.",
                    showCancelButton: false,
                    confirmCallback: function() {
                        $('#1').show();
                        $('#2').hide();
                    }
                })
                return false;
              }
            }
        }
        if (localStorage.tour !== undefined) {
            var data = $.parseJSON(localStorage.tour);
            if (data.PrivateID != "null") {
                if ($("#name").val() == "") {
                    $("#name").val(data.ProjName);
                }
            }
            $('#1').hide();
            $('#2').show();
        } else {
            showModal({
                title: "Choose tour or add new tour.",
                showCancelButton: false,
                confirmCallback: function() {
                    $('#1').show();
                    $('#2').hide();
                }
            })

        }
    },
    signature: function(mode) {
loadedPages.checkout.generateInvoice(mode);
    /*  $("#sign").attr("mode", mode);
        $("#clear").trigger("click");
        $('#sign').modal({
            backdrop: 'static',
            keyboard: false
        })
        $('#sign').modal("show");*/
    },
    initializeTours: function() {
      var oTable =  $('#tours').on('error.dt', function (e, settings, techNote, message) {
//        alert(message);
      }).DataTable({
        "ajax": {
          "url": "https://costercatalog.com/api/index.php?request=getTours",
          "type": "POST",
          "data": { secret:"scddddedff2fg6TH22" }
        },
          info: false,
          columns: [

              {
                  "data": "AVisitDateTime",
                  "render": function(data, type, row) {
                      var dt = new Date(data);
                      return type === 'sort' ? data : moment(dt).format("DD/MM HH:mm");
                  }
              },
              {
                  "data": "ProjId"
              },
              {
                  "data": "ProjName"
              },
              {
                  "data": "PAX"
              },
              {
                  "data": "country",
                  "render": function(data, type, row) {
                      return "<span style='max-width:100%;'><b>" + row["touroperater"] + "</b></span><br />" + data;
                  }
              },
              {
                  "data": "touroperater"
              },

          ],
          dom: 'Bfrtip',
          "order": [
              [0, "desc"]
          ],
          buttons: [

          ],
          "paging": false,
          stateSave: true,
          "initComplete": function(settings, json) {
            loadedPages.checkout.table = oTable;
            loadedPages.checkout.toursTable = oTable;
            loadedPages.checkout.setPayments();
          }
      });

    }
}
