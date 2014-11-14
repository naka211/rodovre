// JavaScript Document
$(document).ready(function(){ 
		
	//JS Menu LEft (show/hide categories/menu when click)
	 $('.btnMenu').addClass("btnActive");	
		$('.ulCate').hide();
		$('.ulMenu').show();
		
		$('.btnMenu').click(function(){
			$(this).addClass("btnActive");
			$('.btnCate').removeClass("btnActive");	    	
			$('.ulCate').hide();
			$('.ulMenu').show();
		});
		
	    $('.btnCate').click(function(){
	    	$(this).addClass("btnActive");
	    	$('.btnMenu').removeClass("btnActive");    	
			$('.ulCate').show();
			$('.ulMenu').hide();
		});

	//JS for checkout page (col-right-checkout)...
 		$(".inner-lb").hide();  
        $('.lb1,.lb3').click(function(){
          $(".inner-lb").hide();
        });   
        $('.lbUden').click(function(){
          $(".inner-lb").show();
        });

	// JS for Checkout page (col-left-checkout)  ...
		//check out
        /*$(".w_Address").hide();
        $(".btnLevering").show();
        
        $('.btnLevering').click(function(event){
           event.preventDefault();
          $(".w_Address").slideToggle();
        });

        $("#w_privat").show;
        $("#w_erhverv").hide();
        $("#w_offentlig").hide();

        // How it all works
        $("#choicemaker").change(function () {
          $value = $("#choicemaker")[0].selectedIndex;
          // You can also use $("#ChoiceMaker").val(); and change the case 0,1,2: to the values of the html select options elements
          switch ($value)
          {
            case 0:
                $("#w_privat").show();
                $("#w_erhverv").hide();
                $("#w_offentlig").hide();
            break;
            case 1:
                $("#w_erhverv").show();
                $("#w_privat").hide();
                $("#w_offentlig").hide();
            break;
            case 2:
                $("#w_offentlig").show();
                $("#w_privat").hide();
                $("#w_erhverv").hide();
            break;
          }
        });

        
 		$(".w-another-add").hide();     
        $('.bnt-another-add').click(function(){
        	$(".w-another-add").slideToggle();
        });*/

	  
	// JavaScript ....
	// JavaScript ....
	// JavaScript ....
	// JavaScript ....

 });