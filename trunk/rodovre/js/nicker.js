$(document).ready(function(){
    var $scrollbar      = $("#scrollbar1"),
        $shopcart       = $(".list-cart"),
        $btnAdd         = $("#btnAddcart"),
        $bgCart         = $('#bg-cart'),
        $closeAll       = $('#btnClose-cart'),
        active          = "active",
        wWidth          = $(window).width(),
        wHeight         = $(window).height();
        window.timer    = "";

    // Set background width and height
    $bgCart.css({
        width   : wWidth,
        height  : wHeight
    });

    // Show shopcart when mouse over the shopcart menu
    // active  autoHide after mouse leaving
    $(".img-cart, .checkcart, .bnt-see-cart").on("click", function(){
        $bgCart.fadeIn('fast');
        $shopcart.stop(true,true).fadeIn('fast').addClass(active);
        $scrollbar.tinyscrollbar({sizethumb: 33});

    });

    $(".cart_content").hover(
        function(){
            //clear timeout
            clearTimeout(window.timer);
        },
        function(e){
            autoHide( e );
        }
    );

    // Close shopcart and hide background clicking on background
    $bgCart.on("click", function(e){
        $shopcart.slideUp('fast').removeClass( active );
        $(this).fadeOut('fast');
        clearTimeout(window.timer);
    });

    // Close shopcart and hide background clicking on close All
    $closeAll.on("click", function(e){
        $shopcart.slideUp('fast').removeClass( active );
        $bgCart.fadeOut('fast');
        clearTimeout(window.timer);
    });

    // Show shopcart and background
    $btnAdd.on("click", function( e ){
        $bgCart.show('fast');
        $shopcart.stop(true,true).fadeIn('fast').addClass(active);
        $scrollbar.tinyscrollbar();
        autoHide( e );
    });
    
});

function autoHide(e){
    var $this           = $('.list-cart'),
        $bgCart         = $('#bg-cart'),
        $thisCor        = $this.offset(),
        XX              = $thisCor.left + $this.width(),
        YY              = $thisCor.top + $this.height(),
        active          = "active";
        window.mouseX   = e.pageX;
        window.mouseY   = e.pageY;

    if( !((window.mouseX > $thisCor.left && window.mouseX < XX) &&
        (window.mouseY > $thisCor.top && window.mouseY < YY) ) ){
        if($this.hasClass( active )){
            //console.log('start time out');
            window.timer = setTimeout(function(){
                $this.slideUp('fast',function(){
                    $this.removeClass( active );
                    $bgCart.fadeOut('fast');
                });
            },2500);
        }
    }

}
