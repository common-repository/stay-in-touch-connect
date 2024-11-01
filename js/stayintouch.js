jQuery(document).ready(function($){
    //do something
    debug('public js script loaded');

    function debug(msg,data){
        try {
            console.log(msg);
            if (typeof data !== "undefined") {
                console.log(data);
            }
        } catch (e) {
            
        }

        // sit login form submit start
        $('#sit_form_submit').click(function(){
           var username = $('#sit_username').val();
           var password = $('#sit_password').val();

           //console.log(username);
           //console.log(password);
           if (username == '' || password == '') {
            console.log('empty');
            alert('Please Enter Valid Data');
           }else{
               //setup wp ajax url
                var wpajax_url = document.location.protocol + '//' + document.location.host + '/wp-admin/admin-ajax.php';
                console.log('clicked');

                //submit the chossen item via AJAX
                

                var settings = {
                    url: wpajax_url + '?action=sit_ajax_save_login',
                    method: "POST",
                    data:{
                        sit_username: username,
                        sit_password: password
                    },
                  };
                  
                  $.ajax(settings).done(function (response) {
                      //var data = $.parseJSON(response);
                    
                    if (response) {
                        //console.log(response);
                        window.location.reload();
                    }
                    //location.reload();
                  });

           };

           
        });

        // sit login form submit end

        //sit attach List start
        $('#list_select').click(function(){
            var list_uid = $('#listid').val();
            //var password = $('#sit_password').val();
 
            //console.log(username);
            //console.log(password);
            if (list_uid == '') {
             console.log('empty');
             alert('Please Enter Valid Data');
            }else{
                //setup wp ajax url
                 var wpajax_url = document.location.protocol + '//' + document.location.host + '/wp-admin/admin-ajax.php';
                 console.log('clicked');
 
                 //submit the chossen item via AJAX
                 
 
                 var settings = {
                     url: wpajax_url + '?action=sit_ajax_save_list',
                     method: "POST",
                     data:{
                         sit_list_uid: list_uid
                     },
                   };
                   
                   $.ajax(settings).done(function (response) {
                       //var data = $.parseJSON(response);
                     
                     if (response) {
                         console.log(response);
                         window.location.reload();
                     }else{
                         console.log('no response');
                     }
                     //location.reload();
                   });
 
            };
 
            
         });
        //sit attach list end

        // sit delete list start
        $('#delete_list').click(function(){
            
                //setup wp ajax url
                 var wpajax_url = document.location.protocol + '//' + document.location.host + '/wp-admin/admin-ajax.php';
                 console.log('clicked');
 
                 //submit the chossen item via AJAX
                 
 
                 var settings = {
                     url: wpajax_url + '?action=sit_ajax_delete_list',
                     method: "POST",
                     data:{
                         sit_list_delete: true
                     },
                   };
                   
                   $.ajax(settings).done(function (response) {
                       //var data = $.parseJSON(response);
                     
                     if (response) {
                         console.log(response);
                         window.location.reload();
                     }else{
                         console.log('no response');
                     }
                     //location.reload();
                   });
 
           
 
            
         });
        // sit delete list end

        //sit delete token start
        $('#delete_token').click(function(){
            
            //setup wp ajax url
             var wpajax_url = document.location.protocol + '//' + document.location.host + '/wp-admin/admin-ajax.php';
             console.log('clicked');

             //submit the chossen item via AJAX
             

             var settings = {
                 url: wpajax_url + '?action=sit_ajax_delete_token',
                 method: "POST",
                 data:{
                     sit_list_delete: true
                 },
               };
               
               $.ajax(settings).done(function (response) {
                   //var data = $.parseJSON(response);
                 
                 if (response) {
                     console.log(response);
                     window.location.reload();
                 }else{
                     console.log('no response');
                 }
                 //location.reload();
               });

       

        
     });
        //sit delete token end

        //sit front end
        $('#sit_front_subscribe').click(function(){
            // var username = $('#sit_username').val();
            // var password = $('#sit_password').val();
 
            //console.log(username);
            //console.log(password);

            console.log('clicked');
            if (username == '' || password == '') {
             console.log('empty');
             alert('Please Enter Valid Data');
            }else{
                //setup wp ajax url
                 var wpajax_url = document.location.protocol + '//' + document.location.host + '/wp-admin/admin-ajax.php';
                 console.log('clicked');
 
                 //submit the chossen item via AJAX
                 
 
                 var settings = {
                     url: wpajax_url + '?action=sit_ajax_save_login',
                     method: "POST",
                     data:{
                         sit_username: username,
                         sit_password: password
                     },
                   };
                   
                   $.ajax(settings).done(function (response) {
                       //var data = $.parseJSON(response);
                     
                     if (response) {
                         //console.log(response);
                         window.location.reload();
                     }
                     //location.reload();
                   });
 
            };
 
            
         });

        //sit front end 


    }

    

   
});


