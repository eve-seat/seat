<div class="row">
    <div class="col-md-12">
        <h4>Search Results
            <span class="small pull-right">
                <span onclick="window.location.reload();" class="btn brn-default">Clear Search</span>
            </span>
        </h4>
    </div>
</div>

{{-- Characters --}}
@if(count($characters))
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Found {{ count($characters) }} character(s):</h3>
        </div>

        <div class="box-body">
            <table class="table table-condensed table-hover" id="datatable">
                <thead>
                    <tr>
                        <th>Character</th>
                        <th>Corporation</th>
                        <th>Wallet Balance</th>
                        <th>Born</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($characters as $character)
                        <tr>
                            <td>
                                <a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID)) }}">
                                    <img src='//image.eveonline.com/Character/{{ $character->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                                    {{ $character->characterName }}
                                </a>
                            </td>
                            <td>{{ $character->corporationName }}</td>
                            <td>{{ $character->balance }}</td>
                            <td>{{ Carbon\Carbon::parse($character->DoB)->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
