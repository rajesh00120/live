@include('Includes.header')
<div style="width: 100%">
    {{-- <Header /> --}}
   
    <div class="col-sm-12 get_div_height">

    <div class="row">
      @include('Includes.sidebar')
        
      <div class="col-sm-9 mx-auto">
        <div class="museum_dashboard pt-4 pb-4">
          <h3 class="col-sm-11 mx-auto pl-0 pb-3">Dashboard</h3>
          <div class="col-sm-11 mx-auto">
            <div class="row">
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-11 background_white float-right pt-3 pb-3 pl-4 pr-4 admin_box">
                    <img
                      class="img_class float-left"
                      src="{{ URL::asset('public/assets/admin.png') }}"
                      alt="total_users"
                    />
                    <span class="mt-4 float-left">Total Users</span>
                    <cite class="float-right font-weight-bold mt-4">{{$usersCount}}</cite>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-11 background_white float-right pt-3 pb-3 pl-4 pr-4 admin_box">
                    <img
                      class="img_class float-left"
                      src="{{ URL::asset('public/assets/admin.png') }}"
                      alt="total_users"
                    />
                    <span class="mt-4 float-left">Total Albums</span>
                    <cite class="float-right font-weight-bold mt-4">{{$albumCounts}}</cite>
                  </div>
                </div>
              </div>
              <div class="col-sm-4">
                <!-- <div class="row">
                  <div class="col-sm-11 background_white float-right pt-3 pb-3 pl-4 pr-4 admin_box">
                    <img
                      class="img_class float-left"
                      src="{{ URL::asset('public/assets/admin.png') }}"
                      alt="total_users"
                    />
                    <span class="mt-4 float-left">
                      Total photos &amp; videos
                    </span>
                    <cite class="float-right font-weight-bold mt-4">10000</cite>
                  </div>
                </div> -->
              </div>
            </div>
          </div>
    
          <div class="dashboard_listing museum_dashboard pt-4 pb-4 col-sm-11 mx-auto">
            <div class="row">
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-11 background_white float-right pt-3 pb-3 pl-4 pr-4 admin_box">
                    <h3>Recently Users</h3>

            @if (!empty($users))
            @foreach($users as $item)
  
                    <div class="row border border-top-0 border-left-0 border-right-0 pb-3">
                      <div class="col-sm-12">
                        
                        
                        @if($item['profile_pic']!=null && $item['profile_pic']!='')         
                          <img
                          class="rounded-circle img_class float-left mr-3"
                          src={{ $item['profile_pic'] }}
                          alt="total_users"
                          />         
                        @else
                          <img
                          class="rounded-circle img_class float-left mr-3"
                          src="{{ URL::asset('public/assets/admin.png') }}"
                          alt="total_users"
                          />      
                        @endif

                        <span class="font-weight-bold text-capitalize d-block mt-3 word_break">
                          {{$item['userName']}}
                        </span>
                        <cite class="text-capitalize">location</cite>
                      </div>
                    </div>
                    @endforeach
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-sm-11 background_white float-right pt-3 pb-3 pl-4 pr-4 admin_box">
                    <h3>Recently Albums</h3>
                    @if (!empty($albums))
                    @foreach($albums as $item)
                    
                    <div class="row border border-top-0 border-left-0 border-right-0 pb-3">
                      <div class="col-sm-12">
                        <div class="row">
                          <div class="col-sm-3 mt-4">
                            @if($item->timeline_cover!=null && $item->timeline_cover!='')         
                            <img
                              class="rounded-lg img_class float-left"
                              src="{{ $item->timeline_cover }}"
                              alt="total_users"
                            />         
                          @else
                            <img
                            class="rounded-circle img_class float-left"
                            src="{{ URL::asset('public/assets/admin.png') }}"
                            alt="total_users"
                            />      
                          @endif
                          
                           
                          </div>
                          <div class="col-sm-4 mt-4">
                            <span class="text-capitalize d-block mt-4">
                              {{$item->name}}
                            </span>
                          </div>
                          <div class="col-sm-5 mt-4">
                      @if($item->profile_pic!=null && $item->profile_pic!='')         
                          <img
                          class="rounded-circle img_class float-left  mr-3"
                          src={{ $item->profile_pic }}
                          alt="total_users"
                          />         
                        @else
                          <img
                          class="rounded-circle img_class float-left mr-3"
                          src="{{ URL::asset('public/assets/admin.png') }}"
                          alt="total_users"
                          />      
                        @endif
                           
                            <span class="font-weight-bold text-capitalize d-block album_txt_font mt-2">
                              {{$item->userNameData}}
                            </span>
                            <cite class="text-capitalize album_txt_font float-left">
                              location
                            </cite>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endforeach
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-sm-4">
                <!-- <div class="row">
                  <div class="col-sm-11 background_white float-right pt-3 pb-3 pl-4 pr-4 admin_box">
                    <h3>Recently Photos &amp; Albums</h3>
                    <div class="row border border-top-0 border-left-0 border-right-0 pb-3">
                      <div class="col-sm-12">
                        <div class="row">
                          <div class="col-sm-3">
                            <img
                              class="rounded-lg img_class float-left"
                              src="{{ URL::asset('public/assets/admin.png') }}"
                              alt="total_users"
                            />
                          </div>
                          <div class="col-sm-4">
                            <span class="text-capitalize d-block mt-4">
                              Dance at bar
                            </span>
                          </div>
                          <div class="col-sm-5 mt-4">
                            <img
                              class="rounded-circle img_class float-left album_img_class"
                              src="{{ URL::asset('public/assets/admin.png') }}"
                              alt="total_users"
                            />
                            <span class="font-weight-bold text-capitalize d-block album_txt_font float-left mt-2">
                              poul molive
                            </span>
                            <cite class="text-capitalize album_txt_font float-left">
                              location
                            </cite>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>



      </div>
    </div>
  </div>

  @include('Includes.footer')