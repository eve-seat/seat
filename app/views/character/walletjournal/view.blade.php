@extends('layouts.masterLayout')

@section('html_title', 'Character Wallet Journal')

@section('page_content')

  <div class="row">
    <div class="col-md-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">
            Wallet Journal for:
            <a href="{{ action('CharacterController@getView', array('characterID' => $characterID)) }}">
              <img src='//image.eveonline.com/Character/{{ $characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
            </a>
            {{ $character_name }}
          </h3>
          <div class="box-tools">
            <ul class="pagination pagination-sm no-margin pull-right">
              {{ $wallet_journal->links() }}
            </ul>
          </div>
        </div><!-- /.box-header -->
        <div class="box-body no-padding">
          <table class="table table-condensed compact table-hover" id="datatable">
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Owner1 Name</th>
                <th>Owner2 Name</th>
                <th>ArgName 1</th>
                <th>Amount</th>
                <th>Balance</th>
              </tr>
            </thead>
            <tbody>

              @foreach ($wallet_journal as $e)

                <tr @if ($e->amount < 0)class="danger" @endif>
                  <td data-order="{{ $e->date }}">
                    <span data-toggle="tooltip" title="" data-original-title="{{ $e->date }}">
                      {{ Carbon\Carbon::parse($e->date)->diffForHumans() }}
                    </span>
                  </td>
                  <td>{{ $e->refTypeName }}</td>
                  <td>
                    <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->ownerID1, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                    {{ $e->ownerName1 }}
                  </td>
                  <td>
                    <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->ownerID2, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                    {{ $e->ownerName2 }}
                  </td>
                  <td>{{ $e->argName1 }}</td>
                  <td>{{ number_format($e->amount, 2, '.', ' ') }}</td>
                  <td>{{ number_format($e->balance, 2, '.', ' ') }}</td>
                </tr>

              @endforeach

            </tbody>
          </table>
        </div><!-- /.box-body -->
        <div class="pull-right">{{ $wallet_journal->links() }}</div>
      </div>
    </div>
  </div>

@stop
