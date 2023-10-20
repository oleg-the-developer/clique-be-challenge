<section id="faq" class="home__faq">
    <div class="home__faq__container">
        <div class="home__faq__copy">
            @if ($faq_title)
                <h2>{!! $faq_title !!}</h2>
            @endif

            <button id="expand">{!! $expand_accordion ?: __('Expand All', 'sage') !!}</button> <span class="divider"></span>
            <button id="collapse">{!! $collapse_accordion ?: __('Collapse All', 'sage') !!}</button>

            @if ($faq_repeater_title)
                <h3>{!! $faq_repeater_title !!}</h3>
            @endif

            <div class="faq">
                @foreach ($faq as $index => $item)
                    <button class="question faq {{ $index === 0 ? 'open' : '' }}">
                        {!! $item->post_title !!}
                    </button>
                    <div class="answer" style="{{ $index === 0 ? 'display:block' : 'display:none' }}">
                        {!! $item->post_content !!}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
