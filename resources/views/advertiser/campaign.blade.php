@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add Campaign') }}</div>

                <div class="card-body">
                    
                    <!-- START Advertiser Form for adding new request--> 
                    
                   <form method="POST" action="{{ route('advertiser.campaignsave') }}">
                        @csrf

                             @if (session('success_status'))
                              <h6 class="alert alert-success">{{ session('success_status') }}</h6>
                           @endif
                             @if (session('error_status'))
                              <h6 class="alert alert-danger">{{ session('error_status') }}</h6>
                           @endif
                        
                        <div class="row mb-3">
                            <label for="advertiser_id" class="col-md-4 col-form-label text-md-end">{{ __('Select Advertiser') }}</label>

                            <div class="col-md-6">
                                <div class="form-group">
                                  
                                    <select class="form-control" name="advertiser_id" id="advertiser_id" >
                                        <option value="0">--SELECT--</option>
                                        @foreach($advertiserObj as $object)
                                            <option value="{{$object->id}}">{{$object->name}} ({{$object->id}})</option>
                                        @endforeach
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
                            <label for="campaign_name" class="col-md-4 col-form-label text-md-end">{{ __('Campaign Name') }}</label>

                            <div class="col-md-6">
                                <input id="campaign_name" type="text" class="form-control @error('campaign_name') is-invalid @enderror" name="campaign_name" value="{{ old('campaign_name') }}" required autocomplete="campaign_name" autofocus>
                                @error('campaign_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>                           
                        
                        
 
                           
                        <div class="row mb-3">
                            <label for="link_type" class="col-md-4 col-form-label text-md-end">{{ __('Link Type') }}</label>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" name="link_type" id="link_type" >
                                        <option value="">--SELECT--</option>
                                        <option value="typein">TypeIn</option>
                                        <option value="n2s">N2S</option>
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
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Target Url') }}</label>

                            <div class="col-md-6">
                                <!--<input id="target_url" type="textarea" class="form-control @error('target_url') is-invalid @enderror" name="target_url" value="{{ old('target_url') }}" required autocomplete="target_url" autofocus>-->
                                <textarea class="form-control" name="target_url" id="target_url" rows="3" required autocomplete="target_url" autofocus>{{ old('target_url') }}</textarea>
                                <span style='font-size: 10px;color: red;'>Target Url required '{keyword}' and if you want to verify lead then add tracking={clkid} in target url. In tracking={clkid}, you can replace 'target' with desired key but '{clkid}' must be fixed<br/> </span>
                                @error('target_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>      

                           
                       <div class="row mb-3">
                            <label for="subid" class="col-md-4 col-form-label text-md-end">{{ __('Subid') }}</label>

                            <div class="col-md-6">
                                <input id="subid" type="text" class="form-control @error('subid') is-invalid @enderror" name="subid" value="{{ old('subid') }}" required autocomplete="subid" autofocus>
                                
                                @error('subid')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>                           
                           
                                            
<!--                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Target Query String') }}</label>

                            <div class="col-md-6">
                                <input id="query_string" type="text" class="form-control @error('query_string') is-invalid @enderror" name="query_string" value="{{ old('query_string') }}" required autocomplete="query_string" autofocus>
                                <textarea class="form-control" name="query_string" id="query_string" rows="3" required autocomplete="query_string" autofocus>{{ old('query_string') }}</textarea>
                                @error('query_string')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>-->
                        
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Target Count') }}</label>

                            <div class="col-md-6">
                                <input id="target_count" type="number" class="form-control @error('target_count') is-invalid @enderror" name="target_count" value="{{ old('target_count') }}" required autocomplete="target_count" autofocus>

                                @error('target_count')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Allow Referer Redirection') }}</label>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" name="enable_referer_redirection" id="enable_referer_redirection" >
                                        <option value="1" selected="selected">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                                @error('enable_referer_redirection')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Allow Mobile') }}</label>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" name="allow_mobile" id="allow_mobile" >
                                        <option value="1" selected="selected">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                                @error('allow_mobile')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Allow Tablet') }}</label>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" name="allow_tablet" id="allow_tablet" >
                                        <option value="1" selected="selected">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                                @error('allow_tablet')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Allow Desktop') }}</label>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" name="allow_desktop" id="allow_desktop" >
                                        <option value="1" selected="selected">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                                @error('allow_desktop')
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
                    </form>
                    
                     <!-- END Advertiser Form for adding new request--> 
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>



@endsection