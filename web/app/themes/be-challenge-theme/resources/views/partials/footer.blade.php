<footer class="footer">
	<div class="footer__container">
		<div class="footer__social">
			<p>Share</p>
			
			<ul class="share">
				<li>
					<a class="footer-share-facebook" href="#" target="_blank">
						<img src="@asset('images/facebook.svg')" alt="Share on Facebook">
						<span class="screen-reader-text">Facebook</span>
					</a>
				</li>
				<li>
					<a class="footer-share-twitter" href="#" target="_blank">
						<img src="@asset('images/twitter.svg')" alt="Share on Twitter">
						<span class="screen-reader-text">Twitter</span>
					</a>
				</li>
				<li>
					<a class="footer-share-mail" href="#" target="_blank">
						<img src="@asset('images/email.svg')" alt="Share via email">
						<span class="screen-reader-text">Mail</span>
					</a>
				</li>
			</ul>
		</div>
		
		@if (has_nav_menu('primary_navigation'))
			<nav class="footer__nav">
				{!! wp_nav_menu(['theme_location' => 'footer_navigation', 'menu_class' => 'nav']) !!}
			</nav>
		@endif
		
		
		<div class="footer__logo">
			<a href="" target="" aria-label="Footer Link Text">
				<img src="@asset('/images/logo-clique.svg')" alt="Clique Studios BE Coding!">
			</a>
		</div>
		
		
		<div class="footer__small">
			<small>&copy; {!! date('Y') . " $site_name All rights reserved." !!}</small>
		</div>
	</div>
</footer>
