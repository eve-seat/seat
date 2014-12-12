{{-- character contacts --}}
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Contact List ({{ count($contact_list) }})</h3>
      </div><!-- /.box-header -->
      <div class="box-body no-padding">
        <div class="row">

          @foreach (array_chunk($contact_list, (count($contact_list) / 6) > 1 ? count($contact_list) / 6 : 6) as $list)

            <div class="col-md-2">
              <table class="table table-hover table-condensed">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Standing</th>
                  </tr>
                </thead>
                <tbody>

                  @foreach ($list as $contact)

                    <tr>
                      <td>
                        <a href="{{ action('CharacterController@getView', array('characterID' => $contact->contactID)) }}">
                          <img src='//image.eveonline.com/Character/{{ $contact->contactID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $contact->contactName }}
                        </a>
                      </td>
                      <td>
                        @if ($contact->standing == 0)
                        {{ $contact->standing }}
                        @elseif ($contact->standing > 0)
                        <span class="text-green">{{ $contact->standing }}</span>
                        @else
                        <span class="text-red">{{ $contact->standing }}</span>
                        @endif
                      </td>
                    </tr>

                  @endforeach

                </tbody>
              </table>
            </div> <!-- ./col-md-2 -->

          @endforeach

        </div><!-- ./row -->
      </div><!-- /.box-body -->
    </div>
  </div> <!-- ./col-md-12 -->
</div> <!-- ./row -->
