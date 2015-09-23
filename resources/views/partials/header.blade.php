<!-- Header -->
<header class="top-head container-fluid">
    <button type="button" class="navbar-toggle pull-left">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>



    <!-- Right navbar -->
    <ul class="list-inline navbar-right top-menu top-right-menu">
        <!-- Notification -->
        <li class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <i class="fa fa-bell-o"></i>
                <span class="badge badge-sm up bg-pink count">3</span>
            </a>
            <ul class="dropdown-menu extended fadeInUp animated nicescroll" tabindex="5002">
                <li class="noti-header">
                    <p>Notifications</p>
                </li>
                <li>
                    <a href="#">
                        <span class="pull-left"><i class="fa fa-user-plus fa-2x text-info"></i></span>
                        <span>New user registered<br><small class="text-muted">5 minutes ago</small></span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="pull-left"><i class="fa fa-diamond fa-2x text-primary"></i></span>
                        <span>Use animate.css<br><small class="text-muted">5 minutes ago</small></span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="pull-left"><i class="fa fa-bell-o fa-2x text-danger"></i></span>
                        <span>Send project demo files to client<br><small class="text-muted">1 hour ago</small></span>
                    </a>
                </li>

                <li>
                    <p><a href="#" class="text-right">See all notifications</a></p>
                </li>
            </ul>
        </li>
        <!-- /Notification -->

        <!-- user login dropdown start-->
        <li class="dropdown text-center">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <img alt="" src="{{ 'http://cinecliq.assets.s3.amazonaws.com/files/'.$user->logo }}" class="img-circle profile-img thumb-sm">
                <span class="username">{{ $user->person }}</span> <span class="caret"></span>
            </a>
            <ul class="dropdown-menu extended pro-menu fadeInUp animated" tabindex="5003" style="overflow: hidden; outline: none;">
                <li><a href="profile.html"><i class="fa fa-briefcase"></i>Profile</a></li>
                <li><a href="#"><i class="fa fa-sign-out"></i> Log Out</a></li>
            </ul>
        </li>
        <!-- user login dropdown end -->
    </ul>
    <!-- End right navbar -->

</header>
<!-- Header Ends -->