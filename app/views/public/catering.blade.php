@extends('tmpl.public')

@section('content')

    

    <section class="page">
        <h2 class="content__title content__title--main">Catering Packages</h2>

        <div class="row content-boxes__wrapper">
            @foreach($cData as $index=>$package)
            <div class="columns small-12 medium-6 large-4 xlarge-3 xxlarge-2 end">
                <article class="content-box">
                    <div class="row collapse">

                        
                        <a href="/package/{{$package->id}}" class="columns small-4 medium-12 end">
                          <img src="/uploads/{{$catering_image[$package->id]}}" />
                        </a>
                        

                        <section class="columns small-8 medium-12">
                            <div class="content-box__copy">
                                <a href="/package/{{$package->id}}" class="content-box__copy__inner">
                                    <h5 class="content-box__title">{{$package->name}}</h5>
                                    <p class="content-box__summary--display">{{$package->quantity}} pieces</p>
                                </a>
                                
                            </div>
                        </section>
                    </div>
                </article>
            </div>
            @endforeach
        </div>
        <div class=" row">
        <section class="columns small-12 medium-8 medium-push-2 large-6 large-push-3 xlarge-4 xlarge-push-4">
            <div class="section section--form" >
                <!-- <h1 class="page-header">@yield('title')</h1> -->
                @if(isset($user->id))
                     {{ Form::open(array('action' => 'CateringController@packageEnquiry', 'class' => 'form-horizontal')) }}
                @else
                     {{ Form::open(array('action' => 'CateringController@packageEnquiry', 'class' => 'form-horizontal')) }}
                @endif
                    <h2 class="content__title--main--signup">@if (Auth::check()) Hi {{ Auth::user()->fname }}, @endif Currently our catering enquiry system is being renovated<br/><br/>To get a price on a catering package please email us today with the product selection you require =)</h2> 
                    <div class="form-group {{ ($errors->has('fname')) ? 'has-error' : '' ; }}">
                        {{ Form::label('fname', 'First Name: ', array('class' => ' content-title--sub ')) }}
                        <div class="">
                            {{ ($errors->has('fname'))? '<p class="error_message">'. $errors->first('fname') .'</p>' : '' }}
                            {{ Form::text('fname', (isset($input['fname'])? Input::old('fname') : (isset($user->fname)? $user->fname : '' )), array('class' => 'input__text')) }} 
                        </div>
                    </div>
                    
                    <div class="form-group {{ ($errors->has('date')) ? 'has-error' : '' ; }}">
                        {{ Form::label('date', 'Delivery Date: ', array('class' => ' content-title--sub ')) }}
                        <div class="">
                            {{ ($errors->has('date'))? '<p class="error_message">'. $errors->first('date') .'</p>' : '' }}
                            {{ Form::text('date', (isset($input['date'])? Input::old('date') : (isset($user->date)? $user->date : '' )), array('class' => 'input__text')) }} 
                        </div>
                    </div>
                    <div class="form-group {{ ($errors->has('time')) ? 'has-error' : '' ; }}">
                        {{ Form::label('time', 'Delivery Time: ', array('class' => ' content-title--sub ')) }}
                        <div class="">
                            {{ ($errors->has('time'))? '<p class="error_message">'. $errors->first('time') .'</p>' : '' }}
                            {{ Form::text('time', (isset($input['time'])? Input::old('time') : (isset($user->time)? $user->time : '' )), array('class' => 'input__text')) }} 
                        </div>
                    </div>

                    <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' ; }}">
                        {{ Form::label('email', 'Email: ', array('class' => ' content-title--sub ')) }}
                        <div class="">
                            {{ ($errors->has('email'))? '<p class="error_message">'. $errors->first('email') .'</p>' : '' }}
                            {{ Form::text('email', (isset($input['email'])? Input::old('email') : (isset($user->email)? $user->email : '' )), array('class' => 'input__text')) }} 
                        </div>
                    </div>
                    <div class="form-group {{ ($errors->has('mobile')) ? 'has-error' : '' ; }}">
                        {{ Form::label('mobile', 'Mobile: ', array('class' => ' content-title--sub ')) }}
                        <div class="">
                            {{ ($errors->has('mobile'))? '<p class="error_message">'. $errors->first('mobile') .'</p>' : '' }}
                            {{ Form::text('mobile', (isset($input['mobile'])? Input::old('mobile') : (isset($user->mobile)? $user->mobile : '' )), array('class' => 'input__text')) }} 
                        </div>
                    </div>
                    
                    <div class="form-group {{ ($errors->has('message')) ? 'has-error' : '' ; }}">
                        {{ Form::label('message', 'Detailed Message: ', array('class' => ' content-title--sub ')) }}
                        <div class="">
                            {{ ($errors->has('message'))? '<p class="error_message">'. $errors->first('message') .'</p>' : '' }}
                            {{ Form::textarea('message', (isset($input['message'])? Input::old('message') : ''), array('class' => 'input__text', 'placeholder' => 'Example: 12 Chocolate Strawberries, 30 Banana Smoothies and can I please get 12 Rasberry Mousse desserts, for my wedding, except in stead of rasberries I would love it to be made with blueberries =)')) }} 
                        </div>
                    </div>
                    {{-- Form::hidden('package_id', $pData[0]->id) --}}
                    <div class="form-group">
                        <div class="form__buttons">
                            <a href="/">
                                {{ Form::button('Cancel' ,array('class' => 'form__button--sub form__button--sub--signup')) }}
                            </a>
                            {{ Form::submit('Send Enquiry', array('class' => 'side__login__button side__login__button--signup')) }}
                        
                        </div>
                    </div>
                {{ Form::close() }}         
            </div>
        </section>
        </div>
    </section><!--End Band Content-->    
        
    	   
@stop

@section('_footer')
	<script type="text/javascript" src="/public/js/flexslider.js"></script>
	<script type="text/javascript" src="/public/js/tabs.js"></script>
    <script type="text/javascript" src="/js/gallery.js"></script>
@stop
