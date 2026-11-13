<?php
/**
 * Title: Header
 * Slug: twentytwentyfive/header
 * Categories: header
 * Block Types: core/template-part/header
 * Description: TopBlogTech Premium Header with Brand Logo and Categories.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

$logo_url = home_url( '/wp-content/uploads/topblogtech-nav-logo.png' );
$home_url = home_url( '/' );
?>
<!-- wp:html -->
<header class="tbt-header" id="tbt-site-header">
	<div class="tbt-header-inner">
		<div class="tbt-brand">
			<a href="<?php echo esc_url( $home_url ); ?>" class="tbt-logo-link" title="TopBlogTech - Learn | Grow | Earn">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="TopBlogTech Logo" class="tbt-logo-img" />
			</a>
		</div>

		<nav class="tbt-nav" id="tbt-main-nav" aria-label="Main Navigation">
			<ul class="tbt-nav-list">
				<li><a href="<?php echo esc_url( $home_url ); ?>" class="tbt-nav-item active">Home</a></li>
				<li><a href="<?php echo esc_url( $home_url . 'category/blogging/' ); ?>" class="tbt-nav-item">Blogging</a></li>
				<li><a href="<?php echo esc_url( $home_url . 'category/online-money/' ); ?>" class="tbt-nav-item">Online Money</a></li>
				<li><a href="<?php echo esc_url( $home_url . 'category/youtube-growth/' ); ?>" class="tbt-nav-item">YouTube</a></li>
				<li><a href="<?php echo esc_url( $home_url . 'category/google-adsense/' ); ?>" class="tbt-nav-item">Google AdSense</a></li>
				<li><a href="<?php echo esc_url( $home_url . 'category/passive-income/' ); ?>" class="tbt-nav-item">Passive Income</a></li>
			</ul>
		</nav>

		<div class="tbt-header-actions">
			<a href="<?php echo esc_url( $home_url . 'how-to-start-a-tech-finance-blog-2024/' ); ?>" class="tbt-btn-cta">
				<span>Featured Guide</span> 🚀
			</a>
			<button class="tbt-mobile-toggle" id="tbt-menu-toggle" aria-label="Toggle navigation menu" aria-expanded="false" onclick="var h=document.getElementById('tbt-site-header');var o=h.classList.toggle('tbt-mobile-open');this.setAttribute('aria-expanded',o);">
				<span class="tbt-bar"></span>
				<span class="tbt-bar"></span>
				<span class="tbt-bar"></span>
			</button>
		</div>
	</div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
	var header = document.getElementById('tbt-site-header');
	var toggle = document.getElementById('tbt-menu-toggle');
	if (!header || !toggle) return;
	document.addEventListener('click', function(e) {
		if (header.classList.contains('tbt-mobile-open') && !header.contains(e.target)) {
			header.classList.remove('tbt-mobile-open');
			toggle.setAttribute('aria-expanded', 'false');
		}
	});
	var navLinks = header.querySelectorAll('.tbt-nav-item');
	for (var i = 0; i < navLinks.length; i++) {
		navLinks[i].addEventListener('click', function() {
			header.classList.remove('tbt-mobile-open');
			toggle.setAttribute('aria-expanded', 'false');
		});
	}
});
</script>
<!-- /wp:html -->
