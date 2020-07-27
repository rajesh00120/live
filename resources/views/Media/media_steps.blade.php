@include('Includes.header')
<link href="//vjs.zencdn.net/7.0/video-js.min.css" rel="stylesheet">
<script src="//vjs.zencdn.net/7.0/video.min.js"></script>
<?php //echo "<pre>"; print_r($datesNew); ?>

<meta name="csrf-token" content="{!! csrf_token() !!}">


    <div class="container-fluid pl-0">

        <!--vertical tab-->
        <div class="col-sm-12 get_div_height">
            <div class="row">
                @include('Includes.sidebar')
                @if(session()->has('delete'))
                    <div class="alert alert-danger alert-dismissible msg_bar">
                        <button type="button" class="close" >&times;</button>
                        Deleted Successfully!
                    </div>
                @endif
                <div class="col-sm-10 pt-4 pb-4 pl-0 mx-auto"> <?php //print_r($user_info[0]->id); ?>
                    <div class="col-sm-11 mx-auto">
                        <div class="row">
                            <div class="col-sm-6">
                                <span class="d-block text-left text-capitalize float-left font_20">Manage media</span>
                            </div>
                            <div class="col-sm-6">
                                <a href="/museum/media" class="d-block text-right float-right font_20 sky_blue">Back</a>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-8">
                                @if(!isset($user_info[0]->id))
                                <span>No Data Found!</span>
                                @else
                                <div class="row">
                                    @if(($user_info[0]->profile_pic!='' && $user_info[0]->profile_pic!='null'))
                                        <div class="col-sm-2">
                                            <img class="rounded-circle" src="{{URL::asset($user_info[0]->profile_pic) }}" alt="complaint_image" width="75px" height="75px" />
                                        </div>
                                    @else
                                        <div class="col-sm-2">
                                            <img class="rounded-circle" src="{{URL::asset('public/assets/admin.png' )}}" alt="complaint_image" width="75px" height="75px" />
                                        </div>
                                    @endif
                                    <div class="col-sm-10 mt-1">
                                        <span class="complainant_name d-block">{{$user_info[0]->name}}</span>
                                        <cite class="complainant_email d-block pt-2">{{$user_info[0]->email}}</cite>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-sm-4 text-right">
                                <div class="trash_div float-right ml-3">
                                    <button class="delete_ads" data-id="{{$user_info[0]->id}}" delete_url="delete_album_user" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    <!-- <a href="delete_media/"><i class="fa fa-trash" aria-hidden="true"></i></a> -->
                                </div> 
                                <!-- <div class="circle_div float-right"></div> -->
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-11 mx-auto mt-5">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="sel1" class="float-left mr-3 font_20 font-weight-bold mt-3">Sort by:</label>
                                    <select class="form-control float-left select_month" data-id="{{$user_info[0]->id}}" id="sel1" value="">

                                        @foreach ($datesNew as $year)
                                            <!-- <option value="<?php //$date = $year->date; $date = date('d F Y', strtotime($date)); echo $date; ?>">
                                                <?php 
                                                    //$date = $year->date; 
                                                    //$date = date('F Y', strtotime($date));
                                                    //echo $date;
                                                ?>
                                            </option> -->
                                            <option value="{{$year->Month}} {{$year->Year}}">{{$year->Month}} {{$year->Year}}</option>
                                            
                                            
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2"></div>
                            <div class="col-sm-7">
                                <ul class="nav nav-tabs border_0 media_tabs float-right">
                                    <li class="nav-item">
                                        <a class="nav-link active btn btn-primary border_radius_10" data-toggle="tab" href="#album">Album</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-primary border_radius_10" data-toggle="tab" href="#photos">Photos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-primary border_radius_10" data-toggle="tab" href="#videos">Videos</a>
                                    </li>
                                </ul>
                            </div>         
                        </div>
                        <div class="row">
                            <div class="tab-content col-sm-12 sort_category">
                                <div id="album" class="tab-pane active"><br>
                                    <span class="d-block text-left font_19 color_grey "><span class="title_date"></span><span class="float-right"><span class="count_albums"><?php echo count($total_album); ?></span> Albums</span></span>
                                    <ul class="list-group list-group-horizontal">

                                        @foreach ($total_album as $albums)
                                            <li class="list-group-item"><?php //print_r($albums); die; ?>
                                                @if($albums->timeline_cover=='') 
                                                <div class="row">
                                                    <img class="img_width2 border_radius_10" src="{{URL::asset('public/assets/Logo.png' )}}" alt="" />
                                                </div> 
                                                @else  
                                                <div class="row">
                                                    <img class="img_width2 border_radius_10" src="{{URL::asset('public/album_images/'.$albums->timeline_cover) }}" alt="" />
                                                </div>  
                                                @endif
                                                <div class="row">
                                                    <div class="col-sm-10 pl-0 pr-0">
                                                        <span class="d-block mt-1">{{$albums->album_name}}</span>
                                                        <span class="d-block light_grey">{{$albums->date}}</span>
                                                    </div>
                                                    <div class="col-sm-2 pl-1">
                                                        <div class="trash_div float-right mt-1 ml-3">
                                                            <button class="delete_ads" data-id="{{$albums->album_id}}" delete_url="delete_album" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                        </div> 
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                        
                                    </ul>
                                </div>
                                <div id="photos" class="tab-pane fade"><br>
                                    <span class="d-block text-left font_19 color_grey"><span class="title_date"></span><span class="float-right"><span class="count_photos"><?php echo count($total_photos); ?></span>  Photos</span></span>
                                    <ul class="list-group list-group-horizontal">

                                        @foreach ($total_photos as $photos)
                                            <li class="list-group-item">
                                                @if($photos->album_media_path=='') 
                                                    <div class="row">
                                                        <img class="img_width2 border_radius_10" src="{{URL::asset('public/assets/Logo.png' )}}" alt="" />
                                                    </div> 
                                                @else
                                                    <div class="row">
                                                        <img class="img_width2 border_radius_10" src="{{URL::asset('public/album_images/'.$photos->album_media_path) }}" alt="" />
                                                    </div> 
                                                @endif      
                                                <div class="row">
                                                    <div class="col-sm-10 pl-0 pr-0">
                                                        <span class="d-block mt-1">{{$photos->album_name}}</span>
                                                        <span class="d-block light_grey">{{$photos->created_at}}</span>
                                                    </div>
                                                    <div class="col-sm-2 pl-1">
                                                        <div class="trash_div float-right mt-1 ml-3">
                                                            <button class="delete_ads" data-id="{{$photos->images_id}}" delete_url="delete_photos" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                        </div> 
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                                <div id="videos" class="tab-pane fade"><br>
                                    <span class="d-block text-left font_19 color_grey"><span class="title_date"></span><span class="float-right"><span class="count_vid"><?php echo count($total_videos); ?></span>   Videos</span></span>
                                    <ul class="list-group list-group-horizontal">

                                        @foreach ($total_videos as $videos)
                                            <li class="list-group-item">
                                                @if($videos->album_media_path=='') 
                                                <div class="row">
                                                    <video width="320" height="240" controls>
                                                        <source src="{{URL::asset('public/assets/video.jpg' )}}" type="video/mp4">
                                                    </video>
                                                </div>
                                                @else 
                                                <div class="row">
                                                    
                                                    <video width="320" height="240" preload controls>
                                                        <source src="{{URL::asset('public/album_images/'.$videos->album_media_path) }}">
                                                    </video>

                                                </div> 
                                                @endif
                                                <div class="row">
                                                    <div class="col-sm-10 pl-0 pr-0">
                                                        <span class="d-block mt-1">{{$videos->album_name}}</span>
                                                        <span class="d-block light_grey">{{$videos->created_at}}</span>
                                                    </div>
                                                    <div class="col-sm-2 pl-1">
                                                        <div class="trash_div float-right mt-1 ml-3">
                                                            <button class="delete_ads" data-id="{{$videos->videos_id}}" delete_url="delete_videos" data-toggle="modal" data-target="#deletModel"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                            <!-- <a href="{{$videos->videos_id}}" ><i class="fa fa-trash" aria-hidden="true"></i></a> -->
                                                        </div> 
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- manage tab-->
                    @endif
                </div>  
            </div> 
        </div>
        <!--vertical tab-->
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

    
    @include('Includes.footer')