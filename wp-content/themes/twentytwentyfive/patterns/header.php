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
<header class="tbt-header">
	<div class="tbt-header-inner">
		<div class="tbt-brand">
			<a href="<?php echo esc_url( $home_url ); ?>" class="tbt-logo-link" title="TopBlogTech - Learn | Grow | Earn">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="TopBlogTech Logo" class="tbt-logo-img" />
			</a>
		</div>

		<nav class="tbt-nav" aria-label="Main Navigation">
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
			<button class="tbt-mobile-toggle" aria-label="Toggle navigation" onclick="document.querySelector('.tbt-header').classList.toggle('tbt-mobile-open')">
				<span class="tbt-bar"></span>
				<span class="tbt-bar"></span>
				<span class="tbt-bar"></span>
			</button>
		</div>
	</div>
</header>
<!-- /wp:html -->
