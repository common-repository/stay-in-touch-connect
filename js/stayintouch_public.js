jQuery(document).ready(function($){
    //do something
    debug('public stayintouch js script loaded');

    function debug(msg,data){
        try {
            console.log(msg);
            if (typeof data !== "undefined") {
                console.log(data);
            }
        } catch (e) {
            
        }

    
        //sit front end
        $('#sit_front_subscribe').click(function(){
            // var username = $('#sit_username').val();
            // var password = $('#sit_password').val();
 
            //console.log(username);
            //console.log(password);

            var email = $('#sit_front_subscriber_email').val();

            console.log(email);
            if (email == "") {
                console.log('empty');
            }else{

                //setup wp ajax url
                var wpajax_url = document.location.protocol + '//' + document.location.host + '/wp-admin/admin-ajax.php';
                console.log('clicked');

                //submit the chossen item via AJAX
                

                var settings = {
                    url: wpajax_url + '?action=sit_ajax_subscribe_a_subscriber',
                    method: "POST",
                    data:{
                        subscriber: email
                    },
                  };
                  
                  $.ajax(settings).done(function (response) {
                      //var data = $.parseJSON(response);
                    
                    if (response == true) {
                        //console.log(response);
                        
                        alert("Confirmation email sent to the subscriber");
                        // swal("Hello world!");
                        //window.location.reload();
                    }else{
                        alert("Unable To Add Subscriber");
                    }
                    //location.reload();
                  });

            }
         
 
            
         });

        //sit front end 


    }

    

   
});