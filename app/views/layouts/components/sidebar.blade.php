<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        {{-- See SettingHelper why this has to be more than 1 --}}
        @if(App\Services\Settings\SettingHelper::getSetting('main_character_id') > 1)
          <img src="{{ App\Services\Helpers\Helpers::generateEveImage( App\Services\Settings\SettingHelper::getSetting('main_character_id'), 32) }}" class="img-circle" alt="User Image" />
        @else
          <img src="//image.eveonline.com/Character/1_32.jpg" class="img-circle" alt="User Image" />
        @endif
      </div>
      <div class="pull-left info">
        @if(App\Services\Settings\SettingHelper::getSetting('main_character_id') > 1)
          <p>Hello, {{ App\Services\Settings\SettingHelper::getSetting('main_character_name') }}!</p>
        @else
          <p>
            Hey! Looks like you havent set your main character yet. You can do so
            <a href="{{ action('ProfileController@getView') }}">here</a> after adding some API keys.
          </p>
        @endif
      </div>
    </div>
    <!-- search form -->
    <form action="#" method="get" class="sidebar-form" id="sidebar-form">
      <div class="input-group">
        <input id="search-field" type="text" name="q" class="form-control" placeholder="Search..."/>
        <span class="input-group-btn">
          <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
        </span>
      </div>
    </form>
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <li>
        <a href="{{ URL::to('/') }}">
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
      {{-- Check that the user has any actual roles that relates to a corporation --}}
      @if(\Auth::hasAnyAccess(array('asset_manager', 'contract_manger', 'market_manager', 'pos_manager', 'recruiter', 'wallet_manager')))
        <li class="treeview @if (Request::is('corporation/*')) active @endif">
          <a href="#">
            <i class="fa  fa-group"></i> <span>Corporations</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @if(\Auth::hasAccess('asset_manager'))
              <li><a href="{{ action('CorporationController@getListAssets') }}"><i class="fa fa-angle-double-right"></i> Assets</a></li>
            @endif
            @if(\Auth::hasAccess('contract_manager'))
              <li><a href="{{ action('CorporationController@getListContracts') }}"><i class="fa fa-angle-double-right"></i> Contracts</a></li>
            @endif
            @if(\Auth::hasAccess('asset_manager'))
              <li><a href="{{ action('CorporationController@getListIndustry') }}"><i class="fa fa-angle-double-right"></i> Industry</a></li>
            @endif
            @if(\Auth::hasAccess('recruiter'))
              <li><a href="{{ action('CorporationController@getListKillMails') }}"><i class="fa fa-angle-double-right"></i> Kill Mails</a></li>
            @endif
            @if (\Auth::hasAccess('market_manager'))
              <li><a href="{{ action('CorporationController@getListMarketOrders') }}"><i class="fa fa-angle-double-right"></i> Market Orders</a></li>
            @endif
            @if (\Auth::hasAccess('recruiter'))
              <li><a href="{{ action('CorporationController@getListMemberSecurity') }}"><i class="fa fa-angle-double-right"></i> Member Security</a></li>
              <li><a href="{{ action('CorporationController@getListMemberTracking') }}"><i class="fa fa-angle-double-right"></i> Member Tracking</a></li>
            @endif
            @if (\Auth::hasAccess('recruiter'))
              <li><a href="{{ action('CorporationController@getListMemberStandings') }}"><i class="fa fa-angle-double-right"></i> Standings</a></li>
            @endif
            @if(\Auth::hasAccess('pos_manager'))
              <li><a href="{{ action('CorporationController@getListStarbase') }}"><i class="fa fa-angle-double-right"></i> Starbases</a></li>
            @endif

            @if (\Auth::hasAccess('wallet_manager'))
              <li><a href="{{ action('CorporationController@getListJournals') }}"><i class="fa fa-angle-double-right"></i> Wallet Journal</a></li>
              <li><a href="{{ action('CorporationController@getListLedgers') }}"><i class="fa fa-angle-double-right"></i> Wallet Ledger</a></li>
              <li><a href="{{ action('CorporationController@getListTransactions') }}"><i class="fa fa-angle-double-right"></i> Wallet Transactions</a></li>
            @endif
          </ul>
        </li>
      @endif
      <li class="treeview @if (Request::is('character/*')) active @endif">
        <a href="#">
          <i class="fa fa-user"></i> <span>Characters</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ action('CharacterController@getAll') }}"><i class="fa fa-angle-double-right"></i> All Characters</a></li>
          <li><a href="{{ action('CharacterController@getSearchAssets') }}"><i class="fa fa-angle-double-right"></i> Asset Search</a></li>
          <li><a href="{{ action('MailController@getSubjects') }}"><i class="fa fa-angle-double-right"></i> Mail Subjects</a></li>
          <li><a href="{{ action('MailController@getTimeline') }}"><i class="fa fa-angle-double-right"></i> Mail Timeline</a></li>
          <li><a href="{{ action('CharacterController@getSearchSkills') }}"><i class="fa fa-angle-double-right"></i> Skill Search</a></li>
        </ul>
      </li>
      <li class="treeview @if (Request::is('eve/*')) active @endif">
        <a href="#">
          <i class="fa fa-globe"></i> <span>Eve</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ action('EveController@getSearchItems') }}"><i class="fa fa-angle-double-right"></i> Item Search</a></li>
        </ul>
      </li>

      {{-- superuser only features --}}
      @if (\Auth::isSuperUser())
        <li class="treeview @if (Request::is('configuration/*')) active @endif">
          <a href="#">
            <i class="fa fa-cogs"></i> <span>Configuration</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ action('SettingsController@getApiApplications') }}"><i class="fa fa-angle-double-right"></i> API Applications</a></li>
            <li><a href="{{ action('UserController@getAll') }}"><i class="fa fa-angle-double-right"></i> Users</a></li>
            <li><a href="{{ action('GroupsController@getAll') }}"><i class="fa fa-angle-double-right"></i> Groups</a></li>
            <li><a href="{{ action('SettingsController@getSettings') }}"><i class="fa fa-angle-double-right"></i> SeAT Settings</a></li>
          </ul>
        </li>
      @endif

      <li class="treeview @if (Request::is('debug/*')) active @endif">
        <a href="#">
          <i class="fa fa-circle"></i> <span>Other</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ action('DebugController@getApi') }}"><i class="fa fa-angle-double-right"></i> API Debugger</a></li>
          <li><a href="{{ action('HelpController@getHelp') }}"><i class="fa fa-angle-double-right"></i> Help &amp; About</a></li>
        </ul>
      </li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
