<?php
/**
 * Title: Footer
 * Slug: twentytwentyfive/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: TopBlogTech Premium Multi-Column Footer with Newsletter and Brand Links.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

$home_url = home_url( '/' );
$logo_url = home_url( '/wp-content/uploads/topblogtech-nav-logo.png' );
?>
<!-- wp:html -->
<footer class="tbt-footer">
	<!-- Newsletter Section -->
	<div class="tbt-footer-newsletter-wrap">
		<div class="tbt-footer-newsletter-box">
			<div class="tbt-newsletter-text">
				<span class="tbt-newsletter-badge">🚀 WEEKLY CREATOR DISPATCH</span>
				<h3 class="tbt-newsletter-title" style="color: #ffffff !important;">Build, Scale &amp; Monetize Your Digital Media Empire</h3>
				<p class="tbt-newsletter-desc" style="color: #cbd5e1 !important;">Get weekly in-depth blogging blueprints, search algorithm breakdowns, and realistic digital monetization strategies delivered straight to your inbox.</p>
			</div>
			<div class="tbt-newsletter-form">
				<form action="<?php echo esc_url( $home_url . 'contact-us/' ); ?>" method="get" class="tbt-form-inline">
					<input type="email" name="subscribe_email" placeholder="Enter your business email..." required class="tbt-input-email" />
					<button type="submit" class="tbt-btn-subscribe">Subscribe Free →</button>
				</form>
				<p class="tbt-form-subtext" style="color: #94a3b8 !important;">🔒 Zero spam. We respect your privacy. Unsubscribe at any time.</p>
			</div>
		</div>
	</div>

	<!-- Main Footer Columns -->
	<div class="tbt-footer-main">
		<div class="tbt-footer-grid">
			<!-- Col 1: Brand -->
			<div class="tbt-footer-col tbt-footer-col-brand">
				<a href="<?php echo esc_url( $home_url ); ?>" class="tbt-footer-logo-link">
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="TopBlogTech.com Logo" class="tbt-footer-logo" />
				</a>
				<p class="tbt-footer-tagline"><strong>TopBlogTech.com</strong> is an independent technical publication delivering evidence-based analysis on blogging, search optimization, website monetization, and digital creator workflows.</p>
				<div class="tbt-status-pill">
					<span class="tbt-status-dot"></span>
					<span>Verified Technical Editorial Standards</span>
				</div>
			</div>

			<!-- Col 2: Core Niches -->
			<div class="tbt-footer-col">
				<h4 class="tbt-footer-heading">Pillar Niches</h4>
				<ul class="tbt-footer-links">
					<li><a href="<?php echo esc_url( $home_url . 'category/blogging/' ); ?>">Blogging Mastery</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'category/online-money/' ); ?>">Online Money Models</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'category/youtube-growth/' ); ?>">YouTube Algorithm</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'category/google-adsense/' ); ?>">Google AdSense</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'category/passive-income/' ); ?>">Passive Income Assets</a></li>
				</ul>
			</div>

			<!-- Col 3: Popular Guides -->
			<div class="tbt-footer-col">
				<h4 class="tbt-footer-heading">Cornerstone Guides</h4>
				<ul class="tbt-footer-links">
					<li><a href="<?php echo esc_url( $home_url . 'how-to-start-a-tech-finance-blog-modern/' ); ?>">Start a Tech &amp; Finance Blog</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'top-5-realistic-ways-to-earn-money-online/' ); ?>">Realistic Ways to Earn Online</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'youtube-algorithm-secrets-to-get-more-views/' ); ?>">YouTube Algorithm Mechanics</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'getting-google-adsense-approval-fast/' ); ?>">AdSense Eligibility Checklist</a></li>
				</ul>
			</div>

			<!-- Col 4: Platform & Trust -->
			<div class="tbt-footer-col">
				<h4 class="tbt-footer-heading">Editorial &amp; Legal</h4>
				<ul class="tbt-footer-links">
					<li><a href="<?php echo esc_url( $home_url . 'about-us/' ); ?>">About TopBlogTech</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'editorial-policy/' ); ?>">Editorial Policy</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'corrections-policy/' ); ?>">Content Corrections</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'privacy-policy/' ); ?>">Privacy Policy (GDPR/CCPA)</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'affiliate-disclosure/' ); ?>">Affiliate Disclosure</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'terms-of-service/' ); ?>">Terms of Service</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'contact-us/' ); ?>">Contact Editorial Team</a></li>
				</ul>
			</div>
		</div>

		<!-- Footer Bottom Copyright -->
		<div class="tbt-footer-bottom">
			<div class="tbt-footer-bottom-inner">
				<p class="tbt-copyright">© <?php echo date( 'Y' ); ?> <strong>TopBlogTech.com</strong>. All rights reserved. Precision Technical Journalism.</p>
				<div class="tbt-footer-bottom-links">
					<a href="<?php echo esc_url( $home_url ); ?>">Home</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'about-us/' ); ?>">About</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'editorial-policy/' ); ?>">Editorial</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'privacy-policy/' ); ?>">Privacy</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'affiliate-disclosure/' ); ?>">Disclosure</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'contact-us/' ); ?>">Contact</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'wp-sitemap.xml' ); ?>">Sitemap</a>
				</div>
			</div>
		</div>
	</div>
</footer>
<!-- /wp:html -->
