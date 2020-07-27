@include('Includes.header')
<div style="width: 100%">
    {{-- <Header /> --}}
   
    <div class="col-sm-12 get_div_height">

    <div class="row">
      @include('Includes.sidebar')
        
      <div class="col-sm-10">
        <div class="museum_dashboard pt-4 pb-4 museum_media">
          <h3 class="col-sm-11 pl-0 pb-3">Manage Media</h3>
          
          <div class="table-responsive">

                
            <table id="MediaList" class="table table-bordred table-striped">
                 
                <thead>
                  <th>Profile Photo</th>
                  <th>User's name</th>
                  <th>Album</th>
                  <th>Photos</th>
                  <th>Videos</th>
                  <th>Action</th>
                </thead>
                <tbody>
                 
                </tbody>
        </div>
      </div>



      </div>
    </div>
  </div>

  @include('Includes.footer')