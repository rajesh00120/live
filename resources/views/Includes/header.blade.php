<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel <?php echo URL("public/profile_pics/"); ?></title>
        <link rel="manifest" href="%PUBLIC_URL%/manifest.json" />
        <link rel="stylesheet" href=<?php echo URL("public/css/bootstrap.min.css"); ?> />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href=<?php echo URL("public/css/pages.css"); ?> />
        <link rel="stylesheet" href=<?php echo URL("public/css/login.css"); ?> />
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href = "https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" />

        <!--datatabels-->
         

              <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
              <!------ Include the above in your HEAD tag ---------->

              <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
              <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
              <script>
                // var baseurl = '{{url('admin')}}';
                var csrfjs = '{{csrf_token()}}';

              </script>
    </head>
    <body>

    @if(Session::get('name'))

    @else
    <script type="text/javascript">
        window.location = "/museum/login";
    </script>
    @endif 

<div style="position: relative;">
    <div class="museum_header col-sm-12 pt-4 pb-4">
      <div class="row">
        <div class="col-sm-2">
          <img
            class="img_class float-left"
            src="{{ URL::asset('public/assets/Logo.png') }}"
            alt="museum_logo"
          />
       
          <div class="logo_txt float-left ml-2"> <?php //print_r($data); ?>
            <span class="text-capitalize font-weight-bold d-block">
              <h3>Micah</h3>
            </span>
            <span class="d-block">Build your legacy</span>
          </div>
        </div>
        <div class="col-sm-8"></div>
        <div class="col-sm-2 float-right">
          <img
            src="{{ URL::asset('public/assets/admin.png') }}"
            class="rounded-circle img-fluid img_class admin_image"
            alt="admin_image"
          />
          <span class="ml-3"><?php $user_name = Session::get('name'); echo $user_name;?></span>
        </div>
      </div>
    </div>

    <!-- menu hamburger -->
    <div class="hamburger_menu" >
      <div class="bar1"></div>
      <div class="bar2"></div>
      <div class="bar3"></div>
    </div>
    <!-- menu hamburger -->

  </div>
<script>
  $(document).ready(function(){
    $('.hamburger_menu').click(function(){
      $(this).toggleClass('change'); 
      $('#sidebar').fadeToggle('slow');
    });
  });
</script>