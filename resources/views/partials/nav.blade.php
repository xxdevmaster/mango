<!-- Navbar Start -->
<nav class="navigation">
    <ul class="list-unstyled">
        <li {{ ($current_menu == 'dashboard')?'class=active':''  }}><a href="/"><i class="ion-home"></i> <span class="nav-label">Dashboard</span></a></li>
        <li class="has-submenu {{ ($current_menu == 'allTitles')?'active':''  }}"><a href="#"><i class="ion-flask"></i> <span class="nav-label">General</span></a>
            <ul class="list-unstyled">
                <li {{ ($current_menu == 'allTitles')?'class=active':''  }}><a href="{{ action('TitlesController@index') }}">All Titles (Total 100)</a></li>
                <li><a href="buttons.html">Sales</a></li>
            </ul>
        </li>
        <li class="has-submenu"><a href="#"><i class="ion-settings"></i> <span class="nav-label">My Content</span></a>
            <ul class="list-unstyled">
                <li><a href="grid.html">Content Provider Profile</a></li>
                <li><a href="portlets.html">Partner Stores</a></li>
                <li><a href="widgets.html">My Titles</a></li>
            </ul>
        </li>
        <li class="has-submenu"><a href="#"><i class="ion-compose"></i> <span class="nav-label">My Store</span></a>
            <ul class="list-unstyled">
                <li><a href="form-elements.html">Store Profile</a></li>
                <li><a href="form-validation.html">Store Settings</a></li>
                <li><a href="form-advanced.html">Content Providers</a></li>
                <li><a href="form-wizard.html">Users (Total 100)</a></li>
                <li><a href="form-editor.html">Slider</a></li>
                <li><a href="code-editor.html">Channels Manager</a></li>
                <li><a href="form-uploads.html">Subscription</a></li>
                <li><a href="image-crop.html">Subscribers</a></li>
                <li><a href="form-xeditable.html">Front Page Manager</a></li>
                <li><a href="form-xeditable.html">Gift Vauchers</a></li>
                <li><a href="form-xeditable.html">Url Setup</a></li>
            </ul>
        </li>
        <li class="has-submenu"><a href="#"><i class="ion-grid"></i> <span class="nav-label">Xchange</span></a>
            <ul class="list-unstyled">
                <li><a href="tables.html">Titles</a></li>
                <li><a href="table-datatable.html">Content Providers</a></li>
                <li><a href="tables-editable.html">Stores</a></li>
            </ul>
        </li>
        <li class="has-submenu"><a href="#"><i class="ion-stats-bars"></i> <span class="nav-label">Account Settings</span></a>
            <ul class="list-unstyled">
                <li><a href="morris-chart.html">Account Details</a></li>
                @if($user->is('owner|administrator'))
                    <li {{ ($current_menu == 'account_users')?'class=active':''  }}><a href="{{ action('Account\UsersController@listAll') }}">Account Users</a></li>
                @endif
                <li><a href="flot-chart.html">Payment Methods</a></li>
                <li><a href="rickshaw-chart.html">Banking Info</a></li>
                <li><a href="peity-chart.html">Storage</a></li>
                <li><a href="c3-chart.html">Streaming</a></li>
                <li><a href="other-chart.html">My Profile</a></li>
            </ul>
        </li>

    </ul>
</nav>