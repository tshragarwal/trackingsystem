@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Update Campaign Detail') }}</div>
                    
                    <div class="card-body">
                        @if (!empty($error))
                            <div class="alert alert-error" role="alert">
                                <h4 class="alert-heading">{{ $error }}</h4>

                            </div>
                        @elseif(!empty($data))
                            <!--  Advertiser Detail Form -->

                            <form method="POST" action="{{ route('campaign.update', ['company_id' => $companyID, 'id' => $data->id]) }}">
                                @csrf
                                @method('patch')

                                @if (session('success_status'))
                                    <h6 class="alert alert-success">{{ session('success_status') }}</h6>
                                @endif
                                @if (session('error_status'))
                                    <h6 class="alert alert-danger">{{ session('error_status') }}</h6>
                                @endif

                                
                                <div class="row mb-3">
                                    <label for="name"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Advertiser Name') }}</label>

                                    <div class="col-md-6">
                                        <input id="name" type="text"
                                            class="form-control @error('name') is-invalid @enderror" readonly="readonly"
                                            name="name"
                                            value="{{ $data->advertiser->name }}  ({{ $data->advertiser->manual_email }})"
                                            required autocomplete="name" autofocus>
                                        <input type="hidden" name="advertiser_id" id="advertiser_id" value="{{ $data->advertiser->id }}" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="campaign_name"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Campaign Name') }}</label>

                                    <div class="col-md-6">
                                        <input id="campaign_name" type="text"
                                            class="form-control @error('campaign_name') is-invalid @enderror"
                                            name="campaign_name" value="{{ $data->campaign_name }}" required
                                            autocomplete="campaign_name" autofocus>

                                        @error('campaign_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>




                                <div class="row mb-3">
                                    <label for="link_type"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Link Type') }}</label>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control" name="link_type" id="link_type">
                                                <option value="">--SELECT Type--</option>
                                                <option value="typein" @if ($data->link_type == 'typein') {{ 'Selected' }} @endif> TypeIn</option>
                                                <option value="n2s" @if ($data->link_type == 'n2s') {{ 'Selected' }} @endif> N2S</option>
                                            </select>
                                        </div>

                                        @error('link_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="target_url"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Target URL') }}</label>

                                    <div class="col-md-6">
                                        <input id="target_url" type="text"
                                            class="form-control @error('target_url') is-invalid @enderror" name="target_url"
                                            value="{{ $data->target_url }}" required>
                                        <span style='font-size: 10px;color: red;'>Target Url required '{keyword}' </span>
                                        @error('target_url')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="subid"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Subid') }}</label>

                                    <div class="col-md-6">
                                        <input id="subid" type="text"
                                            class="form-control @error('subid') is-invalid @enderror" name="subid"
                                            value="{{ $data->subid }}" required autocomplete="subid" autofocus>
                                        @error('subid')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!--                              <div class="row mb-3">
                                     <label for="query_string" class="col-md-4 col-form-label text-md-end">{{ __('Query String') }}</label>

                                     <div class="col-md-6">
                                         <input id="query_string" type="text" class="form-control @error('query_string') is-invalid @enderror" name="query_string" value="{{ $data->query_string }}" required>

                                         @error('query_string')
        <span class="invalid-feedback" role="alert">
                                                     <strong>{{ $message }}</strong>
                                                 </span>
    @enderror
                                     </div>
                                 </div>-->




                                <div class="row mb-3">
                                    <label for="target_count"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Target Count') }}</label>

                                    <div class="col-md-6">
                                        <input id="target_count" type="number"
                                            class="form-control @error('target_count') is-invalid @enderror"
                                            name="target_count" value="{{ $data->target_count }}" required>

                                        @error('target_count')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label for="status"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Status') }}</label>
                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <select class="form-control" name="status" id="status">
                                                <option value="0">--SELECT Status--</option>
                                                <option value="1" @if ($data->status == '1') {{ 'Selected' }} @endif> Active</option>
                                                <option value="2" @if ($data->status == '2') {{ 'Selected' }} @endif> Paused</option>
                                                <option value="3" @if ($data->status == '3') {{ 'Selected' }} @endif> Completed</option>
                                            </select>
                                        </div>

                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="status"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Allow Referer Redirection') }}</label>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control" name="enable_referer_redirection"
                                                id="enable_referer_redirection">
                                                <option value="1" @if ($data->enable_referer_redirection == '1') {{ 'Selected' }} @endif> Enable</option>
                                                <option value="0" @if ($data->enable_referer_redirection == '0') {{ 'Selected' }} @endif> Disable</option>
                                            </select>
                                        </div>
                                        @error('status')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label for="status"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Allow Mobile') }}</label>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control" name="allow_mobile" id="allow_mobile">
                                                <option value="1" @if ($data->allow_mobile == '1') {{ 'Selected' }} @endif> Enable</option>
                                                <option value="0" @if ($data->allow_mobile == '0') {{ 'Selected' }} @endif> Disable</option>
                                            </select>
                                        </div>
                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label for="status"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Allow Tablet') }}</label>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control" name="allow_tablet" id="allow_tablet">
                                                <option value="1" @if ($data->allow_tablet == '1') {{ 'Selected' }} @endif> Enable</option>
                                                <option value="0" @if ($data->allow_tablet == '0') {{ 'Selected' }} @endif> Disable</option>
                                            </select>
                                        </div>
                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label for="status"
                                        class="col-md-4 col-form-label text-md-end">{{ __('Allow Desktop') }}</label>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control" name="allow_desktop" id="allow_desktop">
                                                <option value="1" @if ($data->allow_desktop == '1') {{ 'Selected' }} @endif> Enable</option>
                                                <option value="0" @if ($data->allow_desktop == '0') {{ 'Selected' }} @endif> Disable</option>
                                            </select>
                                        </div>
                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Update') }}
                                        </button>

                                    </div>

                                </div>

                            </form>
                        @endif

                        <!-- END Advertiser Form for adding new request-->

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
