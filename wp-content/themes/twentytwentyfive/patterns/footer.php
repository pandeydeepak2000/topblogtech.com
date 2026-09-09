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
				<p class="tbt-newsletter-desc" style="color: #cbd5e1 !important;">Join 10,000+ ambitious creators getting weekly high-CPC blogging playbooks, YouTube algorithm updates, and realistic online income blueprints.</p>
			</div>
			<div class="tbt-newsletter-form">
				<form action="#" method="post" onsubmit="event.preventDefault(); alert('Thank you for subscribing to TopBlogTech!');" class="tbt-form-inline">
					<input type="email" placeholder="Enter your best email..." required class="tbt-input-email" />
					<button type="submit" class="tbt-btn-subscribe">Subscribe Free →</button>
				</form>
				<p class="tbt-form-subtext" style="color: #94a3b8 !important;">🔒 Zero spam. Unsubscribe at any time with a single click.</p>
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
				<p class="tbt-footer-tagline"><strong>TopBlogTech.com</strong> is your trusted roadmap to digital freedom. We publish battle-tested blueprints on tech blogging, video algorithms, AdSense optimization, and modern digital wealth.</p>
				<div class="tbt-status-pill">
					<span class="tbt-status-dot"></span>
					<span>Updated for 2024–2026 Season</span>
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
					<li><a href="<?php echo esc_url( $home_url . 'how-to-start-a-tech-finance-blog-2024/' ); ?>">Start a Tech Blog in 2024</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'top-5-realistic-ways-to-earn-money-online/' ); ?>">Top 5 Ways to Earn Online</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'youtube-algorithm-secrets-to-get-more-views/' ); ?>">YouTube Algorithm Secrets</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'getting-google-adsense-approval-fast/' ); ?>">Fast AdSense Approval Checklist</a></li>
				</ul>
			</div>

			<!-- Col 4: Platform & Trust -->
			<div class="tbt-footer-col">
				<h4 class="tbt-footer-heading">Editorial &amp; Legal</h4>
				<ul class="tbt-footer-links">
					<li><a href="<?php echo esc_url( $home_url . 'about-us/' ); ?>">About TopBlogTech</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'privacy-policy/' ); ?>">Privacy Policy</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'affiliate-disclosure/' ); ?>">Affiliate Disclosure</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'terms-of-service/' ); ?>">Terms of Service</a></li>
					<li><a href="<?php echo esc_url( $home_url . 'contact-us/' ); ?>">Contact Editorial Team</a></li>
				</ul>
			</div>
		</div>

		<!-- Footer Bottom Copyright -->
		<div class="tbt-footer-bottom">
			<div class="tbt-footer-bottom-inner">
				<p class="tbt-copyright">© <?php echo date( 'Y' ); ?> <strong>TopBlogTech.com</strong>. All rights reserved. Designed for ambitious creators.</p>
				<div class="tbt-footer-bottom-links">
					<a href="<?php echo esc_url( $home_url ); ?>">Home</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'about-us/' ); ?>">About</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'privacy-policy/' ); ?>">Privacy</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'affiliate-disclosure/' ); ?>">Disclosure</a>
					<span>•</span>
					<a href="<?php echo esc_url( $home_url . 'contact-us/' ); ?>">Contact</a>
				</div>
			</div>
		</div>
	</div>
</footer>
<!-- /wp:html -->
