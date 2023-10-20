{{--
  Template used for homepage if defined in General > Reading
--}}

@extends('layouts.app')

@section('content')
	@while(have_posts()) @php the_post() @endphp
		@include('partials.home-hero')
		@include('partials.home-about')
		@include('partials.home-why')
		@include('partials.home-faq')
	@endwhile
@endsection
