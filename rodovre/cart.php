<!DOCTYPE html>
<html lang="en">
  <head>
    <?php require_once('head.php'); ?>
  </head>
  <body>
    <div id="page">
      <?php require_once('header.php'); ?>
      <section class="main mt190">
        <div class="container">
          <div class="template2">
            <div class="cart_page clearfix">
              <h2>DIN INDKØBSKURV</h2>
              <table class="list_item_cart">
                <tr class="title">
                  <th>Varebeskrivelse</th>
                  <th>Antal</th>
                  <th>Pris pr stk.</th>
                  <th>Pris i alt</th>
                </tr>
                <tr>
                  <td>
                    <div class="img_pro">
                      <img src="img/img04.jpg" alt="">
                    </div>
                    <div class="content_pro">
                      <h4>LUCIE ANTIQUE TERRACOTTA</h4>
                      <p>Varenummer: 30283</p>
                      <p>Størrelse: Højde 21 cm-Ø27 cm</p>
                      <p>BORDPLADE 50X60 CM, HVID MATTERET HÆRDET GLAS</p>
                    </div>
                  </td>
                  <td>
                    <div class="relative number">
                      <input type="text" placeholder="1">
                      <a class="add" href="#"><img src="img/icon_add.jpg" alt=""></a>
                      <a class="sub" href="#"><img src="img/icon_sub.jpg" alt=""></a>
                    </div>
                  </td>
                  <td>
                    <p>479 DKK </p>
                  </td>
                  <td>
                    <p>479 DKK </p>
                    <a class="btnDel" href="#"><img src="img/btnClose" alt="">Delete</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="img_pro">
                      <img src="img/img04.jpg" alt="">
                    </div>
                    <div class="content_pro">
                      <h4>LUCIE ANTIQUE TERRACOTTA</h4>
                      <p>Varenummer: 30283</p>
                      <p>Størrelse: Højde 21 cm-Ø27 cm</p>
                      <p>BORDPLADE 50X60 CM, HVID MATTERET HÆRDET GLAS</p>
                    </div>
                  </td>
                  <td>
                    <div class="relative number">
                      <input type="text" placeholder="1">
                      <a class="add" href="#"><img src="img/icon_add.jpg" alt=""></a>
                      <a class="sub" href="#"><img src="img/icon_sub.jpg" alt=""></a>
                    </div>
                  </td>
                  <td>
                    <p>479 DKK </p>
                  </td>
                  <td>
                    <p>479 DKK </p>
                    <a class="btnDel" href="#"><img src="img/btnClose" alt="">Delete</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="img_pro">
                      <img src="img/img04.jpg" alt="">
                    </div>
                    <div class="content_pro">
                      <h4>LUCIE ANTIQUE TERRACOTTA</h4>
                      <p>Varenummer: 30283</p>
                      <p>Størrelse: Højde 21 cm-Ø27 cm</p>
                      <p>BORDPLADE 50X60 CM, HVID MATTERET HÆRDET GLAS</p>
                    </div>
                  </td>
                  <td>
                    <div class="relative number">
                      <input type="text" placeholder="1">
                      <a class="add" href="#"><img src="img/icon_add.jpg" alt=""></a>
                      <a class="sub" href="#"><img src="img/icon_sub.jpg" alt=""></a>
                    </div>
                  </td>
                  <td>
                    <p>479 DKK </p>
                  </td>
                  <td>
                    <p>479 DKK </p>
                    <a class="btnDel" href="#"><img src="img/btnClose" alt="">Delete</a>
                  </td>
                </tr>
                <tr class="cf9f7f3">
                  <td colspan="4">
                    <table class="sub_order_Summary">
                      <tr>
                        <td colspan="2">
                          <ul>
                            <li>Ved køb af varer over 1.000 Dkk. hos Scheel-Larsen.dk leverer og samler vi GRATIS..! på hele Sjælland.</li>
                            <li>Ved køb under 1.000 kr. pålægges et fragtgebyr på 150 DKK.</li>
                            <li>Fragt til Jylland og Fyn 350 kr.</li>
                          </ul>
                        </td>
                        <td colspan="2" width="35%">
                          <table>
                            <tr>
                              <td>SUBTOTAL INKL. MOMS:</td>
                              <td>1.916 DKK</td>
                            </tr> 
                            <tr>
                              <td><h4>AT BETALE INKL. MOMS:</h4></td>
                              <td><h4>1.916 DKK</h4></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
               
               <div class="graris">
                  <p>Har du et gavekort kan du indtaste din kode her.</p>
                  <div class="frm_coupon clearfix">
                      <input name="coupon" placeholder="Indtast koden her ...">
                      <a href="#">Aktiver</a>
                      <input type="submit" value="Send" id="Send" name="Send" style="display:none">
                  </div>
                </div>
                <div class="clear"></div> 
                <a class="btnShopvidere fl  cblack" href="index.php">Shop videre</a>
                <a class="btnCheckout fr cblack" href="checkout.php">Gå til kassen</a>
            </div>
          </div>   
        </div>
      </section>
      <?php require_once('delivery.php'); ?>
      <?php require_once('footer.php'); ?>
    </div>

   <?php require_once('js-footer.php'); ?>
  </body>
</html>