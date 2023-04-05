</div><br><br>

   <footer class="text-center" id="footer">Copyright <span id="copyright">&copy;</span> 2015-<?php echo date('Y');  ?> De Jossy's Boutique.</footer>

<script>
//  jQuery(window).scroll(function(){
//  var vscroll = jQuery(this).scrollTop();
//   jQuery('logo').css(); 
//  	"transform" : "translate(0px, "+vscroll/2+"px)"
//  });

//  jQuery(window).scroll(function(){
//  var vscroll = jQuery(this).scrollTop();
//   jQuery('#back-flower').css(); 
//  	"transform" : "translate("+vscroll/5+"px, -"+vscroll/2+"px)"
//  });
 
//  jQuery(window).scroll(function(){
//  var vscroll = jQuery(this).scrollTop();
//   jQuery('#fore-flower').css(); 
//  	"transform" : "translate(0px, -"+vscroll/2+"px)"
//  });
//  });



  function detailsmodal(id){
     var id;
   var data = {'id' : id};
    jQuery.ajax({
       url :  '/tutorial/includes/detailsmodal.php',
       method : 'post',
       data : data,
       success : function(data){
          if (jQuery('#details-modal').length) {
             jQuery('#details-modal').remove();
          }
          jQuery('body').append(data);
          jQuery('#details-modal').modal('toggle');
       },
       error : function(){
         alert("Sorry, Something went wrong!");
       }

      });
  }
  
  function update_cart(mode,edit_id,edit_size){
    var data = {"mode" : mode, "edit_id" : edit_id, "edit_size" : edit_size};
    jQuery.ajax({
       url : '/tutorial/admin/parsers/update_cart.php',
       method : 'post',
       data : data,
       success : function (){location.reload();
         top.location = self.location;
       },
       error : function (){alert("Something went wrong.");},
    });
  }

  function add_to_cart(){
      jQuery('#modal_errors').html("");
      var size = jQuery('#size').val();
      var quantity = jQuery('#quantity').val();
      var available = jQuery('#available').val();
      quantity = Number(quantity);
      available = Number(available);
      console.log(available);
      var error ='';         
      var data = jQuery('#add_product_form').serialize();

      if (size ==''|| quantity ==''|| quantity <= 0) {
         error +='<h4 class="text-danger text-center">You must choose a size and quantity</h4>';
         jQuery('#modal_errors').html(error);
         return;
      }else if(quantity > available){
      error +='<h4 class="text-danger text-center">There '+((available < 2)?'is':'are')+' only '+available+' Available</h4>';
      jQuery('#modal_errors').html(error);
      return;
   }else{
      jQuery.ajax({
         url : '/tutorial/admin/parsers/add_cart.php',
         method : 'post',
         data : data,
         success : function(){
            location.reload();
            top.location = self.location;
         },
         error : function(){alert("Something went wrong.");}
      });
   }
   }
    
</script>
</body>
</html>