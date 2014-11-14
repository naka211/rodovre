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
           <div class="main_left clearfix">
            <?php require_once('left_content.php'); ?>
          </div>
          <div class="main_right">
            <div class="template">
              <div class="conditions_page">
                <ul class="breadcrumb">
                  <li><a href="index.php">Forside</a></li>
                  <li><a href="newsletter.php">Nyhedsbrev Tilmelding</a></li>
                </ul>
                <div class="newsletter clearfix pt0">
                  <div class="w485 fl">
                    <p>TILMELD DIG VORES NYHEDSBREV OG FÅ GODE TILBUD OG NYHEDER.<br>
                    VI UDSENDER NYHEDSBREVE 1-2 GANGE OM MÅNEDEN!</p>
                  </div>
                  <div class="w430 fl mt10">
                    <input type="text" placeholder="Indtast din e-mail">
                    <a class="btnSubscribe btn2 fl mt10" href="#">Tilmeld</a>
                  </div>
                </div>
              </div>
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