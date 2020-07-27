@include('Includes.header')
<meta name="csrf-token" content="{!! csrf_token() !!}"> 
    <div class="container-fluid pl-0">
        <!--vertical tab-->
        <div class="col-sm-12 get_div_height">
            <div class="row">
                @include('Includes.sidebar')

                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible msg_bar">
                        <button type="button" class="close" >&times;</button>
                        Advertisement added!
                    </div>
                @endif

                @if(session()->has('slash'))
                    <div class="alert alert-success alert-dismissible msg_bar">
                        <button type="button" class="close" >&times;</button>
                        Splash added!
                    </div>
                @endif

                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible msg_bar">
                        <button type="button" class="close" >&times;</button>
                        Please use jpeg format image
                    </div>
                @endif

                @if(session()->has('upload_error'))
                    <div class="alert alert-danger alert-dismissible msg_bar">
                        <button type="button" class="close" >&times;</button>
                        File size should be less than 5mb.
                    </div>
                @endif
               
                @if(session()->has('delete'))
                    <div class="alert alert-danger alert-dismissible msg_bar">
                        <button type="button" class="close" >&times;</button>
                        Advertisement deleted!
                    </div>
                @endif
                @if(session()->has('status'))
                    <div class="alert alert-success alert-dismissible msg_bar">
                        <button type="button" class="close" >&times;</button>
                        Status changed!
                    </div>
                @endif
                <div class="col-lg-9 pt-4 pb-4 mx-auto">
                    <div class="col-sm-11 mx-auto">
                        <div class="row">
                            <div class="col-sm-6">
                                <span class="d-block text-left text-capitalize float-left font_20">Advertisement</span>
                            </div>

                            
                            <div class="col-sm-3">
                                <a href="" class="d-block text-right float-right font_20 color_green text-capitalize" data-toggle="modal" data-target="#create_slash"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create New Splash </a>
                            </div>
                            <div class="col-sm-3">
                                <a href="" class="d-block text-right float-right font_20 color_green text-capitalize" data-toggle="modal" data-target="#create_adv"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create new Advertisement</a>
                            </div>
                        </div>
                    </div>

                    <!-- advertisement -->
                    <div class="col-sm-11 mx-auto">
                        <div class="report_box pt-3 pb-3 mt-4">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group pl-3">
                                           
                                        </div>
                                    </div>
                                    <div class="col-sm-7"></div>
                                    <!-- <div class="col-sm-2"><label for="sel1" class="float-left mr-3 font_20 font-weight-bold mt-3">Saved</label><div class="circle_div float-left mt-3"></div></div> -->
                                </div>
                                <div class="row border_blue pb-4 mt-3">
                                    <div class="col-sm-2 text-capitalize font-weight-bold text-center">
                                        Thumbnail
                                    </div>
                                    <div class="col-sm-3 text-capitalize font-weight-bold">
                                        Title
                                    </div>
                                    <div class="col-sm-3 text-capitalize font-weight-bold">
                                        Description
                                    </div>
                                    <div class="col-sm-2 text-capitalize font-weight-bold">
                                        Started On
                                    </div>
                                    <div class="col-sm-2 text-capitalize font-weight-bold">
                                        Action
                                    </div>
                                </div><?php //echo "<pre>"; print_r($data); echo "</pre>"; die; ?>
                                <!-- dynamic row-->
                                @foreach ($data as $value)

                                <div class="row complaints_border pt-4 pb-4">
                                    <div class="col-sm-2 text-capitalize">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <img class="rounded d-block mx-auto" src="{{URL::asset('public/assets/ad_images/'.$value->ad_images )}}" alt="adv_image" width="110px" height="110px" >         
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 text-capitalize word_break">
                                        {{ $value->ad_title }}
                                        @if($value->ad_title == '')
                                        Splash
                                        @endif
                                    </div>
                                    <div class="col-sm-3 text-capitalize word_break">
                                        {{ $value->ad_url }}
                                    </div>
                                    <div class="col-sm-2 text-capitalize">
                                        {{ $value->created_at }}
                                    </div>
                                    <div class="col-sm-2text-capitalize">

                                        @if(($value->ad_status=='1') && ($value->type!='slash'))
                                        <div class="play_div float-left mt-2">
                                            <a href="pause_users/{{ $value->id }}/{{ $value->ad_status }}"><i class="fa fa-pause" aria-hidden="true"></i></a>
                                        </div>
                                        @endif
                                        
                                        @if($value->ad_status=='0')
                                        <div class="pause_div float-left mt-2">
                                            <a href="pause_users/{{ $value->id }}/{{ $value->ad_status }}"><i class="fa fa-play" aria-hidden="true"></i></a>
                                        </div>
                                        @endif

                                        @if($value->type!='slash')
                                        <div class="trash_div float-right ml-3">
                                            <button class="delete_ads" data-id="{{ $value->id }}" delete_url="delete_ad" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </div>
                                        @else
                                        <div class="trash_div float-right ml-3" style="display: none;">
                                            <button class="delete_ads" data-id="{{ $value->id }}" delete_url="delete_ad" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </div>
                                        @endif

                                    </div>
                                </div>
                                @endforeach
                                <!-- dynamic row-->
                            </div>

                        </div>
                    </div>
                    <!-- advertisement -->

                </div>  
            </div> 
        </div>
        <!--vertical tab-->
    </div>

    <!-- modal create -->
    <div class="modal fade" id="create_adv">
        <div class="modal-dialog">
            <div class="modal-content complainant_modal p-4">

            <!-- Modal Header -->
            
                <!-- Modal Header -->
                <div class="modal-header border-0">
                    <div class="float-left">
                        <span class="modal-title d-block text-capitalize">Create New advertisement</h3>
                    </div>
                    <button type="button" class="close p-0 pr-2" data-dismiss="modal">×</button>
                </div>
                
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{url('/create_ads')}}" method="post" enctype='multipart/form-data' onsubmit="return clicktovalid()" >
                    {{ csrf_field() }}
                        <div class="form-group">
                            <img id="adv_image" class="rounded-circle d-block mx-auto"  src="{{URL::asset('public/assets/admin.png' )}}" alt="image_adver" width="150px" height="150px" />
                            <label for="profile_pic" class="img_adv_upload float-none profile_pic_upload mt-3">
                                <input type="file" name="file" class="form-control" id="profile_pic" onchange="readURL(this);" >
                                Browse
                            </label>
                            <span id="image_class" class="required_class">Image is required*</span>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="adv_title" placeholder="Title of the advertisement" id="title_adv" >
                            <span id="title_class" class="required_class">Title is required*</span>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="adv_url" placeholder="Enter URL for advertisement" id="url_adv" >
                            <span id="url_class" class="required_class">Url is required*</span>
                        </div>
                        <!-- <div class="form-group">
                            <input type="password" class="form-control" name="adv_password" placeholder="Enter password" id="adv_password" required>
                        </div> -->
                        <select name="country" id="country" class="form-control" >
                            <option value="">Select Country</option>
                                @foreach($rowCount as $key => $value)
                                    '<option value='{{$value->country_id}}'>{{$value->country_name}}</option>'
                                @endforeach    
                        </select>
                        <span id="country_class" class="required_class">Country is required*</span>
                        <select name="state" id="state" class="form-control" >
                            <option value="">Select State</option>
                        </select>
                        <span id="state_class" class="required_class">State is required*</span>
                        <select name="city" id="city" class="form-control" >
                            <option value="">Select City</option>
                        </select>
                        <span id="city_class" class="required_class">City is required*</span>
                        <button type="submit" class="btn btn-primary float-right pl-5 pr-5 pt-2 pb-2" >Save</button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
    <!-- modal create -->

     <!-- modal create -->
     <div class="modal fade" id="create_slash">
        <div class="modal-dialog">
            <div class="modal-content complainant_modal p-4">

            <!-- Modal Header -->
            
                <!-- Modal Header -->
                <div class="modal-header border-0">
                    <div class="float-left">
                        <span class="modal-title d-block text-capitalize">Create Slash </h3>
                    </div>
                    <button type="button" class="close p-0 pr-2" data-dismiss="modal">×</button>
                </div>
                
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{url('/create_slash')}}" method="post" enctype='multipart/form-data' onsubmit="return clickSlashtovalid()" >
                    {{ csrf_field() }}
                        <div class="form-group">
                            <img id="slash_image" class="rounded-circle d-block mx-auto"  src="{{URL::asset('public/assets/admin.png' )}}" alt="image_adver" width="150px" height="150px" />
                            <label for="slash_pic" class="img_adv_upload float-none profile_pic_upload mt-3">
                                <input type="file" name="file" class="form-control" id="slash_pic" onchange="readSlashURL(this);" >
                                Browse
                            </label>
                            <span id="image_class" class="required_class">Image is required*</span>
                        </div>
                      
                        <span id="city_class" class="required_class">City is required*</span>
                        <button type="submit" class="btn btn-primary float-right pl-5 pr-5 pt-2 pb-2" >Save</button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
    <!-- modal create -->

    <!-- modal create map -->
    <div class="modal fade" id="select_loc">
        <div class="modal-dialog">
            <div class="modal-content complainant_modal p-4">
            
                <!-- Modal Header -->
                <div class="modal-header border-0">
                    <div class="float-left">
                        <span class="modal-title d-block text-capitalize">Select locations of advertisement</h3>
                    </div>
                    <button type="button" class="close p-0 pr-2" data-dismiss="modal">×</button>
                </div>
                
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="">
                        <div class="mapouter mb-4"><div class="gmap_canvas"><iframe width="420" height="200" id="gmap_canvas" src="https://maps.google.com/maps?q=university%20of%20san%20francisco&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://www.embedgooglemap.net">embedgooglemap.net</a></div><style>.mapouter{position:relative;text-align:right;height:200px;width:420px;}.gmap_canvas {overflow:hidden;background:none!important;height:200px;width:420px;}</style></div>
                        <button type="submit" class="btn btn-primary float-right pl-5 pr-5 pt-2 pb-2">Save</button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
    <!-- modal create map -->

    <!-- delete -->
    <div class="modal fade" id="deletModel" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class=" col-sm-12 modal-title text-center mt-2">Are You Sure You Want to Delete this Advertisement?</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                </div>
                <div class="modal-body" style="margin:0 auto; display:flex;">
                    <div>
                        <button class="btn hor-grd btn-grd-success btn-round mr-2 btn-success" data-dismiss="modal">Cancel</button>
                    </div>
                <div>
                    </div>
                        <button class="btn btn-danger hor-grd btn-grd-danger btn-round remove" delete="1">Delete</button>
                        <input type="hidden" name='delete_id'  class="form-control">
                        <input type="hidden" name="delete_url"  class="form-control">
                        <input type="hidden" name="token" id="csrf-token" value="{{ Session::token() }}" />
                    </div>    
                </div>
            </div>
        </div>
    </div>
    <!-- delete -->

    

    @include('Includes.footer')

    <script>
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#adv_image')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(150);
            };
            reader.readAsDataURL(input.files[0]);
            }
        }

        function readSlashURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#slash_image')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(150);
            };
            reader.readAsDataURL(input.files[0]);
            }
        }
    </script>