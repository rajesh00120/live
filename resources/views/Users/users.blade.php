@include('Includes.header')
<div style="width: 100%">
    {{-- <Header /> --}}
   
    <div class="col-sm-12 get_div_height">

    <div class="row">
      @include('Includes.sidebar')

    @if(session()->has('success'))
      <div class="alert alert-success alert-dismissible msg_bar">
          <button type="button" class="close" >&times;</button>
          Status Updated!
      </div>
    @endif
        <?php //echo "<pre>"; print_r($output); echo "</pre>";?>
      <div class="col-sm-10">
        <div class="museum_dashboard pt-4 pb-4 museum_media">
          <h3 class="col-sm-11 pl-0 pb-3">Users</h3>

          <div class="confirmation_box col-sm-3 mx-auto text-center pt-5 pb-5">
              <span class="d-block font-weight-bold">Are you sure you want to delete this user?</span>
              <div class="col-sm-5 mx-auto">
                <div class="row text-center text-center mt-4">
                  <button type="button" class="btn btn-danger d-inline-block mr-4">No</button>
                  <button type="button" class="btn btn-success d-inline-block ml-2">Yes</button>
                </div>
              </div>
          </div>

          <div class="table-responsive">

                
            <table id="customerList" class="table table-bordred table-striped">
                 
                 <thead>
                
                 <th>Profile photo</th>
                  <th>User's name</th>
                   <th>Location</th>
                   <!-- <th>Media</th> -->
                   <th>Email</th>
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

  
    </script>