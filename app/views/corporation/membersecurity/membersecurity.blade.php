@extends('layouts.masterLayout')

@section('html_title', 'Member Security')

@section('page_content')

<div class="nav-tabs-custom">
  <ul class="nav nav-tabs">
    <li class="active">
      <a href="#roles" data-toggle="tab">Roles</a>
    </li>
    <li>
      <a href="#titles" data-toggle="tab">Titles</a>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="roles">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_roles_general" data-toggle="tab">Roles General</a></li>
          <li><a href="#tab_roles_hq" data-toggle="tab">Roles HQ</a></li>
          <li><a href="#tab_roles_base" data-toggle="tab">Roles Base</a></li>
          <li><a href="#tab_roles_other" data-toggle="tab">Roles Other</a></li>
          <li><a href="#tab_roles_grantable_general" data-toggle="tab">Roles Granted General</a></li>
          <li><a href="#tab_roles_grantable_hq" data-toggle="tab">Roles Granted HQ</a></li>
          <li><a href="#tab_roles_grantable_base" data-toggle="tab">Roles Granted Base</a></li>
          <li><a href="#tab_roles_grantable_other" data-toggle="tab">Roles Granted Other</a></li>
          <li><a href="#tab_roles_log" data-toggle="tab">Roles Changelog</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_roles_general">
            {{-- member roles general --}}
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Member Roles - General</h3>
              </div>
              <div class="box-body no-padding table-responsive">
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="20%">Character</th>
                      <th width="*">Role</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($member_roles as $e)

                      <tr>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->characterID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $e->name }}
                        </td>
                        <td>
                          <ul>
                            @foreach( App\Services\Helpers\Helpers::getSecRolesArray($e->roleID, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div>
          </div><!-- /.tab-pane -->

          <div class="tab-pane" id="tab_roles_hq">
            {{-- member roles headquarter --}}
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Member Roles - Headquarter</h3>
              </div>
              <div class="box-body no-padding table-responsive">
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="20%">Character</th>
                      <th width="*">Role</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($member_roles_hq as $e)

                      <tr>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->characterID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $e->name }}
                        </td>
                        <td>
                          <ul>
                            @foreach( App\Services\Helpers\Helpers::getSecRolesArray($e->roleID, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div>
          </div><!-- /.tab-pane -->

          <div class="tab-pane" id="tab_roles_base">
            {{-- member roles base --}}
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Member Roles - Base</h3>
              </div>
              <div class="box-body no-padding table-responsive">
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="20%">Character</th>
                      <th width="*">Role</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($member_roles_base as $e)

                      <tr>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->characterID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $e->name }}
                        </td>
                        <td>
                          <ul>
                            @foreach( App\Services\Helpers\Helpers::getSecRolesArray($e->roleID, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div>
          </div><!-- /.tab-pane -->

          <div class="tab-pane" id="tab_roles_other">
            {{-- member roles other --}}
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Member Roles - Other</h3>
              </div>
              <div class="box-body no-padding table-responsive">
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="20%">Character</th>
                      <th width="*">Role</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($member_roles_other as $e)

                      <tr>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->characterID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $e->name }}
                        </td>
                        <td>
                          <ul>
                            @foreach( App\Services\Helpers\Helpers::getSecRolesArray($e->roleID, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div>
          </div><!-- /.tab-pane -->

          <div class="tab-pane" id="tab_roles_grantable_general">
            {{-- member roles grantable general --}}
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Member Roles Grantable - General</h3>
              </div>
              <div class="box-body no-padding table-responsive">
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="20%">Character</th>
                      <th width="*">Role</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($member_roles_grantable as $e)

                      <tr>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->characterID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $e->name }}
                        </td>
                        <td>
                          <ul>
                            @foreach( App\Services\Helpers\Helpers::getSecRolesArray($e->roleID, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div>
          </div><!-- /.tab-pane -->

          <div class="tab-pane" id="tab_roles_grantable_hq">
            {{-- member roles grantable headquarter --}}
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Member Roles Grantable - Headquarter</h3>
              </div>
              <div class="box-body no-padding table-responsive">
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="20%">Character</th>
                      <th width="*">Role</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($member_roles_grantable_hq as $e)

                      <tr>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->characterID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $e->name }}
                        </td>
                        <td>
                          <ul>
                            @foreach( App\Services\Helpers\Helpers::getSecRolesArray($e->roleID, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div>
          </div><!-- /.tab-pane -->

          <div class="tab-pane" id="tab_roles_grantable_base">
            {{-- member roles grantable base --}}
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Member Roles Grantable - Base</h3>
              </div>
              <div class="box-body no-padding table-responsive">
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="20%">Character</th>
                      <th width="*">Role</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($member_roles_grantable_base as $e)

                      <tr>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->characterID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $e->name }}
                        </td>
                        <td>
                          <ul>
                            @foreach( App\Services\Helpers\Helpers::getSecRolesArray($e->roleID, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div>
          </div><!-- /.tab-pane -->

          <div class="tab-pane" id="tab_roles_grantable_other">
            {{-- member roles grantable other --}}
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Member Roles Grantable - Other</h3>
              </div>
              <div class="box-body no-padding table-responsive">
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="20%">Character</th>
                      <th width="*">Role</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($member_roles_grantable_other as $e)

                      <tr>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->characterID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $e->name }}
                        </td>
                        <td>
                          <ul>
                            @foreach( App\Services\Helpers\Helpers::getSecRolesArray($e->roleID, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div>
          </div><!-- /.tab-pane -->

          <div class="tab-pane" id="tab_roles_log">
            {{-- member roles change log --}}
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Member Roles - Log</h3>
              </div>
              <div class="box-body no-padding">
                {{-- var_dump($member_roles_log) --}}
                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="12%">Affected Character</th>
                      <th width="12%">Changed by</th>
                      <th width="*">Changed on</th>
                      <th width="10%">Changed at</th>
                      <th width="30%">old Roles</th>
                      <th width="30%">new Roles</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($member_roles_log as $e)

                      <tr>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->characterID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'> {{ $e->characterName }}
                        </td>
                        <td>
                          <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->issuerID, 32) }}' class='img-circle' style='width: 18px;height: 18px;'> {{ $e->issuerName }}
                        </td>
                        <td>{{ $e->roleLocationType }}</td>
                        <td>{{ $e->changeTime }}</td>
                        <td>{{ App\Services\Helpers\Helpers::parseCorpSecurityRoleLog($e->oldRoles) }}</td>
                        <td>{{ App\Services\Helpers\Helpers::parseCorpSecurityRoleLog($e->newRoles) }}</td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div>
          </div><!-- /.tab-pane -->
        </div><!-- /.tab-content -->
      </div>
    </div>
    <div class="tab-pane" id="titles">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          @foreach($member_titles_map as $title)
            @if ( reset($member_titles_map) === $title)
              <li class="active"><a href="#tab_title_{{ $title->titleID }}" data-toggle="tab">{{ $title->titleName }}</a></li>
            @else
              <li><a href="#tab_title_{{ $title->titleID }}" data-toggle="tab">{{ $title->titleName }}</a></li>
            @endif
          @endforeach
        </ul>
        <div class="tab-content">
        @foreach($member_titles_map as $title)
          @if ( reset($member_titles_map) === $title)
            <div class="tab-pane active" id="tab_title_{{ $title->titleID }}">
          @else
            <div class="tab-pane" id="tab_title_{{ $title->titleID }}">
          @endif
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">{{ $title->titleName }}</h3>
              </div>
              <div class="box-body no-padding">
                <table  class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th width="20%"></th>
                      <th>Assigned Permissions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Roles</td>
                      <td>
                        @if(count(App\Services\Helpers\Helpers::getSecRolesArray($title->roles, $corporationID)))
                          <ul>
                            @foreach(App\Services\Helpers\Helpers::getSecRolesArray($title->roles, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td>Roles (HQ)</td>
                      <td>
                        @if(count(App\Services\Helpers\Helpers::getSecRolesArray($title->rolesAtHQ, $corporationID)))
                          <ul>
                            @foreach(App\Services\Helpers\Helpers::getSecRolesArray($title->rolesAtHQ, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td>Roles (Base)</td>
                      <td>
                        @if(count(App\Services\Helpers\Helpers::getSecRolesArray($title->rolesAtBase, $corporationID)))
                          <ul>
                            @foreach(App\Services\Helpers\Helpers::getSecRolesArray($title->rolesAtBase, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td>Roles (other)</td>
                      <td>
                        @if(count(App\Services\Helpers\Helpers::getSecRolesArray($title->rolesAtOther, $corporationID)))
                          <ul>
                            @foreach(App\Services\Helpers\Helpers::getSecRolesArray($title->rolesAtOther, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td>Grantable Roles</td>
                      <td>
                        @if(count(App\Services\Helpers\Helpers::getSecRolesArray($title->grantableRoles, $corporationID)))
                          <ul>
                            @foreach(App\Services\Helpers\Helpers::getSecRolesArray($title->grantableRoles, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td>Grantable Roles (HQ)</td>
                      <td>
                        @if(count(App\Services\Helpers\Helpers::getSecRolesArray($title->grantableRolesAtHQ, $corporationID)))
                          <ul>
                            @foreach(App\Services\Helpers\Helpers::getSecRolesArray($title->grantableRolesAtHQ, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </td>     
                    </tr>
                    <tr>
                      <td>Grantable Roles (Base)</td>
                      <td>
                        @if(count(App\Services\Helpers\Helpers::getSecRolesArray($title->grantableRolesAtBase, $corporationID)))
                          <ul>
                            @foreach(App\Services\Helpers\Helpers::getSecRolesArray($title->grantableRolesAtBase, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td>Grantable Roles (Other)</td>
                      <td>
                        @if(count(App\Services\Helpers\Helpers::getSecRolesArray($title->grantableRolesAtOther, $corporationID)))
                          <ul>
                            @foreach(App\Services\Helpers\Helpers::getSecRolesArray($title->grantableRolesAtOther, $corporationID) as $e)
                              <li>{{ $e }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div> <!-- ./box-body -->
              @if(count(App\Services\Helpers\Helpers::getMembersForTitle($corporationID, $title->titleID)))
                <div class="box-footer">
                  <p>Assigned Member(s):</p>
                    <ul>
                      @foreach(App\Services\Helpers\Helpers::getMembersForTitle($corporationID, $title->titleID) as $e)
                        <li><a href="{{ action('CharacterController@getView', array('characterID' => $e->characterID)) }}">{{ $e-> name }}</a></li>
                      @endforeach
                    </ul>
                </div>
              @endif
            </div><!-- ./box -->
          </div>
        @endforeach
      </div>
    </div>
  </div><!-- /.tab-content -->
</div>

@stop
