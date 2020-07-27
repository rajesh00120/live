@include('Includes.header')
    <div class="container-fluid  pl-0">

        <!--vertical tab-->
        <div class="col-sm-12">
            <div class="row">
                @include('Includes.sidebar')
                <div class="col-sm-10 pt-4 pb-4 pl-0 mx-auto">
                    <div class="col-sm-11 mx-auto">
                        <div class="row">
                            <div class="col-sm-6">
                                <span class="d-block text-left text-capitalize float-left font_20">Notifications</span>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-sm-12 notifications_box pb-5">
                                <div class="row pt-4 pb-4 border_bottom">
                                    <div class="col-sm-1">
                                        <div class="img_width">
                                            <img class="rounded-circle img_width" src="{{ URL::asset('public/assets/Logo.png') }}" alt="notify_img" />
                                        </div>
                                    </div>
                                    <div class="col-sm-9 mt-2">
                                        <span class="d-block text-capitalize">karla molive</span>
                                        <p class="">Karla has reported against <cite class="sky_blue">dance at bar</cite> album</p>
                                    </div>
                                    <div class="col-sm-2">
                                        <span class="d-block">10.12.2019</span>
                                    </div>
                                </div>
                                <div class="row pt-4 pb-4 border_bottom">
                                    <div class="col-sm-1">
                                        <div class="img_width">
                                            <img class="rounded-circle img_width" src="{{ URL::asset('public/assets/Logo.png') }}" alt="notify_img" />
                                        </div>
                                    </div>
                                    <div class="col-sm-9 mt-2">
                                        <span class="d-block text-capitalize">karla molive</span>
                                        <p class="">Karla has reported against <cite class="sky_blue">dance at bar</cite> album</p>
                                    </div>
                                    <div class="col-sm-2">
                                        <span class="d-block">10.12.2019</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div> 
        </div>
        <!--vertical tab-->

    </div>

    @include('Includes.footer')