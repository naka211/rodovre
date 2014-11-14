<?php // no direct access
defined('_JEXEC') or die('Restricted access');

//$data->billTotal = str_replace(",00","",preg_replace("/.*?<strong>([^<]*?)<\/strong>/","$1",$data->billTotal));
//echo '<pre>',print_r($cart),'</pre>';
// Ajax is displayed in vm_cart_products
// ALL THE DISPLAY IS Done by Ajax using "hiddencontainer" ?>
<!-- Virtuemart 2 Ajax Card -->
<?php 
if (!class_exists( 'VmModel' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmmodel.php');
 if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
    if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
$cart = VirtueMartCart::getCart(); 
$cart->prepareCartViewData();
?>
<?php foreach($cart->products as $pid => $product){
    $customhtml = $product->customfields;
//    preg_match_all("#<span class=\"product-field-type-S\"> ([\w\W]*?)</span>#", $product->customfields, $tmp);
//    $select1 = $tmp[1][0];
//    $select2 = $tmp[1][1];
//    preg_match("#src=\"([\w\W]*?)\" alt#", $product->customfields, $tmp1);
//    $img = $tmp1[1];
    preg_match("/<img .*?(?=src)src=\"([^\"]+)\" alt=\"([^\"]+)\"/si", $customhtml, $matches); 
    $cusfinal1 = str_replace($matches[0]."  />", "", $customhtml);
    $cusfinal = str_replace('<span class="product-field-type-M"> </span><br />', '', $cusfinal1);
?>
<li class="clearfix">
    <div class="list-cart-img"><a href="<?php echo $product->url; ?>">
        <?php 
            if(isset($matches) && count($matches)>0){
                echo $matches[0].'/>';
            }else{
                $encode = json_encode($product->image);
                $decode = json_decode($encode);
                echo '<img src="'.$decode->file_url.'"/>';
//                var_dump($product->image->file_url);die;
//                echo $product->image->displayMediaThumb('',false,'',true,true,'false'); 
            }
        ?>
        </a></div>
    <div class="list-cart-content">
      <h4><?php echo $product->product_name; ?></h4>
      <p>Vare-nummer: <?php echo $product->sku; ?></p>
      <p><?php echo $cusfinal ?></p>
    </div>
    <div class="count">
       <p><?php echo $product->quantity;?></p>
    </div>
    <p class="price">
        <?php if ($cart->pricesUnformatted[$pid]['discountedPriceWithoutTax']) {
                echo number_format($cart->pricesUnformatted[$pid]['discountedPriceWithoutTax'], 2,',','.'). ' DKK';
        } else {
                echo number_format($cart->pricesUnformatted[$pid]['basePriceVariant'], 2,',','.'). ' DKK';
        }
        ?>
    </p>
    <p class="price3"><?php echo number_format($cart->pricesUnformatted[$pid]['salesPrice']*$product->quantity, 2,',','.'). ' DKK'; ?></p>
   <a href="javascript:void(0);" onclick="deleteProduct('<?php echo $product->cart_item_id;?>')" rel="nofollow"" class="btnClose">close</a>
</li>
<?php } ?>