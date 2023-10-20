@extends('layouts.app')

@section('content')
	<div class="error-page">
		<div class="page-title">
			<h1>{!! $error['title'] ?: __('Error 4040', 'sage') !!}</h1>
		</div>
		
		<div class="page-content">
			@if ($error['content'])
				<div class="content">
					{!! $error['content'] !!}
				</div>
			@endif
			@if ($error['image'])
				<div class="image">
					{!! \App\Helper\ImageHelper::imgSrcSet($error['image']) !!}
				</div>
			@endif
		</div>
		
	</div>
@endsection
