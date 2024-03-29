if(typeof Virtuemart === "undefined")
	{
		var Virtuemart = {
			setproducttype : function (form, id) {
				form.view = null;
				var $ = jQuery, datas = form.serialize();
				var prices = form.parent(".productdetails").find(".product-price");
				if (0 == prices.length) {
					prices = $("#productPrice" + id);
				}
				datas = datas.replace("&view=cart", "");
				prices.fadeTo("fast", 0.75);
				$.getJSON(window.vmSiteurl + 'index.php?option=com_virtuemart&nosef=1&view=productdetails&task=recalculate&virtuemart_product_id='+id+'&format=json' + window.vmLang, encodeURIComponent(datas),
					function (datas, textStatus) {
						prices.fadeTo("fast", 1);
						// refresh price
						for (var key in datas) {
							var value = datas[key];
							if (value!=0) prices.find("span.Price"+key).show().html(value);
							else prices.find(".Price"+key).html(0).hide();
						}
					});
				return false; // prevent reload
			},
			productUpdate : function(mod) {
				var $ = jQuery ;
				$.ajaxSetup({ cache: false })
                        $.ajax({
                                type: "GET",
                                contentType: "application/json; charset=utf-8",
                                url: "index.php",
                                data: { option: "com_virtuemart", view: "cart", layout: "mycart", format: "json" },
                                dataType: "html",
                                success: function (response) { 
                                    
                                    $('#list-item').html(response);
                                    $('.mwc-tax').html($("div.my-tax").text());
                                    $('.mwc-subtotal').html($("div.my-total").text());
                                    $('.mwc-total-head').html($("div.my-total").text());
                                    $('.img-cart p span').eq(1).text($("div.my-total").text());
                                    $('.img-cart p span').eq(0).text($("div.my-quantity").text());
                                },
//                                        error: function (error){
//                                            console.log(error);
//                                        }
                        });
                               
				$.getJSON(window.vmSiteurl+"index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json"+window.vmLang,
					function(datas, textStatus) {
						if (datas.totalProduct >0) { 
                                                    //console.log(datas);
                                                    var grandtotal = datas.billTotal;
                                                        grandtotal = grandtotal.replace(/<(?:.|\n)*?>/gm, '');
                                                        $('.img-cart p span').eq(0).text(datas.products.length+' VARE(R) =');
                                                        //$('.img-cart p span').eq(1).text(grandtotal);
                                                        
//							mod.find(".vm_cart_products").html("");
//							$.each(datas.products, function(key, val) {
//								$("#hiddencontainer .container").clone().appendTo(".vmCartModule .vm_cart_products");
//								$.each(val, function(key, val) {
//									if ($("#hiddencontainer .container ."+key)) mod.find(".vm_cart_products ."+key+":last").html(val) ;
//								});
//							});
//                                                        
//							mod.find(".total").html(datas.billTotal);
//							mod.find(".show_cart").html(datas.cart_show);
						}
//						mod.find(".total_products").html(datas.totalProductTxt);
					}
				);
			},
			sendtocart : function (form){

				if (Virtuemart.addtocart_popup ==1) {
					Virtuemart.cartEffect(form) ;
				} else {
					form.append('<input type="hidden" name="task" value="add" />');
					form.submit();
				}
			},
			cartEffect : function(form) {

                var $ = jQuery ;
                $.ajaxSetup({ cache: false });
                var datas = form.serialize();

                if(usefancy){
                    $.fancybox.showActivity();
                }

                $.getJSON(vmSiteurl+'index.php?option=com_virtuemart&nosef=1&view=cart&task=addJS&format=json'+vmLang,datas,
                function(datas, textStatus) {
                    //T.Trung
                    if(datas.status == 1){
                        jQuery('#f_note').reveal();
                        return false;
                    }
					
					//Mobile
					$('span.nummber').text(Number($("span.nummber").text()) + 1);
                    //T.Trung end
                    if(datas.stat ==1){

                        var txt = datas.msg;
                    } else if(datas.stat ==2){
                        var txt = datas.msg +"<H4>"+form.find(".pname").val()+"</H4>";
                    } else {
                        var txt = "<H4>"+vmCartError+"</H4>"+datas.msg;
                    }
                    if(usefancy){
                        $.fancybox({
                                "titlePosition" : 	"inside",
                                "transitionIn"	:	"fade",
                                "transitionOut"	:	"fade",
                                "changeFade"    :   "fast",
                                "type"			:	"html",
                                "autoCenter"    :   true,
                                "closeBtn"      :   false,
                                "closeClick"    :   false,
                                "content"       :   txt
                            }
                        );
                    } else {
//                        $.facebox.settings.closeImage = closeImage;
//                        $.facebox.settings.loadingImage = loadingImage;
//                        //$.facebox.settings.faceboxHtml = faceboxHtml;
//                        $.facebox({ text: txt }, 'my-groovy-style');
                    }

                    if ($(".list-cart")[0]) {
                        Virtuemart.productUpdate();
                    }
                    $('.img-cart').trigger('click');
                });

                    
                $.ajaxSetup({ cache: true });
			},
			product : function(carts) {
				carts.each(function(){
					var cart = jQuery(this),
					step=cart.find('input[name="quantity"]'),
					addtocart = cart.find('input.addtocart-button'),
					plus   = cart.find('.quantity-plus'),
					minus  = cart.find('.quantity-minus'),
					select = cart.find('select:not(.no-vm-bind)'),
					radio = cart.find('input:radio:not(.no-vm-bind)'),
					virtuemart_product_id = cart.find('input[name="virtuemart_product_id[]"]').val(),
					quantity = cart.find('.quantity-input');

                    var Ste = parseInt(step.val());
                    //Fallback for layouts lower than 2.0.18b
                    if(isNaN(Ste)){
                        Ste = 1;
                    }
					addtocart.click(function(e) { 
						Virtuemart.sendtocart(cart);
						return false;
					});
					plus.click(function() {
						var Qtt = parseInt(quantity.val());
						if (!isNaN(Qtt)) {
							quantity.val(Qtt + Ste);
						Virtuemart.setproducttype(cart,virtuemart_product_id);
						}
						
					});
					minus.click(function() {
						var Qtt = parseInt(quantity.val());
						if (!isNaN(Qtt) && Qtt>Ste) {
							quantity.val(Qtt - Ste);
						} else quantity.val(Ste);
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
					select.change(function() {
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
					radio.change(function() {
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
					quantity.keyup(function() {
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
				});

			}
		};
		jQuery.noConflict();
		jQuery(document).ready(function($) {

			Virtuemart.product($("form.product"));

			$("form.js-recalculate").each(function(){
				if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
					var id= $(this).find('input[name="virtuemart_product_id[]"]').val();
					Virtuemart.setproducttype($(this),id);

				}
			});
            
		});
	}
