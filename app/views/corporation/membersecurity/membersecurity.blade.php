@extends('layouts.masterLayout')

@section('html_title', 'Member Security')

@section('page_content')

  <div class="box">
    <div class="box-header">
      <h3 class="box-title">Roles</h3>
    </div>
    <div class ="box-body">
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
                        <td>{{ App\Services\Helpers\Helpers::makePrettyMemberRoleList($e->roleName) }}</td>
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
                        <td>{{ App\Services\Helpers\Helpers::makePrettyMemberRoleList($e->roleName) }}</td>
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
                        <td>{{ App\Services\Helpers\Helpers::makePrettyMemberRoleList($e->roleName) }}</td>
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
                        <td>{{ App\Services\Helpers\Helpers::makePrettyMemberRoleList($e->roleName) }}</td>
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
                        <td>{{ App\Services\Helpers\Helpers::makePrettyMemberRoleList($e->roleName) }}</td>
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
                        <td>{{ App\Services\Helpers\Helpers::makePrettyMemberRoleList($e->roleName) }}</td>
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
                        <td>{{ App\Services\Helpers\Helpers::makePrettyMemberRoleList($e->roleName) }}</td>
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
                        <td>{{ App\Services\Helpers\Helpers::makePrettyMemberRoleList($e->roleName) }}</td>
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
    </div><!-- /.box-body -->
  </div><!-- /.box -->

@stop
