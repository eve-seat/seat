<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">                
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
                <span class="input-group-btn">
                    <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li>
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview @if (Request::is('api-key/*')) active @endif">
                <a href="#">
                    <i class="fa fa-folder-open-o"></i> <span>Key Management</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ action('ApiKeyController@getNewKey') }}"><i class="fa fa-angle-double-right"></i> Add New Key</a></li>
                    <li><a href="{{ action('ApiKeyController@getAll') }}"><i class="fa fa-angle-double-right"></i> List All Keys</a></li>
                    <li><a href="{{ action('ApiKeyController@getPeople') }}"><i class="fa fa-angle-double-right"></i> People</a></li>
                </ul>
            </li>
            <li class="treeview @if (Request::is('corporation/*')) active @endif">
                <a href="#">
                    <i class="fa  fa-group"></i> <span>Corporations</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ action('CorporationController@getListStarBase') }}"><i class="fa fa-angle-double-right"></i> Starbases</a></li>
                    <li><a href="{{ action('CorporationController@getListAssets') }}"><i class="fa fa-angle-double-right"></i> Assets</a></li>
                    <li><a href="{{ action('CorporationController@getListContracts') }}"><i class="fa fa-angle-double-right"></i> Contracts</a></li>
                    <li><a href="{{ action('CorporationController@getListJournals') }}"><i class="fa fa-angle-double-right"></i> Wallet Journal</a></li>
                    <li><a href="{{ action('CorporationController@getListTransactions') }}"><i class="fa fa-angle-double-right"></i> Wallet Transactions</a></li>
                    <li><a href="{{ action('CorporationController@getListMarketOrders') }}"><i class="fa fa-angle-double-right"></i> Market Orders</a></li>
                    <li><a href="{{ action('CorporationController@getListLedgers') }}"><i class="fa fa-angle-double-right"></i> Wallet Ledger</a></li>
                    <li><a href="{{ action('CorporationController@getListMemberTracking') }}"><i class="fa fa-angle-double-right"></i> Member Tracking</a></li>
                    <li><a href="{{ action('CorporationController@getListMemberSecurity') }}"><i class="fa fa-angle-double-right"></i> Member Security</a></li>
                </ul>
            </li>
            <li class="treeview @if (Request::is('character/*')) active @endif">
                <a href="#">
                    <i class="fa fa-user"></i> <span>Characters</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ action('CharacterController@getAll') }}"><i class="fa fa-angle-double-right"></i> All Characters</a></li>
                    <li><a href="{{ action('MailController@getSubjects') }}"><i class="fa fa-angle-double-right"></i> Mail Subjects</a></li>                                
                    <li><a href="{{ action('MailController@getTimeline') }}"><i class="fa fa-angle-double-right"></i> Mail Timeline</a></li>                                
                    <li><a href="{{ action('CharacterController@getSearchAssets') }}"><i class="fa fa-angle-double-right"></i> Asset Search</a></li>
                    <li><a href="{{ action('CharacterController@getSearchSkills') }}"><i class="fa fa-angle-double-right"></i> Skill Search</a></li>
                </ul>
            </li>
            <li class="treeview @if (Request::is('debug/*')) active @endif">
                <a href="#">
                    <i class="fa fa-gear"></i> <span>Other</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ action('DebugController@getApi') }}"><i class="fa fa-angle-double-right"></i> API Debugger</a></li>
                </ul>
            </li>            
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
