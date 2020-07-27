$(document).ready(function(){
    baseurl = "http://172.16.200.38/museum/";
    //baseurl = "http://115.112.129.194:7575";
    // csrfjs = "";
$("#mytable #checkall").click(function () {
        if ($("#mytable #checkall").is(':checked')) {
            $("#mytable input[type=checkbox]").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("#mytable input[type=checkbox]").each(function () {
                $(this).prop("checked", false);
            });
        }
    });
    
    // $("[data-toggle=tooltip]").tooltip();


    
     $('#customerList').DataTable({
        "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
      
        "ordering": false,
        "processing": true,
        "pageLength": 10,
        "serverSide": true,
        "ajax": {
            "url": "/museum/getAjaxUsersList",
            "type": "POST",
            "data": {"_token":csrfjs,id:$("#customerList").val()},
        }
        
    });
    $('#MediaList').DataTable({
        "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
      
        "ordering": false,
        "processing": true,
        "pageLength": 10,
        "serverSide": true,
        "ajax": {
            "url": "/museum/getAjaxMediaList",
            "type": "POST",
            "data": {"_token":csrfjs,id:$("#MediaList").val()},
           
        }
    
    });
    
    $('.museum_dashboard').removeClass('active');
    if (window.location.href.indexOf("dashboard") > -1  || window.location.href.indexOf("loginPostUrl") > -1) { //alert('hi');
        $('.museum_dashboard').addClass('active');
    }
    if (window.location.href.indexOf("reports") > -1) {
        $('.museum_reports').addClass('active');
    }
    // if (window.location.href.indexOf("view_complaints") > -1 ) {
    //     $('.museum_media').addClass('active');
    // }
    if (window.location.href.indexOf("users") > -1 ) {
        $('.museum_users').addClass('active');
    }
    if (window.location.href.indexOf("media") > -1) {
        $('.museum_media').addClass('active');
    }
    if (window.location.href.indexOf("media_steps") > -1) {
        $('.museum_media_steps').addClass('active');
    }
    if (window.location.href.indexOf("notifications") > -1) {
        $('.museum_notifications').addClass('active');
    }
    if (window.location.href.indexOf("advertise") > -1) {
        $('.museum_advertise').addClass('active');
    }
    if (window.location.href.indexOf("profile") > -1) {
        $('.museum_profile').addClass('active');
    }
    $('.msg_bar').fadeOut(5000);
    $('.close').click(function(){
        location.reload(true);
        $('#password_update').fadeOut();
    });

    $(".select_month").change(function(){ 
        var selected_val = $(this).val();
        var selected_id= $(this).data('id'); 
        $.ajax({
            url: '/museum/select_mnth',
            type: 'POST',
            data: {"_token": $('meta[name="csrf-token"]').attr('content'),"selected_val": selected_val,"selected_id": selected_id},
            error: function() {
               alert('Something is wrong');
            },
            success: function(data){ 
                if(data.length>0) {
                    $('.title_date').text(selected_val);
                    $('#album ul').remove('');
                    $('#photos ul').remove('');
                    $('#videos ul').remove(''); 
                    $('.count_albums').text('');
                    $('.count_photos').text('');
                    $('.count_vid').text('');
                    var count_albums = 0;
                    var count_photos = 0;
                    var count_videos = 0;
                    //let arr = [];
                    for (var i=0;i<=data.length;i++){
                        //alert(data);
                    $('#album').append('<ul><li class="list-group-item"><div class="row"><img class="img_width2 border_radius_10" src= /museum/public/album_images/'+data[i]['timeline_cover']+' alt=""></div><div class="row"><div class="col-sm-10 pl-0 pr-0"><span class="d-block mt-1">'+data[i]['album_name']+'</span><span class="d-block light_grey">'+data[i]['date']+'</span></div><div class="col-sm-2 pl-1"><div class="trash_div float-right mt-1 ml-3"><button class="delete_ads" data-id='+data[i]['album_id']+' delete_url="delete_album" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button></div></div></div></li></ul>');
                        
                    if(data[i]['type']=='1'){ 
                        $('#photos').append('<ul><li class="list-group-item"><div class="row"><img class="img_width2 border_radius_10" src= /museum/public/album_images/'+data[i]['album_media_path']+' alt=""></div><div class="row"><div class="col-sm-10 pl-0 pr-0"><span class="d-block mt-1">'+data[i]['album_name']+'</span><span class="d-block light_grey">'+data[i]['created_at']+'</span></div><div class="col-sm-2 pl-1"><div class="trash_div float-right mt-1 ml-3"><button class="delete_ads" onclick="myFunction(x='+data[i]['media_id']+')" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button></div></div></div></li></ul>');
                        count_photos++;
                        $('.count_photos').text(count_photos);
                    }
                    if(data[i]['type']=='2'){
                        $('#videos').append('<ul><li class="list-group-item"><div class="row"><video width="320" height="240" controls class="img_width2 border_radius_10"><source src= /museum/public/album_images/'+data[i]['album_media_path']+' type="video/mp4"></video></div><div class="row"><div class="col-sm-10 pl-0 pr-0"><span class="d-block mt-1">'+data[i]['album_name']+'</span><span class="d-block light_grey">'+data[i]['created_at']+'</span></div><div class="col-sm-2 pl-1"><div class="trash_div float-right mt-1 ml-3"><button class="delete_ads" data-id='+data[i]['media_id']+' delete_url="delete_videos" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button></div></div></div></li></ul>');
                        count_videos++;
                        $('.count_vid').text(count_videos);
                    }
                    count_albums++;
                    $('.count_albums').text(count_albums);
                }
                }
            }
        });
    });

    $('#country').on('change',function(){ 
        var countryID = $(this).val();   //alert(countryID);
        if(countryID){
            $.ajax({
                type:'POST',
                url: '/museum/select_state/'+countryID,
                data:{"_token": $('meta[name="csrf-token"]').attr('content'),"countryID":countryID},
                error: function() { 
                    alert('Something is wrong');
                 },
                success:function(data){  //alert(data);
                    //$('#state').remove('');
                    $('#state option').remove();
                    $("#state").prepend("<option value='Select State' selected='selected'>Select State</option>");
                    for (var i=0;i<=data.length;i++){
                        $('#state').append('<option data-id ='+data[i]['state_id']+' value='+data[i]['state_id']+'>'+data[i]['state_name']+'</option>');
                    }
                    $('#city').html('<option value="">Select state first</option>'); 
                }
            }); 
        }
    });
    
    $('#state').on('change',function(){
        //var stateID = $(this).data('id'); 
        var stateID = $(this).val();  //alert(stateID);
        if(stateID){
            $.ajax({
                type:'POST',
                url:'/museum/select_city/'+stateID,
                data:{"_token": $('meta[name="csrf-token"]').attr('content'),"stateID":stateID},
                success:function(data){
                    $('#city option').remove();
                    $("#city").prepend("<option value='Select City' selected='selected'>Select City</option>");
                    for (var i=0;i<=data.length;i++){
                        $('#city').append('<option value='+data[i]['city_id']+'>'+data[i]['city_name']+'</option>');
                    }
                    //$('#city').html('<option value="">Select state first</option>');
                }
            }); 
        }
    });

    $('#listing_reports').click(function(){
        $('.show_reported_list').slideToggle("slow");
    });

    // $(".delete_ads").click(function(){  
    //     var id = $(this).data('id');   alert(id);
    //     var delete_url = $(this).attr('delete_url'); //alert('delete_url');
    //     $("input[name='delete_id']").val(id);
    //     $("input[name='delete_url']").val(delete_url);
    // });

    $('.delete_ads').on('click',function(){
        var id = $(this).data('id');   //alert(id);
        var delete_url = $(this).attr('delete_url'); //alert('delete_url');
        $("input[name='delete_id']").val(id);
        $("input[name='delete_url']").val(delete_url);
     });

    $(".remove").click(function(){ 
        var deleteid = $("input[name='delete_id']").val(); 
        var token = $("input[name='token']").val(); 
        var delete_url = $("input[name='delete_url']").val(); 
        if(deleteid)
        {
            $.ajax({
            url: '/museum/'+delete_url,
            type: 'POST',
            data: {"_token": token,'deleteid': deleteid},
            error: function() {
                alert('Something is wrong');
            },
            success: function(data) {//alert();
                console.log(data);
                location.reload();
                $("#deletModel").modal('hide'); 
            }
            });
        }
        
    });

    $(window).load(function(){
        var div_height = $(".get_div_height").height()+'px';
        $('#sidebar').css('height',div_height);
        //alert(div_height);
    });

   
    // $('.password_div').click(function(){
    //     alert('clicked');
    // });

    // $("input").keyup(function(){
    //     $("input").css("background-color", "pink");
    //   });

    $('#new_pass').click(function(){ //alert('load');
        var curr_pass = $('#current_pass').val();   //alert(curr_pass);
        if(curr_pass){
            $.ajax({
                type:'POST',
                url: '/museum/match_pass',
                data:{"_token": $('meta[name="csrf-token"]').attr('content'),"curr_pass":curr_pass},
                error: function() { 
                    alert('Something is wrong');
                 },
                success:function(data){  //alert(data);
                    //$('#state').remove('');
                    // $('#state option').remove();
                    // $("#state").prepend("<option value='Select State' selected='selected'>Select State</option>");
                    // for (var i=0;i<=data.length;i++){
                    //     $('#state').append('<option data-id ='+data[i]['state_id']+' value='+data[i]['state_id']+'>'+data[i]['state_name']+'</option>');
                    // }
                    // $('#city').html('<option value="">Select state first</option>'); 
                }
            }); 
        }
    });

});
function myFunction(a)
{
    var deleteid = a; 
    //alert(deleteid);
}
function clicktovalid(){ 
    var ad_image = document.getElementById("profile_pic").value; //alert(ad_image.length)
    if (ad_image.length == 0){
        document.getElementById('image_class').style.display='block';
        return false;
    }
    var ad_title= document.getElementById("title_adv").value; //alert(ad_title);
    var ad_url = document.getElementById("url_adv").value;
    var ad_country = document.getElementById("country").value;
    var ad_state = document.getElementById("state").value;
    var ad_city = document.getElementById("city").value;
    if (ad_title.length < 1){
        document.getElementById('title_class').style.display='block';
        return false;
    }
    if (ad_url.length < 1){
        document.getElementById('url_class').style.display='block';
        return false;
        //document.getElementById('person_name_class').style.display='block';
        //return false;
    }
    if (ad_country.length < 1){
        document.getElementById('country_class').style.display='block';
        return false;
    }
    if (ad_state.length < 1){
        document.getElementById('state_class').style.display='block';
        return false;
    }
    if (ad_city.length < 1){
        document.getElementById('city_class').style.display='block';
        return false;
    }
}  

function clickSlashtovalid(){ 
    var ad_image = document.getElementById("slash_pic").value; //alert(ad_image.length)
    if (ad_image.length == 0){
        document.getElementById('image_class').style.display='block';
        return false;
    }
    var ad_title= document.getElementById("title_adv").value; //alert(ad_title);
    var ad_url = document.getElementById("url_adv").value;
    var ad_country = document.getElementById("country").value;
    var ad_state = document.getElementById("state").value;
    var ad_city = document.getElementById("city").value;
}  




