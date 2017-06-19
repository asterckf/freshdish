</div><br><br>

<div class="col-md-12 text-center">&copy; Copyright 2017 Ecommerce-Course freshdish</div>
<script>


function detailsmodal(id){
  var data = {"id": id};
  jQuery.ajax({
    url: '/freshdish/includes/detailsmodal.php',
    method: "post",
    data : data,
    success: function(data){
      jQuery('body').append(data);
      jQuery('#details-modal').modal('toggle');
    },
    error: function(){
      alert("Something went wrong!");
    }

  });
}



function add_to_cart(){
  jQuery('#modal_errors').html("");
  var error = '';
  var quantity = jQuery('#quantity').val();
  var data = jQuery('#add_product_form').serialize();
  if ( quantity =='' || quantity == 0){
    error = '<p class="text-danger text-center">You must choose the quantity.</p>';
    jQuery('#modal_errors').html(error);
    return;
  }else{
    jQuery.ajax({
      url:'/freshdish/admin/parsers/add_cart.php',
      method:'post',
      data: data,
      success: function(){
        location.reload();
      },
      error: function(){
        alert("Something has happened!");
      }
    });
  }
}
</script>
</body>
</html>
