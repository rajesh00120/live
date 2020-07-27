<div className="wrapper">
    <nav id="sidebar">
      <div className="sidebar-header">
        <!-- <img
          className="text-center"
          src="{{ URL::asset('public/assets/Logo.png') }}" 
          height="180px"
          width="180px"
        /> -->
      </div>
      <ul className="list-unstyled components sidebar_list">
        <li class="museum_dashboard">
          <a href="/museum/dashboard">
            <i className="fas fa-plus"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="museum_users">
          <a href="/museum/users">
            <i className="fas fa-gamepad"></i>
            <span>Manage Users</span>
          </a>
        </li>
        <li class="museum_media">
          <a href="/museum/media">
            <i className="fas fa-clipboard-list"></i>
            <span>Manage Media</span>
          </a>
        </li>
        <!-- <li class="museum_media_steps">
          <a href="/museum/media_steps">
            <i className="fas fa-clipboard-list"></i>
            <span>Media Steps</span>
          </a>
        </li> -->
        <!-- <li class="museum_notifications">
          <a href="/museum/notifications">
            <i className="fas fa-user"></i>
            <span>Notifications</span>
          </a>
        </li> -->
        <li class="museum_advertise">
          <a href="/museum/advertise">
            <i className="fas fa-question-circle"></i>
            <span>Advertisements</span>
          </a>
        </li>
        <li class="museum_profile">
          <a href="/museum/profile">
            <i className="fas fa-question-circle"></i>
            <span>My Profile</span>
          </a>
        </li>
        <li class="museum_reports">
          <a href="/museum/reports">
            <i className="fas fa-question-circle"></i>
            <span>Reports</span>
          </a>
        </li>
        <li>
          <a href="/museum/logout">
            <i className="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>