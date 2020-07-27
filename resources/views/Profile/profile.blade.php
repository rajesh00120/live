@include('Includes.header')
<meta name="csrf-token" content="{!! csrf_token() !!}"> 
    <div class="container-fluid pl-0">

        <!--vertical tab-->
        <div class="col-sm-12 get_div_height">
            <div class="row">
                @include('Includes.sidebar')
                <div class="col-lg-9 pt-4 pb-4 pl-0  mx-auto">
                @if(session()->has('success'))
                    <div class="col-sm-3 alert alert-success alert-dismissible msg_bar mx-auto" id="password_update">
                        <button type="button" class="close">&times;</button>
                        Information updated!
                    </div>
                @endif
                    <div class="col-sm-11 mx-auto">
                        <div class="row">
                            <div class="col-sm-6">
                                <span class="d-block text-left text-capitalize float-left font_20">My Profile</span>
                            </div>
                        </div>
                    </div> <?php //print_r($profile_data->password); ?>
                    <!-- profile container -->
                    <div class="col-sm-11 mx-auto mt-1">
                        <div class="row">
                        <form class="col-sm-12 profile_edits" action="{{url('/profile_edit/'.$profile_data->id)}}" method="post" enctype= multipart/form-data>
                        {{ csrf_field() }}
                            <div class="col-sm-12 text-center mt-5">
                                <img id="blah" class="rounded-circle d-block mx-auto"  src='{{$profile_data->profile_pic}}' alt="profile_pic" width="150px" height="150px" />
                                <label class="img_adv_upload float-none profile_pic_upload mt-3">
                                    <input type="file" name="file" class="form-control" id="profile_pic" onchange="readURL(this);" >
                                    Browse to change
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="museum_profile_div overflow-hidden pb-3 mt-5">
                                            <i class="fa fa-user float-left icon" aria-hidden="true"></i>
                                            <input type="text" class="form-control pl-4 pr-4" name="username" placeholder="{{ $profile_data->userName }}" id="museum_user" value="{{ $profile_data->userName }}">
                                            <i class="fa fa-check float-left icon icon_opa" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="museum_profile_div overflow-hidden pb-3 mt-5">
                                            <i class="fa fa-envelope float-left icon" aria-hidden="true"></i>
                                            <input type="email" class="form-control pl-4 pr-4" name="my_email" placeholder="{{ $profile_data->email }}" id="museum_email" value="{{ $profile_data->email }}">
                                            <i class="fa fa-check float-left icon icon_opa" aria-hidden="true"></i>
                                        </div>   
                                    </div>
                                    <button type="button" class="btn btn-primary text-capitalize float-left mt-5 change_pass" data-toggle="modal" data-target="#change_password">Change password</button>
                                    <button type="submit" class="btn btn-primary float-right mt-5 logout_btn pl-5 pr-5">Save</button>
                                    
                                    
                            </div>
                        </div>
                        </form>
                    </div>
                    <!-- manage container -->

                </div>  
            </div> 
        </div>
        <!--vertical tab-->
    </div>

    <!-- modal change password -->
    <div class="modal fade" id="change_password">
        <div class="modal-dialog">
            <div class="modal-content complainant_modal p-4 pb-5">
            
                <!-- Modal Header -->
                <div class="modal-header border-0">
                    <div class="float-left">
                        <span class="modal-title d-block text-capitalize">change password</h3>
                    </div>
                    <button type="button" class="close p-0 pr-2" data-dismiss="modal">×</button>
                </div>
                
                <!-- Modal body --> 
                <div class="modal-body mt-5">
                    
                    <form class="col-sm-12 password_edits" action="{{url('/password_edit/'.$profile_data->id)}}" method="post" enctype= multipart/form-data>
                        {{ csrf_field() }}

                        <!-- <input type="email" class="form-control pl-4 pr-4" name="my_email" placeholder="{{ $profile_data->email }}" id="museum_email" value="{{ $profile_data->email }}"> -->
                        <div class="form-group mt-4">
                            <input type="password" class="form-control" placeholder="Current Password" name="current_password" id="current_pass" required>
                            <span class="pass_check mt-2">Current password and New Password must be different</span>
                        </div>
                        <div class="form-group mt-4">
                            <input type="password" class="form-control" placeholder="New Password" name="new_password" id="new_pass" required>
                        </div>
                        <div class="form-group mt-4">
                            <input type="password" class="form-control" placeholder="Confirm password" name="confirm_password" id="confirm_pass" required>
                            <span class="con_check mt-2">New Password and Confirm password must be same</span>
                        </div>
                        <button type="submit" class="btn btn-primary float-right pl-5 pr-5 pt-2 pb-2 mt-4">Save</button>

                        </form>
                    
                </div>
                
            </div>
        </div>
    </div>
    <!-- modal change password -->

    @include('Includes.footer')

    <script>
        $(document).ready(function(){
            $('form.password_edits').submit(function(){ //alert('clicked');
                current_password = $('#current_pass').val();
                new_pass = $('#new_pass').val();
                confirm_pass = $('#confirm_pass').val();
                if(current_password==new_pass){ 
                    $(".pass_check").addClass('pass_error');
                    return false;
                }
                if(new_pass!=confirm_pass){ 
                    $(".con_check").addClass('pass_error');
                    return false;
                }
                else{
                    return true;
                }
            });
        });
    </script>
    <script>
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#blah')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(150);
            };
            reader.readAsDataURL(input.files[0]);
            }
        }
    </script>