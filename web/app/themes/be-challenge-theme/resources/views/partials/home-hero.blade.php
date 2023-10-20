<section id="intro" class="home__intro">
    <div class="home__intro__container">
        <div class="home__intro__copy">
	        @if ($hero_title)
                <h1>{!! $hero_title !!}</h1>
	        @endif

	        {!! $hero_content ?: '' !!}
        </div>

        <div class="home__intro__image">
            {!! has_post_thumbnail() ? \App\Helper\ImageHelper::imgSrcSet(get_post_thumbnail_id()) : '' !!}
        </div>
    </div>
</section>
