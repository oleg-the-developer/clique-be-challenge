<header id="header" class="header">
	<div class="header__main">
		<div class="header__main__container">

			<div class="header__main__logo">
				<a href="{!! $site_url !!}" class="home-link {!! is_front_page() ? 'on-homepage' : '' !!}">
					<img src="@asset('/images/logo-clique.svg')" alt="Clique Studios BE Coding!">
				</a>
			</div>

			<button class="header__main__mobile-toggle hamburger hamburger--slider js-hamburger">
				<div class="hamburger-box">
					<div class="hamburger-inner"></div>
				</div>
			</button>

			<div class="header__main__nav">
				<nav>
					@if (has_nav_menu('primary_navigation'))
						{!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']) !!}
					@endif
				</nav>
			</div>
		</div>
	</div>
</header>
