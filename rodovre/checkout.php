<!DOCTYPE html>
<html lang="en">
  <head>
    <?php require_once('head.php'); ?>
  </head>
  <body>
    <div id="page">

    <div id="ppCartcredit" class="reveal-modal">
      <div class="f_map clearfix">
        <h4>ViaBill betingelser</h4>
        <p>I samarbejde med ViaBill kan vi tilbyde faktura eller delbetaling. Det betyder at du kan købe dine varer nu, og vente med at betale.</p>
        <p>Når du gennemfører din bestilling, skal du vælge blot vælge ” ViaBill" som betalingsmetode. Klik på ” Gå til betaling ”og du bliver ført til en sikker side som kun ViaBill har adgang til at se, her gennemføres et kredittjek. Du får svar med det samme, og derefter er bestillingen gennemført.</p>
        <p>Vi sender dig dine varer og en faktura som dokumentation på din ordre, sideløbende vil du modtage en opkrævning fra ViaBill, det er denne du skal indbetale efter. </p>
        <p>Har du spørgsmål, er du meget velkommen til at kontakte vores kundeservice på tlf. 4930 1699. </p>
        <p>Alle spørgsmål vedrørende betaling af en faktura, skal rettes til ViaBill på telefon 88 826 826, da det er dem der yder kreditten. </p>
      </div>
       <a id="close-reveal-modal" class="close-reveal-modal"></a>
    </div>

      <?php require_once('header2.php'); ?>
      <section class="main mt190">
        <div class="container">
          <div class="template2 mb70">
            <div class="checkout_page clearfix">
              <div class="w285 fl">
                <h2><span>1</span>Kundeoplysninger</h2>
                <div class="entryInfo">
                  <h3>INDTAST DINE OPLYSNINGER:</h3>
                  <label for="">Vælg kundetype *</label>
                  <select id="choicemaker" name="choicemaker" class="input select">
                    <option value="privat">Privat</option>
                    <option value="erhverv">Erhverv</option>
                    <option value="offentlig">Offentlig instans</option>
                  </select>
                  <div id="w_privat" class="clearfix">
                    <input class="input" type="text" placeholder="Fornavn*">
                    <input class="input" type="text" placeholder="Efternavn*">
                    <input class="input" type="text" placeholder="Vejnavn*">
                    <input class="input" type="text" placeholder="Hus/gade nr.*">
                    <input class="w75 fl input2" type="text" placeholder="Postnr.*">
                    <input class="w203 fr input2" type="text" placeholder="Bynavn*">
                    <input class="input" type="text" placeholder="Telefon*">
                    <input class="input" type="text" placeholder="E-mail adresse*">
                    <textarea class="textarea" placeholder="Evt. din besked"></textarea>
                    
                    <a class="btnLevering cblack" href="#">Levering til anden adresse</a>
                    <div class="w_Address clearfix">
                      <input class="input" type="text" placeholder="Fornavn*">
                      <input class="input" type="text" placeholder="Efternavn*">
                      <input class="input" type="text" placeholder="Vejnavn*">
                      <input class="input" type="text" placeholder="Hus/gade nr.*">
                      <input class="w75 fl input2" type="text" placeholder="Postnr.*">
                      <input class="w203 fr input2" type="text" placeholder="Bynavn*">
                      <input class="input" type="text" placeholder="Telefon*">
                    </div>
                  </div>

                  <div id="w_erhverv" class="clearfix">
                    <input class="input" type="text" placeholder="Firmanavn">
                    <input class="input" type="text" placeholder="CVR-nr.*">
                    <input class="input" type="text" placeholder="Fornavn*">
                    <input class="input" type="text" placeholder="Efternavn*">
                    <input class="input" type="text" placeholder="Vejnavn*">
                    <input class="input" type="text" placeholder="Hus/gade nr.*">
                    <input class="w75 fl input2" type="text" placeholder="Postnr.*">
                    <input class="w203 fr input2" type="text" placeholder="Bynavn*">
                    <input class="input" type="text" placeholder="Telefon*">
                    <input class="input" type="text" placeholder="E-mail adresse*">
                    <textarea class="textarea" placeholder="Evt. din besked"></textarea>
                    
                    <a class="btnLevering cblack" href="#">Levering til anden adresse</a>
                    <div class="w_Address clearfix">
                      <input class="input" type="text" placeholder="Fornavn*">
                      <input class="input" type="text" placeholder="Efternavn*">
                      <input class="input" type="text" placeholder="Vejnavn*">
                      <input class="input" type="text" placeholder="Hus/gade nr.*">
                      <input class="w75 fl input2" type="text" placeholder="Postnr.*">
                      <input class="w203 fr input2" type="text" placeholder="Bynavn*">
                      <input class="input" type="text" placeholder="Telefon*">
                    </div>
                  </div>

                  <div id="w_offentlig" class="clearfix">
                    <input class="input" type="text" placeholder="EAN-nr.*">
                    <input class="input" type="text" placeholder="Myndighed/Institution*">
                    <input class="input" type="text" placeholder="Ordre- el. rekvisitionsnr.*">
                    <input class="input" type="text" placeholder="Personreference*">
                    <input class="input" type="text" placeholder="Fornavn*">
                    <input class="input" type="text" placeholder="Efternavn*">
                    <input class="input" type="text" placeholder="Vejnavn*">
                    <input class="input" type="text" placeholder="Hus/gade nr.*">
                    <input class="w75 fl input2" type="text" placeholder="Postnr.*">
                    <input class="w203 fr input2" type="text" placeholder="Bynavn*">
                    <input class="input" type="text" placeholder="Telefon*">
                    <input class="input" type="text" placeholder="E-mail adresse*">
                    <textarea class="textarea" placeholder="Evt. din besked"></textarea>
                   
                    <a class="btnLevering cblack" href="#">Levering til anden adresse</a>
                    <div class="w_Address clearfix">
                      <input class="input" type="text" placeholder="Fornavn*">
                      <input class="input" type="text" placeholder="Efternavn*">
                      <input class="input" type="text" placeholder="Vejnavn*">
                      <input class="input" type="text" placeholder="Hus/gade nr.*">
                      <input class="w75 fl input2" type="text" placeholder="Postnr.*">
                      <input class="w203 fr input2" type="text" placeholder="Bynavn*">
                      <input class="input" type="text" placeholder="Telefon*">
                    </div>
                  </div>
                   <p>(Felter markeret med * skal udfyldes)</p>

                </div>
              </div>

              <div class="w605 fr">
                <ul class="levering clearfix">
                  <h2><span>2</span>Levering</h2>
                  <li>
                    <input id="r2" name="one" value="1" type="radio" checked>
                    <label for="r2">Leveret til døren for Fyn og Jylland: 350 DKK</label>
                  </li>
                  <li>
                    <input id="r3" name="one" value="1" type="radio">
                    <label for="r3">Ved afhentning i Rødovre Centrum sparer du 10%, som vil blive fratrukket automatisk</label>
                  </li>
                </ul>
                <div class="payment_Method clearfix">
                  <h2><span>3</span>Betalingsmetode</h2>
                  <p>Du kan betale med følgende betalingskort:</p>
                  <p>
                    <span><input id="r4" name="three" value="3" type="radio" checked></span> <img src="img/cart2.png" alt="">
                  </p>
                  <p>
                    <span><input class="mt5" name="three" value="3" type="radio"></span>
                    <a href="#" data-reveal-id="ppCartcredit"><img src="img/icon_via.png" alt=""></a>
                  </p>
                </div>
                <div class="order_Summary clearfix">
                  <h2><span>4</span>Ordreoversigt</h2>
                  <table class="main_order_Summary">
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
                          <h4>Filippa Grå Terracotta 38 cm</h4>
                          <p>Vare-nummer: 30283</p>
                          <p>Størrelse: Højde 21 cm-Ø27 cm</p>
                          <p>BORDPLADE 50X60 CM, HVID MATTERET HÆRDET GLAS</p>
                        </div>
                      </td>
                      <td><p>1</p></td>
                      <td><p>479 DKK </p></td>
                      <td><p>479 DKK </p></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="img_pro">
                          <img src="img/img04.jpg" alt="">
                        </div>
                        <div class="content_pro">
                          <h4>Filippa Grå Terracotta 38 cm</h4>
                          <p>Vare-nummer: 30283</p>
                          <p>Størrelse: Højde 21 cm-Ø27 cm</p>
                          <p>BORDPLADE 50X60 CM, HVID MATTERET HÆRDET GLAS</p>
                        </div>
                      </td>
                      <td><p>1</p></td>
                      <td><p>479 DKK </p></td>
                      <td><p>479 DKK </p></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="img_pro">
                          <img src="img/img04.jpg" alt="">
                        </div>
                        <div class="content_pro">
                          <h4>Filippa Grå Terracotta 38 cm</h4>
                          <p>Vare-nummer: 30283</p>
                          <p>Størrelse: Højde 21 cm-Ø27 cm</p>
                          <p>BORDPLADE 50X60 CM, HVID MATTERET HÆRDET GLAS</p>
                        </div>
                      </td>
                      <td><p>1</p></td>
                      <td><p>479 DKK </p></td>
                      <td><p>479 DKK </p></td>
                    </tr>
                    <tr>
                      <td colspan="4" class="cf9f7f3">
                        <table class="sub_order_Summary">
                          <tr>
                            <td colspan="2">SUBTOTAL INKL. MOMS: </td>
                            <td colspan="2" width="30%">
                              1.916 DKK
                            </td>
                          </tr> 
                          <tr>
                            <td colspan="2">FRAGT: </td>
                            <td>150 DKK</td>
                          </tr>
                          <tr>
                            <td colspan="2"><h4>AT BETALE INKL. MOMS:</h4></td>
                            <td colspan="2"><h4>1.955 DKK</h4></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </div>
                <p class="accetp"><input type="checkbox" checked> Jeg bekræfter hermed at mine data er korrekte, samt at kurven indeholder de varer jeg ønsker. </p>
                <a class="conditions" href="terms.php">Jeg accepterer Handelsbetingelser.</a>
              </div>
              <div class="clear"></div>
              <div class="nextto clearfix">
                <a class="fl btnVarekurv cblack" href="cart.php">Til Varekurv</a>
                <a class="fr btnBetaling cblack" href="thanks.php">til Betaling</a>
              </div>

               <div class="graris" style="margin-top: 20px; display: none;">
                  <p>Har du en værdikode?</p>
                  <div class="frm_coupon clearfix">
                      <input name="coupon" placeholder="Indtast koden her ...">
                      <a href="#">Aktiver</a>
                      <input type="submit" value="Send" id="Send" name="Send" style="display:none">
                  </div>
                </div>
                <div class="clear"></div>
              
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