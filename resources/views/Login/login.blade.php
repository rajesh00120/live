<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
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
    </head>
    <body>
        <div>
            <div class="container-fluid sign-in">
              <div class="col-sm-12 overflow-hidden">
                <div class="row">
                  <div class="col-sm-9 museum_login_set">
                    <div class="text_centered">
                      <img class="d-block mx-auto" src="{{ URL::asset('public/assets/ic_img.png') }}" alt="logo" />
                      <h2 class="text-capitalize text-center font-weight-bold">micah</h2>
                      <h4 class=" text-center">Build your legacy</h4>
                    </div>
                  </div>
                  <div class="col-sm-3 login_border_radius mt-5 mb-5 pb-5">
                    <h3 class="pt-4 pb-4">Log in</h3>
                    @if(!empty($errors->first()))

                    <div class="alert alert-danger">
                        <span>{{ $errors->first() }}</span>
                    </div>

                    @endif
                    @if(session()->has('message'))
                    <div class="alert alert-success">
                    {{ session()->get('message') }}
                    </div>
                    @endif
                    <form class="mt-4" action="{{url('loginPostUrl')}}" method="post">
                    @csrf
                      <div class="form-group mb-5">
                        <label for="email">Email address:</label>
                        <input
                          type="email"
                          name="email"
                          class="form-control"
                          placeholder="Enter email"
                          id="email"
                        />
                       
                      </div>
                      <div class="form-group mb-5">
                        <label for="pwd">Password:</label>
                        <div class="museum_password">
                          <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter password"
                            id="pwd"
                          />
                          <!-- <div class="password_div"><i class="fa fa-eye" aria-hidden="true"></i></div> -->
    
                          <i id="show_passwords" onclick="show_password()" class="fa fa-eye-slash icon" aria-hidden="true"></i>
                          <i id="hide_passwords" onclick="hide_password()" class="fa fa-eye icon" aria-hidden="true"></i>
                        </div>
                      </div>
                      <div class="form-group form-check text-center">
                        <a href="#" class="d-block mb-4">
                          Retrieve password
                        </a>
                      </div>
                      <div class="col-sm-12 text-center mt-5">
                        <button
                          type="submit"
                        
                          class="col-sm-10 btn btn-primary d-block mx-auto "
                        >
                          Log in
                        </button>
                      </div>
                    </form>
                    <div class="col-sm-12 mt-5 text-center">
                      <span class="d-block float-left ml-4">
                        Already have an account?
                      </span>
                      <a href="#" class="d-block float-left">
                        Create an account
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <script>
            function show_password() { 
                var x = document.getElementById("pwd"); //alert(x);
                document.getElementById('show_passwords').style.display='none';
                document.getElementById('hide_passwords').style.display='block';
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }
            function hide_password() { 
                var x = document.getElementById("pwd"); //alert(x);
                document.getElementById('show_passwords').style.display='block';
                document.getElementById('hide_passwords').style.display='none';
                if (x.type === "text") {
                    x.type = "password";
                } else {
                    x.type = "text";
                }
            }
          </script>
</body>
</html>          
    