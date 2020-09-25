Drupal.behaviors.myBehavior = {
  attach: function (context, settings) {
    jQuery("#showModal").click();
    jQuery("#banner-image-1").addClass("active");
  }
};