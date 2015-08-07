jQuery(document).ready(function($){
  var mainheight = jQuery(window).height();
  var mainwidth = jQuery(window).width();
  console.log(mainwidth,mainheight);

  jQuery(".slider").owlCarousel({items:1,dots:true,dotsEach:true,autoplay:true,loop:true,});

if (mainwidth > 801) {
var itemsShow = jQuery(".columnslider").attr('data-items');
var itemsID = jQuery(".columnslider").attr('id');
console.log(itemsShow);

  jQuery(".columnslider").owlCarousel({items:itemsShow,dots:false,margin:20,nav:true,
    responsiveClass:true,
    responsive:{
        0:{
            items:1,
        },
        480:{
            items:2,
        },
        768:{
            items:3,
        },
        1025:{
            items:itemsShow,
        }
    }
  });
}

jQuery(".showsearch,.cat-search-menu-btn,.maincatclass span").click(function(){
    var id = jQuery(this).attr('data-id');
    jQuery("#"+id).slideToggle();
});

jQuery('.product-meta-select li').click(function() {
    var id = jQuery(this).parent().attr('data-id');
    var value = jQuery(this).text();
    jQuery('[data-id=' + id + '] li').removeClass('active');
    jQuery(this).addClass('active');
    jQuery('#'+id+' .selected-value').html(value);
    //console.log(id,value);
});



jQuery(".filters .heading").click(function(){
    var id = jQuery(this).attr('data-id');
    jQuery(this).children('span').toggleClass('plus');
    jQuery("#"+id).slideToggle();
});

if (mainwidth == 768 || mainwidth == 1024){
  window.onorientationchange = function(){
    window.location.reload();
  }
};

if (mainwidth < 800) {

        jQuery(".maincatclass,.cat-menu-btn").click(function(){
            var id = jQuery(this).attr('data-id');
            jQuery("#"+id).slideToggle();
        });

        jQuery(".loadmore").click(function(){
            var id = jQuery(this).attr('data-id');
            console.log(id);
            jQuery("#"+id+" .col-box").fadeIn();
            jQuery("#"+id+" .col-rec").fadeIn();
            jQuery(this).slideUp();
        });

      var col = [
        '#sectionDeals .col-box',
        '#recomandationsSection .col-rec',
        '#bestselingproducts .col-box',
        //"#ComputerAccessories .col-box"
      ]
      var length = {};
      for (var i = 0; i < col.length; i++) {
        length[i] = $(col[i]).length;

        for(var j=0 ;j<length[i];j++){
          if(j>3){
            var d = $(col[i])[j];
            $(d).hide();
          }
        }

      };
    }
});
jQuery(document).ready(function($){
    jQuery('.toggle-menu').jPushMenu();
    jQuery('.selectpicker').selectpicker();
    // jQuery('.view-mode a').click(function(){
    //   jQuery('.view-mode a').removeClass('active');
    //   var viewmode = jQuery(this).attr('data-id');
    //   console.log(viewmode);
    //   jQuery('.products, .product').removeClass('list grid');
    //   jQuery(this).addClass('active');
    //   jQuery('.products,.product').addClass(viewmode);
    // });
  jQuery(function(){
    jQuery(window).scroll(function () {
      if (jQuery(this).scrollTop() > 1500) {
        jQuery('.page_menu').addClass('navfixed');
      } else {
        jQuery('.page_menu').removeClass('navfixed');
      }
    });
  });


});
// (function(){
//     jQuery(window).load(function(){
//         jQuery("a[rel='m_PageScroll2id']").mPageScroll2id();
//     });
// })(jQuery);