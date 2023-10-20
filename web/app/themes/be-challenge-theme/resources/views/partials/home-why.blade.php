<section id="why">
	<div class="home__why">
		<div class="home__why__container">
			<div class="home__why__copy">
				@if ($why_title)
					<h2>{!! $why_title !!}</h2>
				@endif
				
				@if ($why_subtitle)
					<p class="large-text">{!! $why_subtitle !!}</p>
				@endif
				
				{!! $why_content ?: '' !!}
			</div>
			
			@if ($why_image)
				<div class="home__why__image">
					{!! \App\Helper\ImageHelper::imgSrcSet($why_image) !!}
				</div>
			@endif
		</div>
	</div>
</section>
