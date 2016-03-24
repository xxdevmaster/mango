<!-- Navbar Start -->
<?php $current_menu = (isset($current_menu)?$current_menu:'');?>
<nav class="navigation">
    <ul class="list-unstyled">
        @if($user->is('superadmin'))
        <li {{ ($current_menu == 'dashboard')?'class=active':''  }}><a href="/"><i class="ion-home"></i> <span class="nav-label">Dashboard</span></a></li>
        <li class="has-submenu {{ ($current_menu == 'allTitles')?'active':''  }}"><a href="#"><i class="ion-flask"></i> <span class="nav-label">General</span></a>
            <ul class="list-unstyled">
                <li {{ ($current_menu == 'allTitles')?'class=active':''  }}><a href="{{ action('TitlesController@titlesShow') }}">All Titles (Total 100)</a></li>
                <li><a href="buttons.html">Sales</a></li>
                <li><a href="buttons.html">Age Ratings</a></li>
                <li><a href="buttons.html">Content Providers</a></li>
                <li><a href="buttons.html">Stores</a></li>
                <li><a href="buttons.html">Stores Editor</a></li>
                <li><a href="buttons.html">Accounts Components Editor</a></li>
                <li><a href="buttons.html">Accounts Management</a></li>
            </ul>
        </li>
        <li class="has-submenu"><a href="#"><i class="ion-settings"></i> <span class="nav-label">Translations</span></a>
            <ul class="list-unstyled">
                <li><a href="grid.html">Genres</a></li>
                <li><a href="portlets.html">Jobs</a></li>
                <li><a href="widgets.html">Countries</a></li>
            </ul>
        </li>
        <li class="has-submenu"><a href="#"><i class="ion-stats-bars"></i> <span class="nav-label">Account Settings</span></a>
            <ul class="list-unstyled">
                <li><a href="morris-chart.html">Account Details</a></li>
                <li><a href="{{ action('Account\UsersController@listAll') }}">Features Manager</a></li>
                @if($user->is('owner|administrator'))
                    <li {{ ($current_menu == 'account_users')?'class=active':''  }}><a href="{{ action('Account\UsersController@listAll') }}">Account Users</a></li>
                @endif
            </ul>
        </li>
        @else
        <li {{ ($current_menu == 'dashboard')?'class=active':''  }}><a href="/"><i class="ion-home"></i> <span class="nav-label">Dashboard</span></a></li>
        <li class="has-submenu {{ ($current_menu == 'allTitles')?'active':''  }}"><a href="#"><i class="ion-flask"></i> <span class="nav-label">General</span></a>
            <ul class="list-unstyled">
                <li {{ ($current_menu == 'allTitles')?'class=active':''  }}>
                    <a href="{{ action('TitlesController@titlesShow') }}">
                        @inject('helper', 'App\Libraries\CHhelper\CHhelper')
                        All Titles (Total {{ $helper->getTitlesTotal() }})
                    </a>
                </li>
                <li><a class="menuDisabled" href="buttons.html">Sales</a></li>
            </ul>
        </li>
        @if($CHpermissions->isCPPL() || $CHpermissions->isCP())
        <li class="has-submenu"><a href="#"><i class="ion-settings"></i> <span class="nav-label">My Content</span></a>
            <ul class="list-unstyled">
                <li {{ ($current_menu == 'Company Profile')?'class=active':''  }}><a href="/xchange/contentProvider">Content Provider Profile</a></li>
                <li><a href="/partner/stores">Partner Stores</a></li>
                <li><a href="/CPTitles">My Titles</a></li>
            </ul>
        </li>
        @endif
        @if($CHpermissions->isTrueCP())
        <li class="has-submenu"><a href="#"><i class="ion-compose"></i> <span class="nav-label">My Store</span></a>
            <ul class="list-unstyled">
                <li><a href="/store/profile">Store Profile</a></li>
                <li><a href="/store/settings">Store Settings</a></li>
                <li><a href="/store/contentProviders">Content Providers</a></li>
                <li>
                    <a href="/store/userManagement">
                        @inject('helper', 'App\Libraries\CHhelper\CHhelper')
                        Users (Total {{ $helper->countUsers() }})
                    </a>
                </li>
                <li><a href="/store/slider">Slider</a></li>
                <li><a href="/store/channelsManager">Channels Manager</a></li>
                <li><a class="menuDisabled" href="/store/subscription">Subscription</a></li>
                <li><a class="menuDisabled" href="/store/subscribersManagement">Subscribers</a></li>
                <li><a class="menuDisabled" href="/store/frontPageManager">Front Page Manager</a></li>
                <li><a href="/store/giftVoucher">Gift Vauchers</a></li>
                <li><a class="menuDisabled" href="/store/urlSetup">Url Setup</a></li>
            </ul>
        </li>
        @endif
        @if($CHpermissions->isPL() || $CHpermissions->isCPPL())
        <li class="has-submenu"><a href="#"><i class="ion-grid"></i> <span class="nav-label">Xchange</span></a>
            <ul class="list-unstyled">
                <li><a href="/xchange/titles">Titles</a></li>
                <li><a href="/xchange/contentProviders">Content Providers</a></li>
                <li><a href="/xchange/stores">Stores</a></li>
            </ul>
        </li>
        @endif
        <li class="has-submenu"><a href="#"><i class="ion-stats-bars"></i> <span class="nav-label">Account Settings</span></a>
            <ul class="list-unstyled">
                <li><a class="menuDisabled" href="morris-chart.html">Account Details</a></li>
                @if($user->is('owner|administrator'))
                    <li {{ ($current_menu == 'account_users')?'class=active':''  }}><a href="{{ action('Account\UsersController@listAll') }}">Account Users</a></li>
                @endif
                <li><a class="menuDisabled" href="{{ action('Account\UsersController@listAll') }}">Features Manager</a></li>
                <li><a class="menuDisabled" href="flot-chart.html">Payment Methods</a></li>
                <li><a class="menuDisabled" href="rickshaw-chart.html">Banking Info</a></li>
                <li><a class="menuDisabled" href="peity-chart.html">Storage</a></li>
                <li><a class="menuDisabled" href="c3-chart.html">Streaming</a></li>
                <li><a class="menuDisabled" href="other-chart.html">My Profile</a></li>
            </ul>
        </li>
        @endif
    </ul>
</nav>


<style>
    .menuDisabled, .menuDisabled:hover {
        opacity:0.1 !important;
        color:#ddd !important;
        cursor:text !important;
    }
</style>
<script>
    $('.menuDisabled').click(function(){
        return false;
    });
</script>