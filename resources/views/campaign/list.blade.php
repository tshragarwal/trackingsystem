@extends('layouts.app')

@section('content')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .table-responsive {
            max-width: 100%;
            overflow-x: auto;
        }
    </style>
    <link href="{{ asset('css/tablefixed.css') }}" rel="stylesheet">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Campaign List</a></li>

            </ol>
        </nav>
        
        @if (!empty($success))
            <div class="alert alert-success" role="alert">
                New Advertiser data successfully saved.
            </div>
        @endif
        <a href="{{ route('campaign.create', ['company_id' => $companyID]) }}" class="btn btn-primary"
            style="margin-bottom:20px;float:right;"> {{ __('Add New Campaign') }} </a>
        <div class="card card-body col-sm-9" style="margin-bottom: 20px">
            <form class="form-inline" action="{{ route('campaign.list', ['company_id' => $companyID]) }}">
                <div class="form-group ">
                    <div class='row'>
                        <div class="col"> <input type="text" class="form-control" name="id"
                                value="{{ !empty($filter['id']) ? $filter['id'] : '' }}" placeholder="Campaign Id"> </div>
                        <div class="col"> <input type="text" class="form-control" name="adver_name"
                                value="{{ !empty($filter['adver_name']) ? $filter['adver_name'] : '' }}"
                                placeholder="Advertiser Name"> </div>
                        <div class="col"> <input type="text" class="form-control" name="campaign_name"
                                value="{{ !empty($filter['campaign_name']) ? $filter['campaign_name'] : '' }}"
                                placeholder="Campaign Name"> </div>
                        <div class="col"> 
                            <button type="submit" class="btn btn-success mb-2">Filter</button> 
                            <a href="{{ route('campaign.list', ['company_id' => $companyID]) }}" class="btn btn-danger mb-2 " >Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover" id='campaign_list_table'>
                    <thead>
                        <tr>

                            <th data-field="id" data-sortable="true" scope="col">Campaign Id <i class="fa fa-sort"></i>
                            </th>
                            <th data-field="advertiser_name" data-sortable="true" scope="col">Advertiser Name <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="campaign_name" data-sortable="true" scope="col">Campaign Name <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="target_count" data-sortable="true" scope="col">Target Count <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="target_url" data-sortable="true" scope="col">Target Url <i
                                    class="fa fa-sort"></i></th>

                            <th data-field="status" data-sortable="true" scope="col">Status <i class="fa fa-sort"></i>
                            </th>
                            <th data-field="referer_status" data-sortable="true" scope="col">Allow Referer Redirection <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="allow_mobile" data-sortable="true" scope="col">Allow Mobile <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="allow_tablet" data-sortable="true" scope="col">Allow Tablet <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="allow_desktop" data-sortable="true" scope="col">Allow Desktop <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="updated_at" data-sortable="true" scope="col">Last Updated <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="created_at" data-sortable="true" scope="col">Created At <i
                                    class="fa fa-sort"></i></th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($data))
                            @foreach ($data as $record)
                                <tr class="camp_{{ $record->id }}">
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->advertiser->name }} ({{ $record->advertiser->id }})</td>
                                    <td>{{ $record->campaign_name }}</td>
                                    <td>{{ $record->target_count }}</td>
                                    <td>{{ $record->target_url }}</td>
                                    <td>{{ $record->status == 1 ? 'Active' : ($record->status == 2 ? 'Paused' : 'Completed') }}</td>

                                    <td>
                                        <span style="font-size: 20px;cursor: pointer;margin-right:5px"> <i
                                                class="active_inactive_toggle fa fa-solid {{ $record->enable_referer_redirection == 1 ? 'fa-toggle-on' : 'fa-toggle-off' }}"
                                                data-type="enable_referer_redirection" data-campaign_id="{{ $record->id }}">
                                            </i> </span>
                                    </td>
                                    <td>
                                        <span style="font-size: 20px;cursor: pointer;margin-right:5px"> <i
                                                class="active_inactive_toggle fa fa-solid {{ $record->allow_mobile == 1 ? 'fa-toggle-on' : 'fa-toggle-off' }}"
                                                data-type="allow_mobile" data-campaign_id="{{ $record->id }}">
                                            </i> </span>
                                    </td>
                                    <td>
                                        <span style="font-size: 20px;cursor: pointer;margin-right:5px"> <i
                                                class="active_inactive_toggle fa fa-solid {{ $record->allow_tablet == 1 ? 'fa-toggle-on' : 'fa-toggle-off' }}"
                                                data-type="allow_tablet" data-campaign_id="{{ $record->id }}">
                                            </i> </span>
                                    </td>
                                    <td>
                                        <span style="font-size: 20px;cursor: pointer;margin-right:5px"> <i
                                                class="active_inactive_toggle fa fa-solid {{ $record->allow_desktop == 1 ? 'fa-toggle-on' : 'fa-toggle-off' }}"
                                                data-type="allow_desktop" data-campaign_id="{{ $record->id }}">
                                            </i> </span>
                                    </td>

                                    <td>{{ $record->updated_at }}</td>
                                    <td>{{ $record->created_at }}</td>
                                    <td>
                                        <a
                                            href="{{ route('campaign.edit', ['company_id' => $companyID, 'id' => $record->id]) }}"><i
                                                class="fa fa-edit"></i></a>
                                        
                                        @if ($record->status == 1 || $record->status == 2)
                                            <a style="margin-left:5px"
                                                href="{{ route('publisherJob.create', ['company_id' => $companyID, 'campaign_id' => $record->id]) }}"><i
                                                    class="fa fa-tasks" aria-hidden="true"></a></i>
                                        @endif

                                        <a style="margin-left: 5px;" href="javascript:void(0)"
                                            name="{{ $record->campaign_name }}" data-toggle="modal"
                                            data-target="#deletecamp" class="delete_camp" id="{{ $record->id }}"><i
                                                class="fa fa-trash-o "></i></a>
                                        <a style="margin-left: 5px;" href="javascript:void(0)" name=""
                                            data-toggle="modal" data-target="#syncGeoLocation" class="sync_Geo_Location"
                                            id="{{ $record->id }}"><i class="fa fa-location-arrow"></i></a>
                                    </td>

                                </tr>
                            @endforeach
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
        <br />
        <!-- Display pagination links -->
        {{ $data->links() }}
        <br />
        <br />
        <br />
    </div>



    <!-- Modal -->
    <div class="modal fade" id="deletecamp" tabindex="-1" role="dialog" aria-labelledby="deletecampLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body delete_body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger delete_camp_confirm" ad_id="">Delete</button>
                </div>
                <div class="delet_message">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="syncGeoLocation" tabindex="-1" role="dialog" aria-labelledby="syncGeoLocationLavel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sync Geo Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Geo Location Syncing will be take 10 mins for updating data</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary sync_camp_confirm" ad_id="">Sync</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        


        
    </script>
    <script>
        $(document).ready(function() {
            var companyID = {!! $companyID !!};
            $('#campaign_list_table').bootstrapTable();
            $('.fixed-table-loading').css('display', 'none');

            $('.delete_camp').on('click', function() {
                $('.delet_message').html('');
                $('.delete_camp_confirm').attr('ad_id', $(this).attr('id'));
                $('.delete_body').html(
                    'Deleting the record will not be reverted. Do you want to delete <h5>' + $(this)
                    .attr('name') + '</h5>');
            });
            $('.sync_Geo_Location').on('click', function() {
                $('.sync_camp_confirm').attr('ad_id', $(this).attr('id'));
                $('.sync_message').html('');
            });

            $('.active_inactive_toggle').on('click', function() {
                var id = $(this).data('campaign_id');
                var token = $('meta[name="csrf-token"]').attr('content');
                var request = $.ajax({
                    url: "/" + companyID + "/campaign/" + id + "/status",
                    type: "PATCH",
                    dataType: "json",
                    data: {
                        _token: token, // Include the CSRF token
                        status: $(this).data("type"),
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        message = JSON.parse(XMLHttpRequest.responseText);
                        alert(message.message);
                    }
                });
            });

            $('.delete_camp_confirm').on('click', function() {
                var campaign_id = $(this).attr('ad_id');
                $('.delet_message').html('');


                var token = $('meta[name="csrf-token"]').attr('content');
                var request = $.ajax({
                    url: "/" + companyID + "/campaign/" + campaign_id,
                    type: "DELETE",
                    dataType: "json",
                    data: {
                        _token: token, // Include the CSRF token
                    },
                    success: function(data) {
                        if (data.status == 0) {
                            $('.delet_message').html('<div class="alert alert-danger" role="alert">' + data
                                .message + '</div>');
                            setTimeout(function() {
                                var closeButton = $('[data-dismiss="modal"]');
                                closeButton.click();
                            }, 3000); // 10,000 milliseconds (10 seconds)
                        } else {
                            $('.camp_' + campaign_id).remove();
                            $('.delet_message').html('<div class="alert alert-primary" role="alert">' + data
                                .message + '</div>');
                            setTimeout(function() {
                                var closeButton = $('[data-dismiss="modal"]');
                                closeButton.click();
                            }, 3000); // 10,000 milliseconds (10 seconds)

                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        message = JSON.parse(XMLHttpRequest.responseText);
                        $('.delet_message').html('<div class="alert alert-danger" role="alert">' + message.message + '</div>');
                        setTimeout(function() {
                            var closeButton = $('[data-dismiss="modal"]');
                            closeButton.click();
                        }, 5000); // 10,000 milliseconds (10 seconds)
                        
                    }

                });

            });


            $('.sync_camp_confirm').on('click', function() {
                var campaign_id = $(this).attr('ad_id');
                var token = $('meta[name="csrf-token"]').attr('content');
                var request = $.ajax({
                    url: "/" + companyID + "/campaign/sync/geolocation/",
                    type: "POST",
                    dataType: "json",
                    data: {
                        _token: token, // Include the CSRF token
                        campaign_id: campaign_id // Include any other data you need for deletion
                    },
                    success: function(data) {
                        if (data.status == 0) {
                            $('.sync_message').html('<div class="alert alert-danger" role="alert">' + data
                                .message + '</div>');
                            setTimeout(function() {
                                var closeButton = $('[data-dismiss="modal"]');
                                closeButton.click();
                            }, 3000); // 10,000 milliseconds (10 seconds)
                        } else {

                            $('.sync_message').html('<div class="alert alert-primary" role="alert">' + data
                                .message + '</div>');
                            setTimeout(function() {
                                var closeButton = $('[data-dismiss="modal"]');
                                closeButton.click();
                            }, 3000); // 10,000 milliseconds (10 seconds)

                        }
                    }
                });
            });


        });
    </script>
@endsection
