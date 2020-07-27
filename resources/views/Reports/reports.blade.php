@include('Includes.header')
<div style="width: 100%">
    {{-- <Header /> --}}
   
    <div class="col-sm-12 get_div_height">

    <div class="row">
      @include('Includes.sidebar')

                <div class="col-lg-9 pt-4 pb-4 mx-auto">
                <!-- <link rel="stylesheet" href="public/assets/css/bootstrap.min.css"> -->
                    <link rel="stylesheet" href="public/assets/css/styles.css" />
                    <div class="col-sm-11 mx-auto">
                        <span class="d-block text-left text-capitalize font_20">report &amp; complaints</span>
                        <div class="report_box pt-3 pb-3 mt-4">
                            <div class="col-sm-12">
                                <div class="row report_border pb-4">
                                    <div class="col-sm-3 text-capitalize font-weight-bold pl-4">
                                        Complainants
                                    </div>
                                    <div class="col-sm-3 text-capitalize font-weight-bold">
                                        When
                                    </div>
                                    <div class="col-sm-3 text-capitalize font-weight-bold">
                                        Against media
                                    </div>
                                    <div class="col-sm-3 text-capitalize font-weight-bold">
                                        action
                                    </div>
                                </div>
                                @if (!empty($reports))
                                @foreach($reports as $item)
                                <div class="row complaints_border pt-4 pb-4">
                                    <div class="col-sm-3 text-capitalize">
                                        <div class="row">
                                            <div class="col-sm-5 complainant_image">
                                                @if($item->profile_pic!='null' && $item->profile_pic!='')         
                                                    <img
                                                    class="rounded-circle"
                                                    src={{ $item->profile_pic }}
                                                    alt="image" width="110px" height="110px" 
                                                    />         
                                                @else
                                                    <img
                                                    class="rounded-circle"
                                                    src="{{ URL::asset('public/assets/admin.png') }}"
                                                    alt="image" width="110px" height="110px" 
                                                    />
                                                @endif
                                            </div>
                                            <div class="col-sm-7 complainant_namenemail">
                                                <span class="mt-2">{{$item->userName}}</span>
                                                <cite class="mt-2">{{$item->email}}</cite>
                                                <!-- <ul class="complaints_info mt-2 d-block">
                                                    <li class="complaints_info_li"><img class="rounded-circle" src="{{URL::asset('public/album_images/'.$item->media_images) }}" alt="complaint_image" width="30px" height="30px" /></li>
                                                </ul> -->
                                                <span class="total_complaints mt-2 d-block"><?php echo count($counts[$item->user_id][0]); ?> Complainant</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 text-capitalize">
                                        {{$item->created_at}}
                                    </div>
                                    <div class="col-sm-3 text-capitalize">
                                        @if($item->media_types=='1')
                                            <img class="rounded" src="{{URL::asset('public/album_images/'.$item->media_images) }}" alt="complaint_image" width="75px" height="75px" />
                                        @endif
                                        @if($item->media_types=='2')
                                            <video width="75" height="75" controls autoplay>
                                                <source src="{{URL::asset('public/album_images/'.$item->media_images) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif
                                    </div>
                                    <div class="col-sm-3 text-capitalize">
                                        <a href="view_reports/{{$item->user_id}}"><button type="button" class="view_btn btn btn-primary text-capitalize font-weight-bold">view</button></a>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                                

                            </div>
                        </div>
                    </div>
                </div>  
            </div> 
        </div>
        
        <!--footer-->
        <!-- <div class="col-sm-12">
            <div class="row">
                
            </div>
        </div> -->
        <!--footer-->
    <!-- </div> -->
    
    <script src="public/assets/js/jquery.min.js"></script>
    <script src="public/assets/js/popper.min.js"></script>
    <script src="public/assets/js/bootstrap.min.js"></script>
    <script src="public/assets/js/custom.js"></script> 
        @include('Includes.footer')