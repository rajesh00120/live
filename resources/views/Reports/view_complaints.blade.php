@include('Includes.header')


<div class="container-fluid">

        <!--vertical tab-->
        <div class="col-sm-12 pl-0 get_div_height">
            <div class="row">
                     @include('Includes.sidebar')
                     @if(session()->has('delete'))
                        <div class="alert alert-danger alert-dismissible msg_bar">
                            <button type="button" class="close" >&times;</button>
                            Complaint deleted!
                        </div>
                    @endif
                <div class="col-lg-9 pt-4 pb-4 mx-auto">
                    @if (isset($all_data['complainant_info'][0]->user_name) )
                    <div class="col-sm-11 mx-auto">
                        <div class="row">
                            <div class="col-sm-6">
                                <span class="d-block text-left text-capitalize font_20">Manage media</span>
                            </div>
                            <div class="col-sm-6">
                                <a href="/museum/reports" class="d-block text-right float-right">Back</a>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-8"><?php //echo "<pre>"; print_r($all_data['complainant_info']); echo "</pre>"; ?> <?php //echo "<pre>"; print_r($all_data['complainant_data']); echo "</pre>"; ?>
                                
                                <div class="row">
                                    @if($all_data['complainant_info'][0]->profile_pic!='null' && $all_data['complainant_info'][0]->profile_pic!='' )
                                    <div class="col-sm-2">
                                        <img class="rounded-circle" src="{{$all_data['complainant_info'][0]->profile_pic}}" alt="img" width="75px" height="75px" />
                                    </div>
                                    @else
                                    <div class="col-sm-2">
                                        <img class="rounded-circle" src="{{URL::asset('public/assets/admin.png') }}" alt="img" width="75px" height="75px" />
                                    </div>
                                    @endif
                                    <div class="col-sm-10 mt-1">
                                        <span class="complainant_name d-block">{{$all_data['complainant_info'][0]->user_name}}</span>
                                        <cite class="complainant_email d-block pt-2">{{$all_data['complainant_info'][0]->email}}</cite>
                                    </div>
                                    <div class="mt-3">
                                        <span class="complainant_reported d-block float-left mt-2"><?php echo count($all_data['complainant_info']); ?> media has been reported against this user</span><button type="button" id="listing_reports" class="show_all_btn btn btn-primary text-capitalize font-weight-bold pl-4 pr-4 ml-3">Show all</button>
                                    </div>
                                </div>

                                <div class="row show_reported_list">
                                    <div class="col-sm-12">
                                    @foreach($all_data['complainant_info'] as $info)
                                        <!-- complainant_detal -->
                                        <div class="col-sm-3 float-left mt-3 mb-3 pl-0">
                                            <div class="media_image">
                                                @if($info->type=='1')
                                                    <img class="rounded" src="{{URL::asset('public/album_images/'.$info->album_media_path) }}" alt="img" width="150" height="150" />
                                                @endif
                                                @if($info->type=='2')
                                                    <video width="150" height="150" controls autoplay>
                                                        <source src="{{URL::asset('public/album_images/'.$info->album_media_path) }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @endif
                                                
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-10">
                                                    <span class="d-block mt-1">{{$info->album_name}}</span>
                                                    <span class="d-block light_grey">{{$info->date}}</span>
                                                </div>
                                                <!-- <div class="col-sm-2">
                                                    <div class="trash_div float-right mt-1 ml-3">
                                                        <a href="{{$info->complainant_id}}" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                    </div> 
                                                </div> -->
                                            </div>
                                        </div>
                                        <!-- complainant_detal -->
                                    @endforeach
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-4 text-right">
                                <div class="trash_div float-right ml-3">
                                    <button class="delete_ads" data-id="{{$info->complainant_id}}" delete_url="delete_complainant" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </div> 
                                <!-- <div class="circle_div float-right"></div> -->
                            </div>
                        </div>

                        <!-- complainants_details -->
                        @foreach($all_data['complainant_info'] as $info)
                        <div class="row mt-4 mb-4">
                            <div class="col-sm-2">
                                <div class="media_image">
                                    @if($info->type=='1')
                                        <img class="rounded" src="{{URL::asset('public/album_images/'.$info->album_media_path) }}" alt="img" width="150" height="150" />
                                    @endif
                                    @if($info->type=='2')
                                        <video width="150" height="150" controls autoplay>
                                            <source src="{{URL::asset('public/album_images/'.$info->album_media_path) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                    
                                </div>
                                <div class="row">
                                    <div class="col-sm-10">
                                        <span class="d-block mt-1">{{$info->album_name}}</span>
                                        <span class="d-block light_grey">{{$info->date}}</span>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="trash_div float-right mt-1 ml-3">
                                            <button class="delete_ads" data-id="{{$info->complainant_report_id}}" delete_url="delete_complaint" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1"></div>
                            <div class="col-sm-9 float-right">
                                <div class="row">

                                    @foreach($all_data['complainant_data'] as $key => $number_array)
                                        @foreach($number_array as $data => $user_data)
                                             @if($info->media_id==$user_data->media_id && $info->user_name!=$user_data->realted_comp_name)
                                            <div class="col-sm-4 complainant_border pt-3 pb-3">
                                                <span class="text-capitalize light_grey">complainant</span>
                                                <div class="row">

                                                    @if($user_data->realted_comp_pic!='null' && $user_data->realted_comp_pic!='' )
                                                        <div class="col-sm-3 mt-3">
                                                            <img class="rounded-circle" src='{{$user_data->realted_comp_pic}}' alt="img" width="75px" height="75px" />
                                                        </div>
                                                    @else
                                                        <div class="col-sm-2">
                                                            <img class="rounded-circle" src="{{URL::asset('public/assets/admin.png') }}" alt="img" width="75px" height="75px" />
                                                        </div>
                                                    @endif

                                                    
                                                    <div class="col-sm-9 mt-3">
                                                        <span class="d-block complainant_name mt-1 ml-2">{{$user_data->realted_comp_name}}</span>
                                                        <span class="d-block complainant_email mt-1 ml-2">{{$user_data->realted_comp_email}}</span>
                                                        <span class="d-block mt-1 light_grey">{{$user_data->realted_comp_name}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach  
                                    @endforeach 

                                </div>
                            </div>
                        </div>
                        @endforeach
                        <!-- complainants_details -->

                    </div>
                    @else
                        Complainant Deleted!
                    @endif
                </div>  
            </div> 
        </div>
        <!--vertical tab-->

        <div class="modal fade" id="myModal">
            <div class="modal-dialog">
                <div class="modal-content complainant_modal p-4">
                
                    <!-- Modal Header -->
                    <div class="modal-header border-0">
                        <div class="float-left">
                            <span class="modal-title d-block">Cute reaction by animal</h3>
                            <cite>11-12-2019</h2>
                        </div>
                        <div class="trash_div float-right mt-1 ml-3">
                            <a href="#"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </div> 
                        <button type="button" class="close p-0 pr-2" data-dismiss="modal">×</button>
                    </div>
                    
                    <!-- Modal body -->
                    <div class="modal-body">
                        <img class="rounded d-block mx-auto modal_image" src="http://172.16.200.38/museum/public/profile_pics/15785699501522066387.jpg" alt="img" />
                    </div>
                    
                </div>
            </div>
        </div>
        
    </div>

    <!-- delete -->
    <div class="modal fade" id="deletModel" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class=" col-sm-12 modal-title text-center mt-2">Are You Sure You Want to Delete?</h5>
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

    <!-- delete -->
    <!-- <div class="modal fade" id="deletecomplainant" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class=" col-sm-12 modal-title text-center mt-2">Are You Sure You Want to Delete this Complainant?</h5>
                    
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
    </div> -->
    <!-- delete -->


    @include('Includes.footer')