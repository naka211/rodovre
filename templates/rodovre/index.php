<?php
// No direct access.
defined('_JEXEC') or die; 

//Detect mobile
$config =& JFactory::getConfig();
$showPhone = $config->getValue( 'config.show_phone' );
$enablePhone = $config->getValue( 'config.enable_phone' );
require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
if ( ($showPhone || $detect->isMobile()) && ($enablePhone) ) {
    include('index_mobile.php');
    return;
}
//Detect mobile end
$tmpl = JURI::base().'templates/'.$this->template."/";
$session = JFactory::getSession();
$option = JRequest::getVar('option');
$view = JRequest::getVar('view');
$layout = JRequest::getVar('layout');
$optview = $option.$view;
$viewlayout = $view.$layout;
$haveLeft = true;
$showCart = true;
if($optview == "com_virtuemartuser"){
    $haveLeft = false;
    $showCart = false;
}
if($optview == "com_virtuemartproductdetails"){
    $haveLeft = false;
}
if($optview == "com_virtuemartcart"){
    $haveLeft = false;
    $showCart = false;
}
if($viewlayout == "cartorder_done"){
    $haveLeft = false;
}
if($viewlayout == "cartpayment"){
    $haveLeft = false;
}
if($optview == "com_virtuemartpluginresponse"){
    $haveLeft = false;
}

JHtml::_('behavior.formvalidation');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo $tmpl;?>js/jquery-1.9.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <jdoc:include type="head" />
    <?php if($optview=="com_virtuemartproductdetails"){
        $db = JFactory::getDBO();
        $query = 'SELECT product_desc, product_name FROM #__virtuemart_products_da_dk WHERE virtuemart_product_id = '.JRequest::getVar('virtuemart_product_id');	
        $db->setQuery($query);
        $pro = $db->loadObject();
        
        $query = 'SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = (SELECT virtuemart_media_id FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '.JRequest::getVar('virtuemart_product_id').' LIMIT 0,1)';	
        $db->setQuery($query);
        $img = $db->loadResult();
        ?>
    <meta name="productTitle" property="og:title" content="<?php echo $pro->product_name;?>">
    <meta name="productImage" property="og:image" content="<?php echo JURI::base().$img;?>">
    <meta property="og:url" content="<?php echo JRoute::_('http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);?>" />
    <meta property="og:description" content="<?php echo strip_tags($pro->product_desc);?>" />
    <script>
        jQuery(document).ready(function(){
            jQuery("#facebookShare").click(function(){
                postFacebookWallDetail("<?php echo urlencode(JURI::root()."index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=".JRequest::getVar('virtuemart_product_id')."&virtuemart_category_id=".JRequest::getVar('virtuemart_category_id')); ?>");
            });
            function postFacebookWallDetail(urlencode){
                t=document.title; 
                window.open('http://www.facebook.com/sharer.php?u='+urlencode+'&v=<?php echo time();?>','sharer','toolbar=0,status=0,width=626,height=436'); 
                return false; 
            }
            
        });
    </script>
    <?php }?>
    <script src="<?php echo JURI::base();?>components/com_virtuemart/assets/js/vmprices.js" type="text/javascript"></script>
    <!-- Bootstrap -->
    <link href="<?php echo $tmpl;?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $tmpl;?>css/animate.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo $tmpl;?>source/jquery.fancybox.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $tmpl;?>source/helpers/jquery.fancybox-buttons.css"/>
    <link href="<?php echo $tmpl;?>css/reveal.css" rel="stylesheet">
    <link href="<?php echo $tmpl;?>css/prettyPhoto.css" rel="stylesheet">
    <link href="<?php echo $tmpl;?>css/tinyscrollbar.css" rel="stylesheet">
    <link href="<?php echo $tmpl;?>css/style.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="icon" href="<?php echo $tmpl;?>favicon.ico">
    <script src="<?php echo $tmpl;?>js/jquery.js"></script>
    <script src="<?php echo $tmpl;?>js/bootstrap.min.js"></script>
    <script src="<?php echo $tmpl;?>js/jquery.carouFredSel-6.2.1-packed.js"></script>
    <script src="<?php echo $tmpl;?>js/jquery.reveal.js"></script>
    <script src="<?php echo $tmpl;?>source/jquery.fancybox.pack.js"></script>
    <script src="<?php echo $tmpl;?>source/helpers/jquery.fancybox-buttons.js"></script>
    <script src="<?php echo $tmpl;?>source/helpers/jquery.fancybox-media.js"></script>
    <script src="<?php echo $tmpl;?>source/helpers/jquery.fancybox-thumbs.js"></script>
    <script src="<?php echo $tmpl;?>js/jquery.prettyPhoto.js"></script>
    <script src="<?php echo $tmpl;?>js/jquery.tinyscrollbar.js"></script>
    <script src="<?php echo $tmpl;?>js/nicker.js"></script>
    <script>
        jQuery(document).ready(function(){
            deleteProduct = function(cart_item_id){
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?option=com_virtuemart&view=cart&task=deleteAjax&cart_virtuemart_product_id="+cart_item_id
                }).done(function(result) {
                    if(result == 1)
                        Virtuemart.productUpdate();
                });
            }
			
			$('input#subscribe_email').on('change invalid', function() {
				var textfield = $(this).get(0);
				textfield.setCustomValidity('');
				
				if (!textfield.validity.valid) {
				  textfield.setCustomValidity('Venligst indtast din e-mail');  
				}
			});         
        });
    </script>
	<?php if(!$haveLeft){?>
	<style>
	.navbar-form{margin-bottom:18px;}
	</style>
	<?php }?>
    </head>
    <body>
    <div id="page" class="bg_black">
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/da_DK/sdk.js#xfbml=1&appId=176427392509007&version=v2.0";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
        <header class="clearfix">
            <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
                <div class="container relative"> <a class="navbar-brand" href="index.php"><img src="<?php echo $tmpl;?>img/logo.jpg" alt="logo"></a>
                    <div class="w_info clearfix"> {module Information}
                        {module Search product} </div>
                    <?php 
if (!class_exists( 'VmModel' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmmodel.php');
 if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
    if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
        $cart = VirtueMartCart::getCart(); 
$cart->prepareCartViewData();

if(count($cart)>0){
    $pri = number_format($cart->pricesUnformatted['salesPrice'],2,',','.').' DKK';
}
else{
    $pri = 0;
}
?>
                    <?php if($showCart){?>
                    <div class="w_cart"> <img src="<?php echo $tmpl;?>img/cart.png" alt="">
                        <div id="bg-cart"></div>
                        <div class="cart_content clearfix"> <a class="img-cart clearfix hover" href="#"> <img src="<?php echo $tmpl;?>img/icon_cart.png" alt="">
                            <p class="fl"><span><?php echo count($cart->products);?> VARE(R) =</span><span><?php echo $pri; ?></span></p>
                            <p class="icon_down"></p>
                            </a>
                            <div class="list-cart"> <a href="#" id="btnClose-cart" class="bntClose2"></a>
                                <div class="title clearfix"> <span class="w420">Varebeskrivelse</span> <span class="w105 ml45">Antal</span> <span class="w105">Pris pr stk.</span> <span>Pris i alt</span> </div>
                                <div id="scrollbar1">
                                    <div class="scrollbar">
                                        <div class="track">
                                            <div class="thumb">
                                                <div class="end"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="viewport">
                                        <div class="overview">
                                            <ul id="list-item" class="clearfix">
                                                {modulepos cart}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- END SCROLLBAR -->
                                <div class="total clearfix">
                                    <div class="info_ fl">
                                        <ul>
                                            <li>Hotline på tlf. 36 41 11 24.</li>
                                            <li>Køb for 1.000 kr - og får gratis levering.</li>
                                            <li>2.000 kvm butik.</li>
                                        </ul>
                                    </div>
                                    <div class="w345 fr">
                                        <table>
                                            <tr>
                                                <td>Subtotal:</td>
                                                <td width="60%" class="mwc-subtotal"><?php echo number_format($cart->pricesUnformatted['salesPrice'],2,',','.').' DKK'; ?></td>
                                            </tr>
                                            <!--<tr>
                                                <td>Heraf moms: </td>
                                                <td class="mwc-tax"><?php echo number_format($cart->pricesUnformatted['salesPrice']*0.2,2,',','.').' DKK'; ?></td>
                                            </tr>-->
                                            <tr>
                                                <td><h4>total:</h4></td>
                                                <td><h4 class="mwc-total-head"><?php echo number_format($cart->pricesUnformatted['salesPrice'],2,',','.').' DKK'; ?> </h4></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <a class="bntBasket btn2" href="index.php?option=com_virtuemart&view=cart">SE VAREKURV</a>
								<a id="btnClose-cart2" style="margin:20px 10px 0 0;" href="javascript:void(0);" class="btnShopvidere fr hover cblack">Shop videre</a>
							</div>
                            <!--.list-cart--> 
                        </div>
                    </div>
                    <?php }?>
                </div>
                <div class="container-fluid"> 
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                    </div>
                    
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <div class="container"> {module Category Menu}
                            {module Top Menu} </div>
                    </div>
                    <!-- /.navbar-collapse --> 
                </div>
                <!-- /.container-fluid --> 
            </nav>
        </header>
        <section class="main mt190">
            <div class="container">
                <?php 
                if($haveLeft){
            ?>
                <div class="main_left clearfix"> {module Left Category Menu}
                    {modulepos left}
                    <!--<div class="fb-like-box" data-href="https://www.facebook.com/ScheelLarsen" data-width="220" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="false" data-show-border="true"></div>-->
                </div>
                <div class="main_right">
                    <?php 
                }
            ?>
                    <jdoc:include type="component" />
                    <?php 
                if($haveLeft){
            ?>
                </div>
                <?php 
                }
            ?>
            </div>
        </section>
        <section class="delivery"> {module Footer Information} </section>
        <footer>
            <div id="f_map" class="reveal-modal">
                <div class="f_map clearfix">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2249.574247995414!2d12.458156499999989!3d55.67900339999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46525141139225c7%3A0xd78e7070f33dd4a8!2sR%C3%B8dovre+Centrum+132%2C+2610+R%C3%B8dovre%2C+Denmark!5e0!3m2!1sen!2s!4v1414749256933" width="940" height="450" frameborder="0" style="border:0"></iframe>
                </div>
                <a id="close-reveal-modal" class="close-reveal-modal"></a> </div>
            <div class="footer_top clearfix">
                <div class="container">
                    <div class="footer_top_content clearfix">
                        <div class="contact_info">
                            <h3 class="bor_bottom0"><a data-reveal-id="f_map" href="javascript:void(0);" class="btn_Findos">Find os her !</a></h3>
                            {module Footer Information 2} </div>
                        <div class="cate_list">
                            <h3>Mærker</h3>
                            {module Manufacturer Footer} </div>
                        <div class="sitemap_list">
                            <h3>Betingelser & Vilkår</h3>
                            {module Bottom Menu} </div>
                    </div>
                    <div class="newsletter clearfix">
                        <div class="w485 fl">
                            <p>TILMELD DIG VORES NYHEDSBREV OG FÅ GODE TILBUD OG NYHEDER <br>
                                VI UDSENDER NYHEDSBREVE 1-2 GANGE OM MÅNEDEN!</p>
                        </div>
                        <div class="w430 fr mt5">
                            <form action="index.php" method="post" class="form-validate">
                                <input type="text" placeholder="Indtast din e-mail" class="fl required validate-email" name="email" id="subscribe_email">
                                <button type="submit" class="btnSubscribe btn2 fl ml5" style="cursor:pointer; border:none;">Tilmeld</button>
                                <input type="hidden" name="option" value="com_virtuemart" />
                                <input type="hidden" name="controller" value="virtuemart" />
                                <input type="hidden" name="task" value="subscribe" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer_bottom clearfix">
                <div class="container">
                    <p class="fl">Copyright © 2014 <a href="index.php">roedovreisenkram.dk</a> . All rights reserved</p>
                    <!--<p class="fr">Design af <a target="_blank" href="http://www.mywebcreations.dk/">My Web Creations</a></p>-->
                </div>
            </div>
        </footer>
        <?php if($session->get('notify') != 1){?>
        <script language="javascript">
$(document).ready(function() {
    $(".btnCookie").click(function(event) {
        jQuery.post("<?php echo JURI::base().'index.php?option=com_virtuemart&controller=virtuemart&task=set_session'?>");
    });
});
</script>
        <div id="CookieInfo" class="CookieInfo">
            <div class="cookie-content">
                <p>Cookies er nødvendige for at få hjemmesiden til at fungere, men de gemmer også information om hvordan du bruger vores hjemmeside, så vi kan forbedre den både for dig og for andre. Cookies på denne hjemmeside bruges primært til trafikmåling og optimering af sidens indhold. Du kan forsætte med at bruge vores side som altid, hvis du accepterer at vi bruger cookies. Lær mere om hvordan du håndterer cookies på dine enheder.</p>
                <a class="btnCookie" href="javascript:void(0);">Luk</a> </div>
        </div>
        <?php }?>
    </div>
    
    <div id="f_note" class="reveal-modal">
      <div class="f_note clearfix">
        <p>Du bedes købe gavekort eller produkt(er) adskilt!</p>
      </div>
       <a id="close-reveal-modal" class="close-reveal-modal"></a>
    </div>
</body>
</html>