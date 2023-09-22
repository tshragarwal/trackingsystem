@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Update Campaign Detail') }}</div>

                <div class="card-body">
                     @if(!empty($error))
                            <div class="alert alert-error" role="alert">
                                <h4 class="alert-heading">{{$error}}</h4>
                                
                            </div>
                     @elseif(!empty($data))
                    <!--  Advertizer Detail Form --> 
                   
                        <form method="POST" action="{{ route('campaign.update') }}">
                                                             
                            @if (session('success_status'))
                              <h6 class="alert alert-success">{{ session('success_status') }}</h6>
                           @endif
                           
                             @csrf
                             <input id="id" type="hidden" class="form-control  is-invalid " name="id" value="{{ $data->id }}">
                             <div class="row mb-3">
                                 <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Advertizer Name') }}</label>
                                 
                                 <div class="col-md-6">
                                     <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" readonly="readonly" name="name" value="{{ $data->advertiser->name }}  ({{ $data->advertiser->manual_id}})" required autocomplete="name" autofocus>

                                     @error('name')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>
                             
                             <div class="row mb-3">
                                 <label for="campaign_name" class="col-md-4 col-form-label text-md-end">{{ __('Campaign Name') }}</label>
                                 
                                 <div class="col-md-6">
                                     <input id="campaign_name" type="text" class="form-control @error('campaign_name') is-invalid @enderror"  name="campaign_name" value="{{ $data->campaign_name }}" required autocomplete="campaign_name" autofocus>

                                     @error('campaign_name')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>

                                                          
                             <div class="row mb-3">
                                 <label for="subid" class="col-md-4 col-form-label text-md-end">{{ __('Subid') }}</label>
                                 
                                 <div class="col-md-6">
                                     <input id="subid" type="text" class="form-control @error('subid') is-invalid @enderror"  name="subid" value="{{ $data->subid }}" required autocomplete="subid" autofocus>
                                     @error('subid')
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
                                            <option value="">--SELECT Type--</option>
                                            @if ($data->link_type == "typein")
                                                <option value="typein"  Selected > TypeIn</option>
                                            @else
                                                <option value="typein"> TypeIn</option>
                                            @endif

                                            @if ($data->link_type == "n2s")
                                                <option value="n2s"  Selected > N2S</option>
                                            @else
                                                <option value="n2s"> N2S</option>
                                            @endif
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
                                 <label for="target_url" class="col-md-4 col-form-label text-md-end">{{ __('Target URL') }}</label>

                                 <div class="col-md-6">
                                     <input id="target_url" type="text" class="form-control @error('target_url') is-invalid @enderror" name="target_url" value="{{ $data->target_url }}" required>

                                     @error('target_url')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>

                             
                              <div class="row mb-3">
                                 <label for="query_string" class="col-md-4 col-form-label text-md-end">{{ __('Query String') }}</label>

                                 <div class="col-md-6">
                                     <input id="query_string" type="text" class="form-control @error('query_string') is-invalid @enderror" name="query_string" value="{{ $data->query_string }}" required>

                                     @error('query_string')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>

                             
                             
                             
                             <div class="row mb-3">
                                 <label for="target_count" class="col-md-4 col-form-label text-md-end">{{ __('Target Count') }}</label>

                                 <div class="col-md-6">
                                     <input id="target_count" type="number" class="form-control @error('target_count') is-invalid @enderror" name="target_count" value="{{ $data->target_count }}" required >

                                     @error('target_count')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>
           
                             
                            <div class="row mb-3">
                                <label for="status" class="col-md-4 col-form-label text-md-end">{{ __('Status') }}</label>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <select class="form-control" name="status" id="status" >
                                            <option value="0">--SELECT Status--</option>
                                            @if ($data->status == "1")
                                                <option value="1"  Selected > Active</option>
                                            @else
                                                <option value="1"> Active</option>
                                            @endif
                                            
                                            @if ($data->status == "2")
                                                <option value="2"  Selected > Inactive</option>
                                            @else
                                                <option value="2"> Inactive</option>
                                            @endif
                                            
                                            @if ($data->status == "3")
                                                <option value="3"  Selected > Completed</option>
                                            @else
                                                <option value="3"> Completed</option>
                                            @endif
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
                    
                     <!-- END Advertizer Form for adding new request--> 
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>



@endsection