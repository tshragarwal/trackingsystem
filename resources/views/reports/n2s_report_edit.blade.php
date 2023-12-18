@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Report</a></li>
      <li class="breadcrumb-item"><a href="javascript:void(0)">N2S Report Edit</a></li>
    </ol>
  </nav>
  

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                     @if(!empty($error))
                            <div class="alert alert-error" role="alert">
                                <h4 class="alert-heading">{{$error}}</h4>

                            </div>
                     @elseif(!empty($data))

                        <form method="POST" action="{{ route('report.n2s_report_edit_save') }}">

                            @if (session('success_status'))
                              <h6 class="alert alert-success">{{ session('success_status') }}</h6>
                           @endif
                            @if (session('error_status'))
                              <h6 class="alert alert-danger">{{ session('error_status') }}</h6>
                           @endif

                             @csrf
                             <input id="id" type="hidden" class="form-control  is-invalid " name="id" value="{{ $data->id }}">
                             
                             <div class="row mb-3">
                                 <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Date') }}</label>
                                 <div class="col-md-6">
                                     <input id="name" type="text" class="form-control @error('date') is-invalid @enderror"  name="date" value="{{ $data->date }}" required autocomplete="name" autofocus>
                                     @error('date')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>
                             <div class="row mb-3">
                                 <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Subid') }}</label>
                                 <div class="col-md-6">
                                     <input id="name" type="text" class="form-control @error('subid') is-invalid @enderror"  name="subid" value="{{ $data->subid }}" required autocomplete="name" autofocus>
                                     @error('subid')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>
                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Searches') }}</label>
                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('total_searches') is-invalid @enderror" name="total_searches" value="{{ $data->total_searches }}" required>
                                     @error('total_searches')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>
                             
                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Clicks') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('ad_clicks') is-invalid @enderror" name="ad_clicks" value="{{ $data->ad_clicks }}" required>

                                     @error('ad_clicks')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>                             
                            <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Ctr') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('ctr') is-invalid @enderror" name="ctr" value="{{ $data->ctr }}" required>

                                     @error('ctr')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>


                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Tq') }}</label>
                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('tq') is-invalid @enderror" name="tq" value="{{ $data->tq }}" required>
                                     @error('tq')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>                             

                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Advertiser RPM') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('advertiser_RPM') is-invalid @enderror" name="advertiser_RPM" value="{{ $data->advertiser_RPM }}" required>

                                     @error('advertiser_RPM')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>


                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Advertiser CPC') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('advertiser_CPC') is-invalid @enderror" name="advertiser_CPC" value="{{ $data->advertiser_CPC }}" required>

                                     @error('advertiser_CPC')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>

                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Gross Revenue') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('gross_revenue') is-invalid @enderror" name="gross_revenue" value="{{ $data->gross_revenue }}" required>

                                     @error('gross_revenue')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>
                             
                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Offer Id') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('offer_id') is-invalid @enderror" name="offer_id" value="{{ $data->offer_id }}" required>

                                     @error('offer_id')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>


                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Country') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ $data->country}}" required>

                                     @error('country')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>



                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Publisher RPM') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('publisher_RPM') is-invalid @enderror" name="publisher_RPM" value="{{ $data->publisher_RPM }}" required>

                                     @error('publisher_RPM')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>


                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Publisher RPC') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('publisher_RPC') is-invalid @enderror" name="publisher_RPC" value="{{ $data->publisher_RPC }}" required>

                                     @error('publisher_RPC')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>




                             <div class="row mb-3">
                                 <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Net Revenue') }}</label>

                                 <div class="col-md-6">
                                     <input id="email" type="text" class="form-control @error('revenue') is-invalid @enderror" name="revenue" value="{{ $data->revenue }}" required>

                                     @error('revenue')
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



                </div>
            </div>
        </div>
    </div>
    
    
    


@endsection