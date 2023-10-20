<section id="about">
    <div class="home__about">
        <div class="home__about__container">
            <div class="home__about__intro">
                @if ($about_title)
                    <h2>{!! $about_title !!}</h2>
                @endif
            </div>
            <div class="home__about__studies">
                <div class="home__about__studies__item">
                    <div class="home__about__studies__item__logo__container">
                        {!! $about_image ? \App\Helper\ImageHelper::imgSrcSet($about_image) : '' !!}
                    </div>
                    <div>
                        {!! $about_content ?: '' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>