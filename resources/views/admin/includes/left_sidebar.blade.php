========== Left Sidebar Start ========== -->
<div class="vertical-menu">

  <div data-simplebar class="h-100">

    <!--- Sidemenu -->
    <div id="sidebar-menu">

      <!-- Left Menu Start -->
      <ul class="metismenu list-unstyled" id="side-menu">

        <li>
          <a href="{{route('home')}}">
            <i class="bx bxs-dashboard"></i>
            <span>Dashboard</span>
          </a>
        </li>

        <?=populateMenu(); ?>

        <li>
          <a href="javascript: void(0);" class="has-arrow waves-effect">
            <i class="bx bx-food-menu"></i>
            <span>Menu Setup</span>
          </a>
          <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{route('menu.index')}}">Menu List</a></li>
          </ul>
        </li>

      </ul>
    </div>
    <!-- Sidebar -->
  </div>
</div>
<!-- Left Sidebar End -->