<?php
/**
 * Title: Hidden blog heading
 * Slug: twentytwentyfive/hidden-blog-heading
 * Description: Hero banner for TopBlogTech - Cool Light Tech Edition.
 * Inserter: no
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

$home_url = home_url( '/' );
?>
<!-- wp:html -->
<section class="tbt-hero-cool">
	<div class="tbt-hero-glow"></div>
	<div class="tbt-hero-content">
		<div class="tbt-hero-badge">
			<span class="tbt-badge-icon">⚡</span>
			<span>LEARN • GROW • EARN</span>
		</div>
		<h1 class="tbt-hero-title">
			Master Tech, Blogging &amp; <span class="tbt-text-gradient">Modern Digital Wealth</span>
		</h1>
		<p class="tbt-hero-subtitle">
			High-authority SEO blueprints, YouTube algorithms, Google AdSense optimization, and realistic online income strategies for modern creators and entrepreneurs.
		</p>
		<div class="tbt-hero-tags">
			<a href="<?php echo esc_url( $home_url . 'category/blogging/' ); ?>" class="tbt-pill">#Blogging 2024</a>
			<a href="<?php echo esc_url( $home_url . 'category/online-money/' ); ?>" class="tbt-pill">#MakeMoneyOnline</a>
			<a href="<?php echo esc_url( $home_url . 'category/youtube-growth/' ); ?>" class="tbt-pill">#YouTubeGrowth</a>
			<a href="<?php echo esc_url( $home_url . 'category/google-adsense/' ); ?>" class="tbt-pill">#AdSenseMastery</a>
			<a href="<?php echo esc_url( $home_url . 'category/passive-income/' ); ?>" class="tbt-pill">#PassiveIncome</a>
		</div>
	</div>
</section>

<div class="tbt-section-header">
	<div class="tbt-section-title-wrap">
		<span class="tbt-section-bar"></span>
		<h2 class="tbt-section-title">Latest Strategic Guides</h2>
		<span class="tbt-badge-count"><?php echo (int) wp_count_posts()->publish; ?> Guides</span>
	</div>
</div>
<!-- /wp:html -->
