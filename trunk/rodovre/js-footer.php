 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.9.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.carouFredSel-6.2.1-packed.js"></script>
    <script src="js/jquery.reveal.js"></script>
    <script src='source/jquery.fancybox.pack.js'></script>
    <script src='source/helpers/jquery.fancybox-buttons.js'></script>
    <script src='source/helpers/jquery.fancybox-media.js'></script>
    <script src='source/helpers/jquery.fancybox-thumbs.js'></script>
    <script src="js/jquery.prettyPhoto.js"></script>
    <script src='js/jquery.tinyscrollbar.js'></script>
    <script src='js/nicker.js'></script>
    
    <script>
      $(document).ready(function() {
        $(".fancybox").fancybox({
              openEffect  : 'none',
              closeEffect : 'none'
          });

        /*********** BANNER *********/
        $("#foo1").carouFredSel({
          circular: true,
          infinite: true,
          //responsive: true,
          auto    : true, /*{
                  pauseOnHover: "resume"
              },*/
          pagination  : "#block1_pag",
          scroll      :
              {
                  duration    :1200,
                  fx          : "crossfade"
              },
          items       :
              {
                  visible     : 1,
                  width       : 715,
                  height      : 334
              }
        });

        /*$('.products ul li').each(function() {
          animationHover(this, 'zoomIn');
        });*/

      
        $('#scrollbar1, #scrollbar2').tinyscrollbar({sizethumb: 33});

        $(".btnCookie").click(function(event) {
          event.preventDefault();
          $(".CookieInfo").hide('slow/400/fast', function() {});
        });

        //check out

        $(".w_Address").hide();
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

        //Product detail zoom
        $('.imgZoom').prettyPhoto({
            social_tools:'',
            show_title: true,

        });

        //Product change thumnail
        changeThumnail();
      });

       function changeThumnail(){
        var $imgLarge = $('#btnLargeImage'),
            $imgLarge_icon = $('#btnZoomIcon');
        $('#thumblist img').each( function(){
            var $this = $(this),
                imgLink = $this.attr('src');
            $this.click(function(e){
                e.preventDefault();
                $imgLarge.attr('href',imgLink);
                $imgLarge.find('img').attr('src',imgLink);
                $imgLarge_icon.attr('href',imgLink);
            });
        });
      };

      function animationHover(element, animation){
          element = $(element);
          element.hover(
              function() {
                  element.addClass('animated ' + animation);        
              },
              function(){
                  //wait for animation to finish before removing classes
                  window.setTimeout( function(){
                      element.removeClass('animated ' + animation);
                  }, 2000);         
              });
      };
    </script>