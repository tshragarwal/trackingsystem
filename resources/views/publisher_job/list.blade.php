@extends('layouts.app')
@section('content')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="{{ asset('css/tablefixed.css') }}" rel="stylesheet">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Publisher Job List</a></li>
            </ol>
        </nav>
        @if ($user_type == 'admin')
            <a href="{{ route('publisherJob.create', ['company_id' => $companyID]) }}" class="btn btn-primary"
                style="margin-bottom:20px;float:right;">{{ __('Assign Publisher Job') }}</a>
        @endif
        @if (!empty($success))
            <div class="alert alert-success" role="alert"> New Advertiser data successfully saved. </div>
        @endif

        <div class="card card-body col-sm-10" style="margin-bottom: 20px">
            <form class="form-inline" action="{{ route('publisherJob.list', ['company_id' => $companyID]) }}">
                <div class="form-group ">
                    <div class='row'>
                        <div class="col"> <input type="text" class="form-control" name="id"
                                value="{{ !empty($filter['id']) ? $filter['id'] : '' }}" placeholder="Job ID"> </div>
                        <div class="col"> <input type="text" class="form-control" name="pub_name"
                                value="{{ !empty($filter['pub_name']) ? $filter['pub_name'] : '' }}"
                                placeholder="Publisher Name"> </div>
                        <div class="col"> <input type="text" class="form-control" name="adver_name"
                                value="{{ !empty($filter['adver_name']) ? $filter['adver_name'] : '' }}"
                                placeholder="Advertiser Name"> </div>
                        <div class="col"> <input type="text" class="form-control" name="campaign_name"
                                value="{{ !empty($filter['campaign_name']) ? $filter['campaign_name'] : '' }}"
                                placeholder="Campaign Name"> </div>
                        <div class="col"> 
                            <button type="submit" class="btn btn-success mb-2">Filter</button> 
                            <a href="{{ route('publisherJob.list', ['company_id' => $companyID]) }}" class="btn btn-danger mb-2">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-container">
            <div class="table-responsive" style="min-height: 650px;">
                <table class="table table-hover" id='publisher_job_list_table'>
                    <thead>
                        <tr>
                            <th data-field="id" data-sortable="true" scope="col">Job Id <i class="fa fa-sort"></i></th>
                            <th data-field="publisher_name" data-sortable="true" scope="col">Publisher Name <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="advertiser_name" data-sortable="true" scope="col">Advertiser name <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="campaign_name" data-sortable="true" scope="col">Campaign name <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="target_url" data-sortable="true" scope="col">Campaign Target Url <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="proxy_url" data-sortable="true" scope="col">Link <i class="fa fa-sort"></i>
                            </th>
                            <th data-field="target_count" data-sortable="true" scope="col">Target Count <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="tracking_count" data-sortable="true" scope="col">Tracking Count <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="updated_at" data-sortable="true" scope="col">Updated At <i
                                    class="fa fa-sort"></i></th>
                            <th data-field="created_at" data-sortable="true" scope="col">Created At <i
                                    class="fa fa-sort"></i></th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if (!empty($data))
                            @foreach ($data as $record)
                                <tr class="publisher_job_id_{{ $record->id }}">
                                    <th scope="row">{{ $record->id }}</th>
                                    <td>{{ $record->publisher->name }} ({{ $record->publisher->id }})</td>
                                    <td>{{ $record->campaign->advertiser->name }}
                                        ({{ $record->campaign->advertiser->id }})</td>
                                    <td>{{ $record->campaign->campaign_name }}</td>
                                    <td>{{ $record->campaign->target_url }}</td>
                                    <td>{{ $domain }}/search?code={{ $record->proxy_url }}&offerid={{ $record->id }}&q={keyword}
                                    </td>
                                    <td>{{ $record->target_count }}</td>
                                    <td>{{ $record->tracking_count }}</td>
                                    <td>{{ $record->updated_at }}</td>
                                    <td>{{ $record->created_at }}</td>
                                    <td> <span class="active_inactive_toggle" status="{{ $record->status }}"
                                            id="{{ $record->id }}"
                                            style="font-size: 20px;cursor: pointer;margin-right:5px"> <i
                                                class="fa fa-solid {{ $record->status == 1 ? 'fa-toggle-on' : 'fa-toggle-off' }}">
                                            </i> </span>
                                        <a style="font-size: 18px" href="javascript:void(0)" data-toggle="modal"
                                            data-target="#deletecamp" class="delete_camp" id="{{ $record->id }}"> <i
                                                class="fa fa-trash-o "></i></a>
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

        <!-- Modal -->
        <div class="modal fade" id="deletecamp" tabindex="-1" role="dialog" aria-labelledby="deletecampLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Publisher Job</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                                aria-hidden="true">&times;</span> </button>
                    </div>
                    <div class="modal-body delete_body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger delete_camp_confirm" ad_id="">Delete</button>
                    </div>
                    <div class="delet_message"></div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {

                var companyID = {!! $companyID !!};

                $('#publisher_job_list_table').bootstrapTable();
                $('.fixed-table-loading').css('display', 'none');

                $('.delete_camp').on('click', function() {
                    $('.delete_camp_confirm').attr('ad_id', $(this).attr('id'));
                    $('.delete_body').html('Deleting the Joblisher Job will not be reverted.');
                    $('.delet_message').html('');
                });


                $('.active_inactive_toggle').on('click', function() {
                    var id = $(this).attr('id');
                    var token = $('meta[name="csrf-token"]').attr('content');
                    var request = $.ajax({
                        url: "/" + companyID + "/publisher-job/" + id + "/toggle-status",
                        type: "PATCH",
                        dataType: "json",
                        data: {
                            _token: token, // Include the CSRF token
                        },
                        success: function(data) {
                            alert(data.message);
                            location.reload();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            message = JSON.parse(XMLHttpRequest.responseText);
                            alert(message.message);
                        }
                    });
                });




                $('.delete_camp_confirm').on('click', function() {
                    var publisher_job_id = $(this).attr('ad_id');
                    $('.delete_message').html('');
                    var token = $('meta[name="csrf-token"]').attr('content');
                    var request = $.ajax({
                        url: "/" + companyID + "/publisher-job/" + publisher_job_id,
                        type: "DELETE",
                        dataType: "json",
                        data: {
                            _token: token, // Include the CSRF token
                        },
                        success: function(data) {
                            $('.publisher_job_id_' + publisher_job_id).remove();
                            $('.delet_message').html(
                                '<div class="alert alert-primary" role="alert">' + data
                                .message + '</div>');
                            setTimeout(function() {
                                var closeButton = $('[data-dismiss="modal"]');
                                closeButton.click();
                            }, 5000); // 10,000 milliseconds (10 seconds)
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            message = JSON.parse(XMLHttpRequest.responseText);
                            $('.delet_message').html(
                                '<div class="alert alert-danger" role="alert">' + message.message + '</div>');
                            setTimeout(function() {
                                var closeButton = $('[data-dismiss="modal"]');
                                closeButton.click();
                            }, 5000); // 10,000 milliseconds (10 seconds)
                        }
                    });
                });
            });
        </script>


    @endsection
