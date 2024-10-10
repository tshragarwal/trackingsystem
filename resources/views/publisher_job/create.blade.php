@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Assign Publisher Job') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('publisherJob.store', ['company_id' => $companyID,]) }}">
                            @csrf

                            @if (session('success_status'))
                                <h6 class="alert alert-success">{{ session('success_status') }}</h6>
                                <h6 class="alert alert-primary">{{ session('link_url') }}</h6>
                            @endif

                            @if (session('error_status'))
                                <h6 class="alert alert-danger">{{ session('error_status') }}</h6>
                            @endif


                            <div class="row mb-3">
                                <label for="advertiser_id"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Select Advertiser') }}</label>

                                <div class="col-md-6">
                                    <div class="form-group">

                                        <select class="form-control" name="advertiser_id" id="advertiser_id">
                                            <option value="0">--SELECT--</option>
                                            @if (!empty($optionalCampaignDetails))
                                                <option value="{{ $optionalCampaignDetails->advertiser->id }}" selected>
                                                    {{ $optionalCampaignDetails->advertiser->name }}
                                                    ({{ $optionalCampaignDetails->advertiser->id }})</option>
                                            @else
                                                @foreach ($advertisers as $object)
                                                    <option value="{{ $object->id }}">{{ $object->name }}
                                                        ({{ $object->id }})</option>
                                                @endforeach
                                            @endif
                                                
                                        </select>
                                    </div>

                                    @error('advertiser_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="advertiser_campaign_id"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Select Campaign') }}</label>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control advertiser_campaign_id_select"
                                            name="advertiser_campaign_id" id="advertiser_campaign_id">
                                            <option value="0">--SELECT--</option>
                                            @if (!empty($optionalCampaignDetails))
                                                <option value="{{ $optionalCampaignDetails->id }}" selected>{{ $optionalCampaignDetails->campaign_name }}({{ $optionalCampaignDetails->id }})</option>
                                            @endif
                                        </select>
                                    </div>

                                    @error('advertiser_campaign_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div id="form_further_info" style="{{ empty($optionalCampaignDetails) ? 'display:none' : '' }}">
                                <div class="row mb-3">
                                    <label for="publisher_id"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Select Publisher') }}</label>


                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <select class="form-control" name="publisher_id" id="publisher_id">
                                                <option value="0">--SELECT--</option>
                                                @foreach ($publisher as $object)
                                                    <option value="{{ $object->id }}">{{ $object->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @error('publisher_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="target_count"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Target Count') }}</label>

                                    <div class="col-md-6">
                                        <input id="target_count" type="number"
                                            class="form-control @error('target_count') is-invalid @enderror"
                                            name="target_count" value="{{ old('target_count') }}" autocomplete="email">

                                        @error('target_count')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="fallback_url"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Fallback URL') }}</label>

                                    <div class="col-md-6">
                                        <input id="fallback_url" type="text"
                                            class="form-control @error('fallback_url') is-invalid @enderror"
                                            name="fallback_url" value="{{ old('fallback_url') }}" autocomplete="email">

                                        @error('fallback_url')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Submit') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var companyID = {!! $companyID !!}
        $('#advertiser_id').on('change', function() {
            $('#form_further_info').attr('style', 'display:none');
            var request = $.ajax({
                url: "/" + companyID + "/campaign/" + this.value + "/list",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    html = '<option value="0">--SELECT--</option>';
                    if ($.trim(data)) {
                        var data = data.data;
                        $.each(data, function(i) {
                            html += '<option value="' + data[i].id + '">' + data[i]
                                .campaign_name + '</option>';
                        });
                        $('#form_further_info').attr('style', 'display:block');
                    } else {
                        $('#form_further_info').attr('style', 'display:none');
                    }

                    $('.advertiser_campaign_id_select').html(html);

                }
            });
        });
    </script>


@endsection
